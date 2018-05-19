<?php 

define('IN_PHPBB', true);
$phpbb_root_path = '../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
 
include($phpbb_root_path . 'config.' . $phpEx);
// include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
//include_once($phpbb_root_path . 'includes/db/dbal.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions.' . $phpEx);
 
$db = new $sql_db();
 
$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);
 
// We do not need this any longer, unset for safety purposes
unset($dbpasswd);

$max_children 	= 100;
$roleplay_id	= request_var('roleplay_id',0);
$roleplay_url	= request_var('roleplay_url','');
$format			= request_var('format','json');
error_reporting(0);
ini_set('display_errors',true);

if (strlen($roleplay_url) >= 1) {
	$sql_where = array(
		'url' =>	$roleplay_url
	);
} else {
	$sql_where = array(
		'id' =>	$roleplay_id
	);
}

if ($roleplay_id >= 1) {

	// Initial roleplay details
	$sql = 'SELECT id,title,url,description,owner,status FROM rpg_roleplays WHERE '.$db->sql_build_array('SELECT', $sql_where);
	$result = $db->sql_query($sql);
	$roleplay = $db->sql_fetchrow($result);
	$roleplay['id'] = (int) $roleplay['id'];
	$roleplay['owner'] = (int) $roleplay['owner'];
	$db->sql_freeresult($result);


	// Places
	$sql = 'SELECT name,url,synopsis FROM rpg_places WHERE roleplay_id = '.(int) $roleplay['id'].' LIMIT '.$max_children;
	$result = $db->sql_query($sql);
	while ($place = $db->sql_fetchrow($result)) {
		$place['id'] = (int) $place['id'];
		$roleplay['places'][] = $place;
	}
	$db->sql_freeresult($result);


	// Characters
	$sql = 'SELECT name,url,synopsis,owner FROM rpg_characters WHERE roleplay_id = '.(int) $roleplay['id'].' LIMIT '.$max_children;
	$result = $db->sql_query($sql);
	while ($character = $db->sql_fetchrow($result)) {
		$character['id'] = (int) $character['id'];
		$character['owner'] = (int) $character['owner'];
		$roleplay['characters'][] = $character;
	}
	$db->sql_freeresult($result);
	
	// Players
	$sql = 'SELECT DISTINCT username FROM rpg_characters c INNER JOIN gateway_users u ON c.owner = u.user_id WHERE  roleplay_id = '.(int) $roleplay['id'].' LIMIT '.$max_children;
	$result = $db->sql_query($sql);
	while ($player = $db->sql_fetchrow($result)) {
		$roleplay['players'][] = $player;
	}
	$db->sql_freeresult($result);
	
} else {

	$sql = 'SELECT id,title,url,description,owner,status FROM rpg_roleplays WHERE status = "Open" ORDER BY created DESC LIMIT '. $max_children;
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		$row['id'] = (int) $row['id'];
		$row['owner'] = (int) $row['owner'];
		$roleplay[] = $row;
	}
	$db->sql_freeresult($result);

}


// Output...

switch ($format) {
	case 'json':
	default:
		$output = json_encode($roleplay);
	break;
	case 'xml':
		$output = toXml($roleplay,'roleplay');
	break;
}

echo $output;


function toXml($data, $rootNodeName = 'data', $xml=null)
{
	// turn off compatibility mode as simple xml throws a wobbly if you don't.
	if (ini_get('zend.ze1_compatibility_mode') == 1)
	{
		ini_set ('zend.ze1_compatibility_mode', 0);
	}
	
	if ($xml == null)
	{
		$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
	}
	
	// loop through the data passed in.
	foreach($data as $key => $value)
	{
		// no numeric keys in our xml please!
		if (is_numeric($key))
		{
			// make string key...
			$key = "unknownNode_". (string) $key;
		}
		
		// replace anything not alpha numeric
		$key = preg_replace('/[^a-z]/i', '', $key);
		
		// if there is another array found recrusively call this function
		if (is_array($value))
		{
			$node = $xml->addChild($key);
			// recrusive call.
			toXml($value, $rootNodeName, $node);
		}
		else 
		{
			// add single node.
			$value = htmlentities($value);
			$xml->addChild($key,$value);
		}
		
	}
	// pass back as string. or simple xml object if you want!
	return $xml->asXML();
}

?>