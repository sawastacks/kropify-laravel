<?php
/**
 * Copyright (c) 2023 - present
 * Kropify - Kropify.php
 * author: MB'DUSENGE Callixte - info@irebelibrary.com
 * web : github.com/mberecall
 * Initial version created on: 10/03/2023
 * MIT license: https://github.com/mberecall/kropify-laravel/blob/master/LICENSE
 */
namespace Mberecall\Kropify\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * @method static self upload(string $file, string $path, string $filename = null)
 * @method static self getSize()
 * @method static self getWidth()
 * @method static self getHeight()
 * @method static self getName()
 * @method static self maxDim(int $value)
 */
class Kropify extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     * @see Kropify
     */
     protected static function getFacadeAccessor(){
           return 'kropify';
      }
}