<?php

/**
 * Copyright (c) 2023 - present
 * Kropify - Kropify.php facade
 * Author: MB'DUSENGE Callixte - mberecall@gmail.com
 * GitHub : github.com/mberecall
 * Initial version created on: 10/03/2023
 * MIT license: https://github.com/mberecall/kropify-laravel/blob/master/LICENSE
 */

namespace Mberecall\Kropify\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self getFile(string $file, string $filename = null)
 * @method static self maxWoH(int $max)
 * @method static self save(string $path)
 */
class Kropify extends Facade
{
    /**
     * Get the registered name of the component.
     * @return string
     * @see Kropify
     */
    protected static function getFacadeAccessor()
    {
        return 'kropify';
    }
}
