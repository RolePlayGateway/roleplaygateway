<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';

$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'config.' . $phpEx);
include($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
 
$db = new $sql_db();
 
$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);
 
// We do not need this any longer, unset for safety purposes
unset($dbpasswd);


// just so we know it is broken
//error_reporting(E_ALL);
ini_set('display_errors', 0);
error_reporting(0);

$place_id = (int) @$_REQUEST['place_id'];
$place_url = (string) @$_REQUEST['place_url'];
$roleplay_url = (string) @$_REQUEST['roleplay_url'];

if (strlen($place_url) > 0) {
  
	$sql = "SELECT id, synopsis FROM rpg_places WHERE url = '".$db->sql_escape($place_url)."' AND roleplay_id IN (SELECT id FROM rpg_roleplays WHERE url = '".$db->sql_escape($roleplay_url)."')";
	$result = $db->sql_query($sql);
  $place = $db->sql_fetchrow($result);
	$place_id = $place['id'];

  $db->sql_freeresult($result);
}

// some basic sanity checks
if(isset($place_id) && is_numeric($place_id)) {

  if ($place['synopsis'] == 'You have entered the void - an black expanse with absolutely nothing in it.') {
    $sql = "SELECT image_type FROM rpg_places WHERE id = ".$db->sql_escape($place_id);
  } else {
	  // get the image from the db
	  $sql = "SELECT image,image_type FROM rpg_places WHERE id = ".$db->sql_escape($place_id);
  }

	// the result of the query
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		header("Content-type: image/png");

		$expires = 60*60*24*14;
    header("Pragma: public");
    header("Cache-Control: public");
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
	
		if ($row['image']) {	
		
			$im = imagecreatefromstring($row['image']);
		} else {

      if ($place['synopsis'] == 'You have entered the void - an black expanse with absolutely nothing in it.') {
        $im = imagecreate(100, 100);
      } else {
        $im = imagecreatefromgif('http://www.roleplaygateway.com/images/NoImage.gif');
      }
		}
		$new = imagecreatetruecolor(100, 100);
		$x = imagesx($im);
		$y = imagesy($im);

		imagecopyresampled($new, $im, 0, 0, 0, 0, 100, 100, $x, $y);
		imagedestroy($im);
		
		imagesavealpha($new,true);
		
		imagepng($new);
	}

} else {
	echo 'Please use a real id number';
}
	
?>
