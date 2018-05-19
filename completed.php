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

$sql = 'SELECT id, title, description, url, status FROM rpg_roleplays WHERE status = "Completed"';

$result = $db->sql_query_limit($sql, $limit, $start);
while ($roleplay = $db->sql_fetchrow($result)) {

  //die(json_encode($roleplay));
  /*
  $sql = 'SELECT count(*) AS characters FROM rpg_group_members WHERE group_id = '.(int) $row['id']. ' AND status = "Member"';
  $memberResult = $db->sql_query($sql);
  $row['characters'] = $db->sql_fetchfield('characters');
  $db->sql_freeresult($memberResult);
    
  $sql = 'SELECT count(DISTINCT owner) AS players FROM rpg_characters WHERE id IN (SELECT character_id from rpg_group_members WHERE group_id = '.(int) $row['id'] . ' AND status = "Member")';
  $memberResult = $db->sql_query($sql);
  $row['players'] = $db->sql_fetchfield('players');
  $db->sql_freeresult($memberResult);
  */
  
  $sql = 'SELECT total_words FROM rpg_roleplay_stats WHERE roleplay_id = '.(int) $roleplay['id']. '';
  $memberResult = $db->sql_query($sql);
  $roleplay['words'] = $db->sql_fetchfield('total_words');
  $db->sql_freeresult($memberResult);
  
  $sql = 'SELECT count(*) as author_count FROM rpg_roleplay_author_stats WHERE roleplay_id = '.(int) $roleplay['id']. '';
  $memberResult = $db->sql_query($sql);
  $roleplay['author_count'] = $db->sql_fetchfield('author_count');
  $db->sql_freeresult($memberResult);  
  
  //die(json_encode($roleplay));
  
  switch ($roleplay['status']) {
    case 'Open':
      $roleplay['statusColor'] = '#cec';
    break;
    case 'Closed':
      $roleplay['statusColor'] = '#ecc';
    break;
    default:
      $roleplay['statusColor'] = '#ccc';
    break;
  }  

	$template->assign_block_vars('roleplays', array(
		'ID' 				      => $roleplay['id'],
		'TITLE' 				  => $roleplay['title'],
		'SYNOPSIS' 				=> $roleplay['description'],
		'URL' 				    => $roleplay['url'],
		'OWNER_USERNAME'	=> get_username_string('full', $roleplay['owner'], $roleplay['username']),
    'POSTS'           => $roleplay['posts'],
    'WORDS'           => $roleplay['words'],
    'CHARACTERS'      => $roleplay['characters'],
    'UNIQUE_PLAYERS'  => $roleplay['players'],  
    'AUTHOR_COUNT'    => $roleplay['author_count'],
	));
	
  $sql = 'SELECT * FROM rpg_roleplay_author_stats WHERE roleplay_id = '.(int) $roleplay['id'] .' ORDER BY words DESC';
  $authorResult = $db->sql_query($sql);
  while ($author = $db->sql_fetchrow($authorResult)) {

    $sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $author['author_id'];
    $userResult = $db->sql_query($sql);
    $roleplay['authors'][$author['author_id']] = $db->sql_fetchrow($userResult);
    $db->sql_freeresult($userResult);
    
    $roleplay['authors'][$author['author_id']]['words'] = $author['words'];
    $roleplay['authors'][$author['author_id']]['percentage'] = ($author['words'] / $roleplay['words']) * 100;
    
    $template->assign_block_vars('roleplays.authors', array(
      'ID'         => $author['author_id'],
      'USERNAME'   => $roleplay['authors'][$author['author_id']]['username'],
      'PERCENTAGE' => round($roleplay['authors'][$author['author_id']]['percentage'], 1),
    ));
    
  }
  $db->sql_freeresult($authorResult);
  
}
$db->sql_freeresult($result);
/*
$template->assign_vars(array(
	'S_MORE_GROUPS'		=> (@$roleplay['placeCount'] > $limit) ? true : false,
  'MORE_GROUPS_COUNT'	=> (@$roleplay['placeCount'] > $limit) ? @$roleplay['placeCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['placeCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page($roleplay['placeCount'], $limit, $start),
  'TOTAL_GROUPS'       => (int) $roleplay['placeCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url']
));
*/

page_header('Completed Roleplays | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'completed.html'
	)
);

page_footer();

?>
