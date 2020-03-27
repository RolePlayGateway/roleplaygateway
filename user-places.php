<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$username = $_REQUEST['username'];

if (!empty($username)) {
  $sql = 'SELECT user_id, username FROM gateway_users WHERE username = "'.$db->sql_escape($username).'"';
}

$result = $db->sql_query($sql);
if ($userAccount = $db->sql_fetchrow($result)) {

  $template->assign_vars(array(
    'USERNAME' 					=> $userAccount['username'],
  ));
  
  $sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $user->data['user_id'];
  $creditResult = $db->sql_query($sql);
  $credits = $db->sql_fetchrow($creditResult);
  $db->sql_freeresult($creditResult);
  
  $sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $user->data['user_id'];
  $debitResult = $db->sql_query($sql);
  $debits = $db->sql_fetchrow($debitResult);
  $db->sql_freeresult($debitResult);
  
  $userBalance = $credits['total'] - $debits['total'];

  $sql = 'SELECT id, name, synopsis, url, status, price, owner, parent_id, roleplay_id FROM rpg_places
    WHERE owner = '.(int) $userAccount['user_id'] . ' ORDER BY last_activity DESC, id DESC';
  $placeResult = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($placeResult)) {
  
    $sql = 'SELECT id, title, url, require_approval FROM rpg_roleplays WHERE id = '.(int) $place['roleplay_id'].'';
    $result = $db->sql_query($sql);
    $place['roleplay'] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = 'SELECT id, name, synopsis, url, status, roleplay_id FROM rpg_places
      WHERE id = '.(int) $place['parent_id'];
    $result = $db->sql_query($sql);
    $place['parent'] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = 'SELECT count(*) as sightings FROM rpg_content_tags WHERE character_id = '.(int) $place['id'];
    $contentResult = $db->sql_query($sql);
    $place['sightings'] = $db->sql_fetchfield('sightings');
    $db->sql_freeresult($contentResult);
    
    $link = '/places/' . (int) $place['id'];
    
    $sql = 'SELECT * FROM rpg_orders WHERE asset = "'.$db->sql_escape($link).'" AND status = "open" ORDER BY created DESC';
    $orderResult = $db->sql_query($sql);
    $order = $db->sql_fetchrow($orderResult);
    $db->sql_freeresult($orderResult);

	  $template->assign_block_vars('places', array(
		  'ID' 			      	=> $place['id'],
		  'NAME' 				    => $place['name'],
		  'URL' 				    => $place['url'],
		  'OWNER_USERNAME'	=> get_username_string('full', $place['owner'], $place['username']),
		  'SYNOPSIS'			  => $place['synopsis'],
      'SIGHTINGS'       => $place['sightings'],
      'ROLEPLAY_URL' 		=> $place['roleplay']['url'],
      'ROLEPLAY_TITLE' 	=> $place['roleplay']['title'],
      'PARENT_ID' 			=> $place['parent']['id'],
      'PARENT_SLUG' 		=> $place['parent']['url'],
      'PARENT_NAME' 		=> $place['parent']['name'],
      'S_IS_FOR_SALE'   => (!empty($order['price'])) ? true : false,
      'S_CAN_EDIT'      => ($place['owner'] == $user->data['user_id']) ? true : false,
      'S_CAN_AFFORD'    => ($userBalance >= $order['price']) ? true : false,
      'SALE_PRICE'      => money_format('%i', $order['price']),
      'ORDER_ID'        => $order['id'],
	  ));

  }

  $template->assign_vars(array(
    'S_PAGE_ONLY' 					=> true,
  ));

  page_header($username . '\'s Places | ' . $config['sitename']);

  $template->set_filenames(array(
    'body' => 'user_places.html')
  );

  page_footer();

}
$db->sql_freeresult($result);


?>
