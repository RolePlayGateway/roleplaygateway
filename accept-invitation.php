<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_medals.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

$invitationHash = request_var('invitationID', '');
$secret = request_var('secret', '');

$sql = 'SELECT * FROM rpg_invitations
	WHERE hash = "'.$db->sql_escape($invitationHash).'"
	AND secret = "'.$db->sql_escape($secret).'"
	';
$result = $db->sql_query($sql);
$invitation = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (empty($invitation['id'])) {
	return trigger_error('Unknown or expired invitation!');
}

meta_refresh(3, '/ucp.php?mode=register&amp;invitation='. $invitation['hash'] . '&amp;' . 'secret='.$secret);
trigger_error('Invitation accepted!  Standby...');

?>
