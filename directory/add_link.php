<?php

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
// PRS
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

page_header('List Your Site on RolePlayGateway');

$mode = request_var('mode', '');

if ($user->data['user_id'] == ANONYMOUS)
{
    login_box('', $user->lang['LOGIN']);
}

switch ($mode) {

	default:	
		$template->set_filenames(array(
			'body' => 'directory_add_body.html',)
		);
	break;
	
	case 'submit':
		$site_sponsored 	= request_var('site_sponsored','0');
		$site_name 			= request_var('site_name','');
		$site_url			= request_var('site_url', '');
		$site_description 	= request_var('site_description', '');
		$site_owner			= $user->data['user_id'];
		$owner_email		= request_var('owner_email', '');;
		
		if ($site_name != "" && $site_url != "") {

			#Strip off any file after the last /
			$site_url=preg_split('/\//', $site_url, -1);
			if (count($site_url) > 3)
			{
				$site_url[count($site_url)-1]="";
			}
			$site_url=join('/',$site_url);
			#Check it url is already in db
			$sql = "SELECT COUNT(id) as count FROM directory_links WHERE url='".$db->sql_escape($site_url)."'";
                        $result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			if ($row['count']>0) {
		        	trigger_error("This entry already exist. If you believe this to be an error please contact an Admin.");
			}

			$sql = "INSERT INTO directory_links (title,featured,url,description,owner_email,owner) VALUES (
				'".$db->sql_escape($site_name)."',
				'".$db->sql_escape($site_sponsored)."',
				'".$db->sql_escape($site_url)."',
				'".$db->sql_escape($site_description)."',
				'".$db->sql_escape($owner_email)."',
				'".$site_owner."')";
			if ($result = $db->sql_query($sql)) {
			
				$to = 'admin@roleplaygateway.com';
				$subject = 'RPG Directory: New Submission ('.$site_name.')'; 
				$message = "Site URL: ". $site_url;
				$headers = "From: admin@roleplaygateway.com\r\nReply-To: admin@roleplaygateway.com";
				$mail_sent = @mail( $to, $subject, $message, $headers );
			
				trigger_error("Successfully submitted this site for review!");
				
			} else {
				trigger_error("Hrm, there must have been some sort of problem. Try messaging an admin.");
			}
		} else {
			trigger_error("You need to fill out both the <strong>site name</strong> and <strong>site URL</strong> to submit it to the directory. Please go back and try again.");
		}
	break;

}

page_footer();

?>
