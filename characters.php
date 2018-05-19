<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);


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

$roleplayID = $roleplay['id'];
$limit = 54;

if ($roleplay['require_approval'] == 1) {
	$sql = "SELECT id,name,username,user_id,owner,synopsis,url FROM rpg_characters c
			INNER JOIN gateway_users u ON c.owner = u.user_id
		WHERE c.roleplay_id = ".$db->sql_escape($roleplayID). " AND c.owner <> 0 AND c.status = 'Approved' AND c.isAdoptable = 0 ORDER BY id ASC";
} else {
	$sql = "SELECT id,name,username,user_id,owner,synopsis,url FROM rpg_characters c
				INNER JOIN gateway_users u ON c.owner = u.user_id
			WHERE c.roleplay_id = ".$db->sql_escape($roleplayID) . " AND c.owner <> 0 AND isAdoptable = 0 ORDER BY id ASC";
}

$result = $db->sql_query_limit($sql, $limit, $start);
while ($row = $db->sql_fetchrow($result)) {

  $sql = 'SELECT count(*) as sightings FROM rpg_content_tags WHERE character_id = '.(int) $row['id'];
  $contentResult = $db->sql_query($sql);
  $row['sightings'] = $db->sql_fetchfield('sightings');
  $db->sql_freeresult($contentResult);

	$template->assign_block_vars('characters', array(
		'ID' 				=> $row['id'],
		'NAME' 				=> $row['name'],
		'URL' 				=> $row['url'],
		'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
		'SYNOPSIS'			=> $row['synopsis'],
    'SIGHTINGS'     => $row['sightings']
	));
}
$db->sql_freeresult($result);

if ($roleplay['require_approval'] == 1) {
	$sql = "SELECT count(*) as count FROM rpg_characters c
			INNER JOIN gateway_users u ON c.owner = u.user_id
		WHERE c.roleplay_id = ".$db->sql_escape($roleplayID). " AND c.owner <> 0 AND c.status = 'Approved' AND c.isAdoptable = 0 ORDER BY id ASC";
} else {
	$sql = "SELECT count(*) as count FROM rpg_characters c
				INNER JOIN gateway_users u ON c.owner = u.user_id
			WHERE c.roleplay_id = ".$db->sql_escape($roleplayID) . " AND c.owner <> 0 AND isAdoptable = 0 ORDER BY id ASC";
}
$result = $db->sql_query($sql);
$roleplay['characters'] = $db->sql_fetchfield('count');
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_MORE_CHARACTERS'		=> (@$roleplay['characters'] > $limit) ? true : false,
	'MORE_CHARACTERS_COUNT'	=> (@$roleplay['characters'] > $limit) ? @$roleplay['characters'] - $limit : null,
	)
);

page_header('Characters | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'roleplay_characters.html'
	)
);

$template->assign_vars(array(
    'PAGINATION'        => generate_pagination($pagination_url, $roleplay['characters'], $limit, $start),
    'PAGE_NUMBER'       => on_page($roleplay['characters'], $limit, $start),
    'TOTAL_CHARACTERS'       => (int) $roleplay['characters'],
    'ROLEPLAY_NAME'     => $roleplay['title'],
    'ROLEPLAY_URL'    => $roleplay['url']
));


page_footer();
