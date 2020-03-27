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

$Parsedown = new Parsedown();

$characterURL = request_var('characterURL', '');
$roleplayURL = request_var('roleplayURL', '');

$sql = 'SELECT id, name, url, owner FROM rpg_characters WHERE url = "'.$db->sql_escape($characterURL).'"';
$result = $db->sql_query($sql);
$character = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$template->assign_vars(array(
	'CHARACTER_NAME' => $character['name'],
  'S_IS_OWNER' => ($character['owner'] == $user->data['user_id']) ? true : false,
));

$items = array();
$sql = 'SELECT a.id, a.name, a.description, a.slug, i.id as instanceID FROM rpg_item_instances i
  INNER JOIN rpg_items a
    ON i.item_id = a.id
  WHERE i.character_id = '.(int) $character['id'];
$result = $db->sql_query($sql);
while ($instance = $db->sql_fetchrow($result)) {
  $items[] = $instance;
}
$db->sql_freeresult($result);

foreach ($items as $row) {
  $template->assign_vars(array(
    'HAS_ITEMS' => true
  ));
  
  $sql = 'SELECT * FROM rpg_orders WHERE asset = "'.$db->sql_escape($asset).'" AND status = "open" AND creator = '.(int) $character['owner'];
  $orderResult = $db->sql_query($sql);
  $mostRecentSaleOrder = $db->sql_fetchrow($orderResult);
  $db->sql_freeresult($orderResult);

  $template->assign_block_vars('items', array(
    'ID'		    => $row['id'],
    'NAME'		  => $row['name'],
    'DESCRIPTION'	=> $row['description'],
    'SLUG'	=> $row['slug'],
    'INSTANCE_ID'	=> $row['instanceID'],

    'S_IS_FOR_SALE'   => (!empty($mostRecentSaleOrder['price'])) ? true : false,
    'S_CAN_AFFORD'    => ($userBalance >= $mostRecentSaleOrder['price']) ? true : false,
    'SALE_PRICE'      => money_format('%i', $mostRecentSaleOrder['price']),
    'ORDER_ID'        => $mostRecentSaleOrder['id'],
  ));

}
$db->sql_freeresult($result);


$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'inventory.html'
));

page_footer();

?>
