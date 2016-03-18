<?php

require 'HttpUrlImage.php';
require 'Configuration.php';
require 'Resizer.php';

function sanitize($path) {
	return urldecode($path);
}

function isInCache($path, $imagePath) {
	$isInCache = false;
	if(file_exists($path) == true):
		$isInCache = true;
		$origFileTime = date("YmdHis",filemtime($imagePath));
		$newFileTime = date("YmdHis",filemtime($path));
		if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
			$isInCache = false;
		endif;
	endif;

	return $isInCache;
}




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

	$newPath = $configuration->obtainOutputFilePath($sourceFilePath);

    $create = !isInCache($newPath, $sourceFilePath);

	if($create == true):
        try {
            $resizer = new Resizer($configuration);
            $resizer->doResize($sourceFilePath);
		} catch (Exception $e) {
			return 'cannot resize the image';
		}
	endif;

	// The new path must be the return value of resizer resize

	$cacheFilePath = str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);

	return $cacheFilePath;
	
}
