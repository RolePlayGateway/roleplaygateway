<?php
/*
*
* @name most_poster.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: link_us.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information
$server_name = (!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME');
$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');
$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
if (!$script_name)
{
	$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
}

// Replace any number of consecutive backslashes and/or slashes with a single slash
// (could happen on some proxy setups and/or Windows servers)
$script_path = trim(dirname($script_name));
$script_path = preg_replace('#[\\\\/]{2,}#', '/', $script_path);

$url = (($secure) ? 'https://' : 'http://') . $server_name;

if ($server_port && (($secure && $server_port <> 443) || (!$secure && $server_port <> 80)))
{
	$url .= ':' . $server_port;
}

$url .= $script_path;

// Assign specific vars
$template->assign_vars(array(
	'LINK_US_TXT'	=> sprintf($user->lang['LINK_US_TXT'], $config['sitename']),
	'U_LINK_US'		=>  '&lt;a&nbsp;href=&quot;' . $url . '&quot;&nbsp;target=&quot;_blank&quot;&nbsp;title=&quot;' . $config['site_desc'] . '&quot;&gt;' . $config['sitename'] . '&lt;/a&gt;',
	)
);

?>