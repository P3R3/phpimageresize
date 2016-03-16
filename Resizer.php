<?php



class Resizer {

    private $httpUrlImage;
    private $configuration;


    public function __construct($httpUrlImage, $configuration=null) {
        if ($configuration == null) $configuration = new Configuration();
        $this->checkPath($httpUrlImage);
        $this->checkConfiguration($configuration);
        $this->httpUrlImage = $httpUrlImage;
        $this->configuration = $configuration;

    }

    private function checkPath($httpUrlImage) {
        if (!($httpUrlImage instanceof HttpUrlImage)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

}