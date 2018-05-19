<?php
/**
*
* @package phpBB3
* @version $Id: functions_user_reminder.php 92 2008-06-29 22:35:49Z lefty74 $ 
* @copyright (c) 2008 lefty74
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
* Send all the user remindes provided that they are set to
* automatically sent
*/
function send_user_reminders()
{
	global $db, $user, $template, $config;
	global $phpEx, $phpbb_root_path;

	//lets exclude the banned users	 
	$sql = 'SELECT ban_userid 
		FROM ' . BANLIST_TABLE . '
		WHERE ban_userid <> 0';
	$result = $db->sql_query($sql);

	$excl_user_id_ary = $excl_user_type_ary = $massmailchce = array();
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

	// did we override the users 'dont email me if i 
	$massmailchce = ($config['user_reminder_ignore_no_email'] == OVERRIDE) ? array(0,1) : array(1);
			
	if ( $config['user_reminder_inactive_still_enable'] == AUTOMATIC )
	{
		$time = (int) (time() - ($config['user_reminder_inactive_still_days'] * 86400));

		$user_list_ary = array();
		$and_choice = '';

		if ( ($config['user_reminder_inactive_still_opt_zero'] && $config['user_reminder_inactive_still_opt_inactive'] && $config['user_reminder_inactive_still_opt_not_logged_in']) || (!$config['user_reminder_inactive_still_opt_zero'] && !$config['user_reminder_inactive_still_opt_inactive'] && !$config['user_reminder_inactive_still_opt_not_logged_in']) )
		{
			$and_choice = '(user_reminder_zero_poster < ' . $time  . ' AND user_reminder_zero_poster > 0) OR (user_reminder_inactive > 0 AND user_reminder_inactive <  ' . $time  . ') OR (user_reminder_not_logged_in > 0 AND user_reminder_not_logged_in < ' . $time  . ')';
		}
		else
		{
			$and_choice = ($config['user_reminder_inactive_still_opt_zero']) ? '(user_reminder_zero_poster < ' . (int)$time  . ' AND user_reminder_zero_poster > 0)' : '';
			$and_choice .= ($config['user_reminder_inactive_still_opt_inactive']) ? (($and_choice != '') ? ' OR ' : '') . '(user_reminder_inactive > 0 AND user_reminder_inactive <  ' . (int)$time  . ')' : '';
			$and_choice .= ($config['user_reminder_inactive_still_opt_not_logged_in']) ?  (($and_choice != '') ? ' OR ' : '') . '(user_reminder_not_logged_in > 0 AND user_reminder_not_logged_in < ' . (int)$time  . ')' : '';
		}

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
        	WHERE ' . $db->sql_in_set('user_id', $excl_user_id_ary, true) . '
				AND ' . $db->sql_in_set('user_type', $excl_user_type_ary, true) . "
				AND (" . $and_choice . ")
			AND " . $db->sql_in_set('user_allow_massemail', $massmailchce) . '
				AND user_reminder_inactive_still = 0';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
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

			$sql_ary = array(
			'user_reminder_inactive_still'		=> time()
			);

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE user_id = ' . (int) $row['user_id'];
			$db->sql_query($sql);

		}
		$db->sql_freeresult($result);

		if (sizeof($user_list_ary))
		{
			send_reminder_emails($user_list_ary, 'user_reminder_inactive_still');
		}
	}

	if ( $config['user_reminder_zero_poster_enable'] == AUTOMATIC )
	{
		$time = (int) (time() - ($config['user_reminder_zero_poster_days'] * 86400));
		
		$user_list_ary = array();		
		
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
        	WHERE ' . $db->sql_in_set('user_id', $excl_user_id_ary, true) . '
				AND ' . $db->sql_in_set('user_type', $excl_user_type_ary, true) . "
				AND user_posts = 0
				AND user_reminder_zero_poster = 0
				AND " . $db->sql_in_set('user_allow_massemail', $massmailchce) . "
				AND user_regdate <= " . $time;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
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
			
			$sql_ary = array(
			'user_reminder_zero_poster'		=> (int) time()
			);

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE user_id = ' . (int) $row['user_id'];
			$db->sql_query($sql);

		}
		$db->sql_freeresult($result);

		if (sizeof($user_list_ary))
		{
			send_reminder_emails($user_list_ary, 'user_reminder_zero_poster');
		}
	}

	if ( $config['user_reminder_inactive_enable'] == AUTOMATIC )
	{
		$time = (int) (time() - ($config['user_reminder_inactive_days'] * 86400));
		$user_list_ary = array();			
		
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
						AND ' . $db->sql_in_set('u.user_type', $excl_user_type_ary, true) . '
						AND user_reminder_inactive = 0
						AND ' . $db->sql_in_set('user_allow_massemail', $massmailchce) . '
						AND (u.user_lastvisit < " . $time . " OR session_time < " . $time . ")',
		
			'GROUP_BY'	=> 'u.user_id',
			'ORDER_BY'	=> 'u.user_lastvisit DESC'
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{

			if( max($row['session_time'], $row['user_lastvisit']) < $time && ($row['user_lastvisit'] <> 0 && !$row['session_time']))
			{
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
	
				$sql_ary = array(
				'user_reminder_inactive'		=> (int) time()
				);
	
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE user_id = ' . (int) $row['user_id'];
				$db->sql_query($sql);
			}
		}
		$db->sql_freeresult($result);

		if (sizeof($user_list_ary))
		{
			send_reminder_emails($user_list_ary, 'user_reminder_inactive');
		}
	}

	if ( $config['user_reminder_not_logged_in_enable'] == AUTOMATIC )
	{
		$time = (int) (time() - ($config['user_reminder_not_logged_in_days'] * 86400));
		$user_list_ary = array();				
		
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
					AND user_reminder_not_logged_in = 0
					AND user_regdate <= " . $time . "
					AND " . $db->sql_in_set('user_allow_massemail', $massmailchce) . '
					AND u.user_lastvisit = 0',
		
			'GROUP_BY'	=> 'u.user_id',
			'ORDER_BY'	=> 'u.user_regdate DESC'
		);
		
		$sql = $db->sql_build_query('SELECT', $sql_array);

		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{

			if (!$row['session_time'])
			{
		
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
	
	
				$sql_ary = array(
				'user_reminder_not_logged_in'		=> time()
				);
	
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE user_id = ' . (int) $row['user_id'];
				$db->sql_query($sql);
		
			}
		}
		$db->sql_freeresult($result);

		if (sizeof($user_list_ary))
		{
			send_reminder_emails($user_list_ary, 'user_reminder_not_logged_in');
		}
	}

}

/**
* lets check whether any reminders can be cleared
*/
function clear_user_reminders()
{
	global $db, $user, $config;

	// lets see if some of the user_reminders can be cleared
	//  lets start with the zero posters
	if ($user->data['user_posts'] <> 0 && $user->data['user_reminder_zero_poster'] )
	{
		// lets also check whether this person got possibly a second reminder
		if ($user->data['user_reminder_inactive_still'] && ($config['user_reminder_inactive_still_opt_zero'] || (!$config['user_reminder_inactive_still_opt_zero'] && !$config['user_reminder_inactive_still_opt_inactive'] && !$config['user_reminder_inactive_still_opt_not_logged_in'])) )
		{
			delete_second_reminder();
		}
		delete_zero_post_reminder();		
	}
	// and now lets see if the user has reminders for not being active or logged in and clear those
	if ($user->data['user_reminder_inactive'] || $user->data['user_reminder_not_logged_in'])	
	{
		// lets also check whether this person got possibly a second reminder
		if ($user->data['user_reminder_inactive_still'] && ($config['user_reminder_inactive_still_opt_inactive'] || $config['user_reminder_inactive_still_opt_not_logged_in'] || (!$config['user_reminder_inactive_still_opt_zero'] && !$config['user_reminder_inactive_still_opt_inactive'] && !$config['user_reminder_inactive_still_opt_not_logged_in'])) )
		{
			delete_second_reminder();
		}
		delete_inactive_user_reminder();		
	}
	
}

/**
* Deletes the User Reminder timestamp for zero poster
*/
function delete_zero_post_reminder()
{
	global $db, $user;

	$sql_ary = array(
	'user_reminder_zero_poster'		=> 0
	);

	$sql = 'UPDATE ' . USERS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
		WHERE user_id = " . $user->data['user_id'];
	$db->sql_query($sql);

}

/**
* Deletes the User Reminder timestamp from inactive users
*/
function delete_inactive_user_reminder()
{
	global $db, $user;

	$sql_ary = array(
	'user_reminder_inactive'		=> 0,
	'user_reminder_not_logged_in'	=> 0
	);

	$sql = 'UPDATE ' . USERS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
		WHERE user_id = " . $user->data['user_id'];
	$db->sql_query($sql);

}

/**
* Deletes the User Reminder timestamp from the users that 
* have received a second reminder
*/
function delete_second_reminder()
{
	global $db, $user;

	$sql_ary = array(
	'user_reminder_inactive_still'	=> 0
	);

	$sql = 'UPDATE ' . USERS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $sql_ary) . "
		WHERE user_id = " . $user->data['user_id'];
	$db->sql_query($sql);

}

/**
* Sends emails to specified users
*/
function send_reminder_emails($user_list_ary, $case)
{
	global $user, $phpbb_root_path, $template, $db, $phpEx;

    if (!class_exists('messenger'))
    {
    	include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
	}
	
	$messenger = new messenger();

	foreach ($user_list_ary as $pos => $addr)
	{

		$messenger->template($case, $addr['lang']);

		$messenger->to($addr['email'], $addr['name']);
		$messenger->im($addr['jabber'], $addr['name']);

		$messenger->assign_vars(array(
			'USERNAME'		=> htmlspecialchars_decode($addr['name'])
		));

		$messenger->send($addr['method']);

	}
	unset($user_list_ary);

	$messenger->save_queue();
	unset($messenger);
	
}
?>