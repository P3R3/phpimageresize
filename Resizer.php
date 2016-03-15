<?php

require 'FileSystem.php';

class Resizer {

    private $httpUrlImage;
    private $configuration;
    private $fileSystem;

    public function __construct($httpUrlImage, $configuration=null) {
        if ($configuration == null) $configuration = new Configuration();
        $this->checkPath($httpUrlImage);
        $this->checkConfiguration($configuration);
        $this->httpUrlImage = $httpUrlImage;
        $this->configuration = $configuration;
        $this->fileSystem = new FileSystem();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function obtainFilePath() {
        $imagePath = '';

        if($this->httpUrlImage->isHttpProtocol()):
            $filename = $this->httpUrlImage->obtainFileName();
            $local_filepath = $this->configuration->obtainRemote() .$filename;
            $inCache = $this->isInCache($local_filepath);

            if(!$inCache):
                $this->download($local_filepath);
            endif;
            $imagePath = $local_filepath;
        endif;

        if(!$this->fileSystem->file_exists($imagePath)):
            $imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
            if(!$this->fileSystem->file_exists($imagePath)):
                throw new RuntimeException();
            endif;
        endif;

        return $imagePath;
    }


    private function download($filePath) {
        $img = $this->fileSystem->file_get_contents($this->httpUrlImage->sanitizedPath());
        $this->fileSystem->file_put_contents($filePath,$img);
    }

    private function isInCache($filePath) {
        $fileExists = $this->fileSystem->file_exists($filePath);
        $fileValid = $this->fileNotExpired($filePath);

        return $fileExists && $fileValid;
    }

    private function fileNotExpired($filePath) {
        $cacheMinutes = $this->configuration->obtainCacheMinutes();
        $this->fileSystem->filemtime($filePath) < strtotime('+'. $cacheMinutes. ' minutes');
    }

    private function checkPath($httpUrlImage) {
        if (!($httpUrlImage instanceof HttpUrlImage)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

}