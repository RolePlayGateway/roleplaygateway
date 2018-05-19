<?php
/* ------------------------- CONFIGURATION -----------------------


	Set $folder to the full path to the location of your images.
	For example: $folder = '/user/me/example.com/images/';
	If the rotate.php file will be in the same folder as your
	images then you should leave it set to $folder = '.';

*/

	$type = $_GET['type'];
	
		switch ($type) {
			case 'rss':
				$folder = './images/rss/';
			break;
			case 'header':
				$folder = './images/headers/';
			break;
			default:
				$folder = './images/logos/';
			break;
		}
	
			


/*	

	Most users can safely ignore this part.  If you're a programmer,
	keep reading, if not, you're done.  Go get some coffee.

    If you'd like to enable additional image types other than
	gif, jpg, and png, add a duplicate line to the section below
	for the new image type.
	
	Add the new file-type, single-quoted, inside brackets.
	
	Add the mime-type to be sent to the browser, also single-quoted,
	after the equal sign.
	
	For example:
	
	PDF Files:

		$extList['pdf'] = 'application/pdf';
	
    CSS Files:

        $extList['css'] = 'text/css';

    You can even serve up random HTML files:

	    $extList['html'] = 'text/html';
	    $extList['htm'] = 'text/html';

    Just be sure your mime-type definition is correct!

*/

    $extList = array();
	$extList['gif'] = 'image/gif';
	$extList['jpg'] = 'image/jpeg';
	$extList['jpeg'] = 'image/jpeg';
	$extList['png'] = 'image/png';
	

// You don't need to edit anything after this point.


// --------------------- END CONFIGURATION -----------------------

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
		    or die ("Cannot initialize new GD image stream");
		$background_color = imagecolorallocate ($im, 255, 255, 255);
		$text_color = imagecolorallocate ($im, 0,0,0);
		imagestring ($im, 2, 5, 5,  "IMAGE ERROR", $text_color);
		imagepng ($im);
		imagedestroy($im);
	}
}

?>
