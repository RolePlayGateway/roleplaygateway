<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$roleplayURL = $_REQUEST['roleplayURL'];
$start      = (isset($_REQUEST['start'])) ? $_REQUEST['start'] : 0;
$limit      = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : 100;
$QUERY_LIMIT = 50;

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $user->data['user_id'];
$creditResult = $db->sql_query($sql);
$credits = $db->sql_fetchrow($creditResult);
$db->sql_freeresult($creditResult);

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $user->data['user_id'];
$debitResult = $db->sql_query($sql);
$debits = $db->sql_fetchrow($debitResult);
$db->sql_freeresult($debitResult);

$userBalance = $credits['total'] - $debits['total'];

$sql = 'SELECT id, title, url, require_approval, owner FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if ($_REQUEST['format'] == 'json') {
  $places = array();
  
  $sql = 'SELECT id, name, roleplay_id, owner, url FROM rpg_places WHERE roleplay_id = '.(int) $roleplay['id'] . ' AND visibility <> "Hidden"';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $places[] = $place;
  }
  
  header('Content-Type: application/json');
  echo(json_encode($places));
  exit();
}

$game_masters = array();
$sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' /*AND isCoGM = 1*/)';
$result = $db->sql_query($sql);
while ($gm_row = $db->sql_fetchrow($result)) {
  $game_masters[] = $gm_row['user_id'];
}
$db->sql_freeresult($result);

$pagination_url = 'https://www.roleplaygateway.com/universes/'.$roleplay['url'].'/places';

$roleplayID = $roleplay['id'];
$limit = (isset($limit)) ? $limit : $QUERY_LIMIT;
//$limit = 2000;

$sql = 'SELECT count(*) as placeCount FROM rpg_places WHERE roleplay_id = '.(int) $roleplayID;
$placeResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($placeResult);
$roleplay['placeCount'] = $row['placeCount'];
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

$sql = 'SELECT id, name, synopsis, owner, url, parent_id FROM rpg_places
          WHERE rpg_places.roleplay_id = '.(int) $roleplayID .' AND id > 0';

$result = $db->sql_query($sql);
while ($place = $db->sql_fetchrow($result)) {
  $sql = 'SELECT count(*) as posts, UNIX_TIMESTAMP(max(written)) as lastPostTime, author_id, max(id) as id FROM rpg_content
    WHERE place_id = '.(int) $place['id']. '
      AND deleted IS NULL
    ORDER BY written DESC LIMIT 1';
  $contentResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($contentResult);

  $place['posts']           = $stats['posts'];
  $place['lastPostTime']    = $stats['lastPostTime'];
  $place['lastPostID']      = $stats['id'];
  $place['lastPostAuthor']  = $stats['author_id'];

  $db->sql_freeresult($contentResult);

  $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $place['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $place['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $sql = 'SELECT c.*,u.user_id,u.username,p.name as place,p.url, p.url as place_url FROM rpg_content c
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

	$content = generate_text_for_display($latestPost['text'], $latestPost['bbcode_uid'], $latestPost['bbcode_bitfield'], 7);
  $place['lastPostContent'] = $content;

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

  $place['characterCount'] = $character['number'];

  $places[ $place['id'] ] = $place;
}
$db->sql_freeresult($result);

uasort($places, function($a, $b) {
  return $b['lastPostTime'] - $a['lastPostTime'];
});

// $slice      = (int) @$_REQUEST['limit'];
// if ($slice > 0) {
  $places = array_slice($places, $start, $limit);
// }

foreach ($places as $row) {
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
    'LAST_POST_CONTENT'    => $row['lastPostContent'],
    'CHARACTER_COUNT' => $row['characterCount'],
  ));
}

foreach ($places as $place) {
  $template->assign_block_vars('user_places', array(
    'ID'              => $place['id'],
    'NAME'            => $place['name'],
    'SLUG'             => $place['slug'],
    'ROLEPLAY_ID'       => $place['roleplay_id'],
    'ROLEPLAY_NAME'     => $place['roleplay_name'],
    'ROLEPLAY_SLUG'     => $place['roleplay_slug'],
    'PARENT_SYNOPSIS' => $place['roleplay_synopsis'],
  ));
}


$buyablePlaces = array();
$sql = 'SELECT * FROM rpg_orders WHERE asset LIKE "/places/%" AND status = "Open" ORDER BY rand()';
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

  if ($asset['roleplay_id'] !== $roleplay['id']) continue;

  switch ($parts[1]) {
    case 'places':
      $asset['type'] = 'Location';
      $asset['link'] = '/universes/'.$roleplay['url'].'/'.$parts[1].'/'.$asset['url'];
      $asset['image'] = '/universes/'.$roleplay['url'].'/places/'.$asset['url'].'/image';
      break;
    case 'characters':
      $asset['type'] = 'Character';
      $asset['link'] = '/universes/'.$roleplay['url'].'/'.$parts[1].'/'.$asset['url'];
      $asset['image'] = '/universes/'.$roleplay['url'].'/characters/'.$asset['url'].'/image';
      break;
    case 'instances':
      $asset['type'] = 'Item';
      $asset['link'] = '/items/'.$asset['id']; // https://www.gravatar.com/avatar/94d093eda664addd6e450d7e9881bcad?s=100&d=identicon&r=PG
      $asset['image'] = '/universes/'.$roleplay['url'].'/items/'.$asset['id'].'/image';
      break;
  }

  $buyablePlaces[] = array(
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

foreach ($buyablePlaces as $place) {
  $template->assign_block_vars('buyable_places', $place);
}

$template->assign_vars(array(
	'S_MORE_PLACES'		=> (@$roleplay['placeCount'] > $limit) ? true : false,
  'MORE_PLACES_COUNT'	=> (@$roleplay['placeCount'] > $limit) ? @$roleplay['placeCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['placeCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page(count($places), $limit, $start),
  'TOTAL_PLACES'       => (int) $roleplay['placeCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url'],
  'ROLEPLAY_ID'    => $roleplay['id'],
  'S_PAGE_ONLY'     => true,
  'S_CAN_EDIT'        => (($auth->acl_get('m_')) || ($roleplay['owner'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
));

page_header('Places to roleplay in '.$roleplay['title'].' &middot; RPG');

$template->set_filenames(array(
	'body' => 'roleplay_places.html'
));

page_footer();
