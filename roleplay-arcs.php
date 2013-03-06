<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

//ini_set('display_errors', true);
//error_reporting(E_ALL);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$roleplayID = (int) $_REQUEST['roleplayID'];
$roleplayURL = $_REQUEST['roleplayURL'];


if (!empty($roleplayURL)) {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
} else {
  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $roleplayID;
}

$result = $db->sql_query($sql);
if ($roleplay = $db->sql_fetchrow($result)) {

  $template->assign_vars(array(
    'URL'             => $roleplay['url'],
    'ROLEPLAY_ID'     => $roleplay['id'],
    'ROLEPLAY_URL'    => $roleplay['url'],
    'ROLEPLAY_TITLE'  => $roleplay['title'],
    'ROLEPLAY_NAME'   => $roleplay['title'],
    'S_PAGE_ONLY'     => true,
  ));

  $sql = 'SELECT id, name, description, slug, creator FROM rpg_arcs WHERE roleplay_id = '.(int) $roleplay['id'];
  $arcResult = $db->sql_query($sql);
  while ($arc = $db->sql_fetchrow($arcResult)) {
  
	  $template->assign_block_vars('arcs', array(
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

  }
  $db->sql_freeresult($arcResult);


  page_header($arc['name'] . ' | ' . $roleplay['title'] . ' | ' . $config['sitename']);

  $template->set_filenames(array(
    'body' => 'rpg_arcs.html')
  );

  page_footer();

}
$db->sql_freeresult($result);


?>
