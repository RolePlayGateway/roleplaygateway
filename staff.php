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


  $groupID = (int) $_REQUEST['groupID'];
  $username = (string) $_REQUEST['username'];
  
  
  if (($groupID > 0) && (!empty($username))) {
    $sql = 'SELECT user_id, username FROM gateway_users WHERE username = "'.$db->sql_escape($username).'"';
    $result = $db->sql_query($sql);
    $userObject = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);
    
    die(json_encode($userObject));
    
  }



die('end.');


$teamURL = $_REQUEST['team'];

$sql = 'SELECT group_id as id, group_name as name, group_desc as description FROM gateway_groups WHERE group_name LIKE "'.$db->sql_escape($teamURL).'"';
$result = $db->sql_query($sql);
$team = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$pagination_url = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/groups';

$roleplayID = $roleplay['id'];
$limit = 40;

$sql = 'SELECT count(*) as placeCount FROM rpg_groups WHERE roleplay_id = '.(int) $roleplayID;
$placeResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($placeResult);
$roleplay['placeCount'] = $row['placeCount'];
$db->sql_freeresult($placeResult);

$sql = 'SELECT id, name, synopsis, slug FROM rpg_groups
          WHERE roleplay_id = '.(int) $roleplayID .' /* AND synopsis IS NOT NULL */ ORDER BY id DESC';

$result = $db->sql_query_limit($sql, $limit, $start);
while ($row = $db->sql_fetchrow($result)) {
  
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



page_header('Groups | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'roleplay_groups.html'
	)
);

page_footer();

?>
