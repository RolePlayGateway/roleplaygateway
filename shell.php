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

$roleplayURL = request_var('roleplayURL', '');

$sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id, views as total FROM rpg_characters c
      WHERE owner = ".(int) $user->data['user_id']."
      ORDER BY total DESC
      /* LIMIT 32 */";
$result = $db->sql_query($sql);
while ($character = $db->sql_fetchrow($result)) {
  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
  $ownerResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($ownerResult);

  $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);

  $template->assign_block_vars('characters', array(
    'ID'              => $character['id'],
    'NAME'            => $character['name'],
    'URL'             => $character['url'],
    'LINK'            => '/roleplay/'.$roleplay['url'] .'/characters/' . $character['url'],
    'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
    'SYNOPSIS'        => $character['synopsis'],
    'ROLEPLAY_ID'    => $roleplay['id'],
    'ROLEPLAY_URL'    => $roleplay['url'],
    'TOTAL'           => $character['views'],
  ));
}

// Output page
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - META
$seo_meta->collect('description', $config['sitename'] . ' : ' .  $config['site_desc']);
$seo_meta->collect('keywords', $config['sitename'] . ' ' . $seo_meta->meta['description']);
// www.phpBB-SEO.com SEO TOOLKIT END - META
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - TITLE
page_header($config['sitename']);
// www.phpBB-SEO.com SEO TOOLKIT END - TITLE

$template->assign_vars(array(
  'S_PAGE_ONLY' => true
));

$template->set_filenames(array(
	'body' => 'shell.html')
);

page_footer();

?>
