<?php

namespace SawaStacks\Utils;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\URL;
/**
 * Copyright (c) 2023 - present
 * Kropify - KropifyServiceProvider.php
 * Author: Sawa Stacks - sawastacks@gmail.com
 * GitHub : github.com/sawastacks
 * Initial version created on: 10/03/2023
 * MIT license: https://github.com/sawastacks/kropify-laravel/blob/master/LICENSE
 * DO NOT use this class directly.
 * It's auto-registered by Laravel to publish assets.
 */
class KropifyServiceProvider extends ServiceProvider
{
    const PACKAGE_PUBLIC_ASSETS_PATH = 'vendors/sawastacks/kropify';
    const PACKAGE_ASSETS_SOURCE_PATH = __DIR__ . '/resources/assets';
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
       
        if ($this->app->runningInConsole() && is_dir(self::PACKAGE_ASSETS_SOURCE_PATH)) {
            $this->publishes([
                self::PACKAGE_ASSETS_SOURCE_PATH => public_path(self::PACKAGE_PUBLIC_ASSETS_PATH),
            ], 'kropify-assets');
        }

        Blade::directive('kropifyStyles', function () {
            return new HtmlString('<link href="/' . self::PACKAGE_PUBLIC_ASSETS_PATH . '/css/kropify.min.css" rel="stylesheet">');
        });

        Blade::directive('kropifyScripts', function () {
            return new HtmlString('<script src="/' . self::PACKAGE_PUBLIC_ASSETS_PATH . '/js/kropify.min.js"></script>');
        });

        /*
        // When above not work for you, uncomment this section.
        Blade::directive('kropifyStyles', function () {
            return new HtmlString('<link href="' . asset(self::PACKAGE_PUBLIC_ASSETS_PATH . '/css/kropify.min.css') . '" rel="stylesheet">');
        });
        
        Blade::directive('kropifyScripts', function () {
            return new HtmlString('<script src="' . asset(self::PACKAGE_PUBLIC_ASSETS_PATH . '/js/kropify.min.js') . '"></script>');
        });
        */
    }
}
