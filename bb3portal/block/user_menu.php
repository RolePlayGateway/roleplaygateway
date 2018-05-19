<?php
/*
*
* @name user_menu.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: user_menu.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

//
// [+] view new posts number since last visit
//
if ($user->data['is_registered'])
{
    $sql = 'SELECT COUNT(post_id) as total
        FROM ' . POSTS_TABLE . '
        WHERE post_time >= ' . $user->data['session_last_visit'];
    $result = $db->sql_query($sql);
    $new_posts_count = $db->sql_fetchfield('total');
    $db->sql_freeresult($result);
}
//
// [-] view new posts number since last visit
//

//
// [+] Avatar on portal
//
$username = $user->data['username'];
$avatar_img = '';
$user_colour = 'style="color:#000000"';

$sql = 'SELECT 
		user_id,
		/*user_rank,
		user_posts,	*/
		user_colour, 
		user_avatar,
		user_avatar_type, 
		user_avatar_width, 
		user_avatar_height
	FROM 
		' . USERS_TABLE . "
	WHERE 
		user_id = " . $user->data['user_id'];

		$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result))
{		
/*
$rank_title = $rank_img = '';
get_user_rank($row['user_rank'], $row['user_posts'], $rank_title, $rank_img, $rank_img_src);
*/
	if ($row['user_avatar'] && $user->optionget('viewavatars'))
	{
		$avatar_img = '';
		
		switch ($row['user_avatar_type'])
		{
			case AVATAR_UPLOAD:
				//$avatar_img = $config['avatar_path'] . '/';
				$avatar_img = "download.php?avatar=".$row['user_id'].".png";
			break;

			case AVATAR_GALLERY:
				$avatar_img = $config['avatar_gallery_path'] . '/';
				$avatar_img .= $row['user_avatar'];
			break;
		}
		

		$avatar_img = '<img src="' . $avatar_img . '" width="' . $row['user_avatar_width'] . '" height="' . $row['user_avatar_height'] . '" alt="' . $username . '" title="' . $username . '" />';
		$user_colour = ($row['user_colour']) ? ' style="color:#' . $row['user_colour'] .'"' : '';			
	}
}
$db->sql_freeresult($result);
//
// [-] Avatar on portal
//



// 
// [+] user rank
//

/* not use now
$admin_id_ary = $mod_id_ary = '';

		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> 'u.user_id, u.group_id as default_group, u.username, u.user_colour, u.user_rank, u.user_posts, g.group_id, g.group_name, g.group_colour, g.group_type, ug.user_id as ug_user_id',

			'FROM'		=> array(
				USERS_TABLE		=> 'u',
				GROUPS_TABLE	=> 'g'
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USER_GROUP_TABLE => 'ug'),
					'ON'	=> 'ug.group_id = g.group_id AND ug.user_pending = 0 AND ug.user_id = ' . $user->data['user_id']
				)
			),

			'WHERE'		=> $db->sql_in_set('u.user_id', array_unique(array_merge($admin_id_ary, $mod_id_ary))) . '
				AND u.group_id = g.group_id',

			'ORDER_BY'	=> 'g.group_name ASC, u.username ASC'
		));


	$sql = 'SELECT 
		user_rank,
		user_posts
	FROM 
		' . USERS_TABLE . "
	WHERE 
		user_id = " . $user->data['user_id'];
		
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$rank_title = $rank_img = '';
			get_user_rank($row['user_rank'], $row['user_posts'], $rank_title, $rank_img, $rank_img_src);

			$template->assign_block_vars(array(
				'RANK_TITLE'	=> $rank_title,

				'RANK_IMG'		=> $rank_img,
				'RANK_IMG_SRC'	=> $rank_img_src,
				)
			);
		}
		$db->sql_freeresult($result);
*/



/**
* Get user rank title and image
*/
/*
function get_user_rank($user_rank, $user_posts, &$rank_title, &$rank_img, &$rank_img_src)
{
	global $ranks, $config;

	if (!empty($user_rank))
	{
		$rank_title = (isset($ranks['special'][$user_rank]['rank_title'])) ? $ranks['special'][$user_rank]['rank_title'] : '';
		$rank_img = (!empty($ranks['special'][$user_rank]['rank_image'])) ? '<img src="' . $config['ranks_path'] . '/' . $ranks['special'][$user_rank]['rank_image'] . '" alt="' . $ranks['special'][$user_rank]['rank_title'] . '" title="' . $ranks['special'][$user_rank]['rank_title'] . '" />' : '';
		$rank_img_src = (!empty($ranks['special'][$user_rank]['rank_image'])) ? $config['ranks_path'] . '/' . $ranks['special'][$user_rank]['rank_image'] : '';
	}
	else
	{
		if (isset($ranks['normal']))
		{
			foreach ($ranks['normal'] as $rank)
			{
				if ($user_posts >= $rank['rank_min'])
				{
					$rank_title = $rank['rank_title'];
					$rank_img = (!empty($rank['rank_image'])) ? '<img src="' . $config['ranks_path'] . '/' . $rank['rank_image'] . '" alt="' . $rank['rank_title'] . '" title="' . $rank['rank_title'] . '" />' : '';
					$rank_img_src = (!empty($rank['rank_image'])) ? $config['ranks_path'] . '/' . $rank['rank_image'] : '';
					break;
				}
			}
		}
	}
}
*/
// 
// [-] user rank
//


// Assign specific vars
$template->assign_vars(array(
// [+] view new posts number since last visit
	'L_SEARCH_NEW'	=> $user->lang['SEARCH_NEW'] . '&nbsp;(' . $new_posts_count . ')',
// [-] view new posts number since last visit

// [+] Avatar on portal
	'USER_AVATAR'	=> $avatar_img,
// [-] Avatar on portal	
	'USERNAME' 		=> $username,
	'USERNAME_COLOR'=> $user_colour,
	
	/* not use now
	'RANK_TITLE'	=> $rank_title,
	'RANK_IMG'		=> $rank_img,
	'RANK_IMG_SRC'	=> $rank_img_src,
	*/

	)
);

?>