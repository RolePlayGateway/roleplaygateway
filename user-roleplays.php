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

  $sql = 'SELECT id, title, description, url, status FROM rpg_roleplays WHERE owner = '.(int) $userAccount['user_id'].'';
  $roleplayResult = $db->sql_query($sql);
  while ($roleplay = $db->sql_fetchrow($roleplayResult)) {
  
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
        'ID' => $roleplay['id'],
        'URL' => $roleplay['url'],
        'TITLE' => $roleplay['title'],
        'SYNOPSIS' => $roleplay['description'],
        'STATUS' => '<span style="background:'.$roleplay['statusColor'].'; border-radius: 3px; text-transform: uppercase; font-size: 0.7em; padding-left: 5px; padding-right: 5px;">'.$roleplay['status'] . '</span>',
    ));

  }

  page_header($username . '\'s Roleplays | ' . $config['sitename']);

  $template->set_filenames(array(
    'body' => 'user_roleplays.html')
  );

  page_footer();

}
$db->sql_freeresult($result);


?>
