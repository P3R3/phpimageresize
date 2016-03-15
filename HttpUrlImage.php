<?php

class HttpUrlImage {

    private $url;
    private $valid_http_protocols = array('http', 'https');

    public function __construct($url='') {
        $this->url = $this->sanitize($url);
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
}