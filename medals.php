<?php
/***************************************************************************
*
* @package Medals Mod for phpBB3
* @version $Id: medals.php,v 0.8.5 2008/02/06 Gremlinn$
* @copyright (c) 2008 Nathan DuPra (mods@dupra.net)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
***************************************************************************/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_medals.' . $phpEx);

if ( !$config['medals_active'] )
{
	$url     = append_sid($phpbb_root_path . 'index.' . $phpEx);
	$message = "This mod is not active. <br /><br />Click <a href=\"$url\">here</a> to return to the index.<br />";
	trigger_error($message);
}

// Gather post and get variables
$mode		= request_var('m', '');
$from		= request_var('f', '');
$user_id	= request_var('u', 0);
$usernames	= request_var('add', '', true);
$medal_id	= request_var('mid', 0);
$med_id		= request_var('med', 0);
$submit		= request_var('submit','');
$catchoice	= request_var('cat', getfirstcat());

// Dynamic Medal Image creation
if ( $mode == "mi" )
{
	$medal		= request_var('med', '');
	$device		= request_var('d', '');

	include_once($phpbb_root_path . 'includes/dynamic_image.' . $phpEx);
	create_dynamic_image($medal,$device);
	exit;
}

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('common');

include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

$user->add_lang('mods/info_medals_mod');
$user->add_lang('posting');

$uid			= $bitfield			= '';	// will be modified by generate_text_for_storage
$allow_bbcode	= $allow_smilies	= true;
$allow_urls		= false;
$m_flags = '3';  // 1 is bbcode, 2 is smiles, 4 is urls (add together to turn on more than one)

$config['points_enable'] = isset($config['points_enable']) ? $config['points_enable'] : 0;

//$start	= request_var('start', 0);
//$pagination_url =append_sid("{$phpbb_root_path}medals.php?cat=".$catchoice);


$sql = 'SELECT count(user_id) as total FROM gateway_users';
$countResult = $db->sql_query($sql, 3600);
$stats = $db->sql_fetchrow($countResult);
$db->sql_freeresult($countResult);


$sql = "SELECT *
	FROM " . MEDALS_TABLE . "
	ORDER BY order_id ASC";
$result = $db->sql_query($sql);
$medals = array();
while ($row = $db->sql_fetchrow($result))
{
	$medals[$row['id']] = array(
		'name' 			=> $row['name'],
		'slug' 			=> $row['slug'],
		'image'	 		=> $phpbb_root_path . 'images/medals/' . $row['image'],
		'device' 		=> $phpbb_root_path . 'images/medals/devices/' . $row['device'],
		'dynamic'		=> $row['dynamic'],
		'parent' 		=> $row['parent'],
		'id'			=> $row['id'],
		'number'		=> $row['number'],
		'nominated'		=> $row['nominated'],
		'order_id'		=> $row['order_id'],
		'description'	=> $row['description'],
		'points'		=> $row['points'],
	);
}
$db->sql_freeresult($result);

$sql = "SELECT *
	FROM " . MEDALS_CATS_TABLE . "
	ORDER BY order_id ASC";
$result = $db->sql_query($sql);
$cats = array();
while ($row = $db->sql_fetchrow($result))
{
	$cats[$row['id']] = array(
		'name' 		=> $row['name'],
		'id'		=> $row['id'],
		'order_id'	=> $row['order_id'],
	);
	$template->assign_block_vars('catlinkrow', array(
								'U_CATPAGE'		=> append_sid('medals.php?cat=' . $row['id']),
								'MEDAL_CAT'		=> $row['name'],
						));
}
$db->sql_freeresult($result);

generate_smilies('inline', 0);

$template->assign_vars(array(
	'CURRENT_SEARCH_QUERY' => htmlspecialchars(@$_REQUEST['keywords']),
	'S_PAGE_ONLY'		=> true,
	'S_CAN_AWARD_MEDALS'	=> ($user->data['user_type'] == USER_FOUNDER || $auth->acl_get('u_award_medals') ) ? true : false,
	'S_CAN_NOMINATE_MEDALS'	=> ($auth->acl_get('u_nominate_medals') && $user_id != $user->data['user_id'] ) ? true : false,
	'U_NOMINATE_PANEL'		=> append_sid('medals.php?m=nominate&u=' . $user_id),
	'U_AWARD_PANEL'			=> append_sid('medals.php?m=award&u=' . $user_id),
	'U_VALIDATE_PANEL'		=> append_sid('medals.php?m=validate&u=' . $user_id),
	'U_AWARDED_PANEL' 		=> append_sid('medals.php?m=awarded&u=' . $user_id),
));

switch ($mode)
{
	case 'nominate':
		if ( $user->data['user_id'] == ANONYMOUS || !$auth->acl_get('u_nominate_medals') )
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if ( $user_id == 0 || $user_id == ANONYMOUS )
		{
			trigger_error('NO_USER_ID');
		}
		if ( $user_id == $user->data['user_id'] )
		{
			trigger_error('NOT_SELF');
		}
		$sql = "SELECT *
				FROM " . MEDALS_AWARDED_TABLE . "
				WHERE user_id = {$user_id}
				ORDER BY medal_id AND nominated";
		$result = $db->sql_query($sql);
		$my_medals = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$awarded_by_me = (isset($my_medals[$row['medal_id']]['awarded_by_me']) && $row['nominated'] == 1) ? $my_medals[$row['medal_id']]['awarded_by_me'] : 0;
			$row['awarded_by_me'] = ($user->data['user_id'] == $row['awarder_id'] && $awarded_by_me == 0 && $row['nominated'] == 1) ? 1 : $awarded_by_me;
			$my_medals[$row['medal_id']] = $row;
		}
		$db->sql_freeresult($result);
		
		$sql = "SELECT user_id, username, user_colour
			FROM " . USERS_TABLE . "
			WHERE user_id = {$user_id}";
		$result = $db->sql_query($sql, 3600);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']);
		
		$medals_options = '<option value=""></option>';
		$temp_string = '';
		$i = 0;
		foreach ($cats as $key => $value)
		{
			$at_least_one = false;
			foreach ($medals as $key2 => $value2)
			{
				if ($value2['parent'] == $value['id'])
				{
					$can_award = false;

					$my_medals[$value2['id']]['awarded_by_me'] = isset($my_medals[$value2['id']]['awarded_by_me']) ? $my_medals[$value2['id']]['awarded_by_me'] : 0;
					if ( $value2['nominated'] == 1 && $my_medals[$value2['id']]['awarded_by_me'] == 0 )
					{
						$temp_string .= '<option value="' . $value2['id'] . '">&bull;&nbsp;' . $value2['name'] . '</option>';
						$at_least_one = true;
					}
				}
			}
			if ($at_least_one)
			{
				$medals_options .= '<option value="">' . $value['name'] . '</option>';
				$medals_options .= $temp_string;
				$at_least_one = false;
				$temp_string = '';
				$i++;
			}
		}
		if ($i == 0)
		{
			trigger_error(sprintf($user->lang['NO_MEDALS_TO_NOMINATE'], append_sid('memberlist.php?mode=viewprofile&u=' . $user_id)));
		}
		
		$medals_arr       = 'var medals = new Array();';
		$medals_desc_arr  = 'var medals_desc = new Array();' ;
		foreach ($medals as $key => $value)
		{
			$medals_arr .= 'medals[' . $value['id'] . '] = "' . $value['image'] . '";';
			$medals_desc_arr .= 'medals_desc[' . $value['id'] . '] = "' . $value['description'] . '";';
		}
		$medals_arr .= "\n" . $medals_desc_arr . "\n" ;
		
		$bbcode_status	= ($config['allow_bbcode']) ? true : false;
		$smilies_status	= ($bbcode_status && $config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($bbcode_status && $config['allow_post_links']) ? true : false;
		$flash_status	= ($bbcode_status) ? true : false;
		$quote_status	= ($bbcode_status) ? true : false;
		display_custom_bbcodes();
		
		$template->assign_vars(array(
			'USERNAME'				=> $username,
			'MEDALS'				=> $medals_options,
			'JS'					=> $medals_arr,
			'U_MEDALS_ACTION'		=> append_sid('/medals.php?m=submit_nomination&u=' . $user_id),
			'S_BBCODE_ALLOWED'		=> $bbcode_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_BBCODE_FLASH'		=> $flash_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));
		
		page_header($user->lang['NOMINATE']);
		$template->set_filenames(array(
			'body' => 'medals/medalcp_nominate.html')
		);
		page_footer();
	break;
	
	case 'submit_nomination':
		if ( $user->data['user_id'] == ANONYMOUS || !$auth->acl_get('u_nominate_medals') )
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		$medal_id = request_var('medal', 0);
		if (!$medal_id)
		{
			$redirect = append_sid('medals.php?m=nominate&u=' . $user_id);
			meta_refresh(3, $redirect);
			trigger_error('NO_MEDAL_ID');
		}
		
		include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
	
		$user->add_lang('ucp');
		
		$message	= utf8_normalize_nfc(request_var('message', '', true));
		if (!strlen($message))
		{
			trigger_error(sprintf($user->lang['NO_MEDAL_MSG'], append_sid('medals.php?m=nominate&u=' . $user_id)));
		}
		$sql = "SELECT *
				FROM " . MEDALS_AWARDED_TABLE . "
				WHERE user_id = {$user_id}
				AND medal_id = {$medal_id}";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!$medals[$medal_id]['number'] > 1 && !empty($row))
		{
			trigger_error(sprintf($user->lang['CANNOT_AWARD_MULTIPLE'], append_sid('memberlist.php?mode=viewprofile&u=' . $user_id)));
		}
        if ($medals[$medal_id]['nominated'] == 0)
        {
        trigger_error(sprintf($user->lang['CANNOT_AWARD_UNNOMINATED'], append_sid('medals.php?m=nominate&u=' . $user_id)));
        }
		generate_text_for_storage($message, $uid, $bitfield, $m_flags, $allow_bbcode, $allow_urls, $allow_smilies);

		$sql_ary = array(
			'medal_id'			=> $medal_id,
			'user_id'			=> $user_id,
			'awarder_id'		=> $user->data['user_id'],
			'awarder_un'		=> $user->data['username'],
			'awarder_color'		=> $user->data['user_colour'],
			'nominated'			=> 1,
			'nominated_reason'	=> $message,
			'time'				=> time(),
			'bbuid'				=> $uid,
			'bitfield'			=> $bitfield,
		);
		
		$sql = 'INSERT INTO ' . $table_prefix . 'medals_awarded ' . $db->sql_build_array('INSERT', $sql_ary) ;
		$db->sql_query($sql);

		$redirect = append_sid('memberlist.php?mode=viewprofile&u=' . $user_id);
		meta_refresh(3, $redirect);
		trigger_error(sprintf($user->lang['MEDAL_NOMINATE_GOOD']));
	break;
		
	case 'award':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if ($user_id == 0 || $user_id == ANONYMOUS)
		{
			trigger_error('NO_USER_ID');
		}
		$sql = "SELECT *
				FROM " . MEDALS_AWARDED_TABLE . "
				WHERE user_id = {$user_id}
				ORDER BY medal_id AND nominated";
		$result = $db->sql_query($sql);
		$my_medals = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if ( isset($my_medals[$row['medal_id']]['count']) )
			{
				$row['count'] = $my_medals[$row['medal_id']]['count'] + '1';
			}
			else
			{
				$row['count'] = '1';
			}
			$my_medals[$row['medal_id']] = $row;
		}
		$db->sql_freeresult($result);
		
		$sql = "SELECT user_id, username, user_colour
			FROM " . USERS_TABLE . "
			WHERE user_id = {$user_id}";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']);
		
		$medals_options = '<option value=""></option>';
		$temp_string = '';
		$no_medals = true ;
		foreach ($cats as $key => $value)
		{
			$at_least_one = false;
			foreach ($medals as $key2 => $value2)
			{
				if ($value2['parent'] == $value['id'])
				{
					$can_award = false;
					$my_medals[$value2['id']]['count'] = isset($my_medals[$value2['id']]['count']) ? $my_medals[$value2['id']]['count'] : 0;
					if ( $my_medals[$value2['id']]['count'] < $value2['number'] || $medal_id == $value2['id'] )
					{
						$my_medals[$value2['id']]['nominated'] = isset($my_medals[$value2['id']]['nominated']) ? $my_medals[$value2['id']]['nominated'] : 0;
						if (isset($my_medals[$value2['id']]) && $my_medals[$value2['id']]['nominated'] == 1)
						{
							$value2['name'] .= ' ' . sprintf($user->lang['NOMINATED_BY'], $my_medals[$value2['id']]['awarder_un']);
						}
						else if ( $value2['nominated'] )
						{
							$value2['name'] .= ' ' . $user->lang['NOMINATABLE'];
						}
						if ( $medal_id == $value2['id'] )
						{
							$temp_string .= '<option value="' . $value2['id'] . '" selected="selected">&bull;&nbsp;' . $value2['name'] . '</option>';
							$sql = "SELECT *
									FROM " . MEDALS_AWARDED_TABLE . "
									WHERE id={$med_id}" ;
							$result = $db->sql_query($sql);
							$row = $db->sql_fetchrow($result);
							$db->sql_freeresult($result);
							$message = generate_text_for_edit($row['nominated_reason'],$row['bbuid'],$m_flags);
							$medal_edit = "&med=$med_id" ;
						}
						else
						{
							$temp_string .= '<option value="' . $value2['id'] . '">&bull;&nbsp;' . $value2['name'] . '</option>';
						}
						$at_least_one = true;
					}
				}
			}
			if ($at_least_one)
			{
				$medals_options .= '<option value="">' . $value['name'] . '</option>';
				$medals_options .= $temp_string;
				$at_least_one = false;
				$temp_string = '';
				$no_medals = false ;
			}
		}
	
		$medals_arr       = 'var medals = new Array();';
		$medals_desc_arr  = 'var medals_desc = new Array();' ;
		foreach ($medals as $key => $value)
		{
			$medals_arr .= 'medals[' . $value['id'] . '] = "' . $value['image'] . '";';
			$medals_desc_arr .= 'medals_desc[' . $value['id'] . '] = "' . $value['description'] . '";';
		}
		$medals_arr .= "\n" . $medals_desc_arr . "\n" ;
		
		if ($no_medals)
		{
			$medals_options = '<option value="">' . $user->lang['NO_MEDALS'] . '</option>';
		}

		$bbcode_status	= ($config['allow_bbcode']) ? true : false;
		$smilies_status	= ($bbcode_status && $config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($bbcode_status && $config['allow_post_links']) ? true : false;
		$flash_status	= ($bbcode_status) ? true : false;
		$quote_status	= ($bbcode_status) ? true : false;
		display_custom_bbcodes();

		$message = isset($message['text']) ? $message['text'] : '';
		$template->assign_vars(array(
			'USERNAME'				=> $username,
			'MEDALS'				=> $medals_options,
			'JS'					=> $medals_arr,

			'U_MEDALS_ACTION'		=> isset($medal_edit) ? append_sid('/medals.php?m=submit&u=' . $user_id . $medal_edit) : append_sid('medals.php?m=submit&u=' . $user_id),
			'MESSAGE'				=> $message,

			'S_BBCODE_ALLOWED'		=> $bbcode_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_BBCODE_FLASH'		=> $flash_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));
		
		page_header($user->lang['AWARD_MEDAL']);
		$template->set_filenames(array(
			'body' => 'medals/medalcp_award_user.html')
		);
		page_footer();
	break;
		
	case 'awarded':
		$sql = "SELECT user_id, username, user_colour
			FROM " . USERS_TABLE . "
			WHERE user_id = {$user_id}";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']);
			
		$sql3 = "SELECT *
				FROM " . MEDALS_AWARDED_TABLE . "
				WHERE user_id = {$user_id}
					AND nominated <> 1" ;
		$result3 = $db->sql_query($sql3);
		$s_medals = false;
		$users_medals = array();
		while ($row3 = $db->sql_fetchrow($result3))
		{
			$awarder_name = get_username_string('full', $row3['awarder_id'], $row3['awarder_un'], $row3['awarder_color'], $row3['awarder_un']) ;
			$nom_message = sprintf($user->lang['NOMINATE_MESSAGE'], $awarder_name, $medals[$row3['medal_id']]['name']);

			// Parse the message and subject
			$reason = generate_text_for_display($row3['nominated_reason'], $row3['bbuid'], $row3['bitfield'], $m_flags);
			$message = $user->lang['AWARDED_BY'] . ' ' . $awarder_name . ' ' . $user->format_date($row3['time']) . '<br \>' . $reason ;

			$this_cat = $cats[$medals[$row3['medal_id']]['parent']];
			$users_medals[$this_cat['order_id']]['name'] = $this_cat['name'];
			$users_medals[$this_cat['order_id']][$medals[$row3['medal_id']]['order_id']][] = array(
				'MEDAL_NAME'		=> $medals[$row3['medal_id']]['name'],
				'MEDAL_IMAGE'		=> '<img src="' . $phpbb_root_path . $medals[$row3['medal_id']]['image'] . '" title="' . $medals[$row3['medal_id']]['name'] . '" alt="' . $medals[$row3['medal_id']]['name'] . '" />',
				'MEDAL_REASON'		=> $message,
				'ID'				=> $row3['id'],
			);
			$s_medals = true;
		}
		$db->sql_freeresult($result3);
		
		$my_medals_arr = array();
		ksort($users_medals);
		foreach ($users_medals as $key => $value)
		{
			ksort($value);
			foreach ($value as $key2 => $value2)
			{
				if ($key2 != 'name')
				{
					foreach ($value2 as $key3 => $value3)
					{
						$my_medals_arr[] = array($value3, false);
					}
				}
				else
				{
					$my_medals_arr[] = array($value2, true);
				}
			}
		}

		foreach ($my_medals_arr as $key => $value)
		{
			if ($value[1])
			{
				$template->assign_block_vars('medals', array(
					'MEDAL_NAME'		=> $value[0],
					'IS_CAT'			=> true,
				));
			}
			else
			{
				$template->assign_block_vars('medals', array(
					'MEDAL_NAME'		=> $value[0]['MEDAL_NAME'],
					'MEDAL_IMAGE'		=> $value[0]['MEDAL_IMAGE'],
					'MEDAL_REASON'		=> $value[0]['MEDAL_REASON'],
					'U_DELETE'			=> append_sid('medals.php?m=delete&u=' . $user_id . '&med=' . $value[0]['ID']),

					'IS_CAT'			=> false,
				));
			}
		}
			
		$template->assign_vars(array(
			'USERNAME'				=> $username,
			
			'U_MEDALS_ACTION'		=> append_sid('/medals.php?m=submit&u=' . $user_id),
		));

		page_header($user->lang['AWARDED_MEDAL_TO']);
		$template->set_filenames(array(
			'body' => 'medals/medalcp_awarded_user.html')
		);
		page_footer();
	break;

	case 'submit':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if (!$medal_id)
		{
			$redirect = append_sid('medals.php?m=award&u=' . $user_id);
			meta_refresh(3, $redirect);
			trigger_error('NO_MEDAL_ID');
		}
		
		include_once($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
	
		$message	= utf8_normalize_nfc(request_var('message', '', true));
		if (!strlen($message))
		{
			trigger_error(sprintf($user->lang['NO_MEDAL_MSG'], append_sid('medals.php?m=award&u=' . $user_id)));
		}

		$username = array();
		if ( sizeof($user_id) > 1 )
		{
			foreach ($uid as $user_id)
			{
				// Change usernames to ids
				$sql = "SELECT user_id
						FROM " . USERS_TABLE . "
						WHERE username = {$uid}" ;
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				$username[] = $row['user_id'] ;
			}
		}
		else
		{
			$username[] = $user_id ;
		}
		
		foreach ($username as $user_id)
		{
			$sql = "SELECT count(*) as count
					FROM " . MEDALS_AWARDED_TABLE . "
					WHERE medal_id = {$medal_id}
					  AND user_id = {$user_id}
					  AND nominated = 0" ;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if ( $row['count'] >= $medals[$medal_id]['number'] )
			{
				trigger_error(sprintf($user->lang['CANNOT_AWARD_MULTIPLE'], append_sid('memberlist.php?mode=viewprofile&u=' . $user_id)));
			}
			
			// Call award_medal function
			if (isset($med_id))
			{
				award_medal($medal_id, $user_id, $message, time(), $medals[$medal_id]['points'], $med_id) ;
			}
			else
			{
				award_medal($medal_id, $user_id, $message, time(), $medals[$medal_id]['points']) ;
			}
		}
		$redirect = append_sid('memberlist.php?mode=viewprofile&u=' . $user_id);
		meta_refresh(3, $redirect);
		trigger_error(sprintf($user->lang['MEDAL_AWARD_GOOD']));
	break;

	case 'delete':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if (!$med_id)
		{
			trigger_error('NO_MEDAL_ID');
		}
		if (confirm_box(true))
		{
			if ($config['points_enable'] == 1)
			{
				$sql = "SELECT points
					FROM " . MEDALS_AWARDED_TABLE . "
					WHERE id = {$med_id}
					LIMIT 1";
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$sql = "UPDATE " . USERS_TABLE . "
					SET medal_user_points = user_points - " . $row['points'] . "
					WHERE user_id = $user_id" ;
				$db->sql_query($sql);
			}

			$sql = "DELETE FROM " . MEDALS_AWARDED_TABLE . "
				WHERE id = {$med_id}
				LIMIT 1";
			$db->sql_query($sql);
			$redirect = append_sid('medals.php?m=awarded&u=' . $user_id);
			meta_refresh(3, $redirect);
			trigger_error(sprintf($user->lang['MEDAL_REMOVE_GOOD']));
		}
		else
		{
			confirm_box(false, $user->lang['MEDAL_REMOVE_CONFIRM'], build_hidden_fields(array(
				'action'   => 'delete',
			)));
			$redirect = append_sid('medals.php?m=awarded&u=' . $user_id);
			meta_refresh(1, $redirect);
			trigger_error(sprintf($user->lang['MEDAL_REMOVE_NO']));
		}
	break;
		
	case 'approve':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if (!$med_id )
		{
			trigger_error('NO_MEDAL_ID');
		}

		$sql = "SELECT count(*) as count
				FROM " . MEDALS_AWARDED_TABLE . "
				WHERE medal_id = {$medal_id}
				  AND user_id = {$user_id}
				  AND nominated = 0" ;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ( $row['count'] >= $medals[$medal_id]['number'] )
		{
			$redirect = append_sid('memberlist.php?mode=viewprofile&u=' . $user_id);
			meta_refresh(3, $redirect);
			trigger_error(sprintf($user->lang['CANNOT_AWARD_MULTIPLE']));
		}

		$sql = "SELECT *
				FROM " . MEDALS_AWARDED_TABLE . "
				WHERE id = {$med_id}" ;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		$message = generate_text_for_edit($row['nominated_reason'],$row['bbuid'],$m_flags);
		award_medal($row['medal_id'], $row['user_id'], $message['text'], $row['time'], $medals[$medal_id]['points'], $row['id']) ;

		$redirect = append_sid('medals.php?m=validate&u=' . $user_id);
		meta_refresh(3, $redirect);
		trigger_error(sprintf($user->lang['MEDAL_AWARD_GOOD']));
	break;

	case 'validate':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		$sql = 'SELECT user_id, username, user_colour
				FROM ' . USERS_TABLE . "
				WHERE user_id = {$user_id}";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
		$username = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']);
		
		$sql = "SELECT ma.*, m.name
				FROM " . MEDALS_AWARDED_TABLE . " as ma, " . MEDALS_TABLE . " as m
				WHERE ma.user_id = {$user_id}
				  AND ma.medal_id = m.id
				  AND ma.nominated <> 0";
		$result = $db->sql_query($sql);
		$i = 0;
		while ($row = $db->sql_fetchrow($result))
		{
			$awarder_name = get_username_string('full', $row['awarder_id'], $row['awarder_un'], $row['awarder_color'], $row['awarder_un']) ;
			$nom_message = sprintf($user->lang['NOMINATE_MESSAGE'], $awarder_name, $row['name']);

			// Parse the message and subject
			$message = generate_text_for_display($row['nominated_reason'], $row['bbuid'], $row['bitfield'], $m_flags);
			$message = $nom_message . $message ;
			$message = censor_text($message);
		
			$message = str_replace("\n", '<br />', $message);

			$uid = $row['bbuid'];
			$bitfield = $row['bitfield'];

			$template->assign_block_vars('nominations', array(
				'USERNAME'	=> $awarder_name,
				'REASON'	=> $message,
				'U_DELETE'	=> append_sid("medals.php?m=delete&med={$row['id']}&u={$user_id}"),
				'U_APPROVE'	=> append_sid("medals.php?m=approve&med={$row['id']}&mid={$row['medal_id']}&u={$user_id}"),
				'U_MEDAL_EDIT'	=> append_sid("medals.php?m=award&med={$row['id']}&mid={$row['medal_id']}&u={$user_id}"),
			));
			$i++;
		}
		$db->sql_freeresult($result);
		
		$template->assign_vars(array(
			'U_MEDALS_ACTION'		=> append_sid('/medals.php?m=submit&u=' . $user_id),
			'NOMINATE_MEDAL'		=> sprintf($user->lang['NOMINATE_USER_LOG'] , $username),
			'S_ROW_COUNT'			=> $i,
		));
		
		page_header($user->lang['NOMINATE_MEDAL']);
		$template->set_filenames(array(
			'body' => 'medals/medalcp_nominate_user.html')
		);
		page_footer();
		
	break;

	case 'mnd':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if (!$med_id)
		{
			trigger_error('NO_MEDAL_ID');
		}
		
		$sql = "DELETE FROM " . MEDALS_AWARDED_TABLE . "
				WHERE medal_id = {$med_id}
					AND nominated = 1";
		$db->sql_query($sql);
		trigger_error(sprintf($user->lang['NOMINATIONS_REMOVE_GOOD'], append_sid('medals.php')));
	// No break;

	case 'mn':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}

		$sql = "SELECT u.username, u.user_colour, ma.*
				FROM " . USERS_TABLE . " u, " . MEDALS_AWARDED_TABLE . " ma
				WHERE u.user_id = ma.user_id
					AND ma.nominated = 1
					AND ma.medal_id = {$med_id}
				ORDER BY u.username_clean";
		$result = $db->sql_query($sql);
		$users_medals = array();
		$i = 1;
		while ($row = $db->sql_fetchrow($result))
		{
			$awarder_name = get_username_string('full', $row['awarder_id'], $row['awarder_un'], $row['awarder_color'], $row['awarder_un']) ;
			$users_medals[$i] = array(
				'id'	 		=> $row['id'],
				'username' 		=> $row['username'],
				'user_colour' 	=> $row['user_colour'],
				'user_id'		=> $row['user_id'],
				'reason'		=> $user->lang['MEDAL_NOM_BY'] . ' : ' . $awarder_name . '<br />' . $row['nominated_reason'],
				'bbuid'			=> $row['bbuid'],
				'bitfield'		=> $row['bitfield'],
			);
			$i++;
		}
		$db->sql_freeresult($result);

		foreach ( $users_medals as $key => $value )
		{
			$awarded = get_username_string('full', $value['user_id'], $value['username'], $value['user_colour']) ;

			$template->assign_block_vars('nominatedrow', array(
					'NOMINATED'			=> $awarded,
					'REASON'			=> generate_text_for_display($value['reason'], $value['bbuid'], $value['bitfield'], $m_flags),
					'U_MCP'				=> "?m=approve&med={$value['id']}&mid={$med_id}&u={$value['user_id']}",
					'U_USER_DELETE'		=> "?m=delete&med={$value['id']}&u={$value['user_id']}",
			));
			
			$nominated_users[$value['user_id']]['user'] = $awarded;
			$nominated_users[$value['user_id']]['count'] = isset($nominated_users[$value['user_id']]['count']) ? $nominated_users[$value['user_id']]['count'] + '1' : 1;
		}

		if ( isset($nominated_users) )
		{
			$i = 0;
			$nom_users = '';
			foreach ( $nominated_users as $key => $value )
			{
				if ( $i > 0 )
				{
					$nom_users .= ", ";
				}
				$nom_users .= "{$value['user']} ({$value['count']})";
				$i++;
			}
		}

		$template->assign_vars(array(
				'S_MEDAL_NOM'		=> true,
				'MEDAL_NAME'		=> $medals[$med_id]['name'],
				'MEDAL_DESC'		=> $medals[$med_id]['description'],
				'MEDAL_IMG'			=> '<img src="' . $medals[$med_id]['image'] . '">',
				'MEDAL_IMG_URL'		=> $medals[$med_id]['image'],
				'MEDAL_AWARDED'		=> isset($awarded_users) ? $awarded_users : $user->lang['NO_MEDALS_ISSUED'],
				'NOMINATED_USERS'	=> isset($nom_users) ? $nom_users : $user->lang['NO_MEDALS_NOMINATED'],
				'S_DELETE_ALL'		=> isset($nom_users) ? true : false,
				'U_MEDALS_ACTION'	=> "/medals.php?m={$mode}d&med=$med_id",
				'U_FIND_USERNAME'	=> append_sid($phpbb_root_path . 'memberlist.' . $phpEx, 'mode=searchuser&amp;form=post&amp;field=add'),
		));

		page_header($user->lang['MEDALS_VIEW']);
		$template->set_filenames(array(
			'body' => 'medals/medals.html')
		);
		make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
		page_footer();

	break;

	case 'ma':
		if ($user->data['user_type'] != USER_FOUNDER && !$auth->acl_get('u_award_medals'))
		{
			trigger_error($user->lang['NO_GOOD_PERMS']);
		}
		if ( $submit )
		{
			if (!$med_id)
			{
				trigger_error('NO_MEDAL_ID');
			}
			
			$message	= utf8_normalize_nfc(request_var('message', '', true));
			if (!strlen($message))
			{
				trigger_error(sprintf($user->lang['NO_MEDAL_MSG'], append_sid('medals.php?mode=' . $mode . '&med=' . $med_id)));
			}

			$usernames = explode("\n", $usernames) ;
			foreach ( $usernames as $value )
			{
				$username[] = $db->sql_escape(utf8_clean_string($value));
			}
			
			$award_user = $not_award_user = $awarded_user = $no_such_user = array() ;

			// Change usernames to ids
			$sql = 'SELECT user_id, username, username_clean
					FROM ' . USERS_TABLE . '
					WHERE ' . $db->sql_in_set('username_clean', $username) ;
			$result = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($result))
			{
				$sql = "SELECT count(*) as number
						FROM " . MEDALS_AWARDED_TABLE . "
						WHERE medal_id = {$med_id}
							AND user_id = {$row['user_id']}" ;
				$result2 = $db->sql_query($sql);
				$row2 = $db->sql_fetchrow($result2);
				$db->sql_freeresult($result2);

				if ( $row2['number'] < $medals[$med_id]['number'] )
				{
					$award_user[] = $row['user_id'] ;
					$awarded_user[] = $row['username_clean'] ;
				}
			}
			$db->sql_freeresult($result);
			$not_award_user = array_diff($username, $awarded_user);
			// Call award_medal function
			$time = time() ;
      
      /*echo json_encode(array(
        'award_user' => $award_user,
        'not_award_user' => $not_award_user
      ));
      exit();*/

			if ( sizeof($award_user) )
			{
				foreach ( $award_user as $uid )
				{
					award_medal($med_id, $uid, $message, $time, $medals[$med_id]['points']) ;
				}
			}
			if ( sizeof($not_award_user) )
			{
				$redirect = append_sid('medals.php?mode=' . $mode . '&med=' . $med_id);
				meta_refresh(3, $redirect);
				trigger_error(sprintf($user->lang['NO_USER_SELECTED'], implode(", ", $not_award_user)));
			}
			else
			{
				$redirect = append_sid('medals.php?mode=' . $mode . '&med=' . $med_id);
				meta_refresh(3, $redirect);
				trigger_error($user->lang['MEDAL_AWARD_GOOD']);
			}
		}

		$sql = "SELECT u.username, u.user_colour, ma.user_id
				FROM " . USERS_TABLE . " u, " . MEDALS_AWARDED_TABLE . " ma
				WHERE u.user_id = ma.user_id
					AND ma.nominated = 0
					AND ma.medal_id = {$med_id}
				GROUP BY ma.user_id, u.username, ma.medal_id
				ORDER BY u.username";
		$result = $db->sql_query($sql);
		$users_medals = array();
		$i = 1;
		while ($row = $db->sql_fetchrow($result))
		{
			$users_medals[$i] = array(
				'username' 		=> $row['username'],
				'user_colour' 	=> $row['user_colour'],
				'user_id'		=> $row['user_id'],
			);
			$i++;
		}
		$db->sql_freeresult($result);

		foreach ( $users_medals as $key => $value )
		{
			$awarded = get_username_string('full', $value['user_id'], $value['username'], $value['user_colour']) ;
			$awarded_users = isset($awarded_users) ? $awarded_users . ', ' . $awarded : $awarded ;
		}
		$template->assign_vars(array(
				'S_MEDAL_AWARD'		=> true,
				'MEDAL_NAME'		=> $medals[$med_id]['name'],
				'MEDAL_DESC'		=> $medals[$med_id]['description'],
				'MEDAL_IMG'			=> '<img src="' . $medals[$med_id]['image'] . '">',
				'MEDAL_IMG_URL'			=> $medals[$med_id]['image'],
				'MEDAL_AWARDED'		=> isset($awarded_users) ? $awarded_users : $user->lang['NO_MEDALS_ISSUED'],
				'U_MEDALS_ACTION'	=> "/medals.php?m=$mode&med=$med_id",
				'U_FIND_USERNAME'	=> append_sid($phpbb_root_path . 'memberlist.' . $phpEx, 'mode=searchuser&amp;form=post&amp;field=add'),
		));
		
		page_header($user->lang['MEDALS_VIEW']);
		$template->set_filenames(array(
			'body' => 'medals/medals.html')
		);
		make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
		page_footer();

	break;
	
	case 'image':
		$sql = 'SELECT id, name, slug, description, image FROM gateway_medals WHERE slug = "'.$db->sql_escape(request_var('medal', '')).'"';
		$lookupResult = $db->sql_query($sql);
		$medal = $db->sql_fetchrow($lookupResult);
		
		header('Location: /images/medals/'.$medal['image']);
		exit();
	break;
	
	case 'view':
		$sql = 'SELECT id, name, slug, description, points, image FROM gateway_medals WHERE slug = "'.$db->sql_escape(request_var('medal', '')).'"';
		$lookupResult = $db->sql_query($sql);
		$medal = $db->sql_fetchrow($lookupResult);
		
		$med_id = (int) $medal['id'];
		
		$sql = "SELECT u.user_id, u.username, u.user_colour, ma.user_id, ma.nominated_reason as reason, ma.time
				FROM " . USERS_TABLE . " u, " . MEDALS_AWARDED_TABLE . " ma
				WHERE u.user_id = ma.user_id
					AND ma.nominated = 0
					AND ma.medal_id = {$med_id}
				GROUP BY ma.user_id, u.username, ma.medal_id
				ORDER BY ma.time DESC";
		$result = $db->sql_query($sql);
		$users_medals = array();
		$recently = array();
		$i = 1;
		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['user_id'] == $user->data['user_id']) {
				$moment = $row;
			}
			
			$users_medals[$i] = array(
				'username' 		=> $row['username'],
				'user_colour' 	=> $row['user_colour'],
				'user_id'		=> $row['user_id'],
			);
			$i++;
			
			$recently[] = $row;
		}
		$db->sql_freeresult($result);

		$recently = array_slice($recently, 0, 10000);
		foreach ($recently as $key => $row) {
			$template->assign_block_vars('awards', array(
				'USERLINK' 		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $row['username']),
				'TIME' => $row['time'],
				'TIMESTAMP' => $user->format_date($row['time']),
				'REASON' => $row['reason'],
			));
		}

		$awarded_users = array();
		foreach ( $users_medals as $key => $value )
		{
			$awarded = get_username_string('full', $value['user_id'], $value['username'], $value['user_colour']) ;
			$awarded_users[] = $awarded;
		}

		if (!count($awarded_users)) {
			$awardedTo = $user->lang['NO_MEDALS_ISSUED'];
		} else if (count($awarded_users) <= 50) {
			$awardedTo = implode_oxford($awarded_users);
		} else {
			$awardedTo = count($awarded_users);
		}
		
		$template->assign_vars(array(
				'S_PAGE_ONLY'		=> true,
				'S_MEDAL_AWARD'	=> true,
				'MEDAL_NAME'		=> $medal['name'],
				'MEDAL_SLUG'		=> $medal['slug'],
				'MEDAL_DESC'		=> $medal['description'],
				'MEDAL_DESCRIPTION'	=> $medal['description'],
				'MEDAL_IMG'			=> '<img src="/medals/' . $medal['slug'] . '/image">',
				'MEDAL_IMG_URL'		=> '/medals/' . $medal['slug'] . '/image',
				'MEDAL_AWARDED'		=> $awardedTo,
				'MEDAL_VALUE'		=> number_format($medal['points']),
				'AWARDED_PERCENT' => count($awarded_users) ? (float) number_format((count($awarded_users) / $stats['total']) * 100, 3) : 0,
				'U_MEDALS_ACTION'	=> "/medals.php?m=$mode&med=$med_id",
				'U_FIND_USERNAME'	=> append_sid($phpbb_root_path . 'memberlist.' . $phpEx, 'mode=searchuser&amp;form=post&amp;field=add'),
				
				'S_HAS_MOMENT'    => (!empty($moment)) ? true : false,
				
				'MOMENT_USERLINK' 		=> get_username_string('full', $moment['user_id'], $moment['username'], $moment['user_colour'], $moment['username']),
				'MOMENT_TIME' => $moment['time'],
				'MOMENT_TIMESTAMP' => $user->format_date($moment['time']),
				'MOMENT_TIMEAGO' => $user->format_date($moment['time']),
				'MOMENT_REASON' => $moment['reason'],
		));
	
		page_header($medal['name'].', a medal on RPG: read, write, roleplay.');
		$template->set_filenames(array(
			'body' => 'medals/medals.html')
		);
		make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
		page_footer();
	break;

	default:
    $awardMap = array();
    $allMedals = array();
    
		$sql = "SELECT u.username, u.user_colour, ma.id, ma.user_id, ma.medal_id, ma.nominated, ma.time
				FROM " . USERS_TABLE . " u, " . MEDALS_AWARDED_TABLE . " ma
				WHERE u.user_id = ma.user_id
				GROUP BY ma.nominated, ma.user_id, u.username, ma.medal_id
				ORDER BY ma.time ASC";
		$result = $db->sql_query($sql, 3600);

		$users_medals = array();
		$i = 1;
		while ($row = $db->sql_fetchrow($result))
		{
			$users_medals[$i] = array(
				'username' 		=> $row['username'],
				'user_colour' 	=> $row['user_colour'],
				'medal_id' 		=> $row['medal_id'],
				'user_id'		=> $row['user_id'],
				'nominated'		=> $row['nominated'],
				'time'		=> $row['time'],
			);
			$i++;
		}
		$db->sql_freeresult($result);

		$at_least_one_awarded = false;
		foreach ($cats as $key => $value)
		{
			$at_least_one = true;

			foreach ($medals as $key2 => $value2)
			{
				if ($value2['parent'] == $value['id'])
				{
					if ($at_least_one)
					{
						$at_least_one_awarded = true;
						$template->assign_block_vars('medalrow', array(
								'IS_CAT'		=> 1,
								'MEDAL_CAT'		=> $value['name'],
						));
						$at_least_one = false;
					}
					
					$awarded_users = array();
					$nominations = 0 ;
					foreach ($users_medals as $key3 => $value3)
					{
						if ($value3['medal_id'] == $value2['id'] && $value3['nominated'] == 0)
						{
								$awarded = get_username_string('full', $value3['user_id'], $value3['username'], $value3['user_colour']) ;
								$awarded_users[] = $awarded;
						}
						elseif ($value3['medal_id'] == $value2['id'] && $value3['nominated'] == 1)
						{
							$nominations++ ;
						}
					}
          
          $sql = 'SELECT time FROM gateway_medals_awarded WHERE medal_id = '.(int) $value2['id'].' ORDER BY time DESC LIMIT 1';
          $mostRecentResult = $db->sql_query($sql, 3600);
          $mostRecent = $db->sql_fetchrow($mostRecentResult);
          $db->sql_freeresult($mostRecentResult);
          
					if (!count($awarded_users)) {
						$awardedTo = $user->lang['NO_MEDALS_ISSUED'];
					} else if (count($awarded_users) <= 10) {
						$awardedTo = implode_oxford($awarded_users);
					} else {
						$awardedTo = count($awarded_users);
					}
					
					$template->assign_block_vars('medalrow', array(
							'MEDAL_NAME'			=> $value2['name'],
							'MEDAL_SLUG'			=> $value2['slug'],
							'U_MEDAL_AWARD_PANEL'	=> 'medals.php?m=ma&med=' . $value2['id'],
							'MEDAL_IMG'				=> '<img src="' . $value2['image'] . '">',
							'MEDAL_IMG_URL'		=> $value2['image'],
							'MEDAL_DESC'			=> $value2['description'],
							'MEDAL_AWARDED'			=> $awardedTo,
							'AWARDED_PERCENT'		=> count($awarded_users) ? (float) number_format((count($awarded_users) / $stats['total']) * 100, 3) : 0,
							'NOMINATIONS'			=> ($nominations > 0) ? true : false,
							'U_MEDAL_NCP'			=> 'medals.php?m=mn&med=' . $value2['id'],
							'MEDAL_DESC'			=> $value2['description'],
							'MEDAL_VALUE'			=> money_format('%i', $value2['points']),
              'MOST_RECENT'     => timeAgo($mostRecent['time']),
              'MOST_RECENT_RAW'     => (empty($mostRecent['time'])) ? 0 : $mostRecent['time'],
					));
          
          $mostRecent = null;
				}
			}
		}

		$template->assign_vars(array(
				'S_MEDAL_VIEW'		=> true,
				'NO_MEDAL'			=> $at_least_one_awarded ? 0 : 1,
		));

		page_header($user->lang['MEDALS_VIEW'] . ' &amp; Achievements &middot; RPG: read, write, roleplay.');
		$template->set_filenames(array(
			'body' => 'medals/medals.html')
		);
		make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
		page_footer();
		
	break;
}

function getfirstcat()
{
	global $db;
	$sql = "SELECT *
	FROM " . MEDALS_CATS_TABLE . "
	ORDER BY order_id ASC";
	$result = $db->sql_query_limit($sql, 1, 0);
	while ($row = $db->sql_fetchrow($result))
	{
		$cat = $row['id'];
	}
	return $cat;
}

?>
