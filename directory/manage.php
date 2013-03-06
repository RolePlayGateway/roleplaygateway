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

page_header('Manage Your Links on RolePlayGateway');

$mode = request_var('mode', '');

if ($user->data['user_id'] != 4) {
	trigger_error('You are not authorized.');
}

switch ($mode) {

	default:
	
		if ($user->data['user_id'] != 4) {
			$owner_sql = " WHERE owner = ".$user->data['user_id'];
		} else {
			$owner_sql = " WHERE approved = 0";
		}
	
		$sql = "SELECT * FROM directory_links".$owner_sql;
		$result = $db->sql_query_limit($sql, 3600);
		while($row = $db->sql_fetchrow($result)) {

			$template->assign_block_vars('owned_sites', array(
				'ID'			=> $row['id'],
				'FEATURED'		=> $row['featured'],
				'TITLE'			=> $row['title'],
				'DESCRIPTION'	=> $row['description'],
				'URL'			=> $row['url']."<br>ID: ".$row['id']."<br>Owner: ".$row['owner']."<br>Approved: ".$row['approved'],
			));
		}
			
	
		$template->set_filenames(array(
			'body' => 'directory_manage_body.html',)
		);
	break;
	
	case 'approve':
	
		$sql = "SELECT * FROM directory_links WHERE id = '".$db->sql_escape($_REQUEST['id'])."' AND approved = 0";
		$result = $db->sql_query_limit($sql, 3600);
		while($row = $db->sql_fetchrow($result)) {
		
			$sql = "UPDATE directory_links SET approved = 1 WHERE id = '".$db->sql_escape($_REQUEST['id'])."'";
			$result = $db->sql_query_limit($sql, 3600);
		
			$sql = "SELECT user_email FROM gateway_users WHERE user_id = ".$row['owner'];
			$email_result = $db->sql_query_limit($sql, 3600);
			while($email_row = $db->sql_fetchrow($email_result)) {
				$owner_email = $email_row['user_email'];			
			}
			
			$to = $owner_email;
			$subject = 'RPG Directory: Link Approved ('.$row['title'].')'; 
			$message = "Congratulations! The link you submitted to RolePlayGateway's RPG Directory ( http://www.roleplaygateway.com/directory ) has been approved.  Your users can now view, rate, and review your site via <a href=\"http://www.roleplaygateway.com/directory/view.php?id=".$_REQUEST['id']."\">your RPG Directory Listing</a>.";
			$headers = "From: admin@roleplaygateway.com\r\nReply-To: admin@roleplaygateway.com\r\nContent-type: text/html";
			$mail_sent = @mail( $to, $subject, $message, $headers );
			
			trigger_error("Successfully approved that link.");
			
		}
		
		trigger_error("Didn't fall into code...");
		
	break;	
	
	case 'deny':
	
		$sql = "SELECT * FROM directory_links WHERE id = '".$db->sql_escape($_REQUEST['id'])."' AND approved = 0";
		$result = $db->sql_query($sql);
		while($row = $db->sql_fetchrow($result)) {
		
			$sql = "DELETE FROM directory_links WHERE id = '".$db->sql_escape($_REQUEST['id'])."'";
			$result = $db->sql_query($sql);
			
			$sql = "SELECT user_email FROM gateway_users WHERE user_id = ".$row['owner'];
			$email_result = $db->sql_query($sql);
			while($email_row = $db->sql_fetchrow($email_result)) {
				$owner_email = $email_row['user_email'];			
			}
			
			$to = $owner_email;
			$subject = 'RPG Directory: Link Denied ('.$row['title'].')'; 
			$message = "We're sorry, the link you submitted to RolePlayGateway's RPG Directory ( http://www.roleplaygateway.com/directory ) has been denied.";
			$headers = "From: admin@roleplaygateway.com\r\nReply-To: admin@roleplaygateway.com";
			$mail_sent = @mail( $to, $subject, $message, $headers );
			
			trigger_error("Successfully denied that link.");
			
		}
		
		trigger_error("Didn't fall into code...");
		
	break;

}

page_footer();

?>