<?php

require_once 'FileSystem.php';

class Configuration {
    const CACHE_PATH = './cache/';
    const REMOTE_PATH = './cache/remote/';
    const CONVERT_PATH = 'convert';

    const CACHE_KEY = 'cacheFolder';
    const REMOTE_KEY = 'remoteFolder';
    const CACHE_MINUTES_KEY = self::CACHE_HTTP_MINUTES_KEY;
    const OUTPUT_FILENAME_KEY = 'output-filename';
    const CROP_KEY = 'crop';
    const SCALE_KEY = 'scale';
    const THUMBNAIL_KEY = 'thumbnail';
    const MAX_ONLY_KEY = 'maxOnly';
    const CANVAS_COLOR_KEY = 'canvas-color';
    const QUALITY_KEY = 'quality';
    const CACHE_HTTP_MINUTES_KEY = 'cache_http_minutes';
    const WIDTH_KEY = 'w';
    const HEIGHT_KEY = 'h';

    private $opts;

    public function __construct($opts=array()) {
        $sanitized= $this->sanitize($opts);

        $defaults = array(
            self::CROP_KEY => false,
            self::SCALE_KEY => 'false',
            self::THUMBNAIL_KEY => false,
            self::MAX_ONLY_KEY => false,
            self::CANVAS_COLOR_KEY => 'transparent',
            self::OUTPUT_FILENAME_KEY => false,
            self::CACHE_KEY => self::CACHE_PATH,
            self::REMOTE_KEY => self::REMOTE_PATH,
            self::QUALITY_KEY => 90,
            self::CACHE_HTTP_MINUTES_KEY => 20,
            self::WIDTH_KEY => null,
            self::HEIGHT_KEY => null);

        $this->opts = array_merge($defaults, $sanitized);

        if(empty($this->obtainOutputFileName()) && empty($this->obtainWidth()) && empty($this->obtainHeight())) {
            throw new InvalidArgumentException();
        }

        $this->fileSystem = new FileSystem();
    }

    public function asHash() {
        return $this->opts;
    }

    public function obtainOutputFolder() {
        return $this->opts[self::CACHE_KEY];
    }

    public function obtainDownloadFolder() {
        return $this->opts[self::REMOTE_KEY];
    }

    public function obtainConvertPath() {
        return self::CONVERT_PATH;
    }

    public function obtainWidth() {
        return $this->opts[self::WIDTH_KEY];
    }

    public function obtainHeight() {
        return $this->opts[self::HEIGHT_KEY];
    }

    public function obtainQuality() {
        return $this->opts[self::QUALITY_KEY];
    }

    public function obtainCanvasColor() {
        return $this->opts[self::CANVAS_COLOR_KEY];
    }


    public function obtainCacheMinutes() {
        return $this->opts[self::CACHE_MINUTES_KEY];
    }

    private function sanitize($opts) {
        if($opts == null) return array();

        return $opts;
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function obtainOutputFileName() {
        return $this->opts[self::OUTPUT_FILENAME_KEY];
    }

    public function withCrop() {
        return isset($this->opts[self::CROP_KEY]) && $this->opts[self::CROP_KEY] === true;
    }

    public function withScale() {
        return isset($this->opts[self::SCALE_KEY]) && $this->opts[self::SCALE_KEY] === true;
    }


    public function withMaxOnly() {
        return isset($this->opts[self::MAX_ONLY_KEY]) && $this->opts[self::MAX_ONLY_KEY] == true ;
    }

    public function obtainOutputFilePath($sourceFilePath) {
        if($this->obtainOutputFileName()) {
           return $this->obtainOutputFileName();
        }

        $filename = $this->fileSystem->md5_file($sourceFilePath);

        $path = $this->obtainOutputFolder().$filename;

        if ($this->withWidth()) {
            $path=$path.'_w'.$this->obtainWidth();
        }

        if ($this->withHeight()) {
            $path=$path.'_h'.$this->obtainHeight();
        }

        if ($this->withCrop()) {
            $path=$path."_cp";
        }

        if ($this->withScale()) {
            $path=$path."_sc";
        }

        $path=$path.$this->getExtension($sourceFilePath);

        return $path;

    }

    public function withWidth() {
        return !empty($this->obtainWidth());
    }

    public function withHeight() {
        return !empty($this->obtainHeight());
    }

    private function getExtension($filename) {
        return $this->fileSystem->getExtension($filename);
    }



}