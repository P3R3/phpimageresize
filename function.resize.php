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

    $httpUrlImage = new HttpUrlImage($urlImage);

	try {
        $downloadFolder = $configuration->obtainDownloadFolder();
        $expirationTime = $configuration->obtainCacheMinutes();

        $sourceFilePath = $httpUrlImage->downloadTo($downloadFolder, $expirationTime);

	} catch (Exception $e) {
		return 'image not found';
	}


    try {
        $resizer = new Resizer($configuration);
        $newPath = $resizer->doResize($sourceFilePath);
    } catch (Exception $e) {
        return 'cannot resize the image';
    }

	// The new path must be the return value of resizer resize

	$cacheFilePath = str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);

	return $cacheFilePath;
	
}
