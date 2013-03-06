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

$pagination_url = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/places';

$roleplayID = $roleplay['id'];
$limit = 1024;

$sql = 'SELECT count(*) as placeCount FROM rpg_places WHERE roleplay_id = '.(int) $roleplayID;
$placeResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($placeResult);
$roleplay['placeCount'] = $row['placeCount'];
$db->sql_freeresult($placeResult);


$places = array();

$sql = 'SELECT id, name, synopsis, owner, url FROM rpg_places
          WHERE rpg_places.roleplay_id = '.(int) $roleplayID .' AND id > 0';

$result = $db->sql_query_limit($sql, $limit, $start);
while ($row = $db->sql_fetchrow($result)) {

  $sql = 'SELECT count(*) as posts, UNIX_TIMESTAMP(max(written)) as lastPostTime, author_id, id FROM rpg_content
    WHERE place_id = '.(int) $row['id']. ' ORDER BY written DESC LIMIT 1';
  $contentResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($contentResult);

  $row['posts']           = $stats['posts'];
  $row['lastPostTime']    = $stats['lastPostTime'];
  $row['lastPostID']      = $stats['id'];
  $row['lastPostAuthor']  = $stats['author_id'];

  $db->sql_freeresult($contentResult);

  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $row['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $row['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $places[ $row['id'] ] = $row;
}
$db->sql_freeresult($result);

uasort($places, function($a, $b) {
  return $b['lastPostTime'] - $a['lastPostTime'];
});

foreach ($places as $row) {
    $template->assign_block_vars('places', array(
    'ID'              => $row['id'],
    'NAME'            => $row['name'],
    'URL'             => $row['url'],
    'OWNER_USERNAME'  => $row['owner_username'],
    'SYNOPSIS'        => $row['synopsis'],
    'POSTS'           => $row['posts'],
    'LAST_POST_TIME'  => $row['lastPostTime'],
    'LAST_POST_DATE'  => timeAgo($row['lastPostTime']),
    'LAST_POST_ID'    => $row['lastPostID'],
  ));
}

$template->assign_vars(array(
	'S_MORE_PLACES'		=> (@$roleplay['placeCount'] > $limit) ? true : false,
  'MORE_PLACES_COUNT'	=> (@$roleplay['placeCount'] > $limit) ? @$roleplay['placeCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['placeCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page($roleplay['placeCount'], $limit, $start),
  'TOTAL_PLACES'       => (int) $roleplay['placeCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url']
));



page_header('Places | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'roleplay_places.html'
	)
);

page_footer();
