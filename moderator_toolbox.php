<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);

// Start session management
$user->session_begin();         
$auth->acl($user->data);

if (!$auth->acl_get('a_'))
{
        trigger_error('NO_ADMIN');
}


$action = request_var("action","");

switch ($action) {

	case "use_profiles":
			// Notify topic author about topic move.  ~ Eric M 4/13/09
			// note that multibyte support is enabled here 
			
			$my_subject = utf8_normalize_nfc("\"".$row['topic_title']."\" has been moved...");
			$my_text    = utf8_normalize_nfc(request_var('my_text', '', true));
			$my_text    = utf8_normalize_nfc("Hello ".$row['topic_first_poster_name'].",

Thank you for visiting RolePlayGateway.

We would like to recommend that instead of pasting your [url=http://www.roleplaygateway.com/characters/]character profile[/url] into a post, that you utilize your (free) ability to [url=http://www.roleplaygateway.com/ucp.php?i=characters&mode=new]create a new character[/url] and link to their profiles using their vanity URL: [code]http://www.roleplaygateway.com/characters/Your_Character_Name[/code] 

Thank you for your time and cooperation.

Sincerely,

".$user->data['username']."
RolePlayGateway Moderator");

			// variables to hold the parameters for submit_pm
			$poll = $uid = $bitfield = $options = ''; 
			generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
			generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

			$data = array( 
				'address_list'      => array ('u' => array($row['topic_poster'] => 'to')),
				'from_user_id'      => $user->data['user_id'],
				'from_username'     => 'test',
				'icon_id'           => 0,
				'from_user_ip'      => $user->data['user_ip'],
				 
				'enable_bbcode'     => true,
				'enable_smilies'    => true,
				'enable_urls'       => true,
				'enable_sig'        => true,

				'message'           => $my_text,
				'bbcode_bitfield'   => "",
				'bbcode_uid'        => "",
			);

			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
			submit_pm('post', $my_subject, $data, false);	
			// Notify topic author about topic move.  ~ Eric M 4/13/09
			
		break;



}


?>