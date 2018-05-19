<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$sql = 'SELECT roleplay_id FROM rpg_characters WHERE creator = '.(int) $user->data['user_id'];

$result = $db->sql_query_limit($sql, $limit, $start);
while ($row = $db->sql_fetchrow($result)) {

  die($row['roleplay_id']);
  
  $sql = 'SELECT count(*) AS characters FROM rpg_group_members WHERE group_id = '.(int) $row['id']. ' AND status = "Member"';
  $memberResult = $db->sql_query($sql);
  $row['characters'] = $db->sql_fetchfield('characters');
  $db->sql_freeresult($memberResult);
    
  $sql = 'SELECT count(DISTINCT owner) AS players FROM rpg_characters WHERE id IN (SELECT character_id from rpg_group_members WHERE group_id = '.(int) $row['id'] . ' AND status = "Member")';
  $memberResult = $db->sql_query($sql);
  $row['players'] = $db->sql_fetchfield('players');
  $db->sql_freeresult($memberResult);

	$template->assign_block_vars('groups', array(
		'ID' 				=> $row['id'],
		'NAME' 				=> $row['name'],
		'SLUG' 				=> $row['slug'],
		'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
		'SYNOPSIS'			=> $row['synopsis'],
    'POSTS'     => $row['posts'],
    'CHARACTERS'     => $row['characters'],
    'UNIQUE_PLAYERS'     => $row['players'],
	));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_MORE_GROUPS'		=> (@$roleplay['placeCount'] > $limit) ? true : false,
  'MORE_GROUPS_COUNT'	=> (@$roleplay['placeCount'] > $limit) ? @$roleplay['placeCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['placeCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page($roleplay['placeCount'], $limit, $start),
  'TOTAL_GROUPS'       => (int) $roleplay['placeCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url']
));

page_header('Writing | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'writing.html'
	)
);

page_footer();

?>
