<?php

require_once 'FileSystem.php';

class Configuration {
    const CACHE_PATH = './cache/';
    const REMOTE_PATH = './cache/remote/';

    const CACHE_KEY = 'cacheFolder';
    const REMOTE_KEY = 'remoteFolder';
    const CACHE_MINUTES_KEY = 'cache_http_minutes';
    const WIDTH_KEY = 'w';
    const HEIGHT_KEY = 'h';
    const OUTPUT_FILENAME_KEY = 'output-filename';

    const CONVERT_PATH = 'convert';

    private $opts;

    public function __construct($opts=array()) {
        $sanitized= $this->sanitize($opts);

        $defaults = array(
            'crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            self::OUTPUT_FILENAME_KEY => false,
            self::CACHE_KEY => self::CACHE_PATH,
            self::REMOTE_KEY => self::REMOTE_PATH,
            'quality' => 90,
            'cache_http_minutes' => 20,
            'w' => null,
            'h' => null);

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
        return isset($this->opts['crop']) && $this->opts['crop'] == true;
    }

    public function withScale() {
        return isset($this->opts['scale']) && $this->opts['scale'] == true;
    }

    public function obtainOutputFilePath($sourceFilePath) {
        if($this->obtainOutputFileName()) {
           return $this->obtainOutputFileName();
        }

        $w = $this->obtainWidth();
        $h = $this->obtainHeight();
        $filename = $this->fileSystem->md5_file($sourceFilePath);
        $finfo = $this->fileSystem->pathinfo($sourceFilePath);
        $ext = $finfo['extension'];


        $cropSignal = $this->withCrop() ? "_cp" : "";
        $scaleSignal = $this->withScale() ? "_sc" : "";
        $widthSignal = !empty($w) ? '_w'.$w : '';
        $heightSignal = !empty($h) ? '_h'.$h : '';
        $extension = '.'.$ext;

        return  $this->obtainOutputFolder() .$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

    }
}