<p align="center">  
 
 !['Kropify'](img/kropify.png)

</p>

 <p align="center">

![GitHub release](https://img.shields.io/github/v/release/sawastacks/kropify-laravel) <img alt="GitHub code size in bytes" src="https://img.shields.io/github/languages/code-size/sawastacks/kropify-laravel"> [![Total Downloads](https://poser.pugx.org/sawastacks/kropify-laravel/downloads)](https://packagist.org/packages/sawastacks/kropify-laravel) [![Package License](https://img.shields.io/badge/License-MIT-brightgreen.svg)](LICENSE) <img alt="GitHub Org's stars" src="https://img.shields.io/github/stars/sawastacks/kropify-laravel?style=social">


</p>

 # Kropify - Image Cropping Package 

+ Author: Sawa Stacks - GitHub
+ License: MIT License
+ Initial version: 10/03/2023

This package (Kropify class) handles the server-side file upload and saving. The Kropify.js script integrates with the frontend to provide cropping, previewing, and uploading of images with your [`Laravel`](https://laravel.com/) backend.<br>
This package is built using vanilla JavaScript, so it doesn't require jQuery as a dependency. It's lightweight, easy to integrate into your project without adding any extra library overhead, and supports multiple instances on a single page.


 <p align="centerx">  
 <a href="https://www.buymeacoffee.com/sawastacks" target="_blank">
   <img src="img/bmc.png" alt="drawing" style="width:200px;"/>
 </a>
</p>



## Requirements

- PHP >= 7.2
- [Composer](https://getcomposer.org/) is required
- Laravel 8.x, 9.x , 10.x, 11.x and 12.x

## Installation

This package can be installed through `composer require` command. Before install this, make sure that your are working with PHP >= 7.2 in your system.
Just run the following command in your cmd or terminal:

1. Install the package via Composer:

    ```bash
     composer require sawastacks/kropify-laravel
    ```
    The package will automatically register its service provider if your Laravel framework is 8.x or above.
2. Optionally, After you have installed **Kropify**, open your Laravel config file **`config/app.php`** and add the following lines.

    In the **`$providers`** array, add the service providers for this package.
   ```php
     SawaStacks\Utils\KropifyServiceProvider::class,
   ```
 

3. After **Kropify** package installed, you need to publish its css and js minified files in Laravel public folder by running the following command in terminal:

    ```bash
     php artisan vendor:publish --tag=kropify-assets
</br>

> **NOTE:** **`Kropify`** package assets files can be included on blade file via CDN links instead of using published files. If you need to use CDN links, just use below links:


```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/sawastacks/kropify-laravel@3.0.0/src/resources/assets/css/kropify.min.css">
```

```html
<script src="https://cdn.jsdelivr.net/gh/sawastacks/kropify-laravel@3.0.0/src/resources/assets/js/kropify.min.js"></script>
```

### Updating Package

When new **Kropify** version released and try to update the current package to the latest version, you will need to use `composer update` command:

```bash
 composer update sawastacks/kropify-laravel
```
When package not updated by using the above command, use below command that will remove current package version and install new version of package.
```bash
composer remove sawastacks/kropify-laravel && composer require sawastacks/kropify-laravel
```
After Kropify package updated, you need also to update its assets (**css** and **js** minified files) by running the following command in terminal:

```bash
php artisan vendor:publish --tag=kropify-assets --force
 ```

For `Kropify` directives, you have to run this command to get immediately changes in views.

```bash
 php artisan view:clear
```

Finally, It is neccessary to run the following command to autoload package files.

```bash
composer dump-autoload
```

# Usage
This package uses **css** and **js** minified files, that is why you first need to include this package assets on your blade file. Place the following directive inside **`<head>`** tag of your blade file to including Kropify css file on page. So, if you are using published file, use package style directive as shown below:

```html
<html>
 <head>
 <title>Page title</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
    @kropifyStyles 
    ......
    ...
 </head>
```

When you're using CDN links, no need of using any directive, just use below way:
```html
<html>
 <head>
 <title>Page title</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/sawastacks/kropify-laravel@3.0.0/src/resources/assets/css/kropify.min.css">
   ......
   ...
 </head>
```

`NOTICE:` Don't forgot to add `CSRF` meta tags to every blade file included **Kropify** assets as shown in above example.

For **Kropify** Js file, you need to add the following directive inside **`<body>`**  tag but before closing **`</body>`** tag as shown in below example:

```html
  ..........
   .....
   @kropifyScripts
 </body>
</html>
```

But when you're using CDN links, use it in way below:
```html
  ..........
   .....
   <script src="https://cdn.jsdelivr.net/gh/sawastacks/kropify-laravel@3.0.0/src/resources/assets/js/kropify.min.js"></script>
 </body>
</html>
```

### Package initialization

Suppose that you have an input file on your form for user profile picture:
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    @kropifyStyles 
</head>
<body>
    <!-- Element for previewing cropped image -->
    <div style="width: 300px; min-height:24px; background:cyan">
       <img src="" alt="" style="width: 100%" id="image-preview" data-kropify-default-src="...addCurrentImagePathIfExists.png">
    </div>

    <!-- Targeted input file -->
    <input type="file" id="imageInput">

 

    ........
    ..........
    ...........
    @kropifyScripts
    <script>

        const cropper = new Kropify('#imageInput', {
            aspectRatio: 1,
            preview: '#image-preview',
            processURL: '{{ route("crop-handler") }}',
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            showLoader: true,
            animationClass: 'pulse',
            fileName: 'avatar',
            cancelButtonText:'Cancel',
            maxWoH:500,
            onError: function (msg) {
                alert(msg);
                // toastr.error(msg);
            },
            onDone: function(response){
                alert(response.message);
                console.log(response.data);
                // toastr.success(response.message);
            }
        });

      
    </script>
</body>
</html>
```

## Routes
```php
Route::post('/crop',[TestController::class,'cropHandler'])->name('crop-handler');
```
## 🖼️ Kropify scripts - Frontend Integration
The Kropify script handles the cropping interface, sends AJAX requests, and previews the result.
When you want to initiate **Kropify** on that particular input file, you will use the following scripts.
```javascript
  <script>
    const cropper = new Kropify('#imageInput', {
            aspectRatio: 1,
            viewMode: 1,
            preview: 'img#image-preview',
            processURL: '{{ route("crocrop-handler") }}',
            maxSize: 2 * 1024 * 1024, // 2MB
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            showLoader: true,
            animationClass: 'pulse',
            fileName: 'avatar',
            cancelButtonText:'Cancel',
            resetButtonText: 'Reset',
            cropButtonText: 'Crop & Upload',
            maxWoH:500,
            onError: function (msg) {
                alert(msg);
                // toastr.error(msg);
            },
            onDone: function(response){
                alert(response.message);
                console.log(response.data);
                // toastr.success(response.message);
            }
        });
    </script>
```


## ⚙️ Kropify.js Options

| Option              | Type       | Description                                                         |
| ------------------- | ---------- | ------------------------------------------------------------------- |
|`viewMode`|`Number`|You can set this value to (1,2 or 3). But you can not add this option if you are happy with the default value which is 1.|
| `aspectRatio`       | `Number`   | Aspect ratio for cropping. You can add your custom cropped image ratio. You can use fractional numbers and float numbers. **eg**: `16/4`, `10/32`, `0.25`, `2.25`, etc... Default value is `1`                       |
| `preview`           | `String`   | CSS selector for image preview. you must use jquery selector to select **id="..."** or **class="..."** of the img tag element where you want to display cropped image result.                                    |
| `processURL`        | `String`   | URL to send the cropped image to (your Laravel route). This option is very required. You must define your url of croping selected image. eg: **_processURL : "{{ route('crop') }}"_** or **_processURL : "{{ url('crop') }}"_**             |
| `allowedExtensions` | `Array`    | Allowed file extensions default value is: *['jpg', 'jpeg', 'png']*.                                            |
| `showLoader`        | `Boolean`  | Show loading indicator. Default value is `true`                                            |
| `animationClass`    | `String`   | CSS animation class for animate modal box. you may use this option by choosing one of three animation classes allowed `pulse`,`headShake`,`fadeIn` and `pulse`. By default, this value set to `pulse` class                                |
| `fileName`          | `String`   | Desired filename for the uploaded image. This will be used when want to specify or overwrite file name of the input file. Default value is `image`, In controller you may write: *$upload = Kropify::getFile('image')->...* |
`cancelButtonText` | `String` | You can change this button text with your need and according to your language. |
| `resetButtonText` | `String`| You can change this button text with your need and according to your language.|
|`cropButtonText`| `String` | You can change this button text with your need and according to your language.
`maxWoH`|`Number`|Maximum width or height of the image.
`onError`|`Function`|Callback function on error (shows alert/toastr/etc.).
`onDone`|`Function`|Callback function when upload is successful (`response` is passed).
`maxSize`|`Number`|By default, this value set to the maximum size of **2MB** .But, you can set your own maximum size of selected  image.



## Callbacks


```javascript
onError: function (msg) {
    alert(msg);
// toastr.error(msg);
},
onDone: function(response){
    alert(response.message);
    console.log(response.data);
// toastr.success(response.message);
}
```

## In controller
To include **Kropify** class in controller is very simple. Just import the following lines on your controller.

```php
 use SawaStacks\Utils\Kropify;
```
To upload the cropped image you will use the following lines inside method:

```php
$file = $request->file('avatar');

$path = 'uploads';

$upload = Kropify::getFile($file,'userpic.png')
         //   ->setDisk('public') // local, public
              ->setPath($path)
              ->useMove()
              ->save();

/** GET UPLOADED IMAGE DETAILS (INFO) */

// if( $upload ){ $info = $upload->getUploadedInfo(); }

// $info = $upload ? $upload->getUploadedInfo() : null;

// $info = $upload?->getUploadedInfo();

if (!$upload) { return; }
    $info = $upload->getUploadedInfo();

// Store image details into DB
$im = new StoredImage();
$im->filename = $info['filename'];
$im->size = $info['size'];
$im->extension = $info['extension'];
$im->width = $info['width'];
$im->height = $info['height'];
$im->mime = $info['mime'];
$im->path = $info['path'];
$im->url = $info['url'];
$im->save();

// Returning json data
  return response()->json([
            'status'=>'OK',
            'message'=>'Image successfully uploaded',
            'data'=>$info
    ],201);
```


## 🚀 Kropify Class - Methods Overview

| Method                             | Description                                                                                 | Usage/Notes                                                                 |
| ---------------------------------- | ------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------- |
| `getFile($file, $filename = null)` | Initializes the file upload. Must be called first.                                          | `$file`: instance of uploaded file, `$filename`: optional custom filename. |
| `setDisk(string $disk)`            | Set the storage disk to use (`public`, `local`, etc.).                                         | Throws error if `useMove()` already used or if not preceded by `getFile()`. |
| `useMove()`                        | Use PHP’s `move()` instead of Laravel’s `Storage`.                                          | Can’t be used with `setDisk()`.                                             |
| `setPath(string $path)`            | Set the relative path (within storage/public) for the file.                                 | Example: `uploads/avatars`.                                                 |
| `save()`                           | Saves the uploaded file. Must be called after setting up the upload.                        | Can only be called once per instance.                                       |
| `getUploadedInfo()`                | Retrieve details of the saved file: filename, size, extension, dimensions, mime, path, URL. | Can only be called after `save()`.                                          |


# 📄 License
This package is open-sourced software licensed under the [MIT license](https://github.com/sawastacks/kropify-laravel/blob/master/LICENSE).

# 🙌 Credits </br>
Author: Sawa Stacks </br>
📧 Email: sawastacks@gmail.com </br>
🌐 GitHub: [github.com/sawastacks](https://github.com/sawastacks)







