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

    private function defaultShellCommand($configuration, $imagePath, $newPath) {
        $opts = $configuration->asHash();
        $w = $configuration->obtainWidth();
        $h = $configuration->obtainHeight();

        $command = $configuration->obtainConvertPath() ." " . escapeshellarg($imagePath) .
            " -thumbnail ". (!empty($h) ? 'x':'') . $w ."".
            (isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") .
            " -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);

        return $command;
    }

    private function isPanoramic($imagePath) {
        list($width,$height) = $this->fileSystem->getimagesize($imagePath);
        return $width > $height;
    }

    private function composeResizeOptions($imagePath, $configuration) {
        $opts = $configuration->asHash();
        $w = $configuration->obtainWidth();
        $h = $configuration->obtainHeight();

        $resize = "x".$h;

        $hasCrop = (true === $opts['crop']);

        if(!$hasCrop && $this->isPanoramic($imagePath)):
            $resize = $w;
        endif;

        if($hasCrop && !$this->isPanoramic($imagePath)):
            $resize = $w;
        endif;

        return $resize;
    }

    private function commandWithScale($imagePath, $newPath, $configuration) {
        $opts = $configuration->asHash();
        $resize = $this->composeResizeOptions($imagePath, $configuration);

        $cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
            " -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);

        return $cmd;
    }

   private function commandWithCrop($imagePath, $newPath, $configuration) {
        $opts = $configuration->asHash();
        $w = $configuration->obtainWidth();
        $h = $configuration->obtainHeight();
        $resize = $this->composeResizeOptions($imagePath, $configuration);

        $cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
            " -size ". escapeshellarg($w ."x". $h) .
            " xc:". escapeshellarg($opts['canvas-color']) .
            " +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);

        return $cmd;
    }

    public function doResize($imagePath, $newPath, $configuration) {
        $opts = $configuration->asHash();
        $w = $configuration->obtainWidth();
        $h = $configuration->obtainHeight();

        if(!empty($w) and !empty($h)):
            $cmd = $this->commandWithCrop($imagePath, $newPath, $configuration);
            if(true === $opts['scale']):
                $cmd = $this->commandWithScale($imagePath, $newPath, $configuration);
            endif;
        else:
            $cmd = $this->defaultShellCommand($configuration, $imagePath, $newPath);
        endif;

        $output=array();
        $return_code= $this->fileSystem->exec($cmd, $output);
        if($return_code != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            throw new RuntimeException();
        }
    }

}