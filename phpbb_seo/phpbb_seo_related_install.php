<?php
/**
*
* @package phpBB SEO Related topics
* @version $Id: phpbb_seo_related_install.php 222 2010-02-27 13:08:48Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

/*
 * Based on the phpBB3 install package / www.phpBB.com
 */
define('IN_PHPBB', true);
define('IN_INSTALL', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// Try to override some limits - maybe it helps some...
@set_time_limit(0);
$mem_limit = @ini_get('memory_limit');
if (!empty($mem_limit)) {
	$unit = strtolower(substr($mem_limit, -1, 1));
	$mem_limit = (int) $mem_limit;
	if ($unit == 'k') {
		$mem_limit = floor($mem_limit / 1024);
	} else if ($unit == 'g') {
		$mem_limit *= 1024;
	} else if (is_numeric($unit)) {
		$mem_limit = floor((int) ($mem_limit . $unit) / 1048576);
	}
	$mem_limit = max(128, $mem_limit) . 'M';
} else {
	$mem_limit = '128M';
}
@ini_set('memory_limit', $mem_limit);
include($phpbb_root_path . 'common.' . $phpEx);
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/phpbb_seo_related_install');
// Security check
// Circumvent a potential phpbb bug with paths
$redirect = append_sid(generate_board_url() . "/phpbb_seo/phpbb_seo_related_install.$phpEx");
if (!$user->data['is_registered']) {
	login_box($redirect, $user->lang['SEO_LOGIN'],'', false, false);
}
if (!$auth->acl_get('a_')) {
	$user->session_kill(true);
	login_box($redirect, $user->lang['SEO_LOGIN_ADMIN'],'', false, false);
}
if ($user->data['user_type'] != USER_FOUNDER) {
	login_box($redirect, $user->lang['SEO_LOGIN_FOUNDER'],'', false, false);
}
$mode = request_var('mode', 'start');

/**
* seo_related_install Class
* www.phpBB-SEO.com
* @package phpBB SEO Related topics
*/
class seo_related_install {
	var $force_check = 0;
	var $mode = 'install';
	var $silent = false;
	var $config_names = array('seo_related', 'seo_related_fulltext', 'seo_related_check_ignore', 'seo_related_limit', 'seo_related_allforums');
	/**
	* constructor
	*/
	function seo_related_install($mode, $force_check = 0, $silent = false) {
		$this->force_check = $force_check ? $force_check : max(0, request_var('force_check', 0));
		$this->mode = $mode === 'install' ? 'install' : ($mode === 'uninstall' ? 'uninstall' : 'start');
		$this->silent = $silent ? true : false;
		$this->{$this->mode}();
	}
	/**
	* start
	*/
	function start() {
		global $phpbb_root_path, $phpEx, $msg_title, $user;
		$install_url = append_sid($phpbb_root_path . "phpbb_seo/phpbb_seo_related_install.$phpEx?mode=install");
		$install_force_url = append_sid($phpbb_root_path . "phpbb_seo/phpbb_seo_related_install.$phpEx?mode=install&amp;force_check=1");
		$uninstall_url = append_sid($phpbb_root_path . "phpbb_seo/phpbb_seo_related_install.$phpEx?mode=uninstall");
		$msg_title = $user->lang['INSTALLATION'];
		$msg = sprintf($user->lang['INSTALLATION_START'], $install_url, $install_force_url, $uninstall_url);
		trigger_error($msg);
	}
	/**
	* install
	*/
	function install() {
		global $db, $config, $user;
		$fulltext = $already_installed = 0;
		$no_error = 1;
		$errno = E_USER_NOTICE;
		$msg = $user->lang['INSTALLED'];
		if (!isset($config['seo_related']) || $this->force_check) {
			if ($db->sql_layer == 'mysql4' || $db->sql_layer == 'mysqli') {
				// we can proceed with trying to add fulltext
				global $phpbb_root_path, $phpEx;
				if (!class_exists('phpbb_db_tools')) {
					include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
				}
				$db_tools = new phpbb_db_tools($db);
				$indexes = $db_tools->sql_list_index(TOPICS_TABLE);
				if (!in_array('topic_tft', $indexes)) {
					$sql = 'ALTER TABLE ' . TOPICS_TABLE . '
						ADD FULLTEXT topic_tft (topic_title)';
					$db->sql_return_on_error(true);
					$db->sql_query($sql);
					if ($db->sql_error_triggered) {
						$no_error = 0;
						$errno = E_USER_WARNING;
						$msg = $user->lang['INSTALLATION'];
						$msg .= '<br/>' . sprintf($user->lang['SQL_REQUIRED'], $db->sql_error_sql);
					}
					$db->sql_return_on_error(false);
				}
				// make *sure* we have the index !
				$indexes = $db_tools->sql_list_index(TOPICS_TABLE);
				$fulltext = in_array('topic_tft', $indexes) ? 1 : 0;
			}
		} else {
			$msg = $user->lang['ALREADY_INSTALLED'];
			$already_installed = 1;
		}
		if ($no_error) {
			if (!$already_installed) {
				set_config('seo_related_fulltext', $fulltext);
				$msg .= '<br/>' . ($fulltext ? $user->lang['FULLTEXT_INSTALLED'] : $user->lang['FULLTEXT_NOT_INSTALLED']);
			}
			set_config('seo_related', 1);
		}
		// Log this since it could help some to understand
		add_log('admin', $msg);
		if (!$this->silent) {
			trigger_error($msg, $errno);
		} else {
			return $no_error ? true : false;
		}
	}
	/**
	* uninstall
	*/
	function uninstall() {
		global $db, $config, $cache, $phpbb_root_path, $phpEx, $user;
		$no_error = 1;
		$errno = E_USER_NOTICE;
		$msg = $user->lang['UNINSTALLED'];
		// use db_tools to check the index
		if (!class_exists('phpbb_db_tools')) {
			include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
		}
		$db_tools = new phpbb_db_tools($db);
		$indexes = $db_tools->sql_list_index(TOPICS_TABLE);
		if (in_array('topic_tft', $indexes)) {
			$sql = 'ALTER TABLE ' . TOPICS_TABLE . '
				DROP INDEX topic_tft';
			$db->sql_return_on_error(true);
			$db->sql_query($sql);
			if ($db->sql_error_triggered) {
				$msg = $user->lang['UNINSTALLATION'];
				$msg .= '<br/>' . sprintf($user->lang['SQL_REQUIRED'], $db->sql_error_sql);
				$no_error = 0;
				$errno = E_USER_WARNING;
			}
			$db->sql_return_on_error(false);
		}
		$did_something = false;
		foreach ($this->config_names as $config_name) {
			if (isset($config[$config_name])) {
				$sql = 'DELETE FROM ' . CONFIG_TABLE . "
					WHERE config_name = '" . $db->sql_escape($config_name) . "'";
				$db->sql_query($sql);
				unset($config[$config_name]);
				$did_something = true;
			}
		}
		if ($did_something) {
			$cache->destroy('config');
		} else {
			$msg = $user->lang['ALREADY_UNINSTALLED'];
		}
		// Log this since it could help some to understand
		add_log('admin', $msg);
		if (!$this->silent) {
			trigger_error($msg, $errno);
		} else {
			return $no_error ? true : false;
		}
	}
}
$seo_related_install = new seo_related_install($mode);
?>