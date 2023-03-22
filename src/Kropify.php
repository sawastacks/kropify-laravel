<?php

/**
 * Copyright (c) 2023 - present
 * Kropify - Kropify.php
 * author: MB'DUSENGE Callixte - info@irebelibrary.com
 * web : github.com/mberecall
 * Initial version created on: 10/03/2023
 * MIT license: https://github.com/mberecall/kropify-laravel/blob/master/LICENSE
 */

namespace Mberecall\Kropify;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Exception;

/**
 * @method static self file(string $file, string $filename = null, int $maxDim = null)
 * @method static self dest(string $path)
 * @method static self upload()
 */

class Kropify
{
    private static $_file;
    private static $_filename;
    private static $_maxDim;
    private static $uploadTo;

    public function __call($name, $arguments)
    {
        return $this;
    }

    /**
     * @method static string file(string $file, string $filename, int $maxDim)
     */
    public static function file($file, $filename = null, $maxDim = null)
    {
        self::$_file = $file;
        self::$_filename = $filename;
        self::$_maxDim = $maxDim;
        return new static;
    }

    /**
     *  @method static string is_base64(string $value)
     */
    public static function is_base64($value)
    {
        // Check if there are valid base64 characters
        if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) return false;

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($value, true);
        if (false === $decoded) return false;

        // Encode the string again
        if (base64_encode($decoded) != $value) return false;

        return true;
    }

    /**
     *  @method static string dest(string $path)
     */
    public static function dest($path)
    {
        self::$uploadTo = $path;
        return new static;
    }

    /**
     * @method static string setFileName(string $path, string $filename)
     */
    public static function setFileName($path, $filename)
    {
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
     *  @method static string addEndingSlash(string $path)
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
     *  @method static string getUploadeImageSize(string $path, string $filename)
     */
    public static function getUploadeImageSize($path, $filename)
    {
        return File::size(public_path($path . $filename));
    }

    /**
     *  @method static string getUploadeImageDimension(string $path, string $filename)
     */
    public static function getUploadeImageDimension($path, $filename)
    {
        return getimagesize($path . $filename);
    }

    /**
     *  @method static string upload()
     */
    public static function upload()
    {
        if (!self::$_file) {
            throw new Exception('Define "file()" function with value');
        }

        if (!self::$uploadTo) {
            throw new Exception('Define "dest()" function with file path. This function must have an argument.');
        }

        $getpath = self::$uploadTo;
        $path = self::addEndingSlash($getpath);
        File::ensureDirectoryExists($path);
        $sPath = $path;
        $extensions = ['png', 'jpeg', 'jpg'];
        $base64Data = self::$_file;
        $_filename = (self::$_filename) ? self::$_filename :  md5(rand(1111, 99999)) . time() . uniqid() . '.png';
        $_new_filename = self::setFileName($path, $_filename);

        if ( self::$_maxDim && self::$_maxDim != null ) {
            $s_width = self::$_maxDim;
            $s_height = self::$_maxDim;
            $image = str_replace('data:image/png;base64,', '', $base64Data);
            $image = str_replace(' ', '+', $image);
            $imageName = $_new_filename;
            $path = $path . $imageName;
            $input = File::put($path, base64_decode($image));
            $image = Image::make($path);
            $image->height() > $image->width() ? $s_width = null : $s_height = null;
            $image->resize($s_width, $s_height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $_fWidth = $image->width();
            $_fHeight = $image->height();

            if ($result = $image->save($path)) {
                @list($width, $height, $type, $attr) = getimagesizefromstring(file_get_contents($base64Data));

                return [
                    'getName' => $_new_filename,
                    'getSize' => self::getUploadeImageSize($sPath, $_new_filename),
                    'getWidth' => self::getUploadeImageDimension($sPath, $_new_filename)[0],
                    'getHeight' => self::getUploadeImageDimension($sPath, $_new_filename)[1],
                ];
            } else {
                throw new Exception('Something went wrong for uploading image.');
            }
        } else {
            $file_data = $base64Data;
            @list($type, $file_data) = explode(';', $file_data);
            @list(, $file_data) = explode(',', $file_data);
            if (file_put_contents($path . $_new_filename, base64_decode($file_data))) {
                @list($width, $height, $type, $attr) = getimagesizefromstring(file_get_contents($base64Data));

                return [
                    'getName' => $_new_filename,
                    'getSize' => (int)(strlen(rtrim($base64Data, '=')) * 3 / 4),
                    'getWidth' => $width,
                    'getHeight' => $height
                ];
            } else {
                throw new Exception('Something went wrong for uploading image.');
            }
        }

        return new static;
    }
}
