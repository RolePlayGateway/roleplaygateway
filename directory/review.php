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

page_header('Review Site');

if ($user->data['user_id'] == ANONYMOUS)
{
    login_box('', "To review a site, you must first log in to your RolePlayGateway account.");
}

$site_id = request_var("id", 0);
$submit = (isset($_POST['submit'])) ? true : false;

if ($submit)
{

	$review_text 	= request_var("review", "");
	$review_rating 	= request_var("rating", 0);
	
	if (strlen($review_text) <= 0) {
		trigger_error("You must provide some review of the site.");			
	}
	if ($review_rating <= 0) {
		trigger_error("You must provide provide a valid rating for this site.");			
	}	

    if (confirm_box(true))
    {
	
		$view_site_url = "http://www.roleplaygateway.com/directory/view.php?id=".$site_id;
		$sql = 'SELECT id FROM directory_reviews WHERE site_id = '.(int) $site_id.' AND user_id = '. (int) $user->data['user_id'] .' LIMIT 1';
		$result = $db->sql_query($sql);
		$review = $db->sql_fetchrow($result);
		
		if ($review['id']) {
			$sql = 'UPDATE directory_reviews SET time = "'.time().'", rating = '.(int) $review_rating.', review = "'.$db->sql_escape($review_text).'" WHERE id = '.$review['id'];
		} else {
			$sql = "INSERT INTO directory_reviews (time,site_id,rating,review,user_id) VALUES (
				'".time()."','".$db->sql_escape($site_id)."','".$db->sql_escape($review_rating)."','".$db->sql_escape($review_text)."','".$user->data['user_id']."'
			)";
		}
		
		
		$db->sql_query($sql);
		
		// TODO: notify owner of new reviews.  // DONE: note, this code is also used in ../../bin/ping_sites.php 
		// TODO: consolidate code into single function.
		$sql = "SELECT id,title,url,username,user_email,owner_email FROM directory_links l LEFT JOIN gateway_users u ON l.owner = u.user_id WHERE id = ".$db->sql_escape($site_id);

		if ($result = $db->sql_query($sql)) {	

			while ($row = $db->sql_fetchrow($result)) {

				if (strlen($row['owner_email']) > 0) {
					$row['user_email'] = $row['owner_email'];
				}
				
				$message = "<p>Hey there, ".$row['username']."!</p>";
				$message .= "<p>It looks like someone shared their opinion about your site in the RPG Directory!</p>";
				$message .= '<p><strong>Go read what they wrote:</strong> <a href="http://www.roleplaygateway.com/directory/view.php?id='.$db->sql_escape($site_id).'">'.$db->sql_escape($row['title']).' in the RPG Directory</a></p>';
				$message .= "<p>Sincerely,</p>";
				$message .= "<p>Eric Martindale<br />Creator and Owner, <a href=\"http://www.roleplaygateway.com\">RolePlayGateway</a></p>";

				$headers = "From: admin@roleplaygateway.com\r\n";
				$headers .= "Reply-To: admin@roleplaygateway.com\r\n";
				$headers .= "Bcc: admin@roleplaygateway.com\r\n";
				$headers .= "Content-type: text/html\r\n";
				
				$host = str_replace("www.","",parse_url($row['url'],PHP_URL_HOST));
				$to = "admin@$host,webmaster@$host,".$row['user_email'];
				
				mail( $to, "Regarding ".$row['title'].": New Opinion!", $message, $headers );			
				
			}	
		}
		
		meta_refresh(3, $view_site_url);
        trigger_error("Thanks for submitting this review!  We'll include it in our main index now.");
		
    }
    else
    {
        $s_hidden_fields = build_hidden_fields(array(
            'submit'    => true,
            'review'    => $review_text,
            'rating'    => $review_rating,
            )
        );
		
		
		$sql = 'SELECT id FROM directory_reviews WHERE site_id = '.(int) $site_id.' AND user_id = '. (int) $user->data['user_id'] .' LIMIT 1';
		$result = $db->sql_query($sql);
		$review = $db->sql_fetchrow($result);
		
		if ($review['id']) {
			$confirm_body = "<strong>Do you want to update your review with the following information?</strong><br /><br /><strongYour Rating:</strong> $review_rating<br /><strong>Your review:</strong><p>$review_text</p>";			
		} else {
			$confirm_body = "<strongYour Rating:</strong> $review_rating<br /><strong>Your review:</strong><p>$review_text</p>";
        }
		
        // display mode
        confirm_box(false, $confirm_body, $s_hidden_fields);
    }
}

$sql = "SELECT * FROM directory_links WHERE id = ".$db->sql_escape($site_id);
$result = ($db->sql_query($sql));
	while ($row = $db->sql_fetchrow($result)) {
	$template->assign_vars(array(
		'SITE_ID'  		=> $site_id,
		'TITLE'  		=> $row['title'],
		'URL'  			=> $row['url'],
		'DESCRIPTION' 	=> $row['description'],
		)
	);	
}


$template->set_filenames(array(
	'body' => 'directory_review.html',)
);

page_footer();

?>