<?php
/**
*
* @package phpBB3
* @version $Id: index.php,v 1.176 2007/10/05 14:30:06 acydburn Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/

error_reporting(0);

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');
// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> Zero dupe
$phpbb_seo->page_url = $phpbb_seo->seo_path['phpbb_urlR'] . $phpbb_seo->seo_static['index'] . $phpbb_seo->seo_ext['index'] . (!empty($_SID) ? '?sid=' . $_SID : '');
if ( $user->data['is_registered'] ) {
	$phpbb_seo->seo_cond( !isset($_GET['explain']) );
	$phpbb_seo->seo_cond( (utf8_strpos($phpbb_seo->seo_path['uri'], 'mark=') === FALSE));
} 
if ( !$phpbb_seo->seo_opt['zero_dupe']['strict'] ) { // strict mode is here a bit faster
	if ( !$user->data['is_registered'] ) {
		$phpbb_seo->seo_cond( isset($_GET['explain']), false, 'do' );
		$phpbb_seo->seo_cond( (utf8_strpos($phpbb_seo->seo_path['uri'], 'mark=') !== FALSE), false, 'do');
	}
	if ( !empty($phpbb_seo->seo_static['index']) ) {
		$phpbb_seo->seo_cond( (utf8_strpos($phpbb_seo->seo_path['uri'], $phpbb_seo->seo_static['index']) === FALSE), false, 'do');
	} else {
		$phpbb_seo->seo_cond( (utf8_strpos($phpbb_seo->seo_path['uri'], 'index.' . $phpEx) !== FALSE), false, 'do' );
	}
}
$phpbb_seo->seo_chk_dupe($phpbb_seo->seo_path['uri'], $phpbb_seo->page_url);
// www.phpBB-SEO.com SEO TOOLKIT END -> Zero dupe
display_forums('', $config['load_moderators']);

// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
$total_posts	= $config['num_posts'];
$total_topics	= $config['num_topics'];
$total_users	= $config['num_users'];

$l_total_user_s = ($total_users == 0) ? 'TOTAL_USERS_ZERO' : 'TOTAL_USERS_OTHER';
$l_total_post_s = ($total_posts == 0) ? 'TOTAL_POSTS_ZERO' : 'TOTAL_POSTS_OTHER';
$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';

// Grab group details for legend display
if ($auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
{
	$sql = 'SELECT group_id, group_name, group_colour, group_type
		FROM ' . GROUPS_TABLE . '
		WHERE group_legend = 1
		ORDER BY group_name ASC';
}
else
{
	$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type
		FROM ' . GROUPS_TABLE . ' g
		LEFT JOIN ' . USER_GROUP_TABLE . ' ug
			ON (
				g.group_id = ug.group_id
				AND ug.user_id = ' . $user->data['user_id'] . '
				AND ug.user_pending = 0
			)
		WHERE g.group_legend = 1
			AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $user->data['user_id'] . ')
		ORDER BY g.group_name ASC';
}
$result = $db->sql_query($sql);

$legend = '';
while ($row = $db->sql_fetchrow($result))
{
	$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';

	if ($row['group_name'] == 'BOTS')
	{
		$legend .= (($legend != '') ? ', ' : '') . '<span' . $colour_text . '>' . $user->lang['G_BOTS'] . '</span>';
	}
	else
	{
		// www.phpBB-SEO.com SEO TOOLKIT BEGIN
		if ( $phpbb_seo->seo_opt['profile_inj'] && empty($phpbb_seo->seo_url['group'][$row['group_id']]) ) {
			$phpbb_seo->seo_url['group'][$row['group_id']] = $phpbb_seo->format_url($row['group_name'], $phpbb_seo->seo_static['group']);
		}
		// www.phpBB-SEO.com SEO TOOLKIT END
		$legend .= (($legend != '') ? ', ' : '') . '<a' . $colour_text . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']) . '">' . (($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name']) . '</a>';
	}
}
$db->sql_freeresult($result);

// Generate birthday list if required ...
$birthday_list = '';
if ($config['load_birthdays'] && $config['allow_birthdays'])
{
	$now = getdate(time() + $user->timezone + $user->dst - date('Z'));
	$sql = 'SELECT user_id, username, user_colour, user_birthday
		FROM ' . USERS_TABLE . "
		WHERE user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%'
			AND user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$birthday_list .= (($birthday_list != '') ? ', ' : '') . get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);

		if ($age = (int) substr($row['user_birthday'], -4))
		{
			$birthday_list .= ' (' . ($now['year'] - $age) . ')';
		}
	}
	$db->sql_freeresult($result);
}
// if automatic reminders is set, remind people. lets only run this once a day.
if ( $config['user_reminder_enable'] == ENABLED )
{
	$check_time = (int) gmdate('mdY',time() + (3600 * ($config['board_timezone'] + $config['board_dst'])));

	if ( $config['user_reminder_last_auto_run'] < $check_time)
	{
		if (!function_exists('send_user_reminders'))
		{
			include($phpbb_root_path . 'includes/functions_user_reminder.' . $phpEx);
		}
		send_user_reminders();

		set_config('user_reminder_last_auto_run', $check_time);
	}
}
// Assign index specific vars
$template->assign_vars(array(
	'TOTAL_POSTS'	=> sprintf($user->lang[$l_total_post_s], $total_posts),
	'TOTAL_TOPICS'	=> sprintf($user->lang[$l_total_topic_s], $total_topics),
	'TOTAL_USERS'	=> sprintf($user->lang[$l_total_user_s], $total_users),
	'NEWEST_USER'	=> sprintf($user->lang['NEWEST_USER'], get_username_string('full', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])),

	'LEGEND'		=> $legend,
	'BIRTHDAY_LIST'	=> $birthday_list,

	'FORUM_IMG'				=> $user->img('forum_read', 'NO_NEW_POSTS'),
	'FORUM_NEW_IMG'			=> $user->img('forum_unread', 'NEW_POSTS'),
	'FORUM_LOCKED_IMG'		=> $user->img('forum_read_locked', 'NO_NEW_POSTS_LOCKED'),
	'FORUM_NEW_LOCKED_IMG'	=> $user->img('forum_unread_locked', 'NO_NEW_POSTS_LOCKED'),

	'S_LOGIN_ACTION'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
	'S_DISPLAY_BIRTHDAY_LIST'	=> ($config['load_birthdays']) ? true : false,

	'U_MARK_FORUMS'		=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? 'http://www.roleplaygateway.com/forum.html?mark=forums' : '',
	'U_MCP'				=> ($auth->acl_get('m_') || $auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=main&amp;mode=front', true, $user->session_id) : '')
);

// Output page
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - META
$seo_meta->meta['meta_desc'] = $seo_meta->meta_filter_txt($config['sitename'] . ' : ' .  $config['site_desc']);
$seo_meta->meta['keywords'] = $seo_meta->make_keywords($seo_meta->meta['meta_desc']);
// www.phpBB-SEO.com SEO TOOLKIT END - META
page_header('Online Roleplay Forums');

$template->set_filenames(array(
	'body' => 'index_body.html')
);

page_footer();

?>
