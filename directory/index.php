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

page_header('Places to Roleplay on RolePlayGateway');


$limit = 1000;
$pagination_url = append_sid($phpbb_root_path . 'directory/');


$start   = request_var('start', 0);
$limit   = request_var('limit', (int) $limit);

// no result rows greater than 100 per page
//$limit = ($limit > 25) ? 25 : $limit;

$sql = "SELECT * FROM directory_categories";
$result = $db->sql_query_limit($sql, 3600);
while($row = $db->sql_fetchrow($result)) {

	$template->assign_block_vars('categories', array(
		'ID'			=> $row['id'],
		'NAME'			=> $row['name'],
		'DESCRIPTION'	=> $row['description']
	));
}

$sql = "SELECT l.id,title,description,url, sum(r.rating) / count(r.id) as rating
		FROM directory_links l 
			LEFT JOIN directory_reviews r ON l.id = r.site_id
		WHERE featured = 1 AND approved = 1 AND status = 'UP'
		GROUP BY l.id
		ORDER BY rating DESC";
$result = $db->sql_query_limit($sql, $limit, $start, 3600);
while($row = $db->sql_fetchrow($result)) {

	$template->assign_block_vars('featured_sites', array(
		'ID'			=> $row['id'],
		'TITLE'			=> $row['title'],
		'DESCRIPTION'	=> $row['description'],
		'RATING'		=> round($row['rating'],1),
		'REVIEWS'		=> round($row['reviews']),
		'URL'			=> $row['url'],
	));
}

$sql = "SELECT l.id,title,description,url,avg(r.rating) as rating, avg(r.rating) * log(views) as score,count(r.id) as reviews
		FROM directory_links l 
			LEFT JOIN directory_reviews r ON l.id = r.site_id
			LEFT JOIN gateway_user_stats s on r.user_id = s.user_id
		WHERE featured != 1 AND approved = 1 AND status = 'UP'
		GROUP BY l.id
		ORDER BY score DESC, rating desc, reviews DESC, views DESC, id ASC";
$result = $db->sql_query_limit($sql, $limit, $start, 3600);
while($row = $db->sql_fetchrow($result)) {

	$template->assign_block_vars('listed_sites', array(
		'ID'			=> $row['id'],
		'TITLE'			=> $row['title'],
		'DESCRIPTION'		=> $row['description'],
		'RATING'		=> round($row['rating'],1),
		'REVIEWS'		=> round($row['reviews']),
		'URL'			=> $row['url'],
	));
}

$total_sites = 100;

// Assign the pagination variables to the template.
$template->assign_vars(array(
    'PAGINATION'        => generate_pagination($pagination_url, $total_sites, $limit, $start),
    'PAGE_NUMBER'       => on_page($total_sites, $limit, $start),
    'TOTAL_USERS'       => ($total_users == 1) ? $user->lang['LIST_USER'] : sprintf($user->lang['LIST_USERS'], $total_users),
));

$template->set_filenames(array(
	'body' => 'directory_body.html',)
);

page_footer();

?>
