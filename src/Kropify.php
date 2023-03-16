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
 * @method static self upload(string $file, string $path, string $filename = null)
 * @method static self getName()
 * @method static self getSize()
 * @method static self getWidth()
 * @method static self getHeight()
 * @method static self maxDim(int $value)
 */

class Kropify
{
    public static $getName;
    public static $fileName;
    public static $fileSize;
    public static $fileWidth;
    public static $fileHeight;
    public static $maxDim;

    protected static $onlyInstance;

     /**
     * @method static getself()
     */
    protected static function getself()
    {
        if (static::$onlyInstance === null) {
            static::$onlyInstance = new Kropify;
        }

        return static::$onlyInstance;
    }

    /**
     * @method static upload(string $file, string $path, string $filename = null)
     */
    public static function upload($file, $path, $filename = null)
    {   $path = self::addEndingSlash($path);
        File::ensureDirectoryExists($path);
        $extensions = ['png', 'jpeg', 'jpg'];
        $base64Data = $file;
        if (self::$maxDim) {
            $s_width = self::$maxDim;
            $s_height = self::$maxDim;
            $_filename = ($filename == null) ? md5(rand(1111, 99999)).time().uniqid().'.png' : $filename;
            $_new_filename = self::setFileName($path, $_filename);
            $image = str_replace('data:image/png;base64,', '', $base64Data);
            $image = str_replace(' ', '+', $image);
            $imageName = $_new_filename;
            $path = $path . $imageName;
            $input = File::put($path, base64_decode($image));
            $image = Image::make($path);
            $image->height() > $image->width() ? $s_width = null : $s_height = null;
            $image->resize($s_width, $s_height, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $result = $image->save($path);
            @list($width, $height, $type, $attr) = getimagesizefromstring(file_get_contents($base64Data));
            self::$fileName = $_new_filename;
            self::$fileWidth = $width;
            self::$fileHeight = $height;
            return new static();
        } else {
            $_filename = ($filename == null) ? md5(rand(1111, 99999)).time().uniqid().'.png' : $filename;
            $_new_filename = self::setFileName($path, $_filename);
            $file_data = $base64Data;
            @list($type, $file_data) = explode(';', $file_data);
            @list(, $file_data) = explode(',', $file_data);
            if (file_put_contents($path . $_new_filename, base64_decode($file_data))) {
                @list($width, $height, $type, $attr) = getimagesizefromstring(file_get_contents($base64Data));
                self::$fileSize = (int)(strlen(rtrim($base64Data, '=')) * 3 / 4);
                self::$fileName = $_new_filename;
                self::$fileWidth = $width;
                self::$fileHeight = $height;
                return new static();
            } else {
                throw new Exception('Something went wrong for uploading image.');
            }
        }
        return static::getself();
    }

    /**
     * @method static string addEndingSlash(string $path)
     */
    public static function addEndingSlash($path){
        $slashType = (strpos($path,'\\') === 0) ? 'win' : 'unix';
        $lastChar = substr($path, strlen($path) - 1, 1);
        if($lastChar != '/' && $lastChar != '\\'){
            $path.= ($lastChar == 'win' ? '\\' : '/');
        }
        return $path;
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
     *  @method static string getName()
     */
    public static function getName()
    {
        return self::$fileName;
    }

    /**
     * @method static string getSize()
     */
    public static function getSize()
    {
        return self::$fileSize;
    }

    /**
     * @method static string getWidth()
     */
    public static function getWidth()
    {
        return self::$fileWidth;
    }
    /**
     * @method static string getHeight()
     */
    public static function getHeight()
    {
        return self::$fileHeight;
    }
    /**
     * @method static string maxDim(int $value)
     */
    public static function maxDim($value = 0)
    {
        static::$maxDim = $value;
        return static::getself();
    }

}
