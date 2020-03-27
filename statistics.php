<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$start      = (int) @$_REQUEST['start'];
$limit      = (int) @$_REQUEST['limit'];

$transactionsByDay = array();

$sql = 'SELECT DATE(timestamp) as label, COUNT(*) as content FROM rpg_ledger GROUP BY DATE(timestamp)';
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result)) {
  $transactionsByDay[] = array(
    'date' => $row['label'],
    'value' => (int) $row['content']
  );

  /* $transactionsByDay[] = array(
    $row['label'] => $row['content']
  ); */
}
$db->sql_freeresult($result);

$template->assign_vars(array(
  'TRANSACTIONS_BY_DAY_JSON' => json_encode($transactionsByDay),
  'S_PAGE_ONLY' => true
));

page_header('Statistics | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'statistics.html'
	)
);

page_footer();
