<?php
/**
*
* @package prs
* @version 1.0.0 2008/05/30 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);
include($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);
include($phpbb_root_path . 'includes/acp/info/acp_prs.' . $phpEx);

$prs_info = new acp_prs_info();

// Start session management
$user->session_begin();
$auth->acl($user->data);

$i = (int) request_var('passwd', '1');
$passwd = (strpos($_SERVER['SERVER_NAME'], 'prs.kruijff.org') !== false)
	? request_var('passwd', '')
	: (isset($_POST['passwd']) ? $_POST['passwd'] : '');

$form = '<table><tr><td>Enter password:</td><td><form method=post><input type=hidden name=i value=' . $i . '><input type=password name=passwd><input type=submit value=submit></form></td></tr></table>';

if (!strlen($passwd))
{
	trigger_error($form);
}
elseif (strcmp($passwd, 'prs4dummies'))
{
        trigger_error('Wrong password<br />' . $form);
}
else
{
	$msg = '<h1>PRS INFO</h1>';

	// Show config values
	$sql = 'SELECT config_name, config_value
		FROM ' . CONFIG_TABLE . '
		WHERE config_name REGEXP \'prs_.*\'';
	$result = $db->sql_query($sql, 300);
	$msg .= '<h2>Configuration</h2><table>';
	$msg .= '<tr><td>Name</td><td>Value</td></tr>';
	while ($row = $db->sql_fetchrow($result))
	{
		$msg .= '<tr><td>' . $row['config_name'] . '</td>';
		$value = $row['config_value'];
		if (strpos($row['config_name'], '_date') !== FALSE)
		{
			$value = date("D j M Y G:H", $value) . ' GMT';
		}
		elseif (strpos($row['config_name'], '_period') !== FALSE)
		{
			$value /= 86400;
			$value .= ' day(s)';
		}
		elseif (strpos($row['config_name'], '_enabled') !== FALSE)
		{
			$value = $value ? 'yes' : 'no';
		}
		$msg .= '<td>' . $value . '</td></tr>';
	}
	$msg .= '</table>';

/*
	// Show MD5 of the copy and edited files
	$files = array(
		'EDITED'	=> prs_edited_files(),
		'LANGUAGE'	=> prs_language_files(),
		'STYLES'	=> prs_styles_files(),
		'COPIED' 	=> prs_copied_files(),
	);

	if (strpos($_SERVER['SERVER_NAME'], 'prs.kruijff.org') !== false)
	{
		include("prs_md5.php");
	}
*/
	include($phpbb_root_path . 'prs_check.' . $phpEx);

	$msg .= '<h2>MD5</h2><table>';
	$msg .= '<tr><td>File</td><td>Fingerprint</td><td>Package fingerprint</td><td>Match</td></tr>';
	foreach ($prs_check_files as $key => $value)
	{
		$arr_md5 = prs_md5_file($value);
		foreach ($arr_md5 as $file => $md5)
		{
			if (!strcmp($file, 'ALL'))
			{
				continue;
			}
			$msg .= '<tr><td>' . $file. '</td><td>' . $md5 . '</td><td>' . $prs_md5_copied[$key][$file] . '</td><td>' . (strcmp($prs_md5_copied[$key][$file], $md5) ? '<font color=red>NO MATCH</font>' : '<font color=green>MATCH</font>'). '</td></tr>';
		}
		$md5 = $arr_md5['ALL'];
		$msg .= '<tr><td><b>' . $key . '_' . $file. '</b></td><td><b>' . $md5 . '</b></td><td><b>' . $prs_md5_copied[$key][$file] . '</b></td><td><b>' . (strcmp($prs_md5_copied[$key][$file], $md5) ? '<font color=red>NO MATCH</font>' : '<font color=green>MATCH</font>'). '</b></td></tr>';
	}
	$msg .= '</table>';

	// Show stats
	$msg .= '<h2>Statistics</h2><table>';
	$msg .= '<tr><td>Name</td><td>Value</td></tr>';
	$arr = prs_stats();
	foreach ($arr as $key => $value)
	{
		$msg .= '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
	}
	$msg .= '</table>';

	trigger_error($msg);
}

/*
'<h2>OVERVIEW</h2>'

'<h2>DETAILS</h2>'

'<h2>STATS</h2>'
*/

?>
