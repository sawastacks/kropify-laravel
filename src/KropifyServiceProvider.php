<?php
/**
 * Copyright (c) 2023 - present
 * Kropify - KropifyServiceProvider.php
 * author: MB'DUSENGE Callixte - info@irebelibrary.com
 * web : github.com/mberecall
 * Initial version created on: 10/03/2023
 * MIT license: https://github.com/mberecall/kropify-laravel/blob/master/LICENSE
 */
namespace Mberecall\Kropify;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

/**
 * Class KropifyServiceProvider
 * @package Mberecall\Kropify
 */
class KropifyServiceProvider extends ServiceProvider
{

    /**
     * @var string
     */
      const PACKAGE_PUBLIC_ASSETS_PATH = 'packages/mberecall/kropify';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('kropify', function ($app) {
            return new Kropify();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        
        $package_public_assets_path = self::PACKAGE_PUBLIC_ASSETS_PATH;

        if ($this->app->runningInConsole()) {
            
            $viewPath = __DIR__ . '/../resources/assets';
            $this->publishes([
                $viewPath => public_path($package_public_assets_path),
            ], 'kropify-assets');
        }

        Blade::directive('kropifyStyles', function () use ($package_public_assets_path) {
            return '<link href="/' . $package_public_assets_path . '/css/kropify.min.css" rel="stylesheet">';
        });

        Blade::directive('kropifyScripts', function () use ($package_public_assets_path) {
            return '<script src="/' . $package_public_assets_path . '/js/kropify.min.js"></script>';
        });
    }
}
