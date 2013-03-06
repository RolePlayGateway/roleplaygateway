<?php
header('HTTP/1.1 503 Service Temporarily Unavailable',true,503);
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 3600');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="robots" content="noindex,nofollow">
<title>503 - Temporarily Closed For Maintenance</title>
<style type="text/css">
<!--
p
{
    font-family: "Verdana", sans-serif;
}
-->
</style>
</head>
<body>
<div style="width: 600px; margin-left: auto; margin-right: auto;">
	<div style="padding:20px;">
		<img src="http://www.roleplaygateway.com/images/roleplaygateway.png" style="float:left; margin-right: 20px;" />
		<p><b>RolePlayGateway</b></p>
		<p>is temporarily closed for maintenance.</p>
		<p>Normal operation will resume as soon as we're done here.</p> 
	</div>
	<div style="clear:both; text-align: center;">
	<?php

		$fileList = array();
		$handle = opendir('./images/motivationals');
		while ( false !== ( $file = readdir($handle) ) ) {
			if (($file != '.') && ($file != '..')) {
				$fileList[] = $file;
			}
		}
		closedir($handle);
		
		$random_image = array_rand($fileList);
	?>
		<img src="http://www.roleplaygateway.com/images/motivationals/<?php echo $fileList[$random_image]; ?>" style="width: 550px"  />
	</div>
</div>
</body>
</html>
