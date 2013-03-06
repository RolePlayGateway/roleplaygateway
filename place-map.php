<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

error_reporting(0);

require_once 'Image/GraphViz.php';
$gv = new Image_GraphViz(true, array(), 'Map');

if (!empty($_REQUEST['place_url'])) {
  $sql = 'SELECT id FROM rpg_places WHERE url = "'.$db->sql_escape($_REQUEST['place_url']).'"';
  $result = $db->sql_query($sql);
  $row = $db->sql_fetchrow($result);
  $db->sql_freeresult($row);

  $_REQUEST['place_id'] = $row['id'];
}



$sql = 'SELECT id, name, description, url FROM rpg_places WHERE id = '.(int) $_REQUEST['place_id'].'

    OR id IN ( SELECT destination_id FROM rpg_exits WHERE place_id = '.(int) $_REQUEST['place_id'] . ' )
    OR id IN ( SELECT place_id FROM rpg_exits WHERE destination_id = '.(int) $_REQUEST['place_id'] . ' )

   AND visibility = "Public" AND visible = 1';
$result = $db->sql_query($sql);
while ($row = $result->fetch_assoc()) {

	/* $sql = 'SELECT id, name, description, url FROM rpg_places WHERE id IN (SELECT destination_id FROM rpg_exits WHERE place_id = '.(int) $row['id'].' AND direction = "ascend")';
	$groupResult = $db->sql_query($sql);
	$group = $result->fetch_assoc();
	$db->sql_freeresult($groupResult); */

	$gv->addNode(
		$row['name'],
		array(
			'URL'      => 'http://www.roleplaygateway.com/roleplay/the-multiverse/places/'.$row['url'].'/',
			'fontsize' => ($row['id'] == $_REQUEST['place_id']) ? '12' : '8',
			'fontname' => 'courier'
		)
	);
}
$result->free();


$sql = 'SELECT *, p.name AS fromName, pe.name AS toName FROM rpg_exits e
		INNER JOIN rpg_places p ON e.place_id = p.id
		INNER JOIN rpg_places pe ON e.destination_id = pe.id
	WHERE e.mode = "normal"
    AND (e.place_id = '.(int) $_REQUEST['place_id'] .' OR e.destination_id = '.(int) $_REQUEST['place_id'].')  
		AND p.roleplay_id = 1
		AND p.visibility = "Public" AND p.visible = 1';
$result = $db->sql_query($sql);
while ($row = $result->fetch_assoc()) {

	$gv->addEdge(array(
			$row['fromName']        => $row['toName']
		),
		array(
			'label' => $row['direction'],
			'fontsize' => '6',
			'fontname' => 'courier'
		)
	);

}
$result->free();


/* $sql = 'SELECT * FROM rpg_exits WHERE mode = "normal"';
$result = $db->sql_query($sql);
while ($row = $result->fetch_assoc()) {

	$gv->addEdge(array(
		$row['place_id']        => $row['destination_id']
	));

}
$result->free(); */

$gv->image('svg', 'neato');
//$gv->image();

function getPlace($placeID) {
	global $db,$cache;

	$sql = 'SELECT id, name, description FROM rpg_places WHERE id = '.(int) $placeID;
	$result = $db->sql_query($sql);
	$row = $result->fetch_assoc();
	$result->free();

	return $row;

}

?>
