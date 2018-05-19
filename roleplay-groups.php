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

$groupID = (int) $_REQUEST['groupID'];
$groupURL = $_REQUEST['group_url'];

if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $sql = 'SELECT id, name, synopsis, description, description_bitfield, description_uid, slug, owner FROM rpg_groups WHERE roleplay_id = '.(int) $roleplay['id'].' AND slug = "'.$db->sql_escape($groupURL).'"';
  $groupResult = $db->sql_query($sql);
  while ($group = $db->sql_fetchrow($groupResult)) {

    $sql = 'SELECT count(*) AS characters FROM rpg_group_members WHERE group_id = '.(int) $group['id']. ' AND status = "Member"';
    $memberResult = $db->sql_query($sql);
    $group['characters'] = $db->sql_fetchfield('characters');
    $db->sql_freeresult($memberResult);
      
    $sql = 'SELECT count(DISTINCT owner) AS players FROM rpg_characters WHERE id IN (SELECT character_id from rpg_group_members WHERE group_id = '.(int) $group['id'] . ' AND status = "Member")';
    $memberResult = $db->sql_query($sql);
    $group['players'] = $db->sql_fetchfield('players');
    $db->sql_freeresult($memberResult);
     
    /* $sql = 'SELECT DISTINCT place_id, COUNT(*) AS posts FROM rpg_content
              WHERE id IN
                (SELECT content_id FROM rpg_content_tags WHERE character_id IN
                  (SELECT character_id from rpg_group_members WHERE group_id = '.(int) $group['id'] . ' AND status = "Member")
                ) GROUP BY place_id ORDER BY posts DESC';
    $placeResult = $db->sql_query($sql);
    while ($place = $db->sql_fetchrow($placeResult)) {
       
      $sql = 'SELECT id, name, synopsis, url FROM rpg_places WHERE id = '.(int) $place['place_id'];
      $thisResult = $db->sql_query($sql);
      $place = array_merge($place, $db->sql_fetchrow($thisResult));
      $db->sql_freeresult($thisResult);
      
      $template->assign_block_vars('places', array(
        'ID' => $place['id'],
        'URL' => $place['url'],
        'NAME' => $place['name'],
        'SYNOPSIS' => $place['synopsis'],
        'POST_COUNT' => $place['posts'],
      ));      

    }
    $db->sql_freeresult($placeResult); */

	  $template->assign_vars(array(
		  'GROUP_ID' 					=> $group['id'],
		  'GROUP_NAME' 				=> $group['name'],
		  'GROUP_SYNOPSIS' 		=> $group['synopsis'],
		  'GROUP_URL' 		=> $group['slug'],
		  'GROUP_DESCRIPTION' => generate_text_for_display($group['description'], $group['description_uid'], $group['description_bitfield'], 7),
		  'CHARACTER_COUNT' => $group['characters'],
		  'UNIQUE_PLAYERS' => $group['players'],
		  'UNIQUE_PLAYERS' => $group['players'],
		  'ROLEPLAY_ID' => $roleplay['id'],
		  'ROLEPLAY_URL' => $roleplay['url'],
		  'ROLEPLAY_TITLE' => $roleplay['title'],
		  'PLACES' => $group['places'],
		  'PLACE_COUNT' => count($group['places']),
		  'S_CAN_EDIT' => ($group['owner'] == $user->data['user_id'] || $user->data['user_id'] == 4) ? true : false,
		  'S_PAGE_ONLY' => true,
	  ));
	  
	  $sql = 'SELECT * from rpg_group_members WHERE group_id = '.(int) $group['id'] . ' AND status = "Member"';
    $characterResult = $db->sql_query($sql);
    while ($character = $db->sql_fetchrow($characterResult)) {
    
      $sql = 'SELECT id, owner, name, synopsis, url FROM rpg_characters WHERE id = '.(int) $character['character_id'];
      $thisResult = $db->sql_query($sql);
      $character = array_merge($character, $db->sql_fetchrow($thisResult));
      $db->sql_freeresult($thisResult);
      
      $sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $character['owner'];
      $memberResult = $db->sql_query($sql);
      $character['owner_username'] = $db->sql_fetchfield('username');
      $db->sql_freeresult($memberResult);      

      $template->assign_block_vars('characters', array(
        'ID' => $character['id'],
        'URL' => $character['url'],
        'NAME' => $character['name'],
        'SYNOPSIS' => $character['synopsis'],
        'OWNER_USERNAME' => $character['owner_username'],
      ));
      
    }
	  

    page_header($group['name'] . ' | ' . $roleplay['title'] . ' | ' . $config['sitename']);

    $template->set_filenames(array(
	    'body' => 'rpg_groups.html')
    );

    page_footer();
	    
  }
  //$db->sql_freeresult($groupResult);

}
$db->sql_freeresult($result);


?>
