<?php

require 'FileSystem.php';

class HttpUrlImage {

    private $url;
    private $valid_http_protocols = array('http', 'https');

    public function __construct($url='') {
        $this->url = $this->sanitize($url);
        $this->fileSystem = new FileSystem();
    }

    public function sanitizedPath() {
        return $this->url;
    }

    public function isHttpProtocol() {
        return in_array($this->obtainScheme(), $this->valid_http_protocols);
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->url);
        list($filename) = explode('?',$finfo['basename']);
        return $filename;
    }

    private function sanitize($path) {
        return urldecode($path);
    }

    private function obtainScheme() {
        if ($this->url == '') return '';
        $purl = parse_url($this->url);
        return $purl['scheme'];
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }


    private function isInCache($filePath, $expirationTime) {
        $fileExists = $this->fileSystem->file_exists($filePath);
        $fileValid = $this->fileNotExpired($filePath, $expirationTime);

        return $fileExists && $fileValid;
    }

    private function fileNotExpired($filePath, $expirationTime) {
        $cacheMinutes = $expirationTime;
        $this->fileSystem->filemtime($filePath) < strtotime('+'. $cacheMinutes. ' minutes');
    }

    private function getFromCache($downloadFolder, $expirationTime) {
            $downloadFilePath = $downloadFolder.$this->obtainFileName();

            $inCache = $this->isInCache($downloadFilePath, $expirationTime);

            if($inCache):
                return $downloadFilePath;
            else:
                throw new Exception("image not found in cache");
            endif;
    }

    private function doDownload($downloadFolder) {
        $img = $this->fileSystem->file_get_contents($this->sanitizedPath());

        $downloadFilePath = $downloadFolder.$this->obtainFileName();

        $this->fileSystem->file_put_contents($downloadFilePath,$img);

        return $downloadFilePath;
    }

    private function fallbackFile($downloadFilePath) {
        $downloadFilePath = $_SERVER['DOCUMENT_ROOT'].$downloadFilePath;

        if($this->notExists($downloadFilePath)):
            throw new RuntimeException();
        endif;

        return $downloadFilePath;
    }

    public function downloadTo($downloadFolder, $expirationTime) {
        $downloadFilePath = '';

        if($this->isHttpProtocol()):
            try {
                $downloadFilePath = $this->getFromCache($downloadFolder, $expirationTime);
            }catch (Exception $e) {
                $downloadFilePath = $this->doDownload($downloadFolder);
            }
        endif;

        if($this->fileSystem->file_exists($downloadFilePath)):
            return $downloadFilePath;
        else:
            return $this->fallbackFile($downloadFilePath);
        endif;

    }

    /**
     * @param $downloadFilePath
     * @return bool
     */
    private function notExists($downloadFilePath)
    {
        return !$this->fileSystem->file_exists($downloadFilePath);
    }


}