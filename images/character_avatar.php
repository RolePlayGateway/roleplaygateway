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
error_reporting(E_ALL);

$character_id = (int) @$_REQUEST['character_id'];
$roleplay_id = (int) @$_REQUEST['roleplay_id'];
$character_name = (string) @$_REQUEST['character_name'];
$character_url = (string) @$_REQUEST['character_url'];
$roleplay_url = (string) @$_REQUEST['roleplay_url'];

if (!empty($character_id)) {
  $sql = 'SELECT roleplay_id, url FROM rpg_characters WHERE id = '.(int) $character_id;
  $result = $db->sql_query($sql);
  $character = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $sql = 'SELECT url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
  $result = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  header('Location: http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/characters/'.$character['url'].'/image');
  exit();
}

if (strlen($character_url) > 0) {

	$sql = "SELECT id FROM rpg_characters WHERE url = '".$db->sql_escape($character_url)."' AND roleplay_id IN (SELECT id FROM rpg_roleplays WHERE url = '".$db->sql_escape($roleplay_url)."')";
	$result = $db->sql_query($sql);


	$character_id = (int) $db->sql_fetchfield('id');
  

} else if (strlen($character_name) > 0) {

  $sql = 'SELECT roleplay_id, url FROM rpg_characters WHERE name = '.(int) $character_name;
  $result = $db->sql_query($sql);
  $character = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $sql = 'SELECT url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
  $result = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  header('Location: http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/characters/'.$character['url'].'/image');
  exit();

}

// some basic sanity checks
if(isset($character_id) && is_numeric($character_id)) {

	// get the image from the db
	$sql = "SELECT image,image_type FROM rpg_characters WHERE id = ".$db->sql_escape($character_id);

	// the result of the query
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
	
		header("Content-type: image/png");

		$expires = 60*60*24*14;
    header("Pragma: public");
    header("Cache-Control: public");
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
	
		if (!empty($row['image'])) {
			@$im = imagecreatefromstring($row['image']);
		}

    if (empty($im)) {
			$im = imagecreatefromgif('http://www.roleplaygateway.com/images/no.photo.gif');
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
