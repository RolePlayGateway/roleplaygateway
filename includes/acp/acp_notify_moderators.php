<?php
/**
*
* @package notify_moderators
* @version $Id: 1.1.0
* @copyright (c) 2008 david63
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
class acp_notify_moderators
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $config, $phpbb_root_path, $phpEx;	

		$action	= request_var('action', '');
		$start = request_var('start', 0);
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_notify_moderators';
		add_form_key($form_key);

		switch ($mode)
		{
			case 'list':
				$mod_forums = array();

				// Get a list of moderators	
				foreach ($auth->acl_get_list(false, array('m_'), false) as $temp => $mod_ary)
				{
					foreach ($mod_ary as $temp => $mod_ary)
					{
						foreach ($mod_ary as $mod_ary)
						{
							$mod_notify_ary[] = $mod_ary;
						}
					}
				}

				// Get moderator count for pagination
				$sql = 'SELECT COUNT(u.user_id) AS user_count
					FROM ' . USERS_TABLE . ' u
					WHERE ' . $db->sql_in_set('u.user_id', array_unique($mod_notify_ary));
				$result = $db->sql_query($sql);
				$user_count = (int) $db->sql_fetchfield('user_count');
				$db->sql_freeresult($result);

				// Get forum list for group moderators
				$sql = 'SELECT ug.user_id, f.forum_name, mc.group_name
					FROM ' . MODERATOR_CACHE_TABLE . ' mc, ' . FORUMS_TABLE . ' f, ' . USER_GROUP_TABLE . ' ug
					WHERE mc.forum_id = f.forum_id
						AND mc.group_id = ug.group_id
					ORDER BY f.forum_name';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					if (!array_key_exists($row['user_id'], $mod_forums))
					{
						$mod_forums[$row['user_id']] = $row['forum_name'] . ' [' . $user->lang['GROUP'] . ' : '. $row['group_name'] . ']<br />';
					}
					else
					{
						$mod_forums[$row['user_id']] .= $row['forum_name'] . ' [' . $user->lang['GROUP'] . ' : '. $row['group_name'] . ']<br />';
					}
				}
				$db->sql_freeresult($result);

				// Get forum list for individual moderators
				$sql = 'SELECT mc.user_id, f.forum_name
					FROM ' . MODERATOR_CACHE_TABLE . ' mc, ' . FORUMS_TABLE . ' f
					WHERE mc.forum_id = f.forum_id
					ORDER BY f.forum_name';
				$result = $db->sql_query($sql);

				while ($row = $db->sql_fetchrow($result))
				{
					if (!array_key_exists($row['user_id'], $mod_forums))
					{
						$mod_forums[$row['user_id']] = $row['forum_name'] . '<br />';
					}
					else
					{
						$mod_forums[$row['user_id']] .= $row['forum_name'] . '<br />';
					}
				}
				$db->sql_freeresult($result);

				// This code "borrowed" from memberlist.php
				$sql = 'SELECT session_user_id, MAX(session_time) AS session_time
					FROM ' . SESSIONS_TABLE . '
					WHERE session_time >= ' . (time() - $config['session_length']) . '
						AND ' . $db->sql_in_set('session_user_id', array_unique($mod_notify_ary)) . '
					GROUP BY session_user_id';
				$result = $db->sql_query($sql);

				$session_times = array();
				while ($row = $db->sql_fetchrow($result))
				{
					$session_times[$row['session_user_id']] = $row['session_time'];
				}
				$db->sql_freeresult($result);

				// Get moderator details
				$sql = 'SELECT u.user_id, u.username, u.username_clean, u.user_lastvisit, u.user_colour, u.user_notify_type, u.notify_moderator
					FROM ' . USERS_TABLE . ' u
					WHERE ' . $db->sql_in_set('u.user_id', array_unique($mod_notify_ary)) . '
					GROUP BY u.user_id
					ORDER BY u.username_clean ASC';
				$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);
				
				while ($row = $db->sql_fetchrow($result))
				{	
					$row['session_time'] = (!empty($session_times[$row['user_id']])) ? $session_times[$row['user_id']] : 0;
					$row['last_visit'] = (!empty($row['session_time'])) ? $row['session_time'] : $row['user_lastvisit'];

					switch ($row['notify_moderator'])
					{
						case NOTIFY_MOD_NONE:
							$notify = $user->lang['NONE'];
						break;

						case NOTIFY_MOD_NORMAL:
							switch ($row['user_notify_type'])
							{
								case NOTIFY_EMAIL:
									$notify = $user->lang['EMAIL'];
								break;

								case NOTIFY_IM:
									$notify = $user->lang['JABBER'];
								break;

								case NOTIFY_BOTH:
									$notify = $user->lang['EMAIL_JABBER'];
								break;
							}
						break;

						case NOTIFY_MOD_PM:
							$notify = $user->lang['PM'];
						break;
					}

					$template->assign_block_vars('moderator_list', array(
						'FORUMS'		=> (array_key_exists($row['user_id'], $mod_forums)) ? $mod_forums[$row['user_id']] : '',
						'LAST_VISIT'	=> $user->format_date($row['last_visit']),
						'NOTIFY'		=> $notify,
						'USERNAME'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
					));
				}
				$db->sql_freeresult($result);

				$this->tpl_name		= 'acp_list_moderators';
				$this->page_title	= 'ACP_NOTIFY_LIST';
		
				$template->assign_vars(array(
					'PAGINATION'		=> generate_pagination($this->u_action, $user_count, $config['posts_per_page'], $start, true),
					'S_INSTALL_CHECK'	=> file_exists($phpbb_root_path . 'install_notify_moderators.' . $phpEx),
					'S_ON_PAGE'			=> on_page($user_count, $config['posts_per_page'], $start),
					
					'U_ACTION'			=> $this->u_action
				));
			break;
			
			case 'settings':
				$display_vars = array(
					'title'	=> 'ACP_NOTIFY_SETTINGS',
					'vars'	=> array(
						'legend1'					=> 'ACP_NOTIFY_PM_SETTINGS',
						'notify_pm'					=> array('lang' => 'ACP_NOTIFY_PM', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_pm_admin'			=> array('lang' => 'APC_PM_ADMIN', 'type' => 'select', 'function' => 'admin_select', 'params' => array('{CONFIG_VALUE}'), 'explain' => true),

						'legend2'					=> 'ACP_NOTIFY_WHO',
						'notify_admin'				=> array('lang' => 'ACP_COPY_ADMIN', 'type' => 'radio:yes_no', 'explain' => false),
						'notify_moderator'			=> array('lang' => 'ACP_COPY_MODERATOR', 'type' => 'radio:yes_no', 'explain' => false),
						'notify_group'				=> array('lang' => 'ACP_COPY_GROUP', 'type' => 'radio:yes_no', 'explain' => true),
				
						'legend3'					=> 'ACP_NOTIFY_WHAT',
						'notify_post'				=> array('lang' => 'ACP_MODERATOR_POST', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_reply'				=> array('lang' => 'ACP_MODERATOR_REPLY', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_edit'				=> array('lang' => 'ACP_MODERATOR_EDIT', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_queue'				=> array('lang' => 'ACP_MODERATOR_QUEUE', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_approve'			=> array('lang' => 'ACP_MODERATOR_APPROVE', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_report'				=> array('lang' => 'ACP_MODERATOR_REPORT', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_close'				=> array('lang' => 'ACP_MODERATOR_CLOSE', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_overide_report'		=> array('lang' => 'ACP_OVERIDE_REPORT', 'type' => 'radio:yes_no', 'explain' => true),
						'notify_own'				=> array('lang' => 'ACP_MODERATOR_OWN', 'type' => 'radio:yes_no', 'explain' => true),

						'legend4'					=> 'ACP_NOTIFY_COUNTS',
						'notify_report_count'		=> array('lang' => 'ACP_REPORT_COUNT', 'type' => 'radio:yes_no', 'explain' => false),
						'notify_queue_count'		=> array('lang' => 'ACP_QUEUE_COUNT', 'type' => 'radio:yes_no', 'explain' => false),
					)
				);

			if (isset($display_vars['lang']))
			{
				$user->add_lang($display_vars['lang']);
			}

			$this->new_config = $config;
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

				$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

				if ($submit)
				{
					set_config($config_name, $config_value);
				}
			}

			if ($submit)
			{
				add_log('admin', 'LOG_CONFIG_NOTIFY');

				trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
			}

			$this->tpl_name = 'acp_notify_moderators';
			$this->page_title = $display_vars['title'];

			$template->assign_vars(array(
				'L_TITLE'			=> $user->lang[$display_vars['title']],
				'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

				'S_ERROR'			=> (sizeof($error)) ? true : false,
				'S_INSTALL_CHECK'	=> file_exists($phpbb_root_path . 'install_notify_moderators.' . $phpEx),
				'ERROR_MSG'			=> implode('<br />', $error),

				'U_ACTION'			=> $this->u_action)
			);

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
						'S_LEGEND'		=> true,
						'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
					);

					continue;
				}

				$type = explode(':', $vars['type']);

				$l_explain = '';
				if ($vars['explain'] && isset($vars['lang_explain']))
				{
					$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
				}
				else if ($vars['explain'])
				{
					$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
				}
			
				$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);
			
				if (empty($content))
				{
					continue;
				}
			
				$template->assign_block_vars('options', array(
					'KEY'			=> $config_key,
					'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
					'S_EXPLAIN'		=> $vars['explain'],
					'TITLE_EXPLAIN'	=> $l_explain,
					'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
					)
				);

				unset($display_vars['vars'][$config_key]);
			}
			break;
		}
	}
}

function admin_select($default = '')
{
	global $db, $auth;

	foreach ($auth->acl_get_list(false, array('a_'), false) as $admin_temp)
	foreach ($admin_temp as $admin_ary)

	$sql = 'SELECT user_id, username
		FROM ' . USERS_TABLE . '
			WHERE ' . $db->sql_in_set('user_id', array_unique($admin_ary), false, false) . '
			ORDER BY username_clean ASC';		
	$result = $db->sql_query($sql);

	$admin_options = '';
	while ($row = $db->sql_fetchrow($result))
	{
		$selected = ($row['user_id'] == $default) ? ' selected="selected"' : '';
		$admin_options .= '<option value="' . $row['user_id'] . '"' . $selected . '>' . $row['username'] . '</option>';
	}	
	$db->sql_freeresult($result);

	return $admin_options;
}

?>