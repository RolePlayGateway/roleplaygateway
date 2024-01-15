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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Content-Type: application/json');
  echo json_encode(array(
    'resources' => array(
      array(
        'name' => 'Universe'
      )
    )
  ));
  exit();
}

include('includes/featured-users.php');

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $user->data['user_id'];
$creditResult = $db->sql_query($sql);
$credits = $db->sql_fetchrow($creditResult);
$db->sql_freeresult($creditResult);

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $user->data['user_id'];
$debitResult = $db->sql_query($sql);
$debits = $db->sql_fetchrow($debitResult);
$db->sql_freeresult($debitResult);

$userBalance = $credits['total'] - $debits['total'];

$sql = "SELECT DISTINCT r.id,
			/* log(s.average_words) * log(1 / (unix_timestamp() - unix_timestamp(r.updated))) as ranking, */
			id,title,type,description,owner,player_slots,username,require_approval,updated,created FROM rpg_roleplays r
			INNER JOIN gateway_tags t
				ON r.id = t.roleplay_id
			INNER JOIN gateway_users u
				ON r.owner = u.user_id
			INNER JOIN rpg_roleplay_stats s
				ON s.roleplay_id = r.id
			WHERE r.status = 'Open' AND
				( s.posts > 0
				AND s.average_words > 5
				AND s.average_grade_level > 5
				AND length(r.image) > 0
				AND length(r.description) > 5
				AND t.tag = 'original' ) OR featured = 1
			/* ORDER BY r.updated DESC */
			ORDER BY id DESC";
/* $sql = 'select id,title,type,description,owner,player_slots,require_approval,updated FROM rpg_roleplay_stats s INNER JOIN rpg_roleplays r ON s.roleplay_id = r.id
  WHERE posts > 50  ORDER BY average_words DESC'; */
$result = $db->sql_query_limit($sql, 3, null, 3600);

while($row = $db->sql_fetchrow($result)) {
	$roleplays[$row['id']] = $row;
}

uasort($roleplays, function($a, $b) {
	return strtotime($b['created']) - strtotime($a['created']);
});

foreach ($roleplays as $row) {

  $sql          = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $row['owner'] ;
  $countResult  = $db->sql_query($sql);
  $row['username'] = $db->sql_fetchfield('username');
  $db->sql_freeresult($countResult);


	$template->assign_block_vars('roleplays', array(
		'S_CAN_EDIT'		=> (($auth->acl_get('a_')) || ($row['owner'] == $user->data['user_id'])) ? true : false,
		'ID'				=> $row['id'],
		'TITLE'				=> $row['title'],
		'URL'				=> urlify($row['title']),
		'DESCRIPTION'		=> $row['description'],
		'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
		'TOTAL_SLOTS'		=> $row['player_slots'],
		'CHARACTERS'		=> $row['characters'],
		'POSTS'				=> number_format($row['posts']),
		'WORDS_PER_POST' 	=> $row['words_per_post'],
		'TYPE'				=> $row['type'],
		//'TYPE_DESCRIPTION'	=> @$type_description,
		//'ACTIONS'			=> @$actions,
		//'TAGS'				=> @display_roleplay_tags(get_roleplay_tags($row['id'])),
		'LAST_ACTIVITY'		=> $user->format_date($row['last_activity']),
	));
}
// free the result
$db->sql_freeresult($result);

// BEGIN PLACES LIST
$start      = (int) @$_REQUEST['start'];

$places = array();

/*$placeIDs = array();
$sql = 'SELECT c.place_id as id FROM rpg_content c
	INNER JOIN rpg_places p ON p.id = c.place_id
	WHERE c.roleplay_id > 1
	ORDER BY c.id DESC
	LIMIT 10000';
$activityResult = $db->sql_query($sql);
while ($place = $db->sql_fetchrow($activityResult)) {
	$placeIDs[] = $place['id'];
}
$db->sql_freeresult($activityResult);*/

$sql = 'SELECT p.id, p.name, p.synopsis, p.owner, p.url, p.parent_id, p.roleplay_id
	FROM rpg_places p
	WHERE roleplay_id NOT IN (1/*, 31154*/)
		AND length(p.image) > 0 AND length(p.synopsis) > 4
	ORDER BY last_activity DESC';
$result = $db->sql_query_limit($sql, 10);
while ($row = $db->sql_fetchrow($result)) {

  $sql = 'SELECT count(id) as posts, UNIX_TIMESTAMP(max(written)) as lastPostTime, author_id, max(id) as id FROM rpg_content
    WHERE place_id = '.(int) $row['id']. '
    ORDER BY written DESC LIMIT 1';
  $contentResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($contentResult);

  $row['posts']           = $stats['posts'];
  $row['lastPostTime']    = $stats['lastPostTime'];
  $row['lastPostID']      = $stats['id'];
  $row['lastPostAuthor']  = $stats['author_id'];

  $db->sql_freeresult($contentResult);

  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $row['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $row['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $sql = 'SELECT id, name, synopsis, url FROM rpg_places WHERE id = '.(int) $row['parent_id'];
  $parentResult = $db->sql_query($sql, 3600);
  $parent = $db->sql_fetchrow($parentResult);
  $db->sql_freeresult($parentResult);

  $row['parent_id'] = $parent['id'];
  $row['parent_name'] = $parent['name'];
  $row['parent_synopsis'] = $parent['synopsis'];
  $row['parent_slug'] = $parent['url'];

  $sql = 'SELECT count(*) as number FROM rpg_characters WHERE location = '.(int) $row['id'];
  $characterResult = $db->sql_query($sql, 3600);
  $character = $db->sql_fetchrow($characterResult);
  $db->sql_freeresult($characterResult);

  $row['characterCount'] = $character['number'];

  $places[ $row['id'] ] = $row;
}
$db->sql_freeresult($result);

uasort($places, function($a, $b) {
  return $b['lastPostTime'] - $a['lastPostTime'];
});

foreach ($places as $row) {
	$sql = 'SELECT id, title, url FROM rpg_roleplays WHERE id = '.(int) $row['roleplay_id'];
	$roleplayResult = $db->sql_query($sql, 3600);
	$roleplay = $db->sql_fetchrow($roleplayResult);
	$db->sql_freeresult($roleplayResult);

  $template->assign_block_vars('places', array(
    'ID'              => $row['id'],
    'NAME'            => $row['name'],
    'URL'             => $row['url'],
    'OWNER_USERNAME'  => $row['owner_username'],
    'SYNOPSIS'        => $row['synopsis'],
    'POSTS'           => $row['posts'],
    'PARENT_ID'       => $row['parent_id'],
    'PARENT_NAME'     => $row['parent_name'],
    'PARENT_SLUG'     => $row['parent_slug'],
    'PARENT_SYNOPSIS' => $row['parent_synopsis'],
    'LAST_POST_TIME'  => $row['lastPostTime'],
    'LAST_POST_DATE'  => timeAgo($row['lastPostTime']),
    'LAST_POST_ID'    => $row['lastPostID'],
    'CHARACTER_COUNT' => $row['characterCount'],
		'ROLEPLAY_URL'    => $roleplay['url'],
		'ROLEPLAY_NAME'    => $roleplay['title'],
  ));
}

$buyableItems = array();
$sql = 'SELECT * FROM rpg_orders WHERE asset LIKE "/instances/%" AND status = "Open" ORDER BY rand()';
$ordersResult = $db->sql_query($sql);
while ($order = $db->sql_fetchrow($ordersResult)) {
  $parts = explode('/', $order['asset']);

  switch ($parts[1]) {
    case 'places':
      $sql = 'SELECT id, name, synopsis, owner, url, roleplay_id FROM rpg_places WHERE id = '.(int) $parts[2];
      break;
    case 'characters':
      $sql = 'SELECT id, name, synopsis, owner, url, roleplay_id FROM rpg_characters WHERE id = '.(int) $parts[2];
      break;
    case 'instances':
      $sql = 'SELECT a.id, a.name, a.description as synopsis, a.creator, a.slug as url, a.roleplay_id FROM rpg_items a
        INNER JOIN rpg_item_instances i
          ON a.id = i.item_id
        WHERE i.id = '.(int) $parts[2];
      break;
  }

  $assetResult = $db->sql_query($sql);
  $asset = $db->sql_fetchrow($assetResult);
  $db->sql_freeresult($assetResult);

  $universeResult = $db->sql_query('SELECT id, title, url FROM rpg_roleplays WHERE id = '.(int) $asset['roleplay_id']);
  $universe = $db->sql_fetchrow($universeResult);
  $db->sql_freeresult($universeResult);

  switch ($parts[1]) {
    case 'places':
      $asset['type'] = 'Location';
      $asset['link'] = '/universes/'.$universe['url'].'/'.$parts[1].'/'.$asset['url'];
      $asset['image'] = '/universes/'.$universe['url'].'/places/'.$asset['url'].'/image';
      break;
    case 'characters':
      $asset['type'] = 'Character';
      $asset['link'] = '/universes/'.$universe['url'].'/'.$parts[1].'/'.$asset['url'];
      $asset['image'] = '/universes/'.$universe['url'].'/characters/'.$asset['url'].'/image';
      break;
    case 'instances':
      $asset['type'] = 'Item';
      $asset['link'] = '/items/'.$asset['id']; // https://www.gravatar.com/avatar/94d093eda664addd6e450d7e9881bcad?s=100&d=identicon&r=PG
      $asset['image'] = '/universes/'.$universe['url'].'/items/'.$asset['id'].'/image';
      break;
  }

  $buyableItems[] = array(
    'ID' => $order['id'],
    'CREATOR' => $order['creator'],
    'CREATED' => $order['created'],
    'ASSET' => $order['asset'],
    'PRICE' => money_format('%i', $order['price']),
    'PRICE_RAW' => $order['price'],
    'STATUS' => $order['status'],
    'S_CAN_EDIT'      => ($order['creator'] == $user->data['user_id']) ? true : false,
    'S_CAN_AFFORD'    => (($userBalance >= $order['price']) && ($user->data['is_registered'])) ? true : false,
    'SALE_PRICE'      => (($order['price'] == 0.0) || $order['price'] < 1 && $order['price'] > 0) ? money_format('%.8n', $order['price']) : money_format('%i', $order['price']),
    'ORDER_ID'        => $order['id'],
    'ASSET_NAME'      => $asset['name'],
    'ASSET_TYPE'      => $asset['type'],
    'ASSET_DESCRIPTION' => $asset['synopsis'],
    'ASSET_CONTEXT'   => $roleplay['title'],
    'ASSET_CONTEXT_LINK'   => '/universes/'.$roleplay['url'],
    'ASSET_LINK'      => $asset['link'],
    'ASSET_IMG_LINK'  => ($asset['image']) ? $asset['image'] : $asset['link'] . '/image',
  );
}
$db->sql_freeresult($ordersResult);

foreach ($buyableItems as $place) {
  $template->assign_block_vars('buyable_items', $place);
}

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

// Output page
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - META
$seo_meta->collect('description', 'Roleplay with your friends on RolePlayGateway — collaborative storytelling, adventure, and more!');
$seo_meta->collect('keywords', 'roleplay, role play, roleplaying, role playing');
// www.phpBB-SEO.com SEO TOOLKIT END - META
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - TITLE
page_header($config['sitename'] . ' &bull; storytelling &amp; worldbuilding');
// www.phpBB-SEO.com SEO TOOLKIT END - TITLE

$template->set_filenames(array(
	'body' => 'front-page.html')
);

page_footer();

?>
