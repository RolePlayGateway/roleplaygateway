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

$to_sql = (!empty($_REQUEST['to'])) ? ' AND l.to = ' . (int) $_REQUEST['to'] . ' ' : '';
$from_sql = (!empty($_REQUEST['from'])) ? ' AND l.from = ' . (int) $_REQUEST['from'] . ' ' : '';
$account_sql = (!empty($_REQUEST['account'])) ? ' AND (l.to = ' . (int) $_REQUEST['account'] . ' OR l.from = ' . (int) $_REQUEST['account'] . ') ' : '';

$sql = "SELECT
  l.*,
  f.user_id as from_user_id, f.username as from_username, f.user_colour as from_user_colour,
  t.user_id as to_user_id, t.username as to_username, t.user_colour as to_user_colour
  FROM rpg_ledger l
  INNER JOIN gateway_users f
    ON l.from = f.user_id
  INNER JOIN gateway_users t
    ON l.to = t.user_id
  WHERE id > 0
    ".$to_sql."
    ".$from_sql."
    ".$account_sql."
  ORDER BY id ASC
  ";
$overallResult = $db->sql_query($sql);
while ($transaction = $db->sql_fetchrow($overallResult)) {
  $transactions[$transaction['id']] = array(
    'id' => $transaction['id'],
    'from' => $transaction['from'],
    'to' => $transaction['to'],
    'amount' => $transaction['amount'],
    'timestamp' => $transaction['timestamp'],
  );
  
  if (!empty($transaction['for'])) {
    $parts = explode('/', $transaction['for']);
    
    switch ($parts[1]) {
      case 'posts':
        $transaction['link'] = '/viewtopic.php?p='.$parts[2];
        $transaction['asset'] = array(
          '@link' => $order['asset'],
          'name' => 'tip, post #'.$parts[2],
        );
        break;
      case 'topics':
        $transaction['link'] = '/viewtopic.php?t='.$parts[2];
        $transaction['asset'] = array(
          '@link' => $order['asset'],
          'name' => 'tip, topic #'.$parts[2],
        );
        break;
      case 'orders':
        $sql = 'SELECT * FROM rpg_orders WHERE id = '.(int) $parts[2];
        $orderResult = $db->sql_query($sql);
        $order = $db->sql_fetchrow($orderResult);
        $db->sql_freeresult($orderResult);
        
        $transaction['link'] = $order['asset'] . '#orders';
        $transaction['asset'] = array(
          '@link' => $order['asset'],
          'name' => $order['asset'],
        );
        break;
    }
  }
  
  $template->assign_block_vars('transactions', array(
    'ID'        => $transaction['id'],
    'FROM'      => $transaction['from'],
    'TO'        => $transaction['to'],
    'AMOUNT'    => $transaction['amount'],
    'FOR'       => $transaction['for'],
    'FOR_NAME'  => $transaction['asset']['name'],
    'FOR_LINK'  => $transaction['link'],
    'TIMESTAMP' => $transaction['timestamp'],
    'FROM_NAME' => get_username_string('full', $transaction['from_user_id'], $transaction['from_username'], $transaction['from_user_colour']),
    'TO_NAME' => get_username_string('full', $transaction['to_user_id'], $transaction['to_username'], $transaction['to_user_colour']),
  ));
}

if ($_REQUEST['format'] == 'json') {
  header('Content-Type: application/json');
  echo(json_encode($transactions));
  exit();
}

if (!empty($_REQUEST['account'])) {
  $sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $_REQUEST['account'];
  $creditResult = $db->sql_query($sql);
  $credits = $db->sql_fetchrow($creditResult);
  $db->sql_freeresult($creditResult);
  
  $sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $_REQUEST['account'];
  $debitResult = $db->sql_query($sql);
  $debits = $db->sql_fetchrow($debitResult);
  $db->sql_freeresult($debitResult);
  
  $balance = $credits['total'] - $debits['total'];
  
  $template->assign_vars(array(
  	'FINAL_BALANCE' => money_format('%i', $balance)
  ));
}


$template->assign_vars(array(
	'S_PAGE_ONLY' => true
));

page_header($config['sitename']);

$template->set_filenames(array(
	'body' => 'ledger.html'
));

page_footer();

?>
