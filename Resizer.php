<?php

require_once 'FileSystem.php';

class Resizer {


    private $configuration;
    private $fileSystem;


    public function __construct($configuration=null) {
        if ($configuration == null) $configuration = new Configuration();

        $this->checkConfiguration($configuration);

        $this->configuration = $configuration;
        $this->fileSystem = new FileSystem();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    private function isPanoramic($imagePath) {
        list($width,$height) = $this->fileSystem->getimagesize($imagePath);
        return $width > $height;
    }


    private function thumbnailArguments() {
        $command =
            " -thumbnail "
                . ($this->configuration->withHeight() ? 'x':'')
                . $this->configuration->obtainWidth()
                .($this->configuration->withMaxOnly()? "\>" : "") .
            " -quality ". escapeshellarg($this->configuration->obtainQuality())
            ;

        return $command;
    }

    private function respectRatioArguments($isPanoramic) {
        $resize = "x".$this->configuration->obtainHeight();

        if(!($this->configuration->withCrop()) && $isPanoramic):
            $resize = $this->configuration->obtainWidth();
        endif;

        if($this->configuration->withCrop() && !$isPanoramic):
            $resize = $this->configuration->obtainWidth();
        endif;

        return " -resize ". escapeshellarg($resize);
    }

    private function scaleArguments() {
        return  " -quality ". escapeshellarg($this->configuration->obtainQuality());
    }

   private function cropArguments() {
        return " -size "
                . escapeshellarg($this->configuration->obtainWidth() ."x". $this->configuration->obtainHeight())
                ." xc:"
                . escapeshellarg($this->configuration->obtainCanvasColor())
                ." +swap -gravity center -composite -quality "
                . escapeshellarg($this->configuration->obtainQuality())
            ;
    }

    private function respectImageRatio() {
        return  !$this->configuration->withWidth()
                or
                !$this->configuration->withHeight();
    }


    private function resizeArguments($isPanoramic) {

        if ($this->respectImageRatio()) {
            return $this->thumbnailArguments();
        }

        $cmd=$this->respectRatioArguments($isPanoramic);

        if($this->configuration->withScale()):
            return $cmd . $this->scaleArguments();
        endif;

        return $cmd . $this->cropArguments();

    }

    private function resizeFrom($imagePath)
    {
        return $this->configuration->obtainConvertCommand()
        . " "
        . escapeshellarg($imagePath);
    }

    private function resizeTo($ouputFilePath)
    {
        return " " . escapeshellarg($ouputFilePath);
    }

    private function executeCommand($cmd)
    {
        $output = array();

        $return_code = $this->fileSystem->exec($cmd, $output);

        if ($return_code != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            throw new RuntimeException();
        }
    }

    public function isInCache($imagePath) {
        $path = $this->configuration->obtainOutputFilePath($imagePath);

        $isInCache = false;
        if($this->fileSystem->file_exists($path) == true):
            $isInCache = true;
            $origFileTime = date("YmdHis",$this->fileSystem->filemtime($imagePath));
            $newFileTime = date("YmdHis",$this->fileSystem->filemtime($path));
            if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
                $isInCache = false;
            endif;
        endif;

        return $isInCache;
    }

    public function doResize($imagePath) {

        $cmd = $this->resizeFrom($imagePath);

        $cmd.=$this->resizeArguments($this->isPanoramic($imagePath));

        $cmd.= $this->resizeTo($this->configuration->obtainOutputFilePath($imagePath));

        $this->executeCommand($cmd);
    }

}