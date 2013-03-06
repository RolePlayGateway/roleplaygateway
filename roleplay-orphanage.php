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

$roleplayURL = $_REQUEST['roleplayURL'];

if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $template->assign_vars(array(
    'ROLEPLAY_TITLE' 					=> $roleplay['title'],
    'ROLEPLAY_URL' 					=> $roleplay['url'],
  ));

  $sql = 'SELECT id, creator, name, synopsis, url, status, roleplay_id FROM rpg_characters WHERE roleplay_id = '.(int) $roleplay['id'].' AND isAdoptable = 1 AND status = "Approved"';
  $characterResult = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($characterResult)) {
  
    $sql = 'SELECT id, title, url, require_approval FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'].'';
    $result = $db->sql_query($sql);
    $character['roleplay'] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = 'SELECT count(*) as sightings FROM rpg_content_tags WHERE character_id = '.(int) $character['id'];
    $contentResult = $db->sql_query($sql);
    $character['sightings'] = $db->sql_fetchfield('sightings');
    $db->sql_freeresult($contentResult);
    
    $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['creator'];
    $contentResult = $db->sql_query($sql);
    $character['creator'] = $db->sql_fetchrow($contentResult);
    $db->sql_freeresult($contentResult);

	  $template->assign_block_vars('characters', array(
		  'ID' 				=> $character['id'],
		  'NAME' 				=> $character['name'],
		  'URL' 				=> $character['url'],
		  'CREATOR_USERNAME'	=> get_username_string('full', $character['creator']['user_id'], $character['creator']['username']),
		  'SYNOPSIS'			=> $character['synopsis'],
      'SIGHTINGS'     => $character['sightings'],
      'ROLEPLAY_URL' 					=> $character['roleplay']['url'],
      'ROLEPLAY_TITLE' 					=> $character['roleplay']['title'],
	  ));

  }

  page_header('Orphanage | '. $roleplay['title'] . ' | ' . $config['sitename']);

  $template->set_filenames(array(
    'body' => 'roleplay_orphanage.html')
  );

  page_footer();

}
$db->sql_freeresult($result);


?>
