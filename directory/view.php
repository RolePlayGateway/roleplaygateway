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

$site_id = request_var("id", 0);

$sql = "SELECT title,url,description,username,owner,status FROM directory_links l 
			LEFT JOIN gateway_users u ON l.owner = u.user_id
			WHERE id = ".$db->sql_escape($site_id)."
	";
$result = ($db->sql_query($sql));
while ($row = $db->sql_fetchrow($result)) {

        $seo_meta->meta['meta_desc'] = $seo_meta->meta_filter_txt($row['description']);

	
	page_header($row['title'] . ' - Reviews and Opinions');
	
	if ($row['status'] == "UP") {
		$status = '<span style="color:green;">'.$row['status'].'</span>';
	} else {
		$status = '<span style="color:red;">'.$row['status'].'</span>';
	}
	
	$template->assign_vars(array(
		'SITE_ID'  			=> $site_id,
		'TITLE'  			=> $row['title'],
		'URL'  				=> $row['url'],
		'OWNER_USERNAME' 	=> get_username_string('full', $row['owner'], $row['username']),
		'DESCRIPTION' 		=> $row['description'],
		'STATUS' 			=> $status,
		)
	);
}

if ($user->data['is_registered']) {
	$sql = "UPDATE directory_links SET views=views+1 WHERE id = ".$db->sql_escape($site_id);
	$db->sql_query($sql);
}

$sql = "SELECT review,rating,u.user_id,time,username FROM directory_reviews r
			INNER JOIN gateway_users u ON r.user_id = u.user_id
			WHERE site_id = ".$db->sql_escape($site_id)." ORDER BY time DESC";
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result)) {
   $template->assign_block_vars('reviews', array(
      'REVIEW' 		=> nl2br($row['review']),
      'RATING' 		=> $row['rating'],
      'USERNAME' 	=> get_username_string('full', $row['user_id'], $row['username']),
      'TIME' 		=> $user->format_date($row['time']),
   ));
}

$template->set_filenames(array(
	'body' => 'directory_view.html')
);

page_footer();

?>
