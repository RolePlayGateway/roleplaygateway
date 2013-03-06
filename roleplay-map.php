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

require_once 'Image/GraphViz.php';
$gv = new Image_GraphViz(true, array(), 'Map');

error_reporting(E_ALL);


@$roleplayID = (int) $_REQUEST['roleplayID'];
$roleplayURL = $_REQUEST['roleplay_url'];

if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$roleplayID = $roleplay['id'];


$sql = 'SELECT id, name, description, url, parent_id FROM rpg_places WHERE roleplay_id = '.(int) $roleplayID.' AND visibility = "Public" AND visible = 1';
$result = $db->sql_query($sql);
while ($row = $result->fetch_assoc()) {

	/* $sql = 'SELECT id, name, description, url FROM rpg_places WHERE id IN (SELECT destination_id FROM rpg_exits WHERE place_id = '.(int) $row['id'].' AND direction = "ascend")';
	$groupResult = $db->sql_query($sql);
	$group = $result->fetch_assoc();
	$db->sql_freeresult($groupResult); */

  $places[$row['id']] = $row;

	$gv->addNode(
		$row['name'],
		array(
			'URL'      => 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/places/'.$row['url'].'/',
			'fontsize' => '8',
			'fontname' => 'courier'
		)
	);
}
$result->free();


$sql = 'SELECT *, p.name AS fromName, pe.name AS toName FROM rpg_exits e
		INNER JOIN rpg_places p ON e.place_id = p.id
		INNER JOIN rpg_places pe ON e.destination_id = pe.id
	WHERE e.mode = "normal"
		AND p.roleplay_id = '.(int) $roleplayID.'
		AND p.visibility = "Public" AND p.visible = 1';
$result = $db->sql_query($sql);
while ($row = $result->fetch_assoc()) {

	$gv->addEdge(array(
			$row['fromName']        => $row['toName']
		),
		array(
			'label' => $row['direction'],
			'fontsize' => '6',
			'fontname' => 'courier',
			'color' => '#cccccc',
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

$gv->setAttributes(array(
  //'mode'  => 'hier',
  //'model' => 'mds',
  //'nodesep' => 1,
  //'dim' => 1,
  //'pack'  => false,
  'overlap' => 'orthoyx',
  'decorate' => true,
//  'z' => 3,
  //'len' => 2,
  //'defaultdist' => 2,
  //'mindist' => 1,
  //'esep' => 1,
  //'sep' => 10
));

//$gv->setDirected(false);

$gv->image('svg', 'neato');
//$gv->image();

function getPlace($placeID) {
	global $db,$cache;

	$sql = 'SELECT id, name, description, parent_id FROM rpg_places WHERE id = '.(int) $placeID;
	$result = $db->sql_query($sql);
	$row = $result->fetch_assoc();
	$result->free();

	return $row;

}

	function returningDirection($direction) {
		switch ($direction) {
			case 'north':
				$return = 'south';
			break;
			case 'south':
				$return = 'north';
			break;
			case 'east':
				$return = 'west';
			break;
			case 'west':
				$return = 'east';
			break;
			case 'up':
				$return = 'down';
			break;
			case 'down':
				$return = 'up';
			break;
			case 'ascend':
				$return = 'descend';
			break;
			case 'descend':
				$return = 'ascend';
			break;
			case 'in':
				$return = 'out';
			break;
			case 'out':
				$return = 'in';
			break;
		}
		return $return;
	}

?>
