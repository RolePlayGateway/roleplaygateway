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

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

$invitationHash = request_var('invitationHash', 0);

$sql = 'SELECT * FROM rpg_invitations WHERE secret = "'.$db->sql_escape($invitationHash).'"';
$result = $db->sql_query($sql);
$invitation = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$template->assign_vars(array(
  'ID' => $invitation['id'],
  'EMAIL' => md5($invitation['email']),
  'EMAIL_RAW' => $invitation['email'],
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'invitations.html'
));

page_footer();

?>
