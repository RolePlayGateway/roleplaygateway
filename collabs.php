<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'etherpad-lite-client.' . $phpEx);
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$roleplayURL = $_REQUEST['roleplayURL'];
$start      = (int) $_REQUEST['start'];

$sql = 'SELECT id, title, url, require_approval FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$padName = uniqid();
$groupMapper = md5('roleplay_1');
$groupID      = 'g.exU5Fc1wD9NSnbAo';

$etherpad = new EtherpadLiteClient('SOjfxWgt3pjT2w1RC2WgXBTApMFDGr9C');

//die(json_encode($etherpad->listPads($groupID)));

$userObject           = $etherpad->createAuthorIfNotExistsFor($user->data['user_id'], $user->data['username']);
$userGroupObject      = $etherpad->createGroupIfNotExistsFor(md5('user_' . $user->data['user_id']));
$groupObject          = $etherpad->createGroupIfNotExistsFor(md5('roleplay_' . $roleplay['id']));

$groupPad             = $etherpad->createGroupPad($groupObject->groupID, $padName);
$sessionObject        = $etherpad->createSession($groupObject->groupID, $userObject->authorID, time() + (7 * 24 * 60 * 60) );
//$sessionObject->padName = $padName;
//setcookie('sessionID', $sessionObject->sessionID);

//die(json_encode($etherpad->listPads($groupID));
die(json_encode($sessionObject));
die(json_encode($userObject->authorID));

page_header('Collaborations | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'roleplay_collabs.html'
	)
);

page_footer();
?>