<?php
define('DEBUG', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

/*
$filename = "2007-contest.php";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle); 
*/

$contents = '<h2>Links!</h2>
<p>This is a collection of links to other sites we think you might enjoy.  Check them out!</p>
<a href="http://play-free-online-games.com/">Free Online Games</a> - Directory of Free Multiplayer Online Games<br />
<a href="http://www.storycrafter.com">Online Literary RolePlaying</a> - Storycrafter is an interactive story community where people from around the world can roleplay together over the internet.<br />
<a href="http://www.rpgRM.com">rpgResourceMasters</a><br />This is a free, Resource and Play by Post ( PbP ) roleplaying site for HARP, Rolemaster, Spacemaster, Call of Cthulhu, RuneQuest, Steve Jackson Games and any other game, not catered for in the plethora of d20 sites out there, you want to bring along.
';

$contents .= "<br /><br /><br /><a href=\"http://".$_SERVER['REMOTE_HOST']."\">".$_SERVER['REMOTE_HOST']."</a>";

trigger_error($contents);

/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2007 eviL3
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
?>