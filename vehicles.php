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

$vehicleName = request_var('name', '');
$vehicleDescription = request_var('description', '');
$vehicleRoleplayID = (int) request_var('roleplayID', 1);
$vehiclePlaceID = (int) request_var('placeID', 1);
$vehicleClassID = (int) request_var('id', 0);

if (
  strlen($vehicleName) <= 55 && strlen($vehicleName) >= 3
  && $vehicleRoleplayID >= 1
) {
  $db->sql_transaction('begin');

  $sql = 'INSERT INTO rpg_vehicles (
    roleplay_id, place_id, creator, name, description
  ) VALUES (
    "'.(int) $vehicleRoleplayID.'", "'.(int) $vehiclePlaceID.'", "'.(int) $user->data['user_id'].'", "'.$db->sql_escape($vehicleName).'", "'.$db->sql_escape($vehicleDescription).'"
  )';
  $db->sql_query($sql);
  $storeID = $db->sql_nextid();

  $result = $db->sql_query('SELECT * FROM rpg_vehicles WHERE id = '.(int) $storeID);
  $vehicle = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $result = $db->sql_query('SELECT * FROM rpg_roleplays WHERE id = '.(int) $vehicle['roleplay_id']);
  $roleplay = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $db->sql_transaction('commit');

  meta_refresh(3, '/vehicles/' . $vehicle['id']);
  trigger_error('Created Vehicle successfully.  Taking you there now...');
}

if (!empty($vehicleClassID) && $vehicleClassID > 0) {
  $template->assign_vars(array(
    'VEHICLE_CLASS_ID' => $vehicleClassID
  ));

  $sql = 'SELECT v.* FROM rpg_vehicles v
  WHERE id = '.(int) $vehicleClassID.' ORDER BY name ASC';
} else {
  // $sql = 'SELECT * FROM rpg_vehicles WHERE owner = '.(int) $user->data['user_id'].' ORDER BY last_activity DESC';
  $sql = 'SELECT v.* FROM rpg_vehicles v
    ORDER BY id DESC';
}

$result = $db->sql_query($sql);
while ($vehicle = $db->sql_fetchrow($result)) {
  $universeResult = $db->sql_query('SELECT id, title, url FROM rpg_roleplays WHERE id = '.(int) $vehicle['roleplay_id']);
  $roleplay = $db->sql_fetchrow($universeResult);
  $db->sql_freeresult($universeResult);

  if (!empty($vehicleClassID)) {
    $template->assign_vars(array(
      'VEHICLE_CLASS_ID' => $vehicleClassID,
      'VEHICLE_CLASS_NAME' => $vehicle['name'],
    ));

    $sql = 'SELECT i.*, i.location_id as location FROM rpg_vehicle_instances i
      WHERE i.vehicle_id = '.(int) $vehicleClassID.'
      ORDER BY i.id ASC';
    $instanceResult = $db->sql_query($sql);
    while ($instance = $db->sql_fetchrow($instanceResult)) {
      $sql = 'SELECT * FROM rpg_places WHERE id = '. (int) $instance['location'];
      $placeResult = $db->sql_query($sql);
      $place = $db->sql_fetchrow($placeResult);
      $db->sql_freeresult($placeResult);

      $template->assign_block_vars('class_instances', array(
        'ID' => $instance['id'],
        'NAME' => $instance['name'],
        'LINK' => '/places/'.$instance['url'],
        'DESCRIPTION' => $instance['description'],
        'ROLEPLAY_ID' => $roleplay['id'],
        'LOCATION_NAME' => $place['name'],
        'PLACE_NAME' => $place['name'],
        'PLACE_LINK' => '/universes/'.$roleplay['url'].'/places/'.$place['url'],
      ));
    }
    $db->sql_freeresult($instanceResult);
  }

  $template->assign_block_vars('vehicles', array(
    'ID' => $vehicle['id'],
    'NAME' => $vehicle['name'],
    'LINK' => '/vehicles/'. $vehicle['id'],
    'DESCRIPTION' => $vehicle['description'],
    'ROLEPLAY_ID' => $roleplay['id']
  ));
}
$db->sql_freeresult($result);

$sql = 'SELECT i.* FROM rpg_vehicle_instances i ORDER BY i.id ASC';
$result = $db->sql_query($sql);
while ($vehicle = $db->sql_fetchrow($result)) {
  $sql = 'SELECT * FROM rpg_places WHERE id = '. (int) $vehicle['location'];
  $placeResult = $db->sql_query($sql);
  $place = $db->sql_fetchrow($placeResult);
  $db->sql_freeresult($placeResult);

  $template->assign_block_vars('vehicle_instances', array(
    'ID' => $vehicle['id'],
    'NAME' => $vehicle['name'],
    'LINK' => '/vehicles/'.$vehicle['id'],
    'DESCRIPTION' => $vehicle['description'],
    'ROLEPLAY_ID' => $roleplay['id'],
    'LOCATION_NAME' => $place['name']
  ));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'vehicles.html'
));

page_footer();

?>
