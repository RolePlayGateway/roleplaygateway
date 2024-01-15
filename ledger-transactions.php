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

$balances = array();
$credits = array();
$debits = array();

$sql = 'SELECT SUM(amount) as total, `to` as id FROM rpg_ledger GROUP BY `to`';
$creditResult = $db->sql_query($sql, 3600);
while ($credit = $db->sql_fetchrow($creditResult)) {
  $credits[$credit['id']] = (float) $credit['total'];
}
$db->sql_freeresult($creditResult);

$sql = 'SELECT SUM(amount) as total, `from` as id FROM rpg_ledger GROUP BY `from`';
$debitResult = $db->sql_query($sql, 3600);
while ($debit = $db->sql_fetchrow($debitResult)) {
  $debits[$debit['id']] = (float) $debit['total'];
}
$db->sql_freeresult($debitResult);

foreach ($credits as $account => $credit) {
  $balances[$account] = $credits[$account] - $debits[$account];
}

//if ($_REQUEST['format'] == 'json') {
  header('Content-Type: application/json');
  echo(json_encode($balances));
  exit();
//}

?>
