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
$start      = (int) @$_REQUEST['start'];
$limit      = (int) @$_REQUEST['limit'];

$sql = 'SELECT id, title, url, require_approval, owner FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if ($_REQUEST['format'] == 'json') {
  $places = array();
  
  $sql = 'SELECT id, name, roleplay_id, owner, url FROM rpg_places WHERE roleplay_id = '.(int) $roleplay['id'] . ' AND visibility <> "Hidden"';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $places[] = $place;
  }
  
  header('Content-Type: application/json');
  echo(json_encode($places));
  exit();
}


$game_masters = array();
$sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' /*AND isCoGM = 1*/)';
$result = $db->sql_query($sql);
while ($gm_row = $db->sql_fetchrow($result)) {
  $game_masters[] = $gm_row['user_id'];
}
$db->sql_freeresult($result);

$pagination_url = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/places';

$roleplayID = $roleplay['id'];
$limit = (isset($limit)) ? $limit : 2000;
//$limit = 2000;

$sql = 'SELECT count(*) as placeCount FROM rpg_places WHERE roleplay_id = '.(int) $roleplayID;
$placeResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($placeResult);
$roleplay['placeCount'] = $row['placeCount'];
$db->sql_freeresult($placeResult);

$sql = 'SELECT owner as id, count(owner) as owned FROM rpg_places WHERE roleplay_id = '.(int) $roleplay['id'] . ' GROUP BY owner ORDER BY owned DESC';
$ownersResult = $db->sql_query($sql);
while ($owner = $db->sql_fetchrow($ownersResult)) {
  
  $sql = 'SELECT user_id as id, username, user_colour FROM gateway_users WHERE user_id = '.(int) $owner['id'];
  $ownerResult = $db->sql_query($sql);
  $sovereign = $db->sql_fetchrow($ownerResult);
  $db->sql_freeresult($ownerResult);
  
  $template->assign_block_vars('owners', array(
    'USERNAME' => get_username_string('full', $sovereign['id'], $sovereign['username'], $sovereign['user_colour']),
    'OWNED_COUNT' => $owner['owned']
  ));
}
$db->sql_freeresult($ownersResult);

$places = array();

$sql = 'SELECT id, name, synopsis, description, owner, url, parent_id, length(synopsis) as synopsisLength, length(description) as descriptionLength, length(image) as imageSize FROM rpg_places
          WHERE rpg_places.roleplay_id = '.(int) $roleplayID .' AND visibility <> "Hidden"';

$result = $db->sql_query_limit($sql, 2000, $start);
while ($row = $db->sql_fetchrow($result)) {

  $sql = 'SELECT user_id as id, username, user_id FROM gateway_users WHERE user_id = '.(int) $row['creator'];
  $userResult = $db->sql_query($sql);
  $creator = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $sql = 'SELECT user_id as id, username, user_id FROM gateway_users WHERE user_id = '.(int) $row['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $row['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $sql = 'SELECT id, name, synopsis, url FROM rpg_places WHERE id = '.(int) $row['parent_id'];
  $parentResult = $db->sql_query($sql, 3600);
  $parent = $db->sql_fetchrow($parentResult);
  $db->sql_freeresult($parentResult);

  $row['parent_id'] = $parent['id'];
  $row['parent_name'] = $parent['name'];
  $row['parent_synopsis'] = $parent['synopsis'];
  $row['parent_slug'] = $parent['url'];

  $sql = 'SELECT count(*) as number FROM rpg_characters WHERE location = '.(int) $row['id'];
  $characterResult = $db->sql_query($sql, 3600);
  $character = $db->sql_fetchrow($characterResult);
  $db->sql_freeresult($characterResult);

  $row['characterCount'] = $character['number'];

  $places[ $row['id'] ] = $row;

  $template->assign_block_vars('places', array(
    'ID'              => $row['id'],
    'NAME'            => $row['name'],
    'URL'             => $row['url'],
    'OWNER_USERNAME'  => $row['owner_username'],
    'SYNOPSIS'        => $row['synopsis'],
    'SYNOPSIS_LENGTH'   => $row['synopsisLength'],
    'DESCRIPTION'        => $row['description'],
    'DESCRIPTION_LENGTH'   => $row['descriptionLength'],
    'IMAGE_SIZE'        => $row['imageSize'],
    
    'POSTS'           => $row['posts'],
    'PARENT_ID'       => $row['parent_id'],
    'PARENT_NAME'     => $row['parent_name'],
    'PARENT_SLUG'     => $row['parent_slug'],
    'PARENT_SYNOPSIS' => $row['parent_synopsis'],
    'LAST_POST_TIME'  => $row['lastPostTime'],
    'LAST_POST_DATE'  => timeAgo($row['lastPostTime']),
    'LAST_POST_ID'    => $row['lastPostID'],
    'LAST_POST_CONTENT'    => $row['lastPostContent'],
    'ROLEPLAY_URL'    => $roleplay['url'],
    'CHARACTER_COUNT' => $row['characterCount'],
    'S_IS_WARNING' => ($row['synopsisLength'] == 120) ? true : false,
    'S_HAS_SYNOPSIS' => ($row['synopsisLength'] > 0) ? true : false,
    'S_HAS_DESCRIPTION' => ($row['descriptionLength'] > 0) ? true : false,
    'S_HAS_IMAGE' => ((int) $row['imageSize'] !== 40000) ? true : false,
    'S_IS_LAZY' => ($row['synopsis'] == $row['description']) ? true : false,
    'S_CAN_EDIT' > ($owner['id'] == $user->data['user_id']) ? true : false,
    'S_CAN_SELL' > ($owner['id'] == $user->data['user_id']) ? true : false,
  ));

}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_MORE_PLACES'		=> (@$roleplay['placeCount'] > $limit) ? true : false,
  'MORE_PLACES_COUNT'	=> (@$roleplay['placeCount'] > $limit) ? @$roleplay['placeCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['placeCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page(count($places), $limit, $start),
  'TOTAL_PLACES'       => (int) $roleplay['placeCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url'],
  'ROLEPLAY_ID'    => $roleplay['id'],
  'S_PAGE_ONLY'     => true,
  'S_CAN_EDIT'        => (($auth->acl_get('m_')) || ($roleplay['owner'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
));



page_header('Places | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'places-table.html'
	)
);

page_footer();
