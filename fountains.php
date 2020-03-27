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

$fountainAssetID = (int) request_var('assetID', 0);
$fountainPlaceID = (int) request_var('placeID', 0);

if (
  $fountainAssetID >= 1
  && $fountainPlaceID >= 1
) {
  $db->sql_transaction('begin');

  $sql = 'SELECT id, name, roleplay_id FROM rpg_places WHERE owner = '.(int) $user->data['user_id'] . ' AND id = ' . (int) $fountainPlaceID;
  $placeResult = $db->sql_query($sql);
  $place = $db->sql_fetchrow($placeResult);
  $db->sql_freeresult($placeResult);

  if (empty($place['id'])) {
    trigger_error('You own no such place.');
  }

  $sql = 'SELECT id FROM rpg_assets WHERE creator = '.(int) $user->data['user_id'] . ' AND id = ' . (int) $fountainAssetID;
  $assetResult = $db->sql_query($sql);
  $asset = $db->sql_fetchrow($assetResult);
  $db->sql_freeresult($assetResult);

  if (empty($asset['id'])) {
    trigger_error('You own no such asset.');
  }

  $sql = 'INSERT INTO rpg_fountains (
    roleplay_id, place_id, asset_id, creator
  ) VALUES (
    "'.(int) $place['roleplay_id'].'", "'.(int) $fountainPlaceID.'", "'.(int) $fountainAssetID.'", "'.(int) $user->data['user_id'].'"
  )';
  $db->sql_query($sql);
  $fountainID = $db->sql_nextid();
  
  $result = $db->sql_query('SELECT * FROM rpg_assets WHERE id = '.(int) $fountainID);
  $fountain = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  $result = $db->sql_query('SELECT * FROM rpg_roleplays WHERE id = '.(int) $fountain['roleplay_id']);
  $roleplay = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);
  
  $db->sql_transaction('commit');
  
  meta_refresh(3, '/assets/' . $fountain['symbol']);
  trigger_error('Created asset successfully.  Taking you there now...');
}

$sql = 'SELECT * FROM rpg_places WHERE owner = '.(int) $user->data['user_id'].' ORDER BY last_activity DESC';
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

$sql = 'SELECT * FROM rpg_assets WHERE creator = '.(int) $user->data['user_id'].' ORDER BY id DESC';
$result = $db->sql_query($sql);
while ($asset = $db->sql_fetchrow($result)) {
  $template->assign_block_vars('assets', array(
    'ID' => $asset['id'],
    'NAME' => $asset['name'],
    'DESCRIPTION' => $asset['description'],
    'ROLEPLAY_ID' => $roleplay['id']
  ));
}
$db->sql_freeresult($result);

$sql = 'SELECT * FROM rpg_fountains ORDER BY id ASC';
$result = $db->sql_query($sql);
while ($fountain = $db->sql_fetchrow($result)) {
  $sql = 'SELECT p.*, r.url as roleplay_url FROM rpg_places p
    INNER JOIN rpg_roleplays r
      ON p.roleplay_id = r.id
    WHERE p.id = '. (int) $fountain['place_id'];
  $placeResult = $db->sql_query($sql);
  $place = $db->sql_fetchrow($placeResult);
  $db->sql_freeresult($placeResult);

  $sql = 'SELECT * FROM rpg_assets WHERE id = '. (int) $fountain['asset_id'];
  $assetResult = $db->sql_query($sql);
  $asset = $db->sql_fetchrow($assetResult);
  $db->sql_freeresult($assetResult);

  $template->assign_block_vars('fountains', array(
    'ID' => $fountain['id'],
    'ROLEPLAY_ID' => $roleplay['id'],
    'LOCATION_NAME' => $place['name'],
    'LOCATION_LINK' => '/roleplay/' . $place['roleplay_url'] . '/places/'.  $place['url'],
    'ASSET_LINK' => '/assets/' . strtolower($asset['symbol']),
    'ASSET_NAME' => $asset['name'],
    'ASSET_SYMBOL' => $asset['symbol'],
  ));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'fountains.html'
));

page_footer();

?>
