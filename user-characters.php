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

$username = $_REQUEST['username'];

if (!empty($username)) {
  $sql = 'SELECT user_id, username FROM gateway_users WHERE username = "'.$db->sql_escape($username).'"';
}

$result = $db->sql_query($sql);
if ($userAccount = $db->sql_fetchrow($result)) {

  $template->assign_vars(array(
    'USERNAME' 					=> $userAccount['username'],
  ));

  $sql = 'SELECT id, name, synopsis, url, status, roleplay_id FROM rpg_characters WHERE owner = '.(int) $userAccount['user_id'].'';
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

	  $template->assign_block_vars('characters', array(
		  'ID' 				=> $character['id'],
		  'NAME' 				=> $character['name'],
		  'URL' 				=> $character['url'],
		  'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $character['username']),
		  'SYNOPSIS'			=> $character['synopsis'],
      'SIGHTINGS'     => $character['sightings'],
      'ROLEPLAY_URL' 					=> $character['roleplay']['url'],
      'ROLEPLAY_TITLE' 					=> $character['roleplay']['title'],
	  ));

  }

  page_header($username . '\'s Characters | ' . $config['sitename']);

  $template->set_filenames(array(
    'body' => 'user_characters.html')
  );

  page_footer();

}
$db->sql_freeresult($result);


?>
