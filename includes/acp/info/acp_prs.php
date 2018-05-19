<?php
/**
*
* @package prs
* @version 1.0.0 2007/12/23 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

include($phpbb_root_path . 'includes/prs/functions_prs_admin.' . $phpEx);

/**
* @package module_install
*/
class acp_prs_info
{
	var $u_action;
	var $acp;
	var $basename = 'prs';
	var $imageset_bases;
	var $release_date;
	var $templates;
	var $profiles;
	var $version ;

	function acp_prs_info()
	{
/*
		global $db, $user, $auth, $template, $module;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;
*/
		$this->acp = new acp_modules();

		// hour, minute, second, month, day, year
		$this->release_date = mktime(0, 0, 0, 6, 8, 2008);
		set_config('prs_release_date', $this->release_date);
		$this->version = '0.4.0';

		$this->profiles = array(
			'profile_minimal'	=>	array(
				'overall_footer',
				'viewtopic_body',
				'viewforum_body',
			),
			'profile_user'		=>	array(
				'memberlist_view',
				'viewforum_body',
				'search_results',
				'ucp_main_front',
			),
			'profile_mcp'		=>	array(
				'mcp_forum',
				'mcp_topic',
				'mcp_post',
			),
		);
		$this->templates = array(
			'prosilver' => array(
				'imageset'	=> array(
					'prs_star_s_' => array(
						'small'		=> TRUE,
						'height'	=> 9,
						'width'		=> 8,
					),
					'prs_star_v_' => array(
						'small'		=> FALSE,
						'height'	=> 20,
						'width'		=> 17,
					),
					'prs_star_uv_' => array(
						'small'		=> FALSE,
						'height'	=> 20,
						'width'		=> 17,
					),
				),
				'profiles'	=>	array(
					'profile_minimal',
					'profile_user',
					'profile_mcp',
				),
			),
			'subsilver2' => array(
				'imageset'	=> array(
					'prs_star_s_' => array(
						'small'		=> TRUE,
						'height'	=> 9,
						'width'		=> 8,
					),
					'prs_star_v_' => array(
						'small'		=> FALSE,
						'height'	=> 20,
						'width'		=> 17,
					),
					'prs_star_uv_' => array(
						'small'		=> FALSE,
						'height'	=> 20,
						'width'		=> 17,
					),
				),
				'profiles'	=>	array(
					'profile_minimal',
/*
					'profile_user',
					'profile_mcp',
*/
				),
			),
		);
	}

	function module()
	{
		return array(
			'filename'	=> 'acp_prs',
			'title'		=> 'ACP_CAT_PRS',
			'version'	=> $this->version,
			'modes'		=> array(
				'overview'	=> array(
					'title' => 'PRS_OVERVIEW', 
					'auth' => 'acl_a_board && acl_a_server', 
					'cat' => array('ACP_CAT_PRS')
				),
				'details'	=> array(
					'title' => 'PRS_DETAILS', 
					'auth' => 'acl_a_board && acl_a_server', 
					'cat' => array('ACP_CAT_PRS')
				),
				'update'	=> array(
					'title' => 'PRS_UPDATE',
					'auth' => 'acl_a_board && acl_a_server', 
					'cat' => array('ACP_CAT_PRS')
				),
				'stats'	=> array(
					'title' => 'PRS_STATS',
					'auth' => 'acl_a_board && acl_a_server', 
					'cat' => array('ACP_CAT_PRS')
				),
			),
		);
		
	}

	function update_sql() {
		prs_sql_imageset_data($this->templates);
	}
	
	function add_sql() {
		global $db;

		$this->update_sql();

#		Uncomment this if the error troubles you.
#		$db->sql_return_on_error(TRUE);

		prs_sql_table_prs_votes();
		prs_sql_table_prs_votes_chi();
		prs_sql_table_prs_penalty();
		prs_sql_table_prs_modpoints();
		prs_sql_table_posts();
	}

	function del_sql() {
		global $db;

		$sql = 'DROP TABLE ' . PRS_MODSPOINTS_TABLE;
		$db->sql_freeresult($db->sql_query($sql));

		$sql = 'DROP TABLE ' . PRS_PENALTY_TABLE;
		$db->sql_freeresult($db->sql_query($sql));

		$sql = 'DROP TABLE ' . PRS_VOTES_TABLE;
		$db->sql_freeresult($db->sql_query($sql));

		$sql = 'DROP TABLE ' . PRS_VOTES_CHI_TABLE;
		$db->sql_freeresult($db->sql_query($sql));

		$sql = 'ALTER TABLE ' . POSTS_TABLE . '
			DROP COLUMN prs_score,
			DROP COLUMN prs_standard_diviation,
			DROP COLUMN prs_shadowed,
			DROP COLUMN prs_penaltized';
		$db->sql_freeresult($db->sql_query($sql));

		$sql = 'DELETE FROM CONFIG_TABLE
			WHERE config_name REGEXP \'prs_.*\'';
		$db->sql_freeresult($db->sql_query($sql));
	}

	function template($template = '', $profile = '')
	{
		if (!isset($template) || !strlen($template))
		{
			foreach (array_keys($this->templates) as $template)
			{
				$this->template($template);
			}
			return;
		}

		global $cache;

		$file = 'ctpl_admin_acp_prs.html.php';
		$cache->remove_file($cache->cache_dir . $file);

		$cache->destroy('_cfg_template_' . $template);

		$profiles = $list = array();
		if ($profile == NULL || strcmp($profile, ''))
		{
			$profiles = $this->templates[$template]['profiles'];
		}
		else
		{
			$profiles[] = $profile;
		}
		foreach ($profiles as $profile)
		{
			$todo = $this->profiles[$profile];
			$list = array_merge($list, $todo);
		}
		foreach ($list as $file)
		{
			$file = 'tpl_' . $template . '_' . $file . '.html.php';
			$cache->remove_file($cache->cache_dir . $file);
		}

		$cache->destroy('sql', STYLES_IMAGESET_DATA_TABLE);
	}

	function install()
	{
		$module_data = $this->module();
		prs_add_modules($this->basename);
		$this->template();
		$this->add_sql();
		set_config('prs_version', $this->version);
		set_config('prs_release_date', $this->release_date);
		set_config('prs_install_date', time());
		add_log('admin', 'LOG_PRS_INSTALLED');

	}

	function update()
	{
		$module_data = $this->module();
		prs_add_modules($this->basename);
		$this->template();
		$this->update_sql();
		set_config('prs_version', $this->version);
		set_config('prs_release_date', $this->release_date);
		set_config('prs_update_date', time());
		add_log('admin', 'LOG_PRS_UPDATED');
	}

	function uninstall()
	{
		$this->del_modules();
		$this->del_sql();
	}
}
?>
