<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/Parsedown.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$storeName = request_var('name', '');
$storeDescription = request_var('description', '');
$storeRoleplayID = (int) request_var('roleplayID', 1);
$storePlaceID = (int) request_var('placeID', 1);
$storeID = (int) request_var('id', 0);

if (
  strlen($storeName) <= 55 && strlen($storeName) >= 3
  && $storeRoleplayID >= 1
) {
  $db->sql_transaction('begin');

  $sql = 'INSERT INTO rpg_stores (
    roleplay_id, place_id, creator, name, description
  ) VALUES (
    "'.(int) $storeRoleplayID.'", "'.(int) $storePlaceID.'", "'.(int) $user->data['user_id'].'", "'.$db->sql_escape($storeName).'", "'.$db->sql_escape($storeDescription).'"
  )';
  $db->sql_query($sql);
  $storeID = $db->sql_nextid();
  
  $result = $db->sql_query('SELECT * FROM rpg_stores WHERE id = '.(int) $storeID);
  $store = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  $result = $db->sql_query('SELECT * FROM rpg_roleplays WHERE id = '.(int) $store['roleplay_id']);
  $roleplay = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  $db->sql_transaction('commit');
  
  meta_refresh(3, '/stores/' . $store['id']);
  trigger_error('Created Store successfully.  Taking you there now...');
}

if (!empty($storeID)) {
  $sql = 'SELECT * FROM rpg_stores WHERE id = '.(int) $storeID;
  $result = $db->sql_query($sql);
  $store = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $sql = 'SELECT * FROM rpg_places WHERE id = '. (int) $store['place_id'];
  $placeResult = $db->sql_query($sql);
  $place = $db->sql_fetchrow($placeResult);
  $db->sql_freeresult($placeResult);

  $sql = 'SELECT * FROM rpg_roleplays WHERE id = '. (int) $place['roleplay_id'];
  $universeResult = $db->sql_query($sql);
  $universe = $db->sql_fetchrow($universeResult);
  $db->sql_freeresult($universeResult);

  $sql = 'SELECT i.id, i.name, i.description, s.price, i.quantity, i.deposit FROM rpg_item_stores s
      INNER JOIN rpg_items i
        ON i.id = s.item_id
    WHERE s.store_id = '.(int) $storeID;
  $waresResult = $db->sql_query($sql);
  while ($ware = $db->sql_fetchrow($waresResult)) {
    if (empty($ware['price'])) {
      $ware['price'] = number_format($ware['deposit'] / $ware['quantity'], 8);
    }

    $sql = 'SELECT count(*) as total FROM rpg_item_instances WHERE item_id = '.(int) $ware['id'];
    $statResult = $db->sql_query($sql);
    $stat = $db->sql_fetchrow($statResult);
    $db->sql_freeresult($statResult);

    $ware['inventory'] = $ware['quantity'] - $stat['total'];

    $template->assign_block_vars('wares', array(
      'ID' => $ware['id'],
      'NAME' => $ware['name'],
      'PRICE' => $ware['price'],
      'PRICE_IN_BITS' => number_format($ware['price'] * 1000000, 2),
      'LINK' => '/items/'.$ware['id'],
      'DESCRIPTION' => $ware['description'],
      'INVENTORY' => number_format($ware['inventory']),
      'ROLEPLAY_ID' => $universe['id'],
      'LOCATION_NAME' => $place['name']
    ));
  }
  $db->sql_freeresult($waresResult);

  $template->assign_vars(array(
  	'S_IS_SINGLE' => true,
    'STORE_ID' => $store['id'],
    'STORE_NAME' => $store['name'],
    'STORE_DESCRIPTION' => $store['description'],
    'UNIVERSE_NAME' => $universe['title'],
    'UNIVERSE_LINK' => '/universes/'.$universe['url'],
    'UNIVERSE_IMAGE_LINK' => '/universes/'.$universe['url'].'/image',
    'PLACE_NAME' => $place['name'],
    'PLACE_LINK' => '/universes/'.$universe['url'].'/places/'.$place['url'],
    'PLACE_IMAGE_LINK' => '/universes/'.$universe['url'].'/places/'.$place['url'].'/image'
  ));
} else {
  $sql = 'SELECT count(id) as total FROM rpg_items WHERE creator = '. (int) $user->data['user_id'];
  $createdResult = $db->sql_query($sql);
  $createdStats = $db->sql_fetchrow($createdResult);
  $db->sql_freeresult($createdResult);

  $template->assign_vars(array(
  	'S_IS_ITEM_CREATOR' => ($createdStats['total'] > 0) ? true : false
  ));

  // $sql = 'SELECT * FROM rpg_places WHERE owner = '.(int) $user->data['user_id'].' ORDER BY last_activity DESC';
  $sql = 'SELECT * FROM rpg_places WHERE owner = '.(int) $user->data['user_id'].' OR (roleplay_id IN (SELECT id FROM rpg_roleplays WHERE owner = '.(int) $user->data['user_id'].')) OR (roleplay_id IN (SELECT DISTINCT roleplay_id FROM rpg_permissions WHERE isCoGM = 1 AND user_id = '.(int) $user->data['user_id'].')) ORDER BY name ASC';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $template->assign_block_vars('places', array(
      'ID' => $place['id'],
      'NAME' => $place['name'],
      'DESCRIPTION' => $place['description'],
      'ROLEPLAY_ID' => $roleplay['id']
    ));
  }
  $db->sql_freeresult($result);

  $sql = 'SELECT * FROM rpg_stores ORDER BY id ASC';
  $result = $db->sql_query($sql);
  while ($store = $db->sql_fetchrow($result)) {
    $sql = 'SELECT * FROM rpg_places WHERE id = '. (int) $store['place_id'];
    $placeResult = $db->sql_query($sql);
    $place = $db->sql_fetchrow($placeResult);
    $db->sql_freeresult($placeResult);

    $sql = 'SELECT * FROM rpg_roleplays WHERE id = '. (int) $place['roleplay_id'];
    $universeResult = $db->sql_query($sql);
    $universe = $db->sql_fetchrow($universeResult);
    $db->sql_freeresult($universeResult);

    $template->assign_block_vars('stores', array(
      'ID' => $store['id'],
      'NAME' => $store['name'],
      'LINK' => '/stores/'.$store['id'],
      'DESCRIPTION' => $store['description'],
      'ROLEPLAY_ID' => $universe['id'],
      'LOCATION_NAME' => $place['name'],
      'LOCATION_LINK' => '/universes/'.$universe['url'] . '/places/'. $place['url']
    ));
  }
  $db->sql_freeresult($result);
}

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

if (!empty($storeID)) {
  page_header($store['name'].' &middot; ' . $config['sitename']);
} else {
  page_header('Stores'.' &middot; ' . $config['sitename']);
}

$template->set_filenames(array(
	'body' => 'stores.html'
));

page_footer();

?>
