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

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;

class Kropify
{
    protected $file;
    protected $filename;
    protected $disk;
    protected $path = '';
    protected $extension;
    protected $image;
    protected bool $useStorage = false;
    protected bool $useMove = false;
    protected $uploadedInfo = [];
    protected bool $fileInitialized = false;
    protected bool $saveCalled = false;

    /**
     * Create a new instance of Pica and assign the file and optional filename.
     * Must be the first call in the chain.
     * @param mixed $file The file to be uploaded (typically an instance of UploadedFile).
     * @param string|null $filename Optional custom filename.
     * @return self
     * @throws Exception If the file is not provided.
     */
    public static function getFile($file, $filename = null): self
    {
        if (!$file) {
            throw new Exception('The getFile() method requires at least one argument: the file.');
        }
        $instance = new self();
        $instance->file = $file;
        $instance->filename = $filename;
        $instance->fileInitialized = true;
        return $instance;
    }

    /**
     * Set the storage disk to be used when saving the file.
     * @param string $disk The disk name (e.g., "public", "s3").
     * @return self
     * @throws Exception If getFile() was not called before this, if useMove() is already used, or if disk name is empty.
     */
    public function setDisk(string $disk): self
    {
        if (!$this->fileInitialized) {
            throw new Exception('You must call getFile() before setDisk().');
        }
        if ($this->useMove) {
            throw new Exception('You cannot use setDisk() and useMove() together.');
        }
        if (!$disk) {
            throw new Exception('The setDisk() method requires one parameter: disk name.');
        }
        $this->disk = $disk;
        $this->useStorage = true;
        return $this;
    }

    /**
     * Disable Laravel's Storage facade and use move() function instead.
     * @return self
     * @throws Exception If getFile() was not called before this or if setDisk() is already used.
     */
    public function useMove(): self
    {
        if (!$this->fileInitialized) {
            throw new Exception('You must call getFile() before useMove().');
        }
        if ($this->useStorage) {
            throw new Exception('You cannot use setDisk() and useMove() together.');
        }
        $this->useMove = true;
        return $this;
    }

    /**
     * Set the relative path where the file should be saved.
     * @param string $path The desired path (relative to storage or public folder).
     * @return self
     * @throws Exception If getFile() was not called before this.
     */
    public function setPath(string $path): self
    {
        if (!$this->fileInitialized) {
            throw new Exception('You must call getFile() before setPath().');
        }
        $this->path = trim($path, '/');
        return $this;
    }

    /**
     * Save the file. Must be the last call in the chain.
     * @return self
     * @throws Exception If save() is called more than once or getFile() was not called.
     */
    public function save(): self
    {
        if ($this->saveCalled) {
            throw new Exception('The save() method can only be called once.');
        }
        if (!$this->fileInitialized) {
            throw new Exception('You must call getFile() before save().');
        }

        $this->saveCalled = true;

        $og_filename = ($this->filename) ? $this->filename : md5(rand(1, 10)) . time() . bin2hex(random_bytes(10)) . '.png';
        $filename = self::decideFileExtension($og_filename);

        $path = self::addEndingSlash($this->path);
 
        if ($this->useStorage) {
            $disk = $this->disk ?? 'public';
            Storage::disk($disk)->makeDirectory($path);
            $final_filename = self::setFinalStorageFileName($path, $filename, $disk);
            Storage::disk($disk)->putFileAs($path, $this->file, $final_filename);
            $storedPath = Storage::disk($disk)->path($path . $final_filename);
        } else {
            File::ensureDirectoryExists($path);
            $final_filename = self::setFinalFileName($path, $filename);
            $this->file->move(public_path($path), $final_filename);
            $storedPath = public_path($path . $final_filename);
        }

        // Get image details
        [$width, $height] = getimagesize($storedPath);
        $extension = pathinfo($final_filename, PATHINFO_EXTENSION);

        $this->uploadedInfo = [
            'filename'  => $final_filename,
            'size'      => filesize($storedPath),
            'extension' => $extension,
            'width'     => $width,
            'height'    => $height,
            'mime'      => mime_content_type($storedPath),
            'path'      => $storedPath,
            'url'       => $this->useStorage
                ? Storage::disk($this->disk)->path($path . $final_filename)
                : asset($path . $final_filename),
        ];

        return $this;
    }

    /**
     * Get details about the uploaded file.
     *
     * @return array An array containing filename, size, extension, width, height, mime, path, and url.
     * @throws Exception If save() has not been called before this.
     */
    public function getUploadedInfo(): array
    {
        if (!$this->saveCalled) {
            throw new Exception('You must call save() before getting uploaded info.');
        }
        return $this->uploadedInfo;
    }

    // --------------------- Utility Methods ------------------------

    /**
     * Set a unique final filename when using PHP's move() (no Storage).
     *
     * @param string $path The target directory path.
     * @param string $filename The desired filename.
     * @return string Unique filename.
     */
    public static function setFinalFileName($path, $filename)
    {
        $filename = self::decideFileExtension($filename);
        $pos = strrpos($filename, '.');
        $name = $pos !== false ? substr($filename, 0, $pos) : $filename;
        $ext = $pos !== false ? substr($filename, $pos) : '';
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
     * Set a unique final filename when using Laravel's Storage.
     *
     * @param string $path The target directory path.
     * @param string $filename The desired filename.
     * @param string $disk The storage disk.
     * @return string Unique filename.
     */
    public static function setFinalStorageFileName(string $path, string $filename, string $disk = 'public'): string
    {
        $pos = strrpos($filename, '.');
        $name = $pos !== false ? substr($filename, 0, $pos) : $filename;
        $ext = $pos !== false ? substr($filename, $pos) : '';
        $newname = $filename;
        $counter = 1;
        while (Storage::disk($disk)->exists($path . '/' . $newname)) {
            $newname = $name . '_' . $counter . $ext;
            $counter++;
        }
        return $newname;
    }

    /**
     * Ensure file extension is valid; defaults to .png if missing.
     *
     * @param string $filename The filename to check.
     * @return string Filename with extension.
     */
    public static function decideFileExtension($filename)
    {
        if (preg_match('/(\.jpg|\.png|\.bmp|\.jpeg)$/i', $filename)) {
            return $filename;
        }
        return $filename . '.png';
    }

    /**
     * Add a trailing slash to a path if it doesn't have one.
     *
     * @param string $path The path to modify.
     * @return string Path with trailing slash.
     */
    public static function addEndingSlash($path)
    {
        $lastChar = substr($path, -1);
        if ($lastChar !== '/' && $lastChar !== '\\') {
            $path .= '/';
        }
        return $path;
    }
}
