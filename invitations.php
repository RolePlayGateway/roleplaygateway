<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

$senderID = $user->data['user_id'];
$subject = 'Build a new world with me?';

$message = request_var('message', '');
$email = request_var('email', '');

$invitation = array(
	'from' => (int) $user->data['user_id'],
	'email' => $email,
	'hash' => md5($email),
	'secret' => md5(rand()),
);

if ($_POST['email']) {
  $sql = 'INSERT INTO rpg_invitations (
		`from`,
		email,
		hash,
		secret
	) VALUES (
		'.$invitation['from'].',
		"'.$db->sql_escape($invitation['email']).'",
		"'.$db->sql_escape($invitation['hash']).'",
		"'.$db->sql_escape($invitation['secret']).'"
	)';
  $db->sql_query($sql);
	
	$sql = 'SELECT user_id as id, username, user_lang, user_email, user_notify_type, user_last_notified FROM gateway_users WHERE user_id = '. (int) $senderID;
	$result = $db->sql_query($sql);
	$sender = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	$sql = 'SELECT user_id as id, username, user_lang, user_email, user_notify_type, user_last_notified FROM gateway_users WHERE user_email = "'.$db->sql_escape($targetEmail).'"';
	$result = $db->sql_query($sql);
	$existingUser = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if (!empty($existingUser['id'])) {
		return;
	}
	
	$target = array(
		'email' => $email,
		'username' => 'Writer',
	);

	$messenger = new messenger(false);
	$messenger->is_email = true;

	$messenger->template('invitation', 'en');

	$messenger->to($target['email'], $target['username']);

	$messenger->assign_vars(array(
		'USERNAME'		=> htmlspecialchars_decode($existingUser['username']),
		'USERNAME_LINK'		=> 'https://www.roleplaygateway.com/member/' . htmlspecialchars_decode($existingUser['username']),
		'SENDING_USERNAME'		=> htmlspecialchars_decode($sender['username']),
		'SENDING_USERNAME_LINK'		=> 'https://www.roleplaygateway.com/member/' . htmlspecialchars_decode($sender['username']),
		'SUBJECT'     => $subject,
		'TARGET_RESOURCE' => 'https://www.roleplaygateway.com/invitations/' . $invitation['hash'] . '?secret='.$invitation['secret']
	));

	$messenger->send();

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

/*
function sendInvitationFrom ($senderID, $targetEmail, $message = '') {

	
	//$sql = 'UPDATE gateway_users SET user_last_notified = CURRENT_TIMESTAMP() WHERE user_id = '.(int) $target['id'];
	//$db->sql_query($sql);
}
*/

?>
