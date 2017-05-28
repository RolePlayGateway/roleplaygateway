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

if ($_POST['email']) {
  $sql = 'INSERT INTO rpg_invitations (
		`from`,
		email,
		hash,
		secret
	) VALUES (
		'.(int) $user->data['user_id'].',
		"'.$db->sql_escape($_POST['email']).'",
		"'.$db->sql_escape(md5($_POST['email'])).'",
		"'.$db->sql_escape(md5(rand())).'"
	)';
  $db->sql_query($sql);
  
  meta_refresh(3, '/invitations');
  trigger_error('Invitation sent!  Loading stats...');

}

$sql = 'SELECT * FROM rpg_invitations';
$result = $db->sql_query($sql);
while ($invitation = $db->sql_fetchrow($result)) {
  $template->assign_block_vars('invitations', array(
    'ID' => $invitation['id'],
    'EMAIL' => md5($invitation['email']),
    'EMAIL_RAW' => $invitation['email'],
  ));
}
$db->sql_freeresult($result);

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'invitations.html'
));

page_footer();

?>
