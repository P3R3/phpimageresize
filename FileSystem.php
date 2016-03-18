<?php

class FileSystem {

    public function file_exists($filename) {
        return file_exists($filename);
    }

    public function file_get_contents($filename) {
        return file_get_contents($filename);
    }

    public function file_put_contents($filename, $data) {
        return file_put_contents($filename, $data);
    }

    public function filemtime($filename) {
        return filemtime($filename);
    }

    public function md5_file($filename) {
        return md5_file($filename);
    }

    public function pathinfo($filename) {
        return pathinfo($filename);
    }

    public function getExtension($filename) {
        $finfo = $this->pathinfo($filename);
        return '.'.$finfo['extension'];
    }

    public function exec($cmd, $output) {
        exec($cmd, $output, $return_code);
        return $return_code;
    }

    public function getimagesize($imagePath) {
        return getimagesize($imagePath);
    }


}