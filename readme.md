 <p align="center">  
 
 !['Kropify'](img/kropify.png)

 </p>

<p align="center">

[![Latest Version](https://img.shields.io/packagist/v/mberecall/kropify-laravel?label=version)](https://packagist.org/packages/mberecallkropify-laravel/)  <img alt="GitHub code size in bytes" src="https://img.shields.io/github/languages/code-size/mberecall/kropify-laravel"> [![Total Downloads](https://img.shields.io/packagist/dt/mberecall/kropify-laravel.svg)](https://packagist.org/packages/mberecall/kropify-laravel)  [![Package License](https://img.shields.io/badge/License-MIT-brightgreen.svg)](LICENSE) <img alt="GitHub Org's stars" src="https://img.shields.io/github/stars/mberecall/kropify-laravel?style=social"> [![GitHub followers](https://img.shields.io/github/followers/mberecall.svg?style=social&label=Follow&maxAge=2592000)](https://github.com/mberecall?tab=followers)



  
  


</p>

 # Kropify

**Irebe Library** brought you easy cropping image tool for user profile picture, cover image, etc... that can be integrated into Laravel project.

> **NOTE:** **`Kropify`** cannot be integrated into Laravel framework only. It has another php version that can be integrated into CodeIgniter and Core PHP projects.  

## Background: What is a **Kropify**?

A **Kropify** is a tool that can be integrated into `Laravel framework`, `CodeIgniter framework` and `Core PHP` projects for the purpose of giving users easy way to crop their profile pictures and covers. It uses [JQuery 3.x](https://releases.jquery.com/) library in its functionality. That's why it is important to link JQuery library on current page.

## Requirements

- PHP >= 7.2
- [Composer](https://getcomposer.org/) is required
- Laravel 8.x, 9.x and 10.x
- [Image Intervention](https://image.intervention.io/v2) package
- [JQuery 3.x](https://releases.jquery.com/)

# Installation

This package can be installed through `composer require`. Before install this, make sure that your are working with PHP >= 7.2 in your system.
Just run the following command in your cmd or terminal:

1. Install the package via Composer:

    ```bash
     composer require mberecall/kropify-laravel
    ```

    The package will automatically register its service provider if your Laravel framework is 8.x or above. And also, If Image Intervention package was not installed before, This package will install Image Intervention package to your Laravel project.
2. Optionally, After you have installed **Kropify**, open your Laravel config    file **`config/app.php`** and add the following lines.

    In the **`$providers`** array, add the service providers for this package.
   ```php
     Mberecall\Kropify\KropifyServiceProvider::class,
   ```


3. After **Kropify** package installed, you need to publish its css and js files in Laravel public folder by running the following command in terminal:

    ```bash
     php artisan vendor:publish --tag=kropify-assets
    ```

### Updating Package

When new **Kropify** version released and try to update the installed current package to the latest version, you will need to use `composer update` command:

```bash
 composer update mberecall/kropify-laravel
```

After Kropify package updated, you need also to update its assets (css and js files) by running the following command in terminal:

```bash
 php artisan vendor:publish --tag=kropify-assets --force
```

Now finally, for `Kropify` directives, you can run this command to get immediate changes in views.

```bash
 php artisan view:clear
```

# Usage
This package uses css and js files, that is why you first need to include this package assets on your blade file. Place the following directive or helper inside **`<head>`** tag of your blade file for including Kropify css file on page.
```html
<html>
 <head>
 <title>Page title</title>
 ----
    @kropifyStyles 
 -----
 </head>
```
For **Kropify** Js file, you need to add the following directive or helper inside **`<body>`**  tag but before closing **`</body>`** tag.

```html
  ---------
   -----
 <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
  @kropifyScripts
 </body>
</html>
```

### Package initialization

Suppose that you have an input file on your form for user profile picture:

```html
 <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" id="registerForm">
    @csrf
   <div class="form-group">
        <div class="profile-picture-box"></div>
        <label for="" class="d-block">Profile picture</label>

        <input type="file" name="profile-picture" id="profile-picture" class="form-control">
        @error('profile-picture')
            <span class="text-danger">{{ $message }}</span>
        @enderror
                       
    </div>

    <div class="form-group">
        <button class="btn btn-primary" type="submit">
            Register
        </button>
    </div>
 </form>

```
When you want to initiate **Kropify** on that particular input file, you will use the following scripts.
```javascript
  $('#profile-picture').Kropify({
        viewType:1,
        setRatio:1,
        preview:'.profile-picture-box',
        cancelButtonText:'Cancel',
        resetButtonText:'Reset',
        cropButtonText:'Crop',
        maxSize:2097152, //2 MB (maximum size)
        errors:function(type, message){
            alert(message);
        }
    });
```
### Options
| Option | Default | Description 
|-------------  | :-------------: | ---------- |
| `viewType` | 1 | You can set this value to (1,2 or 3). But you can not add this option if you are happy with the default value which is 1. |
| `setRatio` | 1 | You can add your custom cropped image ratio. You can use fractional numbers and float numbers. **eg**: `16/4`, `10/32`, `0.25`, `2.25`, etc. |
|`preview` | **required** | This option is very required, This is where you define the output to preview the cropped image. Here, you must use jquery selector to select **id=""** or **class=""** of the element. We recommended to use `<span>` or `<div>` tags.|
|`cancelButtonText` | Cancel | You can change this button text with your need and according to your language. |
| `resetButtonText` | Reset| You can change this button text with your need and according to your language.|
|`cropButtonText`| Crop | You can change this button text with your need and according to your language. |
|`maxSize`| 2097152 | By default, this value set to the maximum size of **2MB** .But, you can set your own maximum size of selected  image. |

### Errors callback
This callback has two parameters, `type` and `message`

```javascript
 errors:function(type, message){
            alert(message);
        }
```

| Parameter | Description 
|------------- | ---------- |
| `type` | This prameter will return two types of errors **invalidFileType** and  **bigFileSize**. You can make a `if` condition according to the returned error type. | 
| `message` | You can alert this value `eg`: alert(message);. If you are using Toastr.js plugin, You may use `toastr.error(message)'` function to display error alert.|

## In controller
To include **Kropify** class in controller is very simple. Just import the following lines on your controller.

```php
 use Intervention\Image\Facades\Image;
 use Mberecall\Kropify\Kropify;
```

You can use package facade
```php
 use Intervention\Image\Facades\Image;
 use Mberecall\Kropify\Facades\Kropify;
```
**`NB:`** You must be installed Image Intervention Package first before starting using this package.

To upload the cropped image you will use the following lines inside method:

```php
// $path = 'uploads/';
// $path = storage_path('app/public/uploads/');
 $path = public_path('uploads/');  

 $upload =  Kropify::file($request->input('profile-picture'), null, null)
                    ->dest($path)
                    ->upload();

$upload =  Kropify::file($request->input('profile-picture'), 'cover.jpg', 1111)
                    ->dest($path)
                    ->upload();

```
The above lines will upload the cropped image in the specified path. You can upload this image in Laravel public folder or in Laravel storage folder.
Very important function is **`file()`** on the chain. This function has three parameters. First parameter will be the input file data from a form. The second parameter will be the name of the image before uploaded. If you don't need to mention image name, just leave this second parameter to the **null** value. And the third parameter will be the maximum image dimension (width or height) in px. If you do not need to compress and resize the cropped image, just on this third parameter, set this value to **null**.

The second function on the chain **`dist()`** is where you can mention the uploaded image destination. This function parameter is a must, you can not leave this empty.

The last function on this chain is **`upload()`**, This function do not have any parameter. This must be added on the last position on the chain.

When image uploaded successfully, you can get the uploaded image name, size, width and Height. You can store these information into database if you want to.
Below are examples of getting uploaded info:

```php
// $path = 'uploads/';
// $path = storage_path('app/public/uploads/');
 $path = public_path('uploads/');
 
 $upload =  Kropify::file($request->input('profile-picture'), 'cover.jpg', 1111)
                   ->dest($path)
                   ->upload();

 $imageName = $upload['getName']; //mypicture.png
 $mageSize = $upload['getSize']; //232342 in bytes
 $imageWidth = $upload['getWidth']; //1111
 $imageHeight = $upload['getHeight']; //400
```

**`NOTICE:`** When you submit a form with ajax, you will need to add below reset function `kropify.reset()`

```javascript
-----
-----
success:function(response){
    if(response.status == 1 ){
        $(form)[0].reset();
        kropify.reset();
    }
----
----
```

## Not supported
Currently, uploading cropped image using Laravel **`Livewire`** is not supported. This package still in development, Once uploading cropped image in Laravel Livewire available, we will notify you.
You can not also upload image to **`AWS Amazon S3`**.

<br>

## Copyright and License

[kropify-laravel](https://github.com/mberecall/kropify-laravel)
was written by [MB'DUSENGE Callixte (mberecall)](https://github.com/mberecall) and is released under the 
[MIT License](https://github.com/mberecall/kropify-laravel/blob/master/LICENSE).

Copyright (c) 2023 MB'DUSENGE Callixte
