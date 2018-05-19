<?php
/**
*
* @package acp
* @version $Id: acp_update.php 10195 2009-09-29 14:48:24Z acydburn $
* @copyright (c) 2005 phpBB Group
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

/**
* @package acp
*/
class acp_update
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang('install');

		$this->tpl_name = 'acp_update';
		$this->page_title = 'ACP_VERSION_CHECK';

		// Get current and latest version
		$errstr = '';
		$errno = 0;

		$info = obtain_latest_version_info(request_var('versioncheck_force', false), true);

		if ($info === false)
		{
			trigger_error('VERSIONCHECK_FAIL', E_USER_WARNING);
		}

		$info = explode("\n", $info);
		$latest_version = trim($info[0]);

		$announcement_url = trim($info[1]);
		$announcement_url = (strpos($announcement_url, '&amp;') === false) ? str_replace('&', '&amp;', $announcement_url) : $announcement_url;
		$update_link = append_sid($phpbb_root_path . 'install/index.' . $phpEx, 'mode=update');

		// Determine automatic update...
		$sql = 'SELECT config_value
			FROM ' . CONFIG_TABLE . "
			WHERE config_name = 'version_update_from'";
		$result = $db->sql_query($sql);
		$version_update_from = (string) $db->sql_fetchfield('config_value');
		$db->sql_freeresult($result);

		$current_version = (!empty($version_update_from)) ? $version_update_from : $config['version'];

		$up_to_date_automatic = (version_compare(str_replace('rc', 'RC', strtolower($current_version)), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;
		$up_to_date = (version_compare(str_replace('rc', 'RC', strtolower($config['version'])), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;
		// www.phpBB-SEO.com SEO TOOLKIT BEGIN
		$phpbb_seo_update = '';
		if ($up_to_date) {
			$phpbb_seo_update = trim(str_replace($current_version, '', $latest_version));
		}
		$update_instruction = sprintf($user->lang['UPDATE_INSTRUCTIONS'], $announcement_url, $update_link);
		if (!empty($phpbb_seo_update)) {
			$auto_package = trim($info[2]);
			$auto_update = $auto_package === 'auto_update:yes' ? true : false;
			$up_to_date = ($latest_version === @$config['seo_premod_version']) ? true : false;
			if (!$auto_update) {
				$update_instruction = '<br/><br/><hr/>' . sprintf($user->lang['ACP_PREMOD_UPDATE'], $latest_version, $announcement_url) . '<br/><hr/>';
			}
		}
		// www.phpBB-SEO.com SEO TOOLKIT END
		$template->assign_vars(array(
			'S_UP_TO_DATE'		=> $up_to_date,
			'S_UP_TO_DATE_AUTO'	=> $up_to_date_automatic,
			'S_VERSION_CHECK'	=> true,
			'U_ACTION'			=> $this->u_action,
			'U_VERSIONCHECK_FORCE' => append_sid($this->u_action . '&amp;versioncheck_force=1'),

			'LATEST_VERSION'	=> $latest_version,
			'CURRENT_VERSION'	=> $config['version'],
			'AUTO_VERSION'		=> $version_update_from,

			'UPDATE_INSTRUCTIONS'	=> $update_instruction,
		));
	}
}

?>