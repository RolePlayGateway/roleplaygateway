<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$roleplayURL = $_REQUEST['roleplayURL'];
$start      = (int) $_REQUEST['start'];

$sql = 'SELECT id, title, url, require_approval FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$pagination_url = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/groups';

$roleplayID = $roleplay['id'];
$limit = 40;

$sql = 'SELECT count(*) as placeCount FROM rpg_groups WHERE roleplay_id = '.(int) $roleplayID;
$placeResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($placeResult);
$roleplay['placeCount'] = $row['placeCount'];
$db->sql_freeresult($placeResult);

$sql = 'SELECT id, name, synopsis, slug, s.characters, s.players, u.username, g.owner FROM rpg_groups g
          INNER JOIN rpg_group_stats s ON g.id = s.group_id
          INNER JOIN gateway_users u ON u.user_id = g.owner
          WHERE roleplay_id = '.(int) $roleplayID .' /* AND synopsis IS NOT NULL */ ORDER BY s.players DESC, s.characters DESC';

$result = $db->sql_query_limit($sql, $limit, $start);
while ($row = $db->sql_fetchrow($result)) {

	$template->assign_block_vars('groups', array(
		'ID' 				      => $row['id'],
		'NAME' 				    => $row['name'],
		'SLUG' 				    => $row['slug'],
		'OWNER_LINK'	    => get_username_string('full', $row['owner'], $row['username']),
		'OWNER_ID'        => $row['owner'],
		'OWNER_USERNAME'  => $row['username'],
		'SYNOPSIS'			  => $row['synopsis'],
    'POSTS'           => $row['posts'],
    'CHARACTERS'      => $row['characters'],
    'UNIQUE_PLAYERS'  => $row['players'],
	));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_MORE_GROUPS'		=> (@$roleplay['placeCount'] > $limit) ? true : false,
  'MORE_GROUPS_COUNT'	=> (@$roleplay['placeCount'] > $limit) ? @$roleplay['placeCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['placeCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page($roleplay['placeCount'], $limit, $start),
  'TOTAL_GROUPS'       => (int) $roleplay['placeCount'],
  'ROLEPLAY_ID'     => $roleplay['id'],
  'ROLEPLAY_TITLE'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url']
));



page_header('Groups | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'roleplay_groups.html'
	)
);

page_footer();

?>
