<?php
/**
*
* @package acp
* @version $Id: acp_tracker.php 116 2008-05-05 20:13:03Z evil3 $
* @copyright (c) 2008 http://www.jeffrusso.net
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
class acp_tracker
{
	public $u_action;
	public $new_config;
	public $tracker;

	public function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;

		include($phpbb_root_path . 'includes/tracker/tracker_class.' . $phpEx);

		$this->tracker = new tracker();

		$this->tpl_name = 'acp_tracker';
		$action	= request_var('action', '');
		$version_check = (isset($_POST['version_check'])) ? true : false;

		if ($version_check)
		{
			$mode = 'version_check';
		}

		switch ($mode)
		{
			case 'version_check':
				$this->manage_version_check();
			break;

			case 'settings':
				$this->manage_settings();
			break;

			case 'attachments':
				$this->manage_attachments($action);
			break;

			case 'project':
				$this->manage_project($action);
			break;

			case 'component':
				$this->manage_component($action);
			break;

			case 'version':
				$this->manage_version($action);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
	}

	public function manage_version_check()
	{
		global $db, $user, $auth, $template, $cache, $mode;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$user->add_lang('install');
		$this->page_title = 'ACP_VERSION_CHECK';
		$current_version = str_replace(' ', '.', $this->tracker->api->config['version']);

		// Get current and latest version
		$errstr = '';
		$errno = 0;

		$info = get_remote_file('www.jeffrusso.net', '/updatecheck', 'phpBBTracker.txt', $errstr, $errno);

		if ($info === false)
		{
			trigger_error($errstr, E_USER_WARNING);
		}

		$info = explode("\n", $info);
		$latest_version = trim($info[0]);

		$announcement_url = trim($info[1]);
		$update_link = append_sid($phpbb_root_path . 'install/index.' . $phpEx);

		$up_to_date = (version_compare(str_replace('rc', 'RC', strtolower($current_version)), str_replace('rc', 'RC', strtolower($latest_version)), '<')) ? false : true;

		$template->assign_vars(array(
			'S_VERSION_CHECK'	=> true,
			'S_UP_TO_DATE'		=> $up_to_date,

			'LATEST_VERSION'	=> $latest_version,
			'CURRENT_VERSION'	=> $current_version,

			'UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['TRACKER_UPDATE_INSTRUCTIONS'], $announcement_url, $update_link),
		));
	}

	public function manage_settings()
	{
		global $db, $user, $auth, $template, $cache, $mode;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$form_key = 'acp_tracker';
		add_form_key($form_key);

		$submit	= (isset($_POST['submit'])) ? true : false;

		$template->assign_var('S_IN_MANAGE_SETTINGS', true);

		$display_vars = array(
			'title'			=> 'ACP_TRACKER_SETTINGS',
			'title_explain'	=> 'ACP_TRACKER_SETTINGS_EXPLAIN',
			'vars'	=> array(
				'legend1'					=> 'ACP_TRACKER_VERSION_INFO',
				'version'					=> array('lang' => 'TRACKER_VERSION', 	'validate' => 'string', 'type' => 'custom', 'method' => 'tracker_version', 'explain' => true),

				'legend2'					=> 'ACP_TRACKER_SETTINGS_GENERAL',
				'attachment_path'			=> array('lang' => 'TRACKER_ATTACHMENT_PATH',		'validate' => 'path', 	'type' => 'text:30:65', 	'explain' => true),
				'send_email'				=> array('lang' => 'TRACKER_SEND_EMAIL',			'validate' => 'bool', 	'type' => 'radio:yes_no', 	'explain' => true),
				'tickets_per_page'			=> array('lang' => 'TRACKER_TICKETS_PER_PAGE',		'validate' => 'int', 	'type' => 'text:3:4', 		'explain' => true),
				'posts_per_page'			=> array('lang' => 'TRACKER_POSTS_PER_PAGE',		'validate' => 'int', 	'type' => 'text:3:4', 		'explain' => true),
				'top_reporters'				=> array('lang' => 'TRACKER_TOP_REPORTERS',			'validate' => 'int', 	'type' => 'text:3:4', 		'explain' => true),
				'default_status_type'		=> array('lang' => 'TRACKER_DEFAULT_STATUS_TYPE',	'validate' => 'int', 	'type' => 'select', 		'explain' => true, 'method' => 'default_status_type_select'),
		));

		$this->new_config = $this->tracker->api->config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$config_value = $cfg_array[$config_name];
			$this->new_config[$config_name] = $config_value;

			if ($submit)
			{
				$this->tracker->api->set_config($config_name, $config_value);
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_TRACKER_' . strtoupper($mode));
			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{

			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'	=> true,
					'LEGEND'	=> $user->lang[$vars],
				));

				continue;
			}

			$type = explode(':', $vars['type']);

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> $user->lang[$vars['lang']],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> ($vars['explain']) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '',
				'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
			));

			unset($display_vars['vars'][$config_key]);
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
				'L_TITLE'			=> $user->lang[$display_vars['title']],
				'L_EXPLAIN'			=> $user->lang[$display_vars['title_explain']],

				'S_ERROR'			=> (sizeof($error)) ? true : false,
				'ERROR_MSG'			=> implode('<br />', $error),

				'U_ACTION'			=> $this->u_action,
		));
	}

	public function manage_attachments($action)
	{
		global $db, $user, $auth, $template, $cache, $mode;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$form_key = 'acp_tracker';
		add_form_key($form_key);
		$this->page_title = 'ACP_TRACKER_ATTACHMENTS';

		$submit			= (isset($_POST['submit'])) ? true : false;
		$attach_ids		= (isset($_POST['attach_ids'])) ? request_var('attach_ids', array(0)) : array();
		$extra_files	= (isset($_POST['extra_files'])) ? request_var('extra_files', array('')) : array();

		$errors = array();

		$template->assign_vars(array(
			'S_IN_MANAGE_ATTACHMENTS_DEFAULT'	=> true,
			'S_IN_MANAGE_ATTACHMENTS'			=> true,
			'U_ACTION' 							=> "{$this->u_action}&amp;action=delete",
		));

		switch ($action)
		{
			case 'delete':
				if (empty($attach_ids) && empty($extra_files))
				{
					break;
				}

				if (confirm_box(true))
				{
					if (!empty($attach_ids))
					{
						//Remove attachments
						$this->tracker->api->delete_orphan($attach_ids, $errors);
					}

					if (!empty($extra_files))
					{
						//Delete files
						$this->tracker->api->delete_extra_files($extra_files, $errors);
					}

					if (!sizeof($errors))
					{
						trigger_error($user->lang['TRACKER_FILES_DELETED'] . adm_back_link($this->u_action));
					}
				}
				else
				{
					confirm_box(false, 'ACP_TRACKER_ATTACHMENTS_DELETE', build_hidden_fields(array(
						'action'		=> 'delete',
						'submit'		=> true,
						'attach_ids'	=> $attach_ids,
						'extra_files'	=> $extra_files,
					)));
				}
			break;

			default:
			break;
		}

		$orphaned = $this->tracker->api->get_orphaned();
		foreach ($orphaned as $item)
		{
			$filesize = $item['filesize'];
			$size_lang = ($filesize >= 1048576) ? $user->lang['MB'] : ( ($filesize >= 1024) ? $user->lang['KB'] : $user->lang['BYTES'] );
			$filesize = ($filesize >= 1048576) ? round((round($filesize / 1048576 * 100) / 100), 2) : (($filesize >= 1024) ? round((round($filesize / 1024 * 100) / 100), 2) : $filesize);

			$template->assign_block_vars('orphan', array(
				'ATTACH_ID'			=> $item['attach_id'],
				'POSTER_USERNAME'	=> get_username_string('full', $item['poster_id'], $item['username'], $item['user_colour']),
				'REAL_FILENAME'		=> $item['real_filename'],
				'U_DOWNLOAD_LINK'	=> append_sid("{$phpbb_root_path}tracker.$phpEx", "mode=download&amp;id={$item['attach_id']}"),
				'FILESIZE'			=> $filesize,
				'SIZE_LANG'			=> $size_lang,
				'FILETIME'			=> $user->format_date($item['filetime']),
			));
		}

		$extra_files = $this->tracker->api->get_extra_files();
		foreach ($extra_files as $key => $value)
		{
			$template->assign_block_vars('extra_files', array(
				'FILENAME'			=> $key,
				'FULL_FILENAME'		=> $value,
			));
		}

		if (sizeof($errors))
		{
			$template->assign_vars(array(
				'S_ERROR'		=> true,
				'ERROR_MSG'		=> implode('<br />', $errors),
			));
		}
		$this->set_template_title($mode);
	}

	public function manage_project($action)
	{
		global $db, $user, $auth, $template, $cache, $mode;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$form_key = 'acp_tracker';
		add_form_key($form_key);
		$this->page_title = 'ACP_TRACKER_PROJECT';

		$project_id = request_var('p', 0);
		$project_cat_id = request_var('c', 0);
		$submit	= (isset($_POST['submit'])) ? true : false;

		$project_cat_add = (isset($_POST['project_cat_add'])) ? true : false;

		$template->assign_vars(array(
			'S_IN_MANAGE_PROJECT'	=> true,
		));

		if ($project_cat_add)
		{
			$project_cat_data = array(
				'project_name'			=> utf8_normalize_nfc(request_var('project_name', '', true)),
				'project_name_clean'	=> utf8_clean_string(request_var('project_name', '', true)),
			);

			if (utf8_clean_string($project_cat_data['project_name']) === '')
			{
				trigger_error($user->lang['TRACKER_PROJECT_CAT_NO_NAME'] . adm_back_link($this->u_action), E_USER_WARNING);
			}

			$this->tracker->api->add_project_cat($project_cat_data);
			add_log('admin', 'LOG_TRACKER_PROJECT_CAT_ADD', $project_cat_data['project_name']);
			trigger_error($user->lang['ACP_TRACKER_PROJECT_CAT_ADDED'] . adm_back_link($this->u_action));
		}

		switch ($action)
		{
			case 'add':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$project_data = array(
						'project_desc'			=> utf8_normalize_nfc(request_var('project_desc', '', true)),
						'project_cat_id'		=> request_var('project_cat_id', 0),
						'project_group'			=> request_var('project_group', 0),
						'project_type'			=> request_var('project_type', 0),
						'project_enabled'		=> request_var('project_enabled', 0),
						'project_security'		=> request_var('project_security', 0),
						'ticket_security'		=> request_var('ticket_security', 0),
						'show_php'				=> request_var('show_php', 0),
						'show_dbms'				=> request_var('show_dbms', 0),
						'lang_php'				=> utf8_normalize_nfc(request_var('lang_php', '', true)),
						'lang_dbms'				=> utf8_normalize_nfc(request_var('lang_dbms', '', true)),
					);

					$this->tracker->api->add_project($project_data);
					add_log('admin', 'LOG_TRACKER_PROJECT_ADD', $this->tracker->api->get_project_name($project_data['project_cat_id'], false, $project_data['project_type']));
					trigger_error($user->lang['ACP_TRACKER_PROJECT_ADDED'] . adm_back_link($this->u_action));
				}
				else
				{
					$project_data = array();
				}
			break;

			case 'edit_cat':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$project_data = array(
						'project_name'			=> utf8_normalize_nfc(request_var('project_name', '', true)),
						'project_name_clean'	=> utf8_clean_string(request_var('project_name', '', true)),
					);

					$this->tracker->api->update_project_cat($project_data, $project_cat_id);
					add_log('admin', 'LOG_TRACKER_PROJECT_CAT_EDIT', $project_data['project_name']);
					trigger_error($user->lang['ACP_TRACKER_PROJECT_CAT_EDITED'] . adm_back_link($this->u_action));
				}
				else
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_PROJECT_CATS_TABLE . '
							WHERE project_cat_id = ' . $project_cat_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$project_data = $row;
				}
			break;

			case 'edit':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$project_data = array(
						'project_desc'			=> utf8_normalize_nfc(request_var('project_desc', '', true)),
						'project_cat_id'		=> request_var('project_cat_id', 0),
						'project_group'			=> request_var('project_group', 0),
						'project_type'			=> request_var('project_type', 0),
						'project_enabled'		=> request_var('project_enabled', 0),
						'project_security'		=> request_var('project_security', 0),
						'ticket_security'		=> request_var('ticket_security', 0),
						'show_php'				=> request_var('show_php', 0),
						'show_dbms'				=> request_var('show_dbms', 0),
						'lang_php'				=> utf8_normalize_nfc(request_var('lang_php', '', true)),
						'lang_dbms'				=> utf8_normalize_nfc(request_var('lang_dbms', '', true)),
					);

					$this->tracker->api->update_project($project_data, $project_id);
					add_log('admin', 'LOG_TRACKER_PROJECT_EDIT', $this->tracker->api->get_project_name($project_data['project_cat_id'], $project_id));
					trigger_error($user->lang['ACP_TRACKER_PROJECT_EDITED'] . adm_back_link($this->u_action));
				}
				else
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_PROJECT_TABLE . '
							WHERE project_id = ' . $project_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$project_data = $row;
				}
			break;

			case 'delete_cat':
				if (confirm_box(true) && $submit)
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_PROJECT_CATS_TABLE . '
							WHERE project_cat_id = ' . $project_cat_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if ($row)
					{
						$this->tracker->api->delete_project_cat($project_cat_id);
						add_log('admin', 'LOG_TRACKER_PROJECT_CAT_DELETE', $row['project_name']);
						trigger_error($user->lang['ACP_TRACKER_PROJECT_CAT_DELETED'] . adm_back_link($this->u_action));
					}
					else
					{
						trigger_error($user->lang['ACP_TRACKER_PROJECT_CAT_NO_ID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
				}
			break;

			case 'delete':
				if (confirm_box(true) && $submit)
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_PROJECT_TABLE . '
							WHERE project_id = ' . $project_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if ($row)
					{
						$this->tracker->api->delete_project($project_id);
						add_log('admin', 'LOG_TRACKER_PROJECT_DELETE', $this->tracker->api->get_project_name($row['project_cat_id'], $project_id));
						trigger_error($user->lang['ACP_TRACKER_PROJECT_DELETED'] . adm_back_link($this->u_action));
					}
					else
					{
						trigger_error($user->lang['ACP_TRACKER_PROJECT_NO_ID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
				}
			break;

			default:
			break;
		}

		switch ($action)
		{
			case 'delete':
				confirm_box(false, 'ACP_TRACKER_PROJECT_DELETE', build_hidden_fields(array(
					'action'		=> 'delete',
					'submit'		=> true,
					'p'	=> $project_id,
				)));
			break;

			case 'delete_cat':
				confirm_box(false, 'ACP_TRACKER_PROJECT_CAT_DELETE', build_hidden_fields(array(
					'action'		=> 'delete_cat',
					'submit'		=> true,
					'c'				=> $project_cat_id,
				)));
			break;

			case 'edit_cat':
				$template->assign_vars(array(
					'S_IN_MANAGE_PROJECT_CAT_EDIT'	=> true,
					'U_ACTION'						=> $this->u_action . '&amp;action=' . $action,

					'PROJECT_CAT_ID'				=> $project_data['project_cat_id'],
					'PROJECT_CAT_NAME'				=> $project_data['project_name'],
				));

				$this->set_template_title($mode);

				return;
			break;


			case 'edit':
				$template->assign_vars(array(
					'PROJECT_ID'				=> $project_data['project_id'],
				));
			case 'add':
				if ($action == 'add')
				{
					$project_data = array_merge($project_data, array(
						'project_desc'		=> '',
						'project_cat_id'	=> 0,
						'project_group'		=> 5,
						'project_type'		=> 0,
						'project_enabled'	=> false,
						'project_security'	=> false,
						'ticket_security'	=> false,
						'show_php'			=> true,
						'show_dbms'			=> true,
						'lang_php'			=> 'TRACKER_TICKET_PHP',
						'lang_dbms'			=> 'TRACKER_TICKET_DBMS',
					));
				}

				$s_project_cat_options = $this->tracker->api->project_cat_select_options($project_data['project_cat_id']);
				if ($s_project_cat_options == '')
				{
					trigger_error($user->lang['ACP_TRACKER_NO_PROJECT_CAT_CREATED'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$template->assign_vars(array(
					'S_GROUP_OPTIONS'		=> group_select_options($project_data['project_group'], false, (($user->data['user_type'] == USER_FOUNDER) ? false : 0)),
					'S_PROJECT_CAT_OPTIONS'	=> $s_project_cat_options,
					'S_TYPE_OPTIONS'		=> $this->tracker->api->type_select_options($project_data['project_type']),
				));

				$template->assign_vars(array(
					'S_IN_MANAGE_PROJECT_ADD_EDIT'	=> true,
					'U_ACTION'						=> $this->u_action . '&amp;action=' . $action,

					'PROJECT_DESC'					=> $project_data['project_desc'],
					'PROJECT_CAT_ID'				=> $project_data['project_cat_id'],
					'PROJECT_GROUP'					=> $project_data['project_group'],
					'PROJECT_TYPE'					=> $project_data['project_type'],
					'PROJECT_ENABLED'				=> $project_data['project_enabled'],
					'PROJECT_SECURITY'				=> $project_data['project_security'],
					'TICKET_SECURITY'				=> $project_data['ticket_security'],

					'SHOW_PHP'						=> $project_data['show_php'],
					'LANG_PHP'						=> $project_data['lang_php'],
					'SHOW_DBMS'						=> $project_data['show_dbms'],
					'LANG_DBMS'						=> $project_data['lang_dbms'],
				));

				$this->set_template_title($mode);

				return;
			break;

			case 'enable':
			case 'disable':
				$this->tracker->api->set_enabled('project', $project_id, $action);
				redirect($this->u_action);
			break;

			default:
			break;
		}

		$template->assign_vars(array(
			'S_IN_MANAGE_PROJECT_DEFAULT'	=> true,
			'U_ACTION' 						=> "{$this->u_action}&amp;action=add",
		));

		$sql = 'SELECT *
			FROM ' . TRACKER_PROJECT_CATS_TABLE . '
			 ORDER BY project_name_clean ASC';
		$result = $db->sql_query($sql);

		$project_cats = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		$sql = 'SELECT *
			FROM ' . TRACKER_PROJECT_TABLE . '
			ORDER BY project_type ASC';
		$result = $db->sql_query($sql);

		$projects = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);


		if (sizeof($project_cats))
		{
			foreach ($project_cats as $project_cat)
			{
				$template->assign_block_vars('cat', array(
					'PROJECT_CAT_NAME'	=> $project_cat['project_name'],
					'U_EDIT' 			=> "{$this->u_action}&amp;action=edit_cat&amp;c={$project_cat['project_cat_id']}",
					'U_DELETE' 			=> "{$this->u_action}&amp;action=delete_cat&amp;c={$project_cat['project_cat_id']}",
				));

				foreach ($projects as $item)
				{
					if ($item['project_cat_id'] == $project_cat['project_cat_id'])
					{
						$template->assign_block_vars('cat.project', array(
							'PROJECT_DESC'		=> $item['project_desc'],
							'PROJECT_TYPE'		=> $this->tracker->api->set_lang_name($this->tracker->api->types[$item['project_type']]['title']),
							'PROJECT_ENABLED'	=> $item['project_enabled'],
							'U_EDIT' 			=> "{$this->u_action}&amp;action=edit&amp;p={$item['project_id']}",
							'U_DELETE' 			=> "{$this->u_action}&amp;action=delete&amp;p={$item['project_id']}",
							'U_ENABLE' 			=> "{$this->u_action}&amp;action=enable&amp;p={$item['project_id']}",
							'U_DISABLE' 		=> "{$this->u_action}&amp;action=disable&amp;p={$item['project_id']}",
						));
					}
				}
			}
		}
		$this->set_template_title($mode);
	}

	public function manage_component($action)
	{
		global $db, $user, $auth, $template, $cache, $mode;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$form_key = 'acp_tracker';
		add_form_key($form_key);
		$this->page_title = 'ACP_TRACKER_COMPONENT';

		$project_id = request_var('project_id', 0);
		$component_id = request_var('component_id', 0);
		$submit	= (isset($_POST['submit'])) ? true : false;

		$projects = $this->tracker->api->get_projects();

		$template->assign_var('S_IN_MANAGE_COMPONENT', true);

		switch ($action)
		{
			case 'add':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$component_data = array(
						'component_name'		=> utf8_normalize_nfc(request_var('component_name', '', true)),
						'project_id'			=> $project_id,
					);

					if (utf8_clean_string($component_data['component_name']) === '')
					{
						trigger_error($user->lang['TRACKER_COMPONENT_NO_NAME'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"), E_USER_WARNING);
					}

					$this->tracker->api->handle_project_items('add', $mode, $component_data);
					add_log('admin', 'LOG_TRACKER_COMPONENT_ADD', $component_data['component_name']);
					trigger_error($user->lang['ACP_TRACKER_COMPONENT_ADDED'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"));
				}
				else
				{
					$component_data = array();
				}
			break;

			case 'edit':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$component_data = array(
						'component_name'		=> utf8_normalize_nfc(request_var('component_name', '', true)),
						'project_id'			=> $project_id,
					);

					$this->tracker->api->handle_project_items('update', $mode, $component_data, $component_id);
					add_log('admin', 'LOG_TRACKER_COMPONENT_EDIT', $component_data['component_name']);
					trigger_error($user->lang['ACP_TRACKER_COMPONENT_EDITED'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"));
				}
				else
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_COMPONENTS_TABLE . '
							WHERE component_id = ' . $component_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$component_data = $row;
				}
			break;

			case 'delete':
				if (confirm_box(true) && $submit)
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_COMPONENTS_TABLE . '
							WHERE component_id = ' . $component_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if ($row)
					{
						$this->tracker->api->handle_project_items('delete', $mode, false, $component_id);
						add_log('admin', 'LOG_TRACKER_COMPONENT_DELETE', $row['component_name']);
						trigger_error($user->lang['ACP_TRACKER_COMPONENT_DELETED'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"));
					}
					else
					{
						trigger_error($user->lang['ACP_TRACKER_COMPONENT_NO_ID'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"), E_USER_WARNING);
					}
				}
			break;

			default:
			break;
		}

		switch ($action)
		{
			case 'delete':
				$s_hidden_fields = array(
					'action'		=> 'delete',
					'submit'		=> true,
					'component_id'	=> $component_id,
				);
				confirm_box(false, 'ACP_TRACKER_COMPONENT_DELETE', build_hidden_fields($s_hidden_fields));
			break;

			case 'edit':
				$template->assign_var('COMPONENT_ID', $component_data['component_id']);
			case 'add':
				if ($action == 'add')
				{
					$component_data['component_name'] = '';
				}

				$template->assign_vars(array(
					'S_IN_MANAGE_COMPONENT_ADD_EDIT'	=> true,
					'U_BACK'							=> $this->u_action,
					'U_ACTION'							=> $this->u_action . '&amp;action=' . $action,

					'COMPONENT_NAME'					=> $component_data['component_name'],
					'PROJECT_ID'						=> $project_id,
				));

				$this->set_template_title($mode);

				return;
			break;

			case 'view':
				$template->assign_vars(array(
					'S_IN_MANAGE_COMPONENT_VIEW'	=> true,
					'PROJECT_ID'					=> $project_id,
					'U_ACTION' 						=> "{$this->u_action}&amp;action=add",
				));

				$sql = 'SELECT *
					FROM ' . TRACKER_COMPONENTS_TABLE . '
						WHERE project_id = ' . $project_id . '
						ORDER BY component_name';
				$result = $db->sql_query($sql);

				$row = $db->sql_fetchrowset($result);
				$db->sql_freeresult($result);

				foreach ($row as $item)
				{
						$template->assign_block_vars('component', array(
							'COMPONENT_NAME'	=> $this->tracker->api->set_lang_name($item['component_name']),
							'U_EDIT' 			=> "{$this->u_action}&amp;action=edit&amp;component_id={$item['component_id']}&amp;project_id={$item['project_id']}",
							'U_DELETE' 			=> "{$this->u_action}&amp;action=delete&amp;component_id={$item['component_id']}&amp;project_id={$item['project_id']}",
						));
				}

				$this->set_template_title($mode);
				return;
			break;

			default:
			break;
		}

		$template->assign_vars(array(
			'S_IN_MANAGE_COMPONENT_DEFAULT'	=> true,
			'S_PROJECT_OPTIONS'				=> $this->tracker->api->project_select_options($projects, false, $mode),
			'U_ACTION' 						=> "{$this->u_action}&amp;action=view",
		));

		$this->set_template_title($mode);
	}

	public function manage_version($action)
	{
		global $db, $user, $auth, $template, $cache, $mode;
		global $config, $phpbb_admin_path, $phpbb_root_path, $phpEx;

		$form_key = 'acp_tracker';
		add_form_key($form_key);
		$this->page_title = 'ACP_TRACKER_VERSION';

		$project_id = request_var('project_id', 0);
		$version_id = request_var('version_id', 0);
		$submit	= (isset($_POST['submit'])) ? true : false;

		$projects = $this->tracker->api->get_projects();

		$template->assign_var('S_IN_MANAGE_VERSION', true);

		switch ($action)
		{
			case 'add':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$version_data = array(
						'version_name'			=> utf8_normalize_nfc(request_var('version_name', '', true)),
						'project_id'			=> $project_id,
					);

					if (utf8_clean_string($version_data['version_name']) === '')
					{
						trigger_error($user->lang['TRACKER_VERSION_NO_NAME'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"), E_USER_WARNING);
					}

					$this->tracker->api->handle_project_items('add', $mode, $version_data);
					add_log('admin', 'LOG_TRACKER_VERSION_ADD', $version_data['version_name']);
					trigger_error($user->lang['ACP_TRACKER_VERSION_ADDED'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"));
				}
				else
				{
					$version_data = array();
				}
			break;

			case 'edit':
				if ($submit)
				{
					if (!check_form_key($form_key))
					{
						trigger_error('FORM_INVALID', E_USER_WARNING);
					}

					$version_data = array(
						'version_name'			=> utf8_normalize_nfc(request_var('version_name', '', true)),
						'project_id'			=> $project_id,
					);


					$this->tracker->api->handle_project_items('update', $mode, $version_data, $version_id);
					add_log('admin', 'LOG_TRACKER_VERSION_EDIT', $version_data['version_name']);
					trigger_error($user->lang['ACP_TRACKER_VERSION_EDITED'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"));
				}
				else
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_VERSION_TABLE . '
							WHERE version_id = ' . $version_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					$version_data = $row;
				}
			break;

			case 'delete':
				if (confirm_box(true) && $submit)
				{
					$sql = 'SELECT *
							FROM ' . TRACKER_VERSION_TABLE . '
							WHERE version_id = ' . $version_id;
					$result = $db->sql_query($sql);
					$row = $db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if ($row)
					{
						$this->tracker->api->handle_project_items('delete', $mode, false, $version_id);
						add_log('admin', 'LOG_TRACKER_VERSION_DELETE', $row['version_name']);
						trigger_error($user->lang['ACP_TRACKER_VERSION_DELETED'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"));
					}
					else
					{
						trigger_error($user->lang['ACP_TRACKER_VERSION_NO_ID'] . adm_back_link($this->u_action . "&amp;action=view&amp;project_id=$project_id"), E_USER_WARNING);
					}
				}
			break;

			default:
			break;
		}

		switch ($action)
		{
			case 'delete':
				$s_hidden_fields = array(
					'action'		=> 'delete',
					'submit'		=> true,
					'version_id'	=> $version_id,
				);
				confirm_box(false, 'ACP_TRACKER_VERSION_DELETE', build_hidden_fields($s_hidden_fields));
			break;

			case 'edit':
				$template->assign_var('VERSION_ID', $version_data['version_id']);
			case 'add':
				if ($action == 'add')
				{
					$version_data['version_name'] = '';
				}

				$template->assign_vars(array(
					'S_IN_MANAGE_VERSION_ADD_EDIT'		=> true,
					'U_BACK'							=> $this->u_action,
					'U_ACTION'							=> $this->u_action . '&amp;action=' . $action,

					'VERSION_NAME'						=> $version_data['version_name'],
					'PROJECT_ID'						=> $project_id,
				));

				$this->set_template_title($mode);

				return;
			break;

			case 'view':
				$template->assign_vars(array(
					'S_IN_MANAGE_VERSION_VIEW'	=> true,
					'PROJECT_ID'				=> $project_id,
					'U_ACTION' 					=> "{$this->u_action}&amp;action=add",
				));

				$sql = 'SELECT *
					FROM ' . TRACKER_VERSION_TABLE . '
						WHERE project_id = ' . $project_id . '
						ORDER BY version_name';
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrowset($result);
				$db->sql_freeresult($result);

				foreach ($row as $item)
				{
						$template->assign_block_vars('version', array(
							'VERSION_NAME'		=> $this->tracker->api->set_lang_name($item['version_name']),
							'VERSION_ENABLED'	=> $item['version_enabled'],
							'U_EDIT' 			=> "{$this->u_action}&amp;action=edit&amp;version_id={$item['version_id']}&amp;project_id={$item['project_id']}",
							'U_DELETE' 			=> "{$this->u_action}&amp;action=delete&amp;version_id={$item['version_id']}&amp;project_id={$item['project_id']}",
							'U_ENABLE' 			=> "{$this->u_action}&amp;action=enable&amp;version_id={$item['version_id']}&amp;project_id={$item['project_id']}",
							'U_DISABLE' 		=> "{$this->u_action}&amp;action=disable&amp;version_id={$item['version_id']}&amp;project_id={$item['project_id']}",

						));
				}

				$this->set_template_title($mode);
				return;
			break;

			case 'enable':
			case 'disable':
				$this->tracker->api->set_enabled('version', $version_id, $action);
				redirect($this->u_action . "&amp;action=view&amp;project_id=$project_id");
			break;

			default:
			break;
		}


		$template->assign_vars(array(
			'S_IN_MANAGE_VERSION_DEFAULT'	=> true,
			'S_PROJECT_OPTIONS'				=> $this->tracker->api->project_select_options($projects, false, $mode),
			'U_ACTION' 						=> "{$this->u_action}&amp;action=view",
		));

		$this->set_template_title($mode);
	}

	public function set_template_title($mode)
	{
		global $user, $template;

		$l_title = $user->lang['ACP_TRACKER_' . strtoupper($mode)];
		$l_title_explain = $user->lang['ACP_TRACKER_' . strtoupper($mode) . '_EXPLAIN'];

		$template->assign_vars(array(
			'L_TITLE'					=> $l_title,
			'L_EXPLAIN'					=> $l_title_explain,
		));
	}

	public function tracker_version()
	{
		global $user;

		return $this->tracker->api->config['version'] . '&nbsp;&nbsp;&nbsp;<input class="button1" type="submit" id="submit" name="version_check" value="' . $user->lang['TRACKER_CHECK_UPDATES'] . '" />';
	}

	function default_status_type_select($value, $key = '')
	{
		global $user;

		return '<option value="' . TRACKER_ALL . '"' . (($value == TRACKER_ALL) ? ' selected="selected"' : '') . '>' . $user->lang['TRACKER_ALL'] . '</option><option value="' . TRACKER_ALL_OPENED . '"' . (($value == TRACKER_ALL_OPENED) ? ' selected="selected"' : '') . '>' . $user->lang['TRACKER_ALL_OPENED'] . '</option><option value="' . TRACKER_ALL_CLOSED . '"' . (($value == TRACKER_ALL_CLOSED) ? ' selected="selected"' : '') . '>' . $user->lang['TRACKER_ALL_CLOSED'] . '</option>';
	}

}
?>