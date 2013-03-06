<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$roleplayID = (int) $_REQUEST['roleplayID'];
$roleplayURL = $_REQUEST['roleplayURL'];

$arcID = (int) $_REQUEST['arcID'];
$arcURL = $_REQUEST['arcURL'];

if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $game_masters = array();
  $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
  $result = $db->sql_query($sql);
  while ($gm_row = $db->sql_fetchrow($result)) {
    $game_masters[] = $gm_row['user_id'];
  }
  $db->sql_freeresult($result);

  $sql = 'SELECT id, name, description, slug, creator FROM rpg_arcs WHERE roleplay_id = '.(int) $roleplay['id'].' AND slug = "'.$db->sql_escape($arcURL).'"';
  $arcResult = $db->sql_query($sql);
  while ($arc = $db->sql_fetchrow($arcResult)) {
  
	  $template->assign_vars(array(
		  'ARC_ID' 					=> $arc['id'],
		  'ARC_NAME' 				=> $arc['name'],
		  'ARC_SYNOPSIS' 		=> $arc['synopsis'],
      'ARC_URL'         => $arc['slug'],
      'ARC_SLUG'        => $arc['slug'],
		  'ARC_DESCRIPTION' => generate_text_for_display($arc['description'], $arc['description_uid'], $arc['description_bitfield'], 7),
		  'CHARACTER_COUNT' => $arc['characters'],
		  'UNIQUE_PLAYERS'  => $arc['players'],
		  'UNIQUE_PLAYERS'  => $arc['players'],
      'URL'             => $roleplay['url'],
      'ROLEPLAY_ID'     => $roleplay['id'],
		  'ROLEPLAY_URL'    => $roleplay['url'],
      'ROLEPLAY_TITLE'  => $roleplay['title'],
      'ROLEPLAY_NAME'   => $roleplay['title'],
		  'PLACES'          => $arc['places'],
		  'PLACE_COUNT'     => count($arc['places']),
		  'S_CAN_EDIT'      => ($arc['owner'] == $user->data['user_id'] || $user->data['user_id'] == 4) ? true : false,
		  'S_PAGE_ONLY'     => true,
	  ));

    $posts = array();
    $sql = 'SELECT content_id FROM rpg_arc_content where arc_id = '.(int) $arc['id'];
    $result = $db->sql_query($sql);
    while ($row = $db->sql_fetchrow($result)) {
      $posts[$row['content_id']] = (int) $row['content_id'];
    }
    $db->sql_freeresult($result);

    $sql = 'SELECT DISTINCT c.id, c.*,u.user_id,u.username,p.name as place,p.url FROM rpg_content c
        LEFT OUTER JOIN gateway_users u
          ON c.author_id = u.user_id
        LEFT OUTER JOIN rpg_places p
          ON c.place_id = p.id
        WHERE c.id IN  (' . implode(',', $posts) . ') ORDER BY c.written ASC';

    $result = @$db->sql_query($sql);
    while ($content_row = $db->sql_fetchrow($result)) {
    
      if ($character_id = $content_row['character_id']) {
    
        $sql = 'SELECT id,name,url FROM rpg_characters WHERE id = '. (int) $character_id ;
        $character_result = $db->sql_query($sql);
        $character = $db->sql_fetchrow($character_result);
        $db->sql_freeresult($character_result);
      }
    
      $content_row['content'] = generate_text_for_display($content_row['text'], $content_row['bbcode_uid'], $content_row['bbcode_bitfield'], 7);

      if ($content_row['type'] == 'Dialogue') {
        $content_row['tokens'] = explode(' ', $content_row['content']);
        if ($content_row['tokens'][0] == '/say') {
          $newContent = array_slice($content_row['tokens'], 1);
          $content_row['content'] = implode(' ', $newContent);
        }
      }

      $template->assign_block_vars('activity', array(
        'S_BBCODE_ALLOWED'    => true,
        'S_SMILIES_ALLOWED'   => true,
        'S_CAN_EDIT'      =>  (($auth->acl_get('m_')) || ($content_row['author_id'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
        'S_IS_DIALOGUE'     => ($content_row['type'] == 'Dialogue') ? true : false,
        'ID'          => $content_row['id'],
        'AUTHOR'        => get_username_string('full', @$content_row['user_id'], @$content_row['username']),
        'PLAYER_ID'       => @$content_row['user_id'],
        'LOCATION'        => @$content_row['place'],
        'LOCATION_ID'       => @$content_row['place_id'],
        'LOCATION_URL'      => urlify(@$content_row['place']),
        'CONTENT'       => $content_row['content'],
        'TIME_AGO'        => timeAgo(strtotime($content_row['written'])),
        'CHARACTER_NAME'    => $character['name'],
        'CHARACTER_URL'     => $character['url'],
        'TIME_ISO'        => date('c',strtotime($content_row['written'])),
      ));

      $sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $content_row['id'] . '';
      $tags_result = $db->sql_query($sql);
      while ($tags_row = $db->sql_fetchrow($tags_result)) {
        $template->assign_block_vars('activity.characters', array(
          'ID'    => $tags_row['id'],
          'NAME'    => $tags_row['name'],
          'URL'   => $tags_row['url'],
          'SYNOPSIS'  => $tags_row['synopsis'],
        )); 
      }
      $db->sql_freeresult($tags_result); 

    }
    $db->sql_freeresult($result);

    page_header($arc['name'] . ' | ' . $roleplay['title'] . ' | ' . $config['sitename']);

    $template->set_filenames(array(
	    'body' => 'rpg_arc.html')
    );

    page_footer();
	    
  }
  //$db->sql_freeresult($arcResult);

}
$db->sql_freeresult($result);


?>
