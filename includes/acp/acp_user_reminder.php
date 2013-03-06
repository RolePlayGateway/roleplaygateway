<?php

/**
*
* @package phpBB3
* @version $Id: acp_user_reminder.php 92 2008-06-29 22:35:49Z lefty74 $
* @copyright (c) 2008 lefty74
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

class acp_user_reminder
{
	var $u_action;

	function main($id, $mode)
	{
		global $user, $db, $config;
		
		$user->add_lang(array('mods/acp_user_reminder','acp/users'));
		$this->tpl_name = 'acp_user_reminder_userrow';
		add_form_key('user_reminder');
		$action = request_var('action', '');
		$mark	= request_var('mark', array(0));

		//lets exclude the banned users	 
		$sql = 'SELECT ban_userid 
			FROM ' . BANLIST_TABLE . '
			WHERE ban_userid <> 0';
		$result = $db->sql_query($sql);

		$excl_user_id_ary = $excl_user_type_ary = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$excl_user_id_ary[] = $row['ban_userid'];
		}
		$db->sql_freeresult($result);
		
		//lets also exclude the guest user
		$excl_user_id_ary[] = ANONYMOUS;
		//... and the ones the admins have spared
		$excl_user_id_ary = explode(',', $config['user_reminder_protected_users']);
		//lets exclude also some user types
		$excl_user_type_ary = array(USER_IGNORE, USER_INACTIVE);

		switch ($mode)
		{
			case 'zero_poster':
				$title = 'ACP_USER_REMINDER_ZERO_POSTER';
				$this->page_title = $user->lang[$title];
				$this->zero_poster($excl_user_id_ary, $excl_user_type_ary);
				$this->dropdown_action($action, $mark, 'user_reminder_zero_poster');
				$this->build_dropdown('user_reminder_zero_poster');
			break;

			case 'inactive':
				$title = 'ACP_USER_REMINDER_INACTIVE';
				$this->page_title = $user->lang[$title];
				$this->dropdown_action($action, $mark, 'user_reminder_inactive');
				$this->build_dropdown('user_reminder_inactive');
				$this->inactive($excl_user_id_ary, $excl_user_type_ary);
			break;
			
			case 'inactive_still':
				$title = 'ACP_USER_REMINDER_INACTIVE_STILL';
				$this->page_title = $user->lang[$title];
				$this->dropdown_action($action, $mark, 'user_reminder_inactive_still');
				$this->build_dropdown('user_reminder_inactive_still');
				$this->inactive_still($excl_user_id_ary, $excl_user_type_ary);
			break;
			
			case 'not_logged_in':
				$title = 'ACP_USER_REMINDER_NOT_LOGGED_IN';
				$this->page_title = $user->lang[$title];
				$this->dropdown_action($action, $mark, 'user_reminder_not_logged_in');
				$this->build_dropdown('user_reminder_not_logged_in');
				$this->not_logged_in($excl_user_id_ary, $excl_user_type_ary);
			break;
			
			case 'protected_users':
				$title = 'ACP_USER_REMINDER_PROTECTED_USERS';
				$this->page_title = $user->lang[$title];
				$this->dropdown_action($action, $mark, 'user_reminder_protected_users');
				$this->build_dropdown('user_reminder_protected_users');
				$this->protected_users();
			break;
			
			default:
				$title = 'ACP_USER_REMINDER_CONFIGURATION';
				$this->page_title = $user->lang[$title];
				$this->tpl_name = 'acp_user_reminder';
				$this->configuration();
			break;
		}
	}

	function configuration()
	{
		global $template, $user, $auth, $phpbb_root_path, $phpEx, $config;

		$submit 	= (isset($_POST['submit'])) ? true : false;

		$config_user_reminder_row = array(
		'user_reminder_enable' 								=> request_var('user_reminder_enable', 0),
		'user_reminder_zero_poster_enable' 					=> request_var('user_reminder_zero_poster_enable', 0),
		'user_reminder_ignore_no_email' 					=> request_var('user_reminder_ignore_no_email', 0),
		'user_reminder_delete_choice' 						=> request_var('user_reminder_delete_choice', 0),
		'user_reminder_zero_poster_days' 					=> request_var('user_reminder_zero_poster_days', 0),
		'user_reminder_inactive_enable' 					=> request_var('user_reminder_inactive_enable', 0),
		'user_reminder_inactive_days' 						=> request_var('user_reminder_inactive_days', 0),
		'user_reminder_inactive_still_enable' 				=> request_var('user_reminder_inactive_still_enable', 0),
		'user_reminder_inactive_still_days' 				=> request_var('user_reminder_inactive_still_days', 0),
		'user_reminder_not_logged_in_enable' 				=> request_var('user_reminder_not_logged_in_enable', 0),
		'user_reminder_not_logged_in_days' 					=> request_var('user_reminder_not_logged_in_days', 0),
		'user_reminder_inactive_still_opt_zero' 			=> request_var('user_reminder_inactive_still_opt_zero', 0),
		'user_reminder_inactive_still_opt_inactive' 		=> request_var('user_reminder_inactive_still_opt_inactive', 0),
		'user_reminder_inactive_still_opt_not_logged_in' 	=> request_var('user_reminder_inactive_still_opt_not_logged_in', 0)
		);
		$user_reminder_protected_users 						= request_var('user_reminder_protected_users', '0');	
		
		if ($submit)
		{
			if (!check_form_key('user_reminder'))
			{
				trigger_error('FORM_INVALID');
			}

			foreach ($config_user_reminder_row as $config_name => $config_value)
			{
				set_config($config_name, (int) $config_value);
			}

			// ok, in case people put too many spaces after the comma, lets make it simple and remove them all before storage
			$user_reminder_protected_users = str_replace(' ', '', $user_reminder_protected_users);
			
			set_config('user_reminder_protected_users', (string) $user_reminder_protected_users);	

			add_log('admin', 'LOG_USER_REMINDER_CONFIG_UPDATED');
			trigger_error($user->lang['USER_REMINDER_CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}		

		//lets just make the id's look pretty and sort them numerical 
		$temp_sort_ids = '';
		if ($config['user_reminder_protected_users'])
		{
			$temp_sort_ids_ary = explode(',', $config['user_reminder_protected_users']);
			sort($temp_sort_ids_ary);
			$temp_sort_ids = implode(',', $temp_sort_ids_ary);
	
			$temp_sort_ids = ltrim($temp_sort_ids, ',');
		}
						
		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
			
			'USER_REMINDER_ENABLE'							=> $config['user_reminder_enable'],
			'USER_REMINDER_ZERO_POSTER_ENABLE'				=> $config['user_reminder_zero_poster_enable'],
			'USER_REMINDER_IGNORE_NO_EMAIL'					=> $config['user_reminder_ignore_no_email'],
			'USER_REMINDER_PROTECTED_USERS'					=> str_replace(',', ', ', $temp_sort_ids),
			'USER_REMINDER_DELETE_CHOICE'					=> $config['user_reminder_delete_choice'],
			'USER_REMINDER_ZERO_POSTER_DAYS'				=> $config['user_reminder_zero_poster_days'],
			'USER_REMINDER_INACTIVE_ENABLE'					=> $config['user_reminder_inactive_enable'],
			'USER_REMINDER_INACTIVE_DAYS'					=> $config['user_reminder_inactive_days'],
			'USER_REMINDER_INACTIVE_STILL_ENABLE'			=> $config['user_reminder_inactive_still_enable'],
			'USER_REMINDER_INACTIVE_STILL_DAYS'				=> $config['user_reminder_inactive_still_days'],
			'USER_REMINDER_NOT_LOGGED_IN_ENABLE'			=> $config['user_reminder_not_logged_in_enable'],
			'USER_REMINDER_NOT_LOGGED_IN_DAYS'				=> $config['user_reminder_not_logged_in_days'],
			'USER_REMINDER_INACTIVE_STILL_OPT_ZERO'			=> $config['user_reminder_inactive_still_opt_zero'],
			'USER_REMINDER_INACTIVE_STILL_OPT_INACTIVE'		=> $config['user_reminder_inactive_still_opt_inactive'],
			'USER_REMINDER_INACTIVE_STILL_OPT_NOT_LOGGED_IN'=> $config['user_reminder_inactive_still_opt_not_logged_in'],

			'U_FIND_USERNAME'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=searchuser&amp;form=acp_user_reminder&amp;field=user_reminder_protected_users'),

			'S_CAN_DELETE_USER'						=> ($auth->acl_get('a_userdel')) ? true : false,
			)
		);
	}

	function zero_poster($excl_user_id_ary, $excl_user_type_ary)
	{
		global $db, $template, $config, $user;
		$time = (int) (time() - ($config['user_reminder_zero_poster_days'] * 86400));
		
		$sql_choice = $this->build_choice('user_reminder_zero_poster');
		$no_email_arry = array();		
					 
		$sql = 'SELECT * 
			FROM ' . USERS_TABLE . '
        	WHERE ' . $db->sql_in_set('user_id', $excl_user_id_ary, true) . '
				AND ' . $db->sql_in_set('user_type', $excl_user_type_ary, true) . "
				AND user_posts = 0
				AND user_regdate <= " . $time .
				$sql_choice . "
			ORDER BY user_regdate DESC";
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{

			$registered = (int) (floor((time() - $row['user_regdate']) / 86400));
//			$month = floor($registered / 30);
//			$day = floor($registered- ($month * 30));
			
			if (!$row['user_allow_massemail'])
			{
				$no_email_arry[] = $row['user_id'];
			}
		
			$template->assign_block_vars('userrow', array(
				'USERNAME'			=> (!$row['user_allow_massemail']) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '<span style="color: red;">&nbsp;(x)</span>' : get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'USER_ID'			=> $row['user_id'],
//				'USER_ROW'			=> ( $row['user_regdate'] ) ? sprintf($user->lang['TIME_SPENT'],$month, $day) : '-',
				'USER_ROW'			=> ( $row['user_regdate'] ) ? sprintf($user->lang['TIME_SPENT'],$registered) : '-',
				'USER_POSTS'		=> $row['user_posts'],
				'S_IS_ZERO_POSTS'	=> true,
				'USER_REMINDER_ZERO_POSTER'		=> ($row['user_reminder_zero_poster']) ? $user->format_date(($row['user_reminder_zero_poster']), 'd M Y') : '-',
				'USER_REMINDER_INACTIVE'		=> ($row['user_reminder_inactive']) ? $user->format_date(($row['user_reminder_inactive']), 'd M Y') : '-',
				'USER_REMINDER_NOT_LOGGED_IN'	=> ($row['user_reminder_not_logged_in']) ? $user->format_date(($row['user_reminder_not_logged_in']), 'd M Y') : '-',
				'USER_REMINDER_INACTIVE_STILL'	=> ($row['user_reminder_inactive_still']) ? $user->format_date(($row['user_reminder_inactive_still']), 'd M Y') : '-',
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_IS_ZERO_POSTS'	=> true,
			'S_IS_RECEIVE_NO_MAILS'	=> sizeof($no_email_arry) ? true : false,
			'L_USER_ROW'	=> $user->lang['USER_REGDATE'],
			'L_SUBTITLE'	=> $user->lang['ZERO_POSTS_TITLE'],
			'L_SUBTITLE_EXPLAIN'	=> sprintf($user->lang['ZERO_POSTS_TITLE_EXPLAIN'],$config['user_reminder_zero_poster_days']),
		));
	}

	function inactive($excl_user_id_ary, $excl_user_type_ary)
	{
		global $db, $template, $config, $user;
		
		$time = (int) (time() - ($config['user_reminder_inactive_days'] * 86400));
		$sql_choice = $this->build_choice('user_reminder_inactive');
		$no_email_arry = array();	
		
		$sql_array = array(
			'SELECT'	=> 'u.*, s.*, MAX(s.session_time) AS session_time',
		
			'FROM'		=> array(
				USERS_TABLE	=> 'u'
			),
		
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(SESSIONS_TABLE => 's'),
					'ON'	=> 's.session_user_id = u.user_id'
				)
			),
		
			'WHERE'		=> $db->sql_in_set('u.user_id', $excl_user_id_ary, true) . '
						AND ' . $db->sql_in_set('u.user_type', $excl_user_type_ary, true) . "
						AND (u.user_lastvisit < " . (int) $time . " OR session_time < " . $time . ")" .  
						$sql_choice,
		
			'GROUP_BY'	=> 'u.user_id',
			'ORDER_BY'	=> 'u.user_lastvisit DESC'
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);			
		while ($row = $db->sql_fetchrow($result))
		{
			if( max($row['session_time'], $row['user_lastvisit']) < (int) $time && ($row['user_lastvisit'] <> 0 && !$row['session_time']))
			{
				$lastvisit = (int) (floor((time() - max($row['session_time'], $row['user_lastvisit'])) / 86400));
				if (!$row['user_allow_massemail'])
				{
					$no_email_arry[] = $row['user_id'];
				}
		
				$template->assign_block_vars('userrow', array(
					'USERNAME'			=> (!$row['user_allow_massemail']) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '<span style="color: red;">&nbsp;(x)</span>' : get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
					'USER_ID'			=> $row['user_id'],
					'USER_ROW'			=> ( $row['user_lastvisit'] ) ? sprintf($user->lang['TIME_SPENT'],$lastvisit) : '-',
		
					'USER_REMINDER_ZERO_POSTER'		=> ($row['user_reminder_zero_poster']) ? $user->format_date(($row['user_reminder_zero_poster']), 'd M Y') : '-',
					'USER_REMINDER_INACTIVE'		=> ($row['user_reminder_inactive']) ? $user->format_date(($row['user_reminder_inactive']), 'd M Y') : '-',
					'USER_REMINDER_NOT_LOGGED_IN'	=> ($row['user_reminder_not_logged_in']) ? $user->format_date(($row['user_reminder_not_logged_in']), 'd M Y') : '-',
					'USER_REMINDER_INACTIVE_STILL'	=> ($row['user_reminder_inactive_still']) ? $user->format_date(($row['user_reminder_inactive_still']), 'd M Y') : '-',
				));
			}
		}
		$db->sql_freeresult($result);

		
		$template->assign_vars(array(
			'S_IS_RECEIVE_NO_MAILS'	=> sizeof($no_email_arry) ? true : false,
			'L_USER_ROW'	=> $user->lang['USER_LASTVISIT'],
			'L_SUBTITLE'	=> $user->lang['INACTIVE_TITLE'],
			'L_SUBTITLE_EXPLAIN'	=> sprintf($user->lang['INACTIVE_TITLE_EXPLAIN'],$config['user_reminder_inactive_days']),
			)
		);
	}


	function not_logged_in($excl_user_id_ary, $excl_user_type_ary)
	{
		global $db, $template, $user, $config;

		$time = (int) (time() - ($config['user_reminder_not_logged_in_days'] * 86400));
		$sql_choice = $this->build_choice('user_reminder_not_logged_in');
		$no_email_arry = array();
				
		$sql_array = array(
			'SELECT'	=> 'u.*, s.*, MAX(s.session_time) AS session_time',
		
			'FROM'		=> array(
				USERS_TABLE	=> 'u'
			),
		
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(SESSIONS_TABLE => 's'),
					'ON'	=> 's.session_user_id = u.user_id'
				)
			),
		
			'WHERE'		=> $db->sql_in_set('u.user_id', $excl_user_id_ary, true) . '
						AND ' . $db->sql_in_set('u.user_type', $excl_user_type_ary, true) . "
						AND u.user_lastvisit = 0
						AND user_regdate <= " . $time . 
						$sql_choice,
		
			'GROUP_BY'	=> 'u.user_id',
			'ORDER_BY'	=> 'u.user_regdate DESC'
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);
		
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			if (!$row['session_time'])
			{
				$lastvisit = (int) (floor((time() - $row['user_regdate']) / 86400));
				if (!$row['user_allow_massemail'])
				{
					$no_email_arry[] = $row['user_id'];
				}
	
				$template->assign_block_vars('userrow', array(
					'USERNAME'			=> (!$row['user_allow_massemail']) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '<span style="color: red;">&nbsp;(x)</span>' : get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
					'USER_ID'			=> $row['user_id'],
					'USER_ROW'			=> ( $row['user_regdate'] ) ? sprintf($user->lang['TIME_SPENT'], $lastvisit) : '-',
		
					'USER_REMINDER_ZERO_POSTER'		=> ($row['user_reminder_zero_poster']) ? $user->format_date(($row['user_reminder_zero_poster']), 'd M Y') : '-',
					'USER_REMINDER_INACTIVE'		=> ($row['user_reminder_inactive']) ? $user->format_date(($row['user_reminder_inactive']), 'd M Y') : '-',
					'USER_REMINDER_NOT_LOGGED_IN'	=> ($row['user_reminder_not_logged_in']) ? $user->format_date(($row['user_reminder_not_logged_in']), 'd M Y') : '-',
					'USER_REMINDER_INACTIVE_STILL'	=> ($row['user_reminder_inactive_still']) ? $user->format_date(($row['user_reminder_inactive_still']), 'd M Y') : '-',

				));
			}
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_IS_RECEIVE_NO_MAILS'	=> sizeof($no_email_arry) ? true : false,
			'L_USER_ROW'	=> $user->lang['USER_REGDATE'],
			'L_SUBTITLE'	=> $user->lang['NOT_LOGGED_IN_TITLE'],
			'L_SUBTITLE_EXPLAIN'	=> $user->lang['NOT_LOGGED_IN_TITLE_EXPLAIN'],
			)
		);
	}

	
	function inactive_still($excl_user_id_ary, $excl_user_type_ary)
	{
		global $db, $template, $config, $user;
		$time = (int) (time() - ($config['user_reminder_inactive_still_days'] * 86400));
		$sql_choice = $this->build_choice('user_reminder_inactive_still');
		$no_email_arry = array();		
		
		$and_choice = '';
		if ( ($config['user_reminder_inactive_still_opt_zero'] && $config['user_reminder_inactive_still_opt_inactive'] && $config['user_reminder_inactive_still_opt_not_logged_in']) || (!$config['user_reminder_inactive_still_opt_zero'] && !$config['user_reminder_inactive_still_opt_inactive'] && !$config['user_reminder_inactive_still_opt_not_logged_in']) )
		{
			$and_choice = '(user_reminder_zero_poster < ' . $time  . ' AND user_reminder_zero_poster > 0) OR (user_reminder_inactive > 0 AND user_reminder_inactive <  ' . (int)$time  . ') OR (user_reminder_not_logged_in > 0 AND user_reminder_not_logged_in < ' . $time  . ')';
		}
		else
		{
			$and_choice = ($config['user_reminder_inactive_still_opt_zero']) ? '(user_reminder_zero_poster < ' . $time  . ' AND user_reminder_zero_poster > 0)' : '';
			$and_choice .= ($config['user_reminder_inactive_still_opt_inactive']) ? (($and_choice != '') ? ' OR ' : '') . '(user_reminder_inactive > 0 AND user_reminder_inactive <  ' . $time  . ')' : '';
			$and_choice .= ($config['user_reminder_inactive_still_opt_not_logged_in']) ?  (($and_choice != '') ? ' OR ' : '') . '(user_reminder_not_logged_in > 0 AND user_reminder_not_logged_in < ' . $time  . ')' : '';
		}

		$sql = 'SELECT * 
			FROM ' . USERS_TABLE . '
         	WHERE ' . $db->sql_in_set('user_id', $excl_user_id_ary, true) . '
				AND ' . $db->sql_in_set('user_type', $excl_user_type_ary, true) . 
				$sql_choice . "
				AND (" . $and_choice . ")
			ORDER BY user_lastvisit DESC";
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$lastvisit = (int) (floor(( time() - $row['user_lastvisit']) / 86400));
			if (!$row['user_allow_massemail'])
			{
				$no_email_arry[] = $row['user_id'];
			}

			$template->assign_block_vars('userrow', array(
				'USERNAME'			=> (!$row['user_allow_massemail']) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '<span style="color: red;">&nbsp;(x)</span>' : get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'USER_ROW'			=> ( $row['user_lastvisit'] ) ? sprintf($user->lang['TIME_SPENT'],$lastvisit) : '-',
				'USER_ID'			=> $row['user_id'],

				'USER_REMINDER_ZERO_POSTER'		=> ($row['user_reminder_zero_poster']) ? $user->format_date(($row['user_reminder_zero_poster']), 'd M Y') : '-',
				'USER_REMINDER_INACTIVE'		=> ($row['user_reminder_inactive']) ? $user->format_date(($row['user_reminder_inactive']), 'd M Y') : '-',
				'USER_REMINDER_NOT_LOGGED_IN'	=> ($row['user_reminder_not_logged_in']) ? $user->format_date(($row['user_reminder_not_logged_in']), 'd M Y') : '-',
				'USER_REMINDER_INACTIVE_STILL'	=> ($row['user_reminder_inactive_still']) ? $user->format_date(($row['user_reminder_inactive_still']), 'd M Y') : '-',
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_IS_RECEIVE_NO_MAILS'	=> sizeof($no_email_arry) ? true : false,
			'L_USER_ROW'	=> $user->lang['USER_LASTVISIT'],
			'L_SUBTITLE'	=> $user->lang['INACTIVE_STILL_TITLE'],
			'L_SUBTITLE_EXPLAIN'	=> sprintf($user->lang['INACTIVE_STILL_TITLE_EXPLAIN'], $config['user_reminder_inactive_still_days']),
			)
		);
	}
	
	function protected_users()
	{
		global $db, $template, $config, $user;
		
		$protected_users_ids = $no_email_arry = array();
		$protected_users_ids = explode(",", $config['user_reminder_protected_users']);

		$sql = 'SELECT * 
			FROM ' . USERS_TABLE . '
         	WHERE ' . $db->sql_in_set('user_id', $protected_users_ids) . '
			ORDER BY user_lastvisit DESC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
			$lastvisit = (int) (floor((time() - $row['user_lastvisit']) / 86400));

			if (!$row['user_allow_massemail'])
			{
				$no_email_arry[] = $row['user_id'];
			}

			$template->assign_block_vars('userrow', array(
				'USERNAME'			=> (!$row['user_allow_massemail']) ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) . '<span style="color: red;">&nbsp;(x)</span>' : get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'USER_ROW'			=> ( $row['user_lastvisit'] ) ? sprintf($user->lang['TIME_SPENT'],$lastvisit) : '-',
				'USER_ID'			=> $row['user_id'],

				'USER_REMINDER_ZERO_POSTER'		=> ($row['user_reminder_zero_poster']) ? $user->format_date(($row['user_reminder_zero_poster']), 'd M Y') : '-',
				'USER_REMINDER_INACTIVE'		=> ($row['user_reminder_inactive']) ? $user->format_date(($row['user_reminder_inactive']), 'd M Y') : '-',
				'USER_REMINDER_NOT_LOGGED_IN'	=> ($row['user_reminder_not_logged_in']) ? $user->format_date(($row['user_reminder_not_logged_in']), 'd M Y') : '-',
				'USER_REMINDER_INACTIVE_STILL'	=> ($row['user_reminder_inactive_still']) ? $user->format_date(($row['user_reminder_inactive_still']), 'd M Y') : '-',
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_IS_RECEIVE_NO_MAILS'	=> sizeof($no_email_arry) ? true : false,
			'S_IS_PROTECTED_USERS'	=> true,
			'L_USER_ROW'			=> $user->lang['USER_LASTVISIT'],
			'L_SUBTITLE'			=> $user->lang['PROTECTED_USERS_TITLE'],
			'L_SUBTITLE_EXPLAIN'	=> $user->lang['PROTECTED_USERS_TITLE_EXPLAIN'],
			)
		);
	}

	function dropdown_action($action, $mark, $case)
	{
		global $user, $auth, $phpbb_root_path, $db, $phpEx, $config;
		
		if ($case == 'user_reminder_protected_users')
		{
			switch ($action)
			{
				case $case:
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$protected_user_ids = $new_protected_user_ids_ary = array();
							$new_protected_user_ids = '';
							
							//here we do a bit of putting current protected users in an array, take out the ones from the $mark array and save it							
							$protected_user_ids = explode(',', $config['user_reminder_protected_users']);
							$new_protected_user_ids_ary = array_diff($protected_user_ids, $mark);
							$new_protected_user_ids = implode(',', $new_protected_user_ids_ary);
										
							set_config('user_reminder_protected_users', (string) ltrim($new_protected_user_ids, ','));
							
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT * 
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
							
							$user_id_ary = $username_ary = $user_list_ary = array();
							while ($row = $db->sql_fetchrow($result))
							{
									$user_id_ary[] = (int) $row['user_id'];
									$username_ary[] = (string) $row['username_clean'];
							}
							$db->sql_freeresult($result);
								
								add_log('admin', 'LOG_USER_UNPROTECTED', implode(', ', $username_ary));
								trigger_error($user->lang['USER_UPDATED'] . adm_back_link($this->u_action));
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action))
							);
						}
					}
				break;
	
				case 'user_delete':
					// lets just be sure that only an admin with user delete rights is here
					if ( !$auth->acl_get('a_userdel'))
					{
						trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
					
					if ( !function_exists('user_delete'))
					{
						include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
					}
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$email_delete = request_var('email_delete', 0);
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT *
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
	
							$username_ary = $username_neg_ary = $user_list_ary = array();
							$delete_type = ($config['user_reminder_delete_choice'] == RETAIN_POSTS) ? 'retain' : 'remove';
							while ($row = $db->sql_fetchrow($result))
							{
								// Some basic rules, you can't delete a founder, the guest user(should not happen anyway but better be sure) or yourself ;P
								if ($row['user_type'] != USER_FOUNDER || ($row['user_id'] != (ANONYMOUS || $user->data['user_id'])) )
								{
	
									$username_ary[] = (string) $row['username_clean'];
									user_delete($delete_type, $row['user_id'], $row['username']);
									if ($email_delete && ($row['user_allow_massemail']  || $config['user_reminder_ignore_no_email'] == OVERRIDE) && trim($row['user_email']))
									{
										$user_list_ary[] = array(
											'method'	=> $row['user_notify_type'],
											'email'		=> $row['user_email'],
											'jabber'	=> $row['user_jabber'],
											'name'		=> $row['username'],
											'lang'		=> $row['user_lang']
										);
									}
								}
								else 
								{
									$username_neg_ary[] = (string) $row['username_clean'];
								}
							}	
							$db->sql_freeresult($result);
			
							if (sizeof($username_ary))
							{
	
								if(!function_exists('send_reminder_emails'))
								{
	    							include($phpbb_root_path . 'includes/functions_user_reminder.' . $phpEx);
								}
	    						send_reminder_emails($user_list_ary, 'user_reminder_delete_notify');
	
								$message = $user->lang['USERS_DELETED'];
								$message .= sizeof($username_neg_ary) ? '<br />' . sprintf($user->lang['ERROR_USERS_DELETED'], implode(', ', $username_neg_ary)) : '';
								
								add_log('admin', 'LOG_USER_DELETED', implode(', ', $username_ary));
								trigger_error($message . adm_back_link($this->u_action));
							}
							else 
							{
								trigger_error((sprintf($user->lang['ERROR_USERS_DELETED'], implode(', ', $username_neg_ary)))  . adm_back_link($this->u_action), E_USER_WARNING);
							}
						}
						else
						{
							
							$message = $user->lang['DELETE_USER_CONFIRM_OPERATION'];
							$message .= $user->lang['CONFIRM_OPERATION'];
							confirm_box(false, $message, build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action)), 'confirm_body_user_reminder_delete.html'
							);
						}
					}		
				break;
				case 'clear_reminders':
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT user_id, username_clean 
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
	
							$user_id_ary = array();
							while ($row = $db->sql_fetchrow($result))
							{
								$user_id_ary[] = (int) $row['user_id'];
								$username_ary[] = (string) $row['username_clean'];
							}
							$db->sql_freeresult($result);
							$db->sql_transaction('begin');
	
							$sql_ary = array(
								'user_reminder_inactive'		=> 0,
								'user_reminder_zero_poster'		=> 0,
								'user_reminder_not_logged_in'	=> 0,
								'user_reminder_inactive_still'	=> 0,
							);
	
							$sql = 'UPDATE ' . USERS_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
								WHERE user_id $sql_id";
							$db->sql_query($sql);
	
							$db->sql_transaction('commit');
	
	
							add_log('admin', 'LOG_USER_REMINDER_CLEARED', implode(', ', $username_ary));
							trigger_error($user->lang['USER_UPDATED'] . adm_back_link($this->u_action));
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action))
							);
						}
					}
			
				break;
			}
			
		}
		else 
		{
			switch ($action)
			{
				case (string) $case:
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT * 
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
							
							$user_id_ary = $username_ary = $user_list_ary = array();
							while ($row = $db->sql_fetchrow($result))
							{
								if($row['user_allow_massemail'] || $config['user_reminder_ignore_no_email'] == OVERRIDE)
								{
									$user_id_ary[] = (int) $row['user_id'];
									$username_ary[] = (string) $row['username_clean'];
		
									if (trim($row['user_email']))
									{
										$user_list_ary[] = array(
											'method'	=> $row['user_notify_type'],
											'email'		=> $row['user_email'],
											'jabber'	=> $row['user_jabber'],
											'name'		=> $row['username'],
											'lang'		=> $row['user_lang']
										);
									}
								}
							}
							$db->sql_freeresult($result);
							$db->sql_transaction('begin');
							
							if (sizeof($user_list_ary))
							{						
								$sql_id = ' IN (' . implode(', ', $user_id_ary) . ')';
								$sql_ary = array(
									(string) $case		=> (int) time(),
								);
		
								$sql = 'UPDATE ' . USERS_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
									WHERE user_id $sql_id";
								$db->sql_query($sql);
		
								$db->sql_transaction('commit');
		
									if(!function_exists('send_reminder_emails'))
									{
		    							include($phpbb_root_path . 'includes/functions_user_reminder.' . $phpEx);
									}
		    						send_reminder_emails($user_list_ary, (string) $case);
								
								add_log('admin', 'LOG_' . strtoupper($case), implode(', ', $username_ary));
								trigger_error($user->lang['USER_UPDATED'] . adm_back_link($this->u_action));
							}
							else
							{
								trigger_error($user->lang['ERROR_USER_UPDATED'] . adm_back_link($this->u_action), E_USER_WARNING);
							}
						}
						else
						{
							// lets check if we want to carry out an action that has previously been done already and make sure the admin is aware of this
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT user_id, username, user_allow_massemail, ' . (string) $case . '
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id
								AND ($case > 0 OR user_allow_massemail = 0)";
							$result = $db->sql_query($sql);
	
							$no_emails_ary = $already_emailed_ary = array();
							while ($row = $db->sql_fetchrow($result))
							{
								if(!$row['user_allow_massemail'] && $config['user_reminder_ignore_no_email'] != OVERRIDE)
								{
									$no_emails_ary[] = $row['username'];
								}
								elseif($row['' . (string) $case . ''])
								{
									$already_emailed_ary[] = $row['user_id'];							
								}
							}
							$db->sql_freeresult($result);
							
							$lang_confirm = (sizeof($already_emailed_ary)) ? $user->lang['ERROR_EMAIL_CONFIRM_OPERATION'] : '';
							$lang_confirm .= (sizeof($no_emails_ary)) ? $user->lang['ERROR_NOEMAIL_CONFIRM_OPERATION'] : '';
							$lang_confirm .= $user->lang['CONFIRM_OPERATION'];
	
							confirm_box(false, $lang_confirm, build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action))
							);
						}
					}
				break;
	
				case $case . '_clear':
					
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT user_id, username_clean 
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
	
							$user_id_ary = array();
							while ($row = $db->sql_fetchrow($result))
							{
								$user_id_ary[] = (int) $row['user_id'];
								$username_ary[] = (string) $row['username_clean'];
							}
							$db->sql_freeresult($result);
							$db->sql_transaction('begin');
	
							$sql_ary = array(
								$case		=> 0,
							);
	
							$sql = 'UPDATE ' . USERS_TABLE . '
								SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
								WHERE user_id $sql_id";
							$db->sql_query($sql);
	
							$db->sql_transaction('commit');
	
	
							add_log('admin', 'LOG_' . strtoupper($case) . '_CLEAR', implode(', ', $username_ary));
							trigger_error($user->lang['USER_UPDATED'] . adm_back_link($this->u_action));
						}
						else
						{
							confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action))
							);
						}
					}
			
				break;
				case 'user_delete':
					// lets just be sure that only an admin with user delete rights is here
					if ( !$auth->acl_get('a_userdel'))
					{
						trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
					
					if ( !function_exists('user_delete'))
					{
						include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
					}
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$email_delete = request_var('email_delete', 0);
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT *
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
	
							$username_ary = $username_neg_ary = $user_list_ary = array();
							$delete_type = ($config['user_reminder_delete_choice'] == RETAIN_POSTS) ? 'retain' : 'remove';
							while ($row = $db->sql_fetchrow($result))
							{
								// Some basic rules, you can't delete a founder, the guest user(should not happen anyway but better be sure) or yourself ;P
								if ($row['user_type'] != USER_FOUNDER || ($row['user_id'] != (ANONYMOUS || $user->data['user_id'])) )
								{
	
									$username_ary[] = (string) $row['username_clean'];
									user_delete($delete_type, $row['user_id'], $row['username']);
									if ($email_delete && ($row['user_allow_massemail']  || $config['user_reminder_ignore_no_email'] == OVERRIDE) && trim($row['user_email']))
									{
										$user_list_ary[] = array(
											'method'	=> $row['user_notify_type'],
											'email'		=> $row['user_email'],
											'jabber'	=> $row['user_jabber'],
											'name'		=> $row['username'],
											'lang'		=> $row['user_lang']
										);
									}
								}
								else 
								{
									$username_neg_ary[] = (string) $row['username_clean'];
								}
							}	
							$db->sql_freeresult($result);
			
							if (sizeof($username_ary))
							{
	
								if(!function_exists('send_reminder_emails'))
								{
	    							include($phpbb_root_path . 'includes/functions_user_reminder.' . $phpEx);
								}
	    						send_reminder_emails($user_list_ary, 'user_reminder_delete_notify');
	
								$message = $user->lang['USERS_DELETED'];
								$message .= sizeof($username_neg_ary) ? '<br />' . sprintf($user->lang['ERROR_USERS_DELETED'], implode(', ', $username_neg_ary)) : '';
								
								add_log('admin', 'LOG_USER_DELETED', implode(', ', $username_ary));
								trigger_error($message . adm_back_link($this->u_action));
							}
							else 
							{
								trigger_error((sprintf($user->lang['ERROR_USERS_DELETED'], implode(', ', $username_neg_ary)))  . adm_back_link($this->u_action), E_USER_WARNING);
							}
						}
						else
						{
							
							$message = $user->lang['DELETE_USER_CONFIRM_OPERATION'];
							$message .= $user->lang['CONFIRM_OPERATION'];
							confirm_box(false, $message, build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action)), 'confirm_body_user_reminder_delete.html'
							);
						}
					}		
				break;
				case 'user_protect':
					if (sizeof($mark))
					{
						if (confirm_box(true))
						{
							$protected_user_ids = $new_protected_user_ids_ary = array();
							$new_protected_user_ids = '';
							
							//here we do a bit of putting current protectd users in an array, merge the new protected users in it and save it
							$protected_user_ids = explode(',', $config['user_reminder_protected_users']);
							$new_protected_user_ids_ary = array_merge($protected_user_ids, $mark);
							$new_protected_user_ids = implode(',', $new_protected_user_ids_ary);
										
							set_config('user_reminder_protected_users', ltrim($new_protected_user_ids, ','));
							
							$sql_id = ' IN (' . implode(', ', $mark) . ')';
							$sql = 'SELECT user_id, username_clean 
								FROM ' . USERS_TABLE . " 
								WHERE user_id $sql_id";
							$result = $db->sql_query($sql);
	
							$user_id_ary = array();
							while ($row = $db->sql_fetchrow($result))
							{
								$user_id_ary[] = (int) $row['user_id'];
								$username_ary[] = (string) $row['username_clean'];
							}
							$db->sql_freeresult($result);
	
							add_log('admin', 'LOG_USER_PROTECTED', implode(', ', $username_ary));
							trigger_error($user->lang['USER_UPDATED'] . adm_back_link($this->u_action));
						}
						else
						{
							$message = $user->lang['CONFIRM_OPERATION'];
							confirm_box(false, $message, build_hidden_fields(array(
								'mark'		=> $mark,
								'action'	=> $action))
							);
						}
					}		
				break;
			}
		}
	}
	
	function build_dropdown($case)
	{
		global $template, $auth, $user;

		$s_options = '';

		if ($case == 'user_reminder_protected_users' )
		{
			$_options = $auth->acl_get('a_userdel') ? array($case => 'UNPROTECT_USER', 'clear_reminders' => 'CLEAR_ALL', 'user_delete' => 'DELETE_USER') : array($case => 'DESELECT_USER', 'clear_reminders' => 'CLEAR_ALL');
		}
		else
		{
			$_options = $auth->acl_get('a_userdel') ? array($case => 'REMINDER', $case . '_clear' => 'CLEAR', 'user_protect' => 'PROTECT_USER', 'user_delete' => 'DELETE_USER') : array($case => 'REMINDER', $case . '_clear' => 'CLEAR', 'user_protect' => 'PROTECT_USER');
		}

		foreach ($_options as $value => $lang)
		{
			$s_options .= '<option value="' . $value . '">' . $user->lang[$lang] . '</option>';
		}

		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
			'S_USER_REMINDER_OPTIONS'	=> $s_options)
		);
	}
	
	function build_choice($choice_option)
	{
		global $template, $user;

		$choice_key	= request_var('ck', 'i');
		$limit_choice = array('i' => $user->lang['ALL'], 'j' => $user->lang['REMINDED'], 'l' => $user->lang['NOT_REMINDED']);

		$s_limit_choice = '<select name="ck" id="ck">';
		foreach ($limit_choice as $choice => $text)
		{
			$selected = ($choice_key == $choice) ? ' selected="selected"' : '';
			$s_limit_choice .= '<option value="' . $choice . '"' . $selected . '>' . $text . '</option>';
		}
		$s_limit_choice .= '</select>';

		if ( $choice_key == 'i' )
		{
			$sql_choice = '';
		}
		elseif ( $choice_key == 'j' )
		{
			$sql_choice = ' AND ' . $choice_option . ' > 0';
		}
		elseif ( $choice_key == 'l' )
		{
			$sql_choice = ' AND ' . $choice_option . ' = 0';
		}

		$template->assign_vars(array(
			'S_LIMIT_CHOICE'	=> $s_limit_choice,	
		));
		
		return $sql_choice;
	}
}
?>