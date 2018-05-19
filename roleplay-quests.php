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

$questID = (int) $_REQUEST['questID'];

if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $sql = 'SELECT id, name, synopsis FROM rpg_quests WHERE roleplay_id = '.(int) $roleplay['id'].' AND id = "'.(int) $questID.'"';
  $groupResult = $db->sql_query($sql);
  while ($quest = $db->sql_fetchrow($groupResult)) {
  
    $sql = 'SELECT count(*) AS characters FROM rpg_quest_characters WHERE quest_id = '.(int) $quest['id']. ' /* AND status = "Member" */';
    $memberResult = $db->sql_query($sql);
    $quest['characters'] = $db->sql_fetchfield('characters');
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
    	'S_PAGE_ONLY'       => true,
		  'QUEST_ID' 					=> $quest['id'],
		  'QUEST_NAME' 				=> $quest['name'],
		  'QUEST_SYNOPSIS' 		=> $quest['synopsis'],
		  'QUEST_URL' 		    => $quest['id'],
		  'QUEST_DESCRIPTION' => $quest['description'],
		  'CHARACTER_COUNT'   => $quest['characters'],
		  'ROLEPLAY_URL'      => $roleplay['url'],
      'ROLEPLAY_TITLE'    => $roleplay['title'],
      'ROLEPLAY_SYNOPSIS' => $roleplay['description'],
		  'PLACES'            => $quest['places'],
		  'PLACE_COUNT'       => count($quest['places']),
	  ));
	  
	  $sql = 'SELECT * from rpg_quest_characters WHERE quest_id = '.(int) $quest['id'] . ' /* AND status = "Member" */';
    $characterResult = $db->sql_query($sql);
    while ($character = $db->sql_fetchrow($characterResult)) {
    
      $sql = 'SELECT id, owner, name, synopsis, url FROM rpg_characters WHERE id = '.(int) $character['character_id'];
      $thisResult = $db->sql_query($sql);
      $character = @array_merge($character, $db->sql_fetchrow($thisResult));
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
        'QUEST_STATUS' => (!empty($character['completed'])) ? 'COMPLETED' : 'ACCEPTED',
      ));
      
      $characters[] = $character['id'];
      
    }

    page_header('Quest #'.$quest['id'] . ' | ' . $roleplay['title'] . ' | ' . $config['sitename']);

    $template->set_filenames(array(
	    'body' => 'rpg_quests.html')
    );

    page_footer();
	    
  }
  //$db->sql_freeresult($groupResult);

}
$db->sql_freeresult($result);


?>
