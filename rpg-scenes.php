<?php
/**
* @package phpBB3
* @version $Id$
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Idea and original RSS Feed 2.0 MOD (Version 1.0.8/9) by leviatan21
* Original MOD: http://www.phpbb.com/community/viewtopic.php?f=69&t=1214645
* MOD Author Profile: http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
* MOD Author Homepage: http://www.mssti.com/phpbb3/
*
**/

/**
* @ignore
**/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$sql = 'SELECT count(*) as placeCount FROM rpg_places WHERE owner = '.(int) $user->data['user_id'];
$placeResult = $db->sql_query($sql);
$placeStatRow = $db->sql_fetchrow($placeResult);
$db->sql_freeresult($placeResult);

$characters = array();
$places = array();

$sql = 'SELECT c.id, c.name, c.url as slug, r.id as roleplay_id, r.title as roleplay_name, r.url as roleplay_slug FROM rpg_characters c
  INNER JOIN rpg_roleplays r
    ON r.id = c.roleplay_id
  WHERE c.owner = '.(int) $user->data['user_id'];
$charactersResult = $db->sql_query($sql);
while ($character = $db->sql_fetchrow($charactersResult)) {
  $characters[] = $character;
}
$db->sql_freeresult($charactersResult);


$sql = 'SELECT id, name, synopsis, owner, url, parent_id, roleplay_id FROM rpg_places
  ORDER BY last_activity DESC';
$placeCollection = $db->sql_query_limit($sql, 50, 0);
while ($place = $db->sql_fetchrow($placeCollection)) {
  $sql = 'SELECT count(*) as posts, UNIX_TIMESTAMP(max(written)) as lastPostTime, author_id, max(id) as id FROM rpg_content
    WHERE place_id = '.(int) $place['id']. '
      AND deleted IS NULL
    ORDER BY written DESC LIMIT 1';
  $contentResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($contentResult);

  $place['posts']           = number_format($stats['posts']);
  $place['lastPostTime']    = $stats['lastPostTime'];
  $place['lastPostID']      = $stats['id'];
  $place['lastPostAuthor']  = $stats['author_id'];

  $db->sql_freeresult($contentResult);

  $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $place['roleplay_id'];
  $roleplayResult = $db->sql_query($sql, 3600);
  $roleplay = $db->sql_fetchrow($roleplayResult);
  $db->sql_freeresult($roleplayResult);

  $place['roleplay_id'] = $roleplay['id'];
  $place['roleplay_name'] = $roleplay['title'];
  $place['roleplay_synopsis'] = $roleplay['description'];
  $place['roleplay_slug'] = $roleplay['url'];

  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $place['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $place['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $sql = 'SELECT c.*,u.user_id,u.username,p.name as place, p.url, p.url as place_url FROM rpg_content c
        INNER JOIN rpg_content_tags t ON c.id = t.content_id
        LEFT OUTER JOIN gateway_users u
          ON c.author_id = u.user_id
        LEFT OUTER JOIN rpg_places p
          ON c.place_id = p.id
        WHERE c.place_id = '.(int) $place['id'] . '
          AND c.deleted IS NULL
        ORDER BY c.written DESC LIMIT 1';
  $contentResult = $db->sql_query($sql);
  $latestPost = $db->sql_fetchrow($contentResult);
  $db->sql_freeresult($contentResult);

  $place['lastPostContent'] = generate_text_for_display($latestPost['text'], $latestPost['bbcode_uid'], $latestPost['bbcode_bitfield'], 7);

  $sql = 'SELECT id, name, synopsis, url FROM rpg_places WHERE id = '.(int) $place['parent_id'];
  $parentResult = $db->sql_query($sql, 3600);
  $parent = $db->sql_fetchrow($parentResult);
  $db->sql_freeresult($parentResult);

  $place['parent_id'] = $parent['id'];
  $place['parent_name'] = $parent['name'];
  $place['parent_synopsis'] = $parent['synopsis'];
  $place['parent_slug'] = $parent['url'];

  $sql = 'SELECT count(*) as number FROM rpg_characters WHERE location = '.(int) $place['id'];
  $characterResult = $db->sql_query($sql, 3600);
  $character = $db->sql_fetchrow($characterResult);
  $db->sql_freeresult($characterResult);

  $place['characterCount'] = number_format($character['number']);

  $places[ $place['id'] ] = $place;
}
$db->sql_freeresult($placeCollection);

uasort($places, function($a, $b) {
  return $b['lastPostTime'] - $a['lastPostTime'];
});

$slice      = (int) @$_REQUEST['limit'];
if ($slice > 0) {
  $places = array_slice($places, $start, $limit);
}

foreach ($places as $place) {
  $template->assign_block_vars('places', array(
    'ID'              => $place['id'],
    'NAME'            => $place['name'],
    'URL'             => $place['url'],
    'OWNER_USERNAME'  => $place['owner_username'],
    'SYNOPSIS'        => $place['synopsis'],
    'POSTS'           => $place['posts'],
    'PARENT_ID'       => $place['parent_id'],
    'PARENT_NAME'     => $place['parent_name'],
    'PARENT_SLUG'     => $place['parent_slug'],
    'PARENT_SYNOPSIS' => $place['parent_synopsis'],
    'LAST_POST_TIME'  => $place['lastPostTime'],
    'LAST_POST_DATE'  => timeAgo($place['lastPostTime']),
    'LAST_POST_ID'    => $place['lastPostID'],
    'LAST_POST_CONTENT' => $place['lastPostContent'],
    'CHARACTER_COUNT' => $place['characterCount'],
    'ROLEPLAY_SLUG' => $place['roleplay_slug'],
  ));

  $sql = 'SELECT c.id, c.name, c.url as slug, r.id as roleplay_id, r.title as roleplay_name, r.url as roleplay_slug FROM rpg_characters c
    INNER JOIN rpg_roleplays r
      ON r.id = c.roleplay_id
    WHERE c.location = '.(int) $place['id'] . ' AND c.owner = '.(int) $user->data['user_id'];
  $characterResult = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($characterResult)) {
    $template->assign_block_vars('places.user_characters', array(
      'ID'              => $character['id'],
      'NAME'            => $character['name'],
      'SLUG'             => $character['slug'],
      'ROLEPLAY_ID'       => $character['roleplay_id'],
      'ROLEPLAY_NAME'     => $character['roleplay_name'],
      'ROLEPLAY_SLUG'     => $character['roleplay_slug'],
      'PARENT_SYNOPSIS' => $character['roleplay_synopsis'],
    ));
  }
  $db->sql_freeresult($characterResult);
}

foreach ($characters as $character) {
  $template->assign_block_vars('user_characters', array(
    'ID'              => $character['id'],
    'NAME'            => $character['name'],
    'SLUG'             => $character['slug'],
    'ROLEPLAY_ID'       => $character['roleplay_id'],
    'ROLEPLAY_NAME'     => $character['roleplay_name'],
    'ROLEPLAY_SLUG'     => $character['roleplay_slug'],
    'PARENT_SYNOPSIS' => $character['roleplay_synopsis'],
  ));
}

$template->assign_vars(array(
  'S_PAGE_ONLY'							=> true,
));

page_header('Active Roleplay Scenes &middot; RPG');

$template->set_filenames(array(
  'body' => 'rpg-scenes.html'
));

page_footer();

?>
