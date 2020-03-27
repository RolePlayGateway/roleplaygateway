<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$targetID = request_var('id', 0);
$username = request_var('username', '');
$userID = request_var('account', 0);
$itemName = null;

if (!empty($username)) {
  $userID = $user->data['user_id'];
}

if (!empty($targetID)) {
  $sql = 'SELECT * FROM rpg_items WHERE id = '.(int) $targetID;
  $template->assign_vars(array(
    'S_IS_SINGLE' => true
  ));
} else if (!empty($userID)) {
  $sql = 'SELECT t.* FROM rpg_item_instances i INNER JOIN rpg_items t ON t.id = i.item_id WHERE i.owner = '.(int) $userID;
} else {
  $sql = 'SELECT * FROM rpg_items WHERE deleted IS NULL';
}

$result = $db->sql_query($sql);
while ($item = $db->sql_fetchrow($result)) {
  if (!empty($targetID)) {
    $itemName = $item['name'];
  }

  $sql = 'SELECT id, title, url, owner FROM rpg_roleplays WHERE id = '.(int) $item['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);
  $db->sql_freeresult($roleplayResult);

  $game_masters = array();
  $sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay['id'].' AND isCoGM = 1)';
  $gmResult = $db->sql_query($sql);
  while ($gm_row = $db->sql_fetchrow($gmResult)) {
    $game_masters[] = $gm_row['user_id'];
  }
  $db->sql_freeresult($gmResult);

  $item['roleplay'] = $roleplay;

  $sql = 'SELECT count(item_id) as total, count(DISTINCT character_id) as characters FROM rpg_item_instances WHERE item_id = '.(int) $item['id'];
  $statsResult = $db->sql_query($sql);
  $stats = $db->sql_fetchrow($statsResult);
  $db->sql_freeresult($statsResult);

  $template->assign_block_vars('items', array(
    'S_CAN_EDIT' => in_array($user->data['user_id'], $game_masters) ? true : false,
    'ID' => $item['id'],
    'NAME' => $item['name'],
    'DESCRIPTION' => $item['description'],
    'QUANTITY' => number_format($item['quantity']),
    'DEPOSIT' => $item['deposit'],
    'VALUE' => number_format(($item['deposit'] / $item['quantity']) * 1000000, 2),
    'VALUE_IN_BITS' => number_format(($item['deposit'] / $item['quantity']) * 1000000, 2),
    'VALUE_IN_INK' => number_format($item['deposit'] / $item['quantity'], 2),
    'ISSUED' => $stats['total'],
    'HOLDERS' => $stats['characters'],
    'ROLEPLAY_ID'         => $item['roleplay']['id'],
    'ROLEPLAY_NAME'       => $item['roleplay']['title'],
    'ROLEPLAY_URL'        => $item['roleplay']['url'],
    'S_HAS_IMAGE' => (strlen($item['image']) > 0) ? true : false
  ));

  $sql = 'SELECT DISTINCT s.store_id as id, l.name, m.url as placeSlug, m.name as placeName FROM rpg_item_stores s
    INNER JOIN rpg_stores l ON l.id = s.store_id
    INNER JOIN rpg_places m ON m.id = l.place_id
    WHERE s.item_id = '.(int) $item['id'];
  $storeResult = $db->sql_query($sql);
  while ($store = $db->sql_fetchrow($storeResult)) {
    $template->assign_vars(array(
      'S_HAS_STORES' => true
    ));
    $template->assign_block_vars('items.stores', array(
      'ID' => $store['id'],
      'NAME' => $store['name'],
      'PLACE_NAME' => $store['placeName'],
      'PLACE_LINK' => '/roleplay/'. $roleplay['url'] . '/places/'. $store['placeSlug'],
    ));
  }
  $db->sql_freeresult($storeResult);
}
$db->sql_freeresult($result);

$sql = 'SELECT DISTINCT character_id, c.name, c.url, c.roleplay_id FROM rpg_item_instances i
  INNER JOIN rpg_characters c
    ON c.id = i.character_id';
$result = $db->sql_query($sql);
while ($character = $db->sql_fetchrow($result)) {
  $sql = 'SELECT id, title, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
  $roleplayResult = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($roleplayResult);
  $db->sql_freeresult($roleplayResult);
  
  $character['roleplay'] = $roleplay;
  
  $template->assign_block_vars('characters', array(
    'ID' => $character['id'],
    'NAME' => $character['name'],
    'DESCRIPTION' => $character['description'],
    'LINK' => '/roleplay/'.$character['roleplay']['url'] .'/characters/' . $character['url'],
    'ROLEPLAY_ID'         => $character['roleplay']['id'],
    'ROLEPLAY_NAME'       => $character['roleplay']['title'],
    'ROLEPLAY_URL'        => $character['roleplay']['url'],
  ));
}
$db->sql_freeresult($result);

$sql = 'SELECT k.*, i.name, i.description, r.url as roleplay_url, count(k.item_id) as stack_count FROM rpg_item_instances k
  INNER JOIN rpg_items i
    ON i.id = k.item_id
  INNER JOIN rpg_roleplays r
    ON r.id = i.roleplay_id
  WHERE character_id IN (SELECT id FROM rpg_characters WHERE owner = '.(int) $user->data['user_id'].')
    OR k.owner = '.(int) $user->data['user_id'] .'
    GROUP BY k.item_id
  ';
$instancesResult = $db->sql_query($sql);
while ($instance = $db->sql_fetchrow($instancesResult)) {
  $template->assign_block_vars('instances', array(
    'ID' => $instance['id'],
    'NAME' => $instance['name'],
    'LINK' => '/universes/'.$instance['roleplay_url'].'/items/'.$instance['item_id'],
    'DESCRIPTION' => $instance['description'],
    'ITEM_ID' => $instance['item_id'],
    'ROLEPLAY_URL' => $instance['roleplay_url'],
    'STACK_COUNT' => $instance['stack_count'],
  ));
}
$db->sql_freeresult($instancesResult);

$template->assign_vars(array(
  'S_PAGE_ONLY' => true
));

if (!empty($targetID)) {
  page_header($itemName. ' &middot; Items &middot; Roleplay on ' . $config['sitename']);
} else if (!empty($userID)) {
  page_header('Inventory &middot; ' . $config['sitename']);
} else  {
  page_header('Items &middot; Roleplay on ' . $config['sitename']);
}

if (!empty($userID)) {
  $template->set_filenames(array(
    'body' => 'user_inventory.html'
  ));
} else {
  $template->set_filenames(array(
    'body' => 'rpg_items.html'
  ));
}

page_footer();

?>
