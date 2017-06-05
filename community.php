<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$sql = 'SELECT u.user_id, u.username, u.user_avatar, u.user_avatar_type, s.total_words as total FROM gateway_users u
  INNER JOIN gateway_user_stats s
    ON u.user_id = s.user_id
  ORDER BY total_words DESC LIMIT 42';
$result = $db->sql_query($sql, 3600);
while ($author = $db->sql_fetchrow($result)) {
  $template->assign_block_vars('mostwords', array(
    'USER_AVATAR_URL'	=> get_user_avatar_url($author['user_avatar'], $author['user_avatar_type'], 100, 100),
    'ID' => $author['user_id'],
    'USERNAME' => $author['username'],
    'TOTAL' => $author['total']
  ));
}



$sql = "SELECT a.*, a.type FROM rpg_content a
        ORDER BY a.written DESC LIMIT 1";
$result = $db->sql_query($sql);
while ($content_row = $db->sql_fetchrow($result)) {
  $sql = 'SELECT id,title,url FROM rpg_roleplays WHERE id = '. (int) $content_row['roleplay_id'] ;
  $roleplayResult = $db->sql_query($sql, 3600);
  $roleplay = $db->sql_fetchrow($roleplayResult);
  $db->sql_freeresult($roleplayResult);
  
  $template->assign_vars(array(
    'URL' => $roleplay['url']
  ));
  
  $sql = 'SELECT id,name,anonymous,url FROM rpg_characters WHERE id = '. (int) $content_row['character_id'] ;
  $character_result = $db->sql_query($sql, 3600);
  $character = $db->sql_fetchrow($character_result);
  $db->sql_freeresult($character_result);

  $sql = 'SELECT name as place, url as place_url FROM rpg_places WHERE id = '. (int) $content_row['place_id'] ;
  $thisResult = $db->sql_query($sql, 3600);
  $content_row = array_merge($content_row,  $db->sql_fetchrow($thisResult));
  $db->sql_freeresult($thisResult);

  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '. (int) $content_row['author_id'] ;
  $thisResult = $db->sql_query($sql, 3600);
  $content_row = @array_merge($content_row,  $db->sql_fetchrow($thisResult));
  $db->sql_freeresult($thisResult);

  $content_row['oldContent'] = $content_row['content'] = generate_text_for_display($content_row['text'], $content_row['bbcode_uid'], $content_row['bbcode_bitfield'], 7);

  if (@$content_row['type'] == 'Dialogue') {
    $content_row['tokens'] = explode(' ', $content_row['content']);
    if ($content_row['tokens'][0] == '/say') {
      $newContent = array_slice($content_row['tokens'], 1);
      $content_row['content'] = implode(' ', $newContent);
    }
  }
  
  $pageBaseURL = '/roleplay/' . $roleplay['url'];

  @$permalink = $pageBaseURL . '#roleplay'.$content_row['id'];

  @$template->assign_block_vars('activity', array(
  	'S_BBCODE_ALLOWED'	=> true,
  	'S_SMILIES_ALLOWED'	=> true,
  	'S_CAN_EDIT'				=> 		(($auth->acl_get('m_')) || ($content_row['author_id'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
  	'S_CAN_DELETE'			=> 		(($auth->acl_get('m_')) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
  	'S_IS_DIALOGUE'			=> ($content_row['type'] == 'Dialogue') ? true : false,
  	'S_IS_ANONYMOUS'		=> ((@$content_row['anonymous'] == 1) || (@$character['anonymous'] == 1)) ? true : false,
  	'S_IS_DELETED'		=>		(!empty($content_row['deleted'])) ? true : false,
  	'ID'	 							=> $content_row['id'],
  	'AUTHOR'	 					=> get_username_string('full', @$content_row['user_id'], @$content_row['username']),
  	'PLAYER_ID' 				=> @$content_row['user_id'],
  	'LOCATION' 					=> @$content_row['place'],
  	'LOCATION_ID' 			=> @$content_row['place_id'],
  	'LOCATION_URL' 			=> @$content_row['place_url'],
  	'CONTENT'						=> $content_row['content'],
  	'TIME_ISO'					=> date('c', strtotime($content_row['written'])),
  	'TIME_AGO'					=> timeAgo(strtotime($content_row['written'])),
  	'CHARACTER_NAME'		=> $character['name'],
  	'CHARACTER_URL'			=> $character['url'],
    'PERMALINK'         => $permalink,
  ));

  @$sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $content_row['id'] . '';
  $tags_result = $db->sql_query($sql, 60);
  while ($tags_row = $db->sql_fetchrow($tags_result)) {
  	$template->assign_block_vars('activity.characters', array(
  		'ID'		=> $tags_row['id'],
  		'NAME'		=> $tags_row['name'],
  		'URL'		=> $tags_row['url'],
  		'SYNOPSIS'	=> $tags_row['synopsis'],
  	));
  }
  $db->sql_freeresult($tags_result);

  $sql = 'SELECT f.*,username FROM rpg_footnotes f INNER JOIN gateway_users u ON f.author = u.user_id WHERE f.content_id = '.(int) $content_row['id'];
  $footnotes_result = $db->sql_query($sql);
  while ($footnote = $db->sql_fetchrow($footnotes_result)) {
  	$template->assign_block_vars('activity.footnotes', array(
  		'ID'		=> $footnote['id'],
  		'FOOTNOTE'		=> $Parsedown->text($footnote['footnote']),
  		'AUTHOR_USERNAME'	=> $footnote['username'],
  		'TIME_ISO'				  => date('c', strtotime($footnote['timestamp'])),
  		'TIME_AGO'				  => timeAgo(strtotime($footnote['timestamp'])),
  	));
  }
  $db->sql_freeresult($footnotes_result);
}

$sql = 'SELECT u.user_id as id, u.username, u.user_avatar, u.user_avatar_type, u.user_regdate, d.pf_biography as bio FROM gateway_users u
  INNER JOIN gateway_user_stats s
    ON s.user_id = u.user_id
  INNER JOIN gateway_profile_fields_data d
    ON d.user_id = u.user_id
  WHERE s.total_words > 120000
ORDER BY RAND(CURRENT_DATE()) LIMIT 3';
$result = $db->sql_query($sql);
while ($author = $db->sql_fetchrow($result)) {
  $medals = array();

  $sql = 'SELECT m.id, m.name, m.slug, m.description, m.image FROM gateway_medals_awarded a
    INNER JOIN gateway_medals m ON a.medal_id = m.id
    WHERE a.user_id = '.(int) $author['id'];
  $medalsResult = $db->sql_query($sql);
  while ($medal = $db->sql_fetchrow($medalsResult)) {
    $medals[] = $medal;
  }

  $sql = 'SELECT prs_reputation,total_words,average_words
    FROM gateway_user_stats
    WHERE user_id = '. (int) $author['id'];
  $statsResult = $db->sql_query($sql, 3600);
  $user_stats = $db->sql_fetchrow($statsResult);
  $user_stats['novels'] = round($user_stats['total_words'] / 80000, 2);

  $template->assign_block_vars('featuredusers', array(
    'USER_AVATAR_URL'	=> get_user_avatar_url($author['user_avatar'], $author['user_avatar_type'], 100, 100),
    'ID' => $author['id'],
    'USERNAME' => $author['username'],
    'TENURE' => (date('Y') - date('Y', $author['user_regdate'])),
    'BIO' => $author['bio'],
    'TOTAL_WORDS' => $user_stats['total_words'],
    'NOVELS' => $user_stats['novels'],
    'MEDAL_COUNT' => count($medals),
  ));
  
  foreach ($medals as $medal) {
    $template->assign_block_vars('featuredusers.medals', array(
      'ID' => $medal['id'],
      'NAME' => $medal['name'],
      'SLUG' => $medal['slug'],
      'IMAGE' => $medal['image'],
      'DESCRIPTION' => $medal['description'],
    ));
  }

  $sql = 'SELECT c.id, c.name, c.url, r.url as roleplay_url FROM rpg_characters c
    INNER JOIN rpg_roleplays r
      ON r.id = c.roleplay_id
    WHERE c.owner = '.(int) $author['id'] . '
    AND length(c.image) > 0
    ORDER BY c.views DESC LIMIT 4';
  $characterResult = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($characterResult)) {
    $template->assign_block_vars('featuredusers.characters', array(
      'ID' => $character['id'],
      'URL' => $character['url'],
      'ROLEPLAY_URL' => $character['roleplay_url'],
    ));
  }

  $sql = 'SELECT id, title, url, views FROM rpg_roleplays r
    WHERE r.owner = '.(int) $author['id'] . '
    AND length(image) > 0
    ORDER BY views DESC LIMIT 7';
  $roleplayResult = $db->sql_query($sql);
  while ($roleplay = $db->sql_fetchrow($roleplayResult)) {
    $template->assign_block_vars('featuredusers.universes', array(
      'ID' => $roleplay['id'],
      'URL' => $roleplay['url'],
      'TOTAL' => $roleplay['views']
    ));
  }

}

$sql = 'SELECT DISTINCT
  awards.user_id as id, u.username, u.user_avatar, u.user_avatar_type, total 
FROM (
  SELECT user_id, count(*) AS total FROM gateway_medals_awarded GROUP BY user_id
) awards
INNER JOIN gateway_users u
  ON awards.user_id = u.user_id
ORDER BY total DESC, awards.user_id ASC LIMIT 42';
$result = $db->sql_query($sql);
while ($author = $db->sql_fetchrow($result)) {
  $template->assign_block_vars('topdecorations', array(
    'USER_AVATAR_URL'	=> get_user_avatar_url($author['user_avatar'], $author['user_avatar_type'], 100, 100),
    'ID' => $author['id'],
    'USERNAME' => $author['username'],
    'TOTAL' => $author['total']
  ));
}

$sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id FROM rpg_characters c
      ORDER BY c.views DESC
      LIMIT 42";
$result = $db->sql_query($sql);
while ($character = $db->sql_fetchrow($result)) {
  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
  $ownerResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($ownerResult);

  $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);

  $template->assign_block_vars('topcharacters', array(
    'ID'              => $character['id'],
    'NAME'            => $character['name'],
    'URL'             => $character['url'],
    'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
    'SYNOPSIS'        => $character['synopsis'],
    'ROLEPLAY_URL'    => $roleplay['url'],
    'TOTAL'           => $character['views'],
  ));
}

$sql = "SELECT p.id, p.name, p.owner, p.synopsis, p.url, p.views, p.roleplay_id FROM rpg_places p
      ORDER BY p.views DESC
      LIMIT 42";
$result = $db->sql_query($sql);
while ($place = $db->sql_fetchrow($result)) {
  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $place['owner'];
  $ownerResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($ownerResult);

  $sql = 'SELECT id, url, title FROM rpg_roleplays WHERE id = '.(int) $place['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);

  $template->assign_block_vars('topsettings', array(
    'ID'              => $place['id'],
    'NAME'            => $place['name'],
    'URL'             => $place['url'],
    'OWNER_USERNAME'	=> get_username_string('full', $place['owner'], $owner['username']),
    'SYNOPSIS'        => $place['synopsis'],
    'ROLEPLAY_URL'    => $roleplay['url'],
    'ROLEPLAY_NAME'    => $roleplay['title'],
    'TOTAL'           => $place['views'],
  ));
}

$sql = "SELECT r.id, r.title, r.description, r.url, r.owner, u.username, u.user_id FROM rpg_roleplays r
      INNER JOIN gateway_users u ON r.owner = u.user_id
      ORDER BY r.views DESC
      LIMIT 42";
$result = $db->sql_query($sql);
while ($roleplay = $db->sql_fetchrow($result)) {
  $template->assign_block_vars('toproleplays', array(
    'ID'              => $roleplay['id'],
    'TITLE'           => $roleplay['title'],
    'URL'             => $roleplay['url'],
    'OWNER_USERNAME'	=> get_username_string('full', $roleplay['owner'], $roleplay['username']),
    'DESCRIPTION'     => $roleplay['description'],
  ));
}

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'community.html'
));

page_footer();

?>