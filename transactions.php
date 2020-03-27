<?php

define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_money.' . $phpEx);
include($phpbb_root_path . 'includes/functions_medals.' . $phpEx);
include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$db->sql_transaction('begin');

$recipient = (int) $_POST['to'];
$amount = (float) $_POST['amount'];
$subject = $_POST['for'];
@$orderLink = $_POST['order'];

if (0 >= $user->data['user_id']) {
  header('Content-Type: application/json');
  echo(json_encode(array(
    'status' => 'error',
    'message' => 'Signature required.'
  )));
  exit();
}

if ($recipient == $user->data['user_id']) {
  header('Content-Type: application/json');
  echo(json_encode(array(
    'status' => 'error',
    'message' => 'You cannot send money to yourself.'
  )));
  exit();
}

if (!empty($orderLink)) {
  $parts = explode('/', $orderLink);

  $sql = 'SELECT * FROM rpg_orders WHERE id = '.(int) $parts[2] . ' AND price <= '.$amount;
  $result = $db->sql_query($sql);
  $order = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $recipient = $order['creator'];
  $subject = $orderLink;

  if (empty($order) || empty($order['id'])) {
    header('Content-Type: application/json');
    echo(json_encode(array(
      'status' => 'error',
      'message' => 'No orders could be filled for this request.'
    )));
    exit();
  }
  
  award_medal_from(0, 35, $recipient, 'You made your first sale!  Congratulations!', time());
  award_medal_from(0, 43, $user->data['user_id'], 'You just made your first purchase!  Congratulations!', time());
  
} else {
  $parts = explode('/', $subject);
  switch ($parts[1]) {
    case 'posts':
    case 'snippets':
      award_medal_from(0, 41, $recipient, 'You received your first tip for a post you made!  Don\'t spend it all in one place...', time());
      break;
  }
}

$data = array(
  'link' => $subject,
);

if ($_POST['characterID']) {
  $data['characterID'] = (int) $_POST['characterID'];
}

switch ($parts[1]) {
  case 'stores':
    $sql = 'SELECT * FROM rpg_items WHERE id = '.(int) $parts[4];
    $itemResult = $db->sql_query($sql);
    $item = $db->sql_fetchrow($itemResult);
    $db->sql_freeresult($itemResult);

    $sql = 'SELECT count(*) as total FROM rpg_item_instances WHERE item_id = '.(int) $parts[4];
    $statResult = $db->sql_query($sql);
    $stat = $db->sql_fetchrow($statResult);
    $db->sql_freeresult($statResult);

    if ($stat['total'] < $item['quantity']) {
      $recipient = $item['creator'];
      $db->sql_query('INSERT INTO rpg_item_instances (item_id, owner) VALUES ('.(int) $item['id'].', '.(int) $user->data['user_id'].')');
    } else {
      header('Content-Type: application/json');
      echo(json_encode(array(
        'status' => 'error',
        'message' => 'There are no more of this item available.'
      )));
      exit();
    }
    break;
}

$message = spend($recipient, $amount, $subject, $data);

if (!empty($order) && !empty($order['id']) && $message['status'] == 'success') {
  award_medal_from(0, 35, $order['creator'], 'Someone bought something you were selling on the marketplace!', time());
}

$db->sql_transaction('commit');

header('Content-Type: application/json');
echo(json_encode($message));

exit();

?>
