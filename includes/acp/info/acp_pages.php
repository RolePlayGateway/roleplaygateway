<?php
/**
*
* Static Pages MOD acp module info file
*
* @version $Id$
* @copyright (c) 2009 Vojtěch Vondra
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

class acp_pages_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_pages',
			'title'		=> 'ACP_PAGES',
			'version'	=> '1.0.3',
			'modes'		=> array(
				'pages'		=> array('title' => 'ACP_MANAGE_PAGES', 'auth' => '', 'cat' => array('ACP_PAGES')),
			),
		);
	}

	function install($u_action)
	{
		global $phpbb_root_path, $phpEx, $db, $user;

		// Setup $auth_admin class so we can add permission options
		include($phpbb_root_path . 'includes/acp/auth.' . $phpEx);
		$auth_admin = new auth_admin();

		// Add permission for manage cvsdb
		$auth_admin->acl_add_option(array(
			'local'		=> array(),
			'global'	=> array('a_manage_pages')
		));

		$module_data = $this->module();

		$module_basename = substr(strchr($module_data['filename'], '_'), 1);

		$sql = 'SELECT module_id
				FROM ' . MODULES_TABLE . "
				WHERE module_basename = '$module_basename'";
		$result = $db->sql_query($sql);
		$module_id = $db->sql_fetchfield('module_id');
		$db->sql_freeresult($result);

		$sql = 'UPDATE ' . MODULES_TABLE . " SET module_auth = 'acl_a_manage_pages' WHERE module_id = $module_id";
		$db->sql_query($sql);

		set_config('static_pages_version', $module_data['version']);

		trigger_error(sprintf($user->lang['STATIC_PAGES_MOD_INSTALLED'], $module_data['version']) . adm_back_link($u_action));
	}

	function uninstall()
	{
	}
	
	function update($u_action)
	{
		global $user, $db, $config, $phpbb_root_path, $phpEx;

		$module_data = $this->module();
		
		// Include DB tools for db schema changes
		include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
		$db_tools = new phpbb_db_tools($db);
		
		$update_ary = array();
		
		// Determine necessray changes
		$old_version = $config['static_pages_version'];
		switch ($old_version)
		{
		  case '0.1.0':
		  case '0.1.1':
		  case '0.1.2':
		    $update_ary['add_columns'][PAGES_TABLE]['page_display'] = array('BOOL', 1);
		  case '0.1.3':
			default:
				$update_ary['add_columns'][PAGES_TABLE]['page_display_guests'] = array('BOOL', 1);
			break;
		}

		// Submit the changes to the database
		$db_tools->perform_schema_changes($update_ary);
		
		// Update the version
		set_config('static_pages_version', $module_data['version']);

		// MOD Updated, finito
		trigger_error(sprintf($user->lang['STATIC_PAGES_MOD_UPDATED'], $module_data['version']) . adm_back_link($u_action));
	}
}

?>
