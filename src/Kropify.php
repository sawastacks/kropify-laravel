<?php

/**
 * Copyright (c) 2023 - present
 * Kropify - Kropify.php
 * Author: Sawa Stacks - sawastacks@gmail.com
 * GitHub : github.com/sawastacks
 * Initial version created on: 10/03/2023
 * MIT license: https://github.com/sawastacks/kropify-laravel/blob/master/LICENSE
 */

namespace SawaStacks\Utils;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Intervention\Image\Facades\Image;
use Exception;

/**
 * @method static self getFile(string $file, string $filename = null)
 * @method static self maxWoH(int $max)
 * @method static self save(string $path)
 */

class Kropify
{
    /**
     * The requested file.
     *
     * @var string
     */
    private static $_getFile;
    /**
     * Set new file name
     *
     * @var string
     */
    private static $_setFileName;
    /**
     * Set path
     *
     * @var string
     */
    private static $_setPath;

    /**
     * Set maximum width or height
     *
     * @var int
     */
    private static $_maxWoH;

    /**
     * Get file info
     */
    private static $_fileInfo = null;

    public $getName = null;
    public $getSize = null;
    public $getWidth = null;
    public $getHeight = null;

    public function __call($name, $arguments)
    {
        return $this;
    }

    /**
     * Get requested image file
     * 
     * @param string $file
     * @param string $filename
     * @return SawaStacks\Utils\Kropify
     */
    public static function getFile($file, $filename = null)
    {
        self::$_getFile = $file;
        self::$_setFileName = $filename;
        return new static;
    }

    /**
     * @param int $max
     * @return SawaStacks\Utils\Kropify
     */
    public static function maxWoH($max = 500)
    {
        self::$_maxWoH = $max;
        return new static;
    }

    /**
     * @method static string addEndingSlash(string $path)
     */
    public static function addEndingSlash($path)
    {
        $slashType = (strpos($path, '\\') === 0) ? 'win' : 'unix';
        $lastChar = substr($path, strlen($path) - 1, 1);
        if ($lastChar != '/' && $lastChar != '\\') {
            $path .= ($lastChar == 'win' ? '\\' : '/');
        }
        return $path;
    }

    /**
     * Generate and return unique image filename
     * 
     * @method static string setFileName(string $path, string $filename)
     */
    public static function setFileName($path, $filename)
    {
        $filename = self::decideFileExtension($filename);
        if ($pos = strrpos($filename, '.')) {
            $name = substr($filename, 0, $pos);
            $ext = substr($filename, $pos);
        } else {
            $name = $filename;
        }

        $newpath = $path . '/' . $filename;
        $newname = $filename;
        $counter = 1;
        while (file_exists($newpath)) {
            $newname = $name . '_' . $counter . $ext;
            $newpath = $path . '/' . $newname;
            $counter++;
        }
        return $newname;
    }

    /**
     * Return real file extension
     * 
     * @method static string decideFileExtension(string $filename)
     */
    public static function decideFileExtension($filename)
    {
        $fn = null;
        if (preg_match('/(\.jpg|\.png|\.bmp|\.jpg)$/i', $filename)) {
            $fn = $filename;
        } else {
            $fn = $filename .= ".png";
        }
        return $fn;
    }

    /**
     * Upload cropped image without maximum width or height option
     * 
     * @method static string uploadWithoutMaxWoH(string $file, string $path, string $filename)
     */
    public static function uploadWithoutMaxWoH($file, $path, $filename)
    {
        try {
            $upload = $file->move($path, $filename);
            if ($upload) {
                $arr = [
                    'getName' => $filename,
                    'getSize' => self::getUploadeImageSize($path, $filename),
                    'getWidth' => self::getUploadeImageDimension($path, $filename)[0],
                    'getHeight' => self::getUploadeImageDimension($path, $filename)[1],
                ];
                self::$_fileInfo = (object)$arr;
            } else {
                return [];
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Upload cropped image with maximum width or height option
     * 
     * @method static string uploadWithMaxWoH(string $file, string $path, string $filename, int $maxWoH)
     */
    public static function uploadWithMaxWoH($file, $path, $filename, $maxWoH)
    {
        try {

            if ($maxWoH && $maxWoH != null) {
                $s_width = $maxWoH;
                $s_height = $maxWoH;
                $actual_path = $path;
                $path = $path . $filename;
                $image = Image::make($file->path());
                $image->height() > $image->width() ? $s_width = null : $s_height = null;
                $image->resize($s_width, $s_height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save($path);

                $arr = [
                    'getName' => $filename,
                    'getSize' => self::getUploadeImageSize($actual_path, $filename),
                    'getWidth' => self::getUploadeImageDimension($actual_path, $filename)[0],
                    'getHeight' => self::getUploadeImageDimension($actual_path, $filename)[1],
                ];
                self::$_fileInfo = (object)$arr;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Return all Image Details
     * 
     * @return self
     */
    public static function getInfo()
    {
        return self::$_fileInfo;
    }

    /**
     * Return Image Name
     * 
     * @return self
     */
    public static function getName()
    {
        return self::getInfo()->getName;
    }

    /**
     * Return Image Size
     * 
     * @return self
     */
    public static function getSize()
    {
        return self::getInfo()->getSize;
    }

    /**
     * Return Image Width
     * 
     * @return self
     */
    public static function getWidth()
    {
        return self::getInfo()->getWidth;
    }

    /**
     * Return Image Height
     * 
     * @return self
     */
    public static function getHeight()
    {
        return self::getInfo()->getHeight;
    }

    /**
     * Save cropped image to the specified path
     * 
     * @param  string $path
     * @return self
     */
    public static function save($path)
    {
        if (!self::$_getFile) {
            throw new Exception('Define "getFile()" function with value');
        }

        if ($path == null || empty($path)) {
            throw new Exception('Define "save($path)" by adding real path argument');
        }
        self::$_setPath = $path;
        $getpath = self::$_setPath;
        $path = self::addEndingSlash($getpath);
        File::ensureDirectoryExists($path);
        $toPath = $path;
        $filename = (self::$_setFileName) ? self::$_setFileName :  md5(rand(1, 10)) . time() . bin2hex(random_bytes(10)) . '.png';
        $new_filename = self::setFileName($path, $filename);
        $file = self::$_getFile;
        self::$_maxWoH ? self::uploadWithMaxWoH($file, $toPath, $new_filename, self::$_maxWoH) : self::uploadWithoutMaxWoH($file, $toPath, $new_filename);

        return new static;
    }

    /**
     *  @method static string getUploadeImageSize(string $path, string $filename)
     */
    public static function getUploadeImageSize($path, $filename)
    {
        return File::size($path . $filename);
    }

    /**
     *  @method static string getUploadeImageDimension(string $path, string $filename)
     */
    public static function getUploadeImageDimension($path, $filename)
    {
        return getimagesize($path . $filename);
    }
}
