<?php

	$folder = './rotationheader/';
	$extList = array();
	$extList['gif'] = 'image/gif';
	$extList['jpg'] = 'image/jpeg';
	$extList['jpeg'] = 'image/jpeg';
	$extList['png'] = 'image/png';
	
$img = null;

if (substr($folder,-1) != '/') {
	$folder = $folder.'/';
}

if (isset($_GET['img'])) {
	$imageInfo = pathinfo($_GET['img']);
	if (
	    isset( $extList[ strtolower( $imageInfo['extension'] ) ] ) &&
        file_exists( $folder.$imageInfo['basename'] )
    ) {
		$img = $folder.$imageInfo['basename'];
	}
} else {
	$fileList = array();
	$handle = opendir($folder);
	while ( false !== ( $file = readdir($handle) ) ) {
		$file_info = pathinfo($file);
		if (
		    isset( $extList[ strtolower( $file_info['extension'] ) ] )
		) {
			$fileList[] = $file;
		}
	}
	closedir($handle);

	if (count($fileList) > 0) {
		$imageNumber = time() % count($fileList);
		$img = $folder.$fileList[$imageNumber];
	}
}

if ($img!=null) {
	$imageInfo = pathinfo($img);
	$contentType = 'Content-type: '.$extList[ $imageInfo['extension'] ];
	header ($contentType);
	readfile($img);
} else {
	if ( function_exists('imagecreate') ) {
		header ("Content-type: image/png");
		$im = @imagecreate (100, 100)
		    or die ("Something messed up.  Fix me sometime.");
		$background_color = imagecolorallocate ($im, 255, 255, 255);
		$text_color = imagecolorallocate ($im, 0,0,0);
		imagestring ($im, 2, 5, 5,  "Whoops.", $text_color);
		imagepng ($im);
		imagedestroy($im);
	}
}

?>
