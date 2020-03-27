<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/Parsedown.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$Parsedown = new Parsedown();

$sql = 'SELECT a.*, a.type FROM rpg_content a
				WHERE a.id = '.(int) $_REQUEST['id'];
$result = $db->sql_query($sql);
$snippet = $db->sql_fetchrow($result);

$sql = 'SELECT id,name,anonymous,url FROM rpg_characters WHERE id = '. (int) $snippet['character_id'] ;
$character_result = $db->sql_query($sql);
$character = $db->sql_fetchrow($character_result);
$db->sql_freeresult($character_result);


$sql = 'SELECT name as place, url as place_url, synopsis as place_synopsis FROM rpg_places WHERE id = '. (int) $snippet['place_id'] ;
$thisResult = $db->sql_query($sql);
$place = $db->sql_fetchrow($thisResult);
$snippet = array_merge($snippet,  $place);
$db->sql_freeresult($thisResult);

$sql = 'SELECT id, title, url FROM rpg_roleplays WHERE id = '. (int) $snippet['roleplay_id'] ;
$roleplayResult = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($roleplayResult);
$db->sql_freeresult($roleplayResult);

$sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '. (int) $snippet['author_id'] ;
$thisResult = $db->sql_query($sql);
$snippet = @array_merge($snippet,  $db->sql_fetchrow($thisResult));
$db->sql_freeresult($thisResult);

$snippet['oldContent'] = $snippet['content'] = generate_text_for_display($snippet['text'], $snippet['bbcode_uid'], $snippet['bbcode_bitfield'], 7);

if (@$snippet['type'] == 'Dialogue') {
  $snippet['tokens'] = explode(' ', $snippet['content']);
  if ($snippet['tokens'][0] == '/say') {
    $newContent = array_slice($snippet['tokens'], 1);
    $snippet['content'] = implode(' ', $newContent);
  }
}

$roleplay['link'] = '/universes/'.$roleplay['url'];
$place['link'] = $roleplay['link'].'/places/'. $place['place_url'];

$pageBaseURL = '/universes/' . $roleplay_data['url'] . '/places/' . $snippet['place_url'];

@$permalink = '/snippets/'.(int) $snippet['id'];

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `for` = "/snippets/' . (int) $snippet['id'] .'"';
$creditResult = $db->sql_query($sql);
$credits = $db->sql_fetchrow($creditResult);
$db->sql_freeresult($creditResult);

@$template->assign_vars(array(
  'ROLEPLAY_ID'	=> $roleplay['id'],
  'ROLEPLAY_NAME'	=> $roleplay['title'],
  'ROLEPLAY_URL'	=> $roleplay['url'],
  'SNIPPET_TIPS_TOTAL'	=> money_format('%i', $credits['total']),
	'SNIPPET_S_BBCODE_ALLOWED'	=> true,
	'SNIPPET_S_SMILIES_ALLOWED'	=> true,
	'SNIPPET_S_CAN_EDIT'				=> 		(($auth->acl_get('m_')) || ($snippet['author_id'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
	'SNIPPET_S_CAN_DELETE'			=> 		(($auth->acl_get('m_')) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
	'SNIPPET_S_IS_DIALOGUE'			=> ($snippet['type'] == 'Dialogue') ? true : false,
	'SNIPPET_S_IS_ANONYMOUS'		=> ((@$snippet['anonymous'] == 1) || (@$character['anonymous'] == 1)) ? true : false,
	'SNIPPET_S_IS_DELETED'		=>		(!empty($snippet['deleted'])) ? true : false,
	'SNIPPET_ID'	 							=> $snippet['id'],
	'SNIPPET_AUTHOR'	 					=> get_username_string('full', @$snippet['user_id'], @$snippet['username']),
	'SNIPPET_PLAYER_ID' 				=> @$snippet['user_id'],
	'SNIPPET_LOCATION' 					=> @$snippet['place'],
	'SNIPPET_LOCATION_ID' 			=> @$snippet['place_id'],
	'SNIPPET_LOCATION_NAME' 			=> @$snippet['place'],
	'SNIPPET_LOCATION_URL' 			=> @$snippet['place_url'],
	'SNIPPET_LOCATION_LINK' 			=> @$place['link'],
	'SNIPPET_LOCATION_SYNOPSIS' 			=> @$snippet['place_synopsis'],
	'SNIPPET_CONTENT'						=> $snippet['content'],
	'SNIPPET_TIME_ISO'					=> date('c', strtotime($snippet['written'])),
	'SNIPPET_TIME_AGO'					=> timeAgo(strtotime($snippet['written'])),
	'SNIPPET_CHARACTER_NAME'		=> $character['name'],
	'SNIPPET_CHARACTER_URL'			=> $character['url'],
	'SNIPPET_PERMALINK'         => $permalink,
  'SNIPPET_ROLEPLAY_LINK'     => $roleplay['link'],
  'SNIPPET_ROLEPLAY_NAME'     => $roleplay['title'],
));

@$sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $snippet['id'] . '';
$tags_result = $db->sql_query($sql, 60);
while ($tags_row = $db->sql_fetchrow($tags_result)) {
	$template->assign_block_vars('characters', array(
		'ID'		=> $tags_row['id'],
		'NAME'		=> $tags_row['name'],
		'URL'		=> $tags_row['url'],
		'SYNOPSIS'	=> $tags_row['synopsis'],
	));
}
$db->sql_freeresult($tags_result);

$sql = 'SELECT f.*,username FROM rpg_footnotes f INNER JOIN gateway_users u ON f.author = u.user_id WHERE f.content_id = '.(int) $snippet['id'];
$footnotes_result = $db->sql_query($sql);
while ($footnote = $db->sql_fetchrow($footnotes_result)) {
	$template->assign_block_vars('footnotes', array(
		'ID'		=> $footnote['id'],
		'FOOTNOTE'		=> $Parsedown->text($footnote['footnote']),
		'AUTHOR_USERNAME'	=> $footnote['username'],
		'TIME_ISO'				  => date('c', strtotime($footnote['timestamp'])),
		'TIME_AGO'				  => timeAgo(strtotime($footnote['timestamp'])),
	));
}
$db->sql_freeresult($footnotes_result);

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

page_header('Post #'.$snippet['id'].' in ' . $roleplay['title'] .', a roleplay on ' . $config['sitename']);

$template->set_filenames(array(
	'body' => 'snippets.html'
));

page_footer();

?>
