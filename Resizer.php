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

    private function respectRatioArguments($imagePath) {
        $resize = "x".$this->configuration->obtainHeight();

        if(!($this->configuration->withCrop()) && $this->isPanoramic($imagePath)):
            $resize = $this->configuration->obtainWidth();
        endif;

        if($this->configuration->withCrop() && !$this->isPanoramic($imagePath)):
            $resize = $this->configuration->obtainWidth();
        endif;

        return " -resize ". escapeshellarg($resize);
    }

    private function scaleArguments() {
        $cmd = " -quality ". escapeshellarg($this->configuration->obtainQuality());

        return $cmd;
    }

   private function cropArguments() {
        $cmd = " -size "
                . escapeshellarg($this->configuration->obtainWidth() ."x". $this->configuration->obtainHeight())
                ." xc:"
                . escapeshellarg($this->configuration->obtainCanvasColor())
                ." +swap -gravity center -composite -quality "
                . escapeshellarg($this->configuration->obtainQuality())
            ;

        return $cmd;
    }

    private function respectImageRatio() {
        return  !$this->configuration->withWidth()
                or
                !$this->configuration->withHeight();
    }


    private function resizeArguments($imagePath) {

        if ($this->respectImageRatio()) {
            return $this->thumbnailArguments();
        } else {
            $cmd=$this->respectRatioArguments($imagePath);
            if($this->configuration->withScale()):
                return $cmd . $this->scaleArguments();
            else:
                return $cmd . $this->cropArguments();
            endif;
        }

    }

    public function doResize($imagePath) {

        $cmd = $this->resizeFrom($imagePath);

        $cmd.=$this->resizeArguments($imagePath);

        $cmd.= $this->resizeTo($imagePath);

        $output=array();
        $return_code= $this->fileSystem->exec($cmd, $output);
        if($return_code != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            throw new RuntimeException();
        }
    }

    /**
     * @param $imagePath
     * @return string
     */
    private function resizeFrom($imagePath)
    {
        return $this->configuration->obtainConvertPath()
        . " "
        . escapeshellarg($imagePath);
    }

    /**
     * @param $imagePath
     * @return string
     */
    private function resizeTo($imagePath)
    {
        return " " . escapeshellarg($this->configuration->obtainOutputFilePath($imagePath));
    }

}