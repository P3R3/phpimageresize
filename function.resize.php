<?php

require 'HttpUrlImage.php';
require 'Configuration.php';
require 'Resizer.php';


function resize($urlImage,$opts=null){

    try {
        $configuration = new Configuration($opts);
    }catch (Exception $e) {
        return 'cannot resize the image';
    }

	try {
        $downloadFolder = $configuration->obtainDownloadFolder();
        $expirationTime = $configuration->obtainCacheMinutes();

        $sourceFilePath = (new HttpUrlImage($urlImage))->downloadTo($downloadFolder, $expirationTime);
	} catch (Exception $e) {
		return 'image not found';
	}

    try {
        $newPath = (new Resizer($configuration))->doResize($sourceFilePath);
    } catch (Exception $e) {
        return 'cannot resize the image';
    }

    return str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);

}
