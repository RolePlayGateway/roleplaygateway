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

if ($_POST['secret']) {
  $db->sql_transaction('BEGIN');
  $db->sql_query('INSERT INTO rpg_wagers (
    `secret`,
    `deposit`,
    `expires`,
    `asset`,
    `amount`,
    `challenge`
  ) VALUES (
    "'.$db->sql_escape($_POST['secret']).'",
    1, /* TODO: document 1 as default */
    144,
    1, /* TODO: document INK as default (until BTC ready) */
    1, /* TODO: document 1 as default */
    "'.hash('sha256', $_POST['secret']).'"
  )');

  $db->sql_transaction('commit');
  meta_refresh(3, '/wagers');
  trigger_error('Wager created.  Loading next phase...');
}

if (!empty($targetID)) {
  $sql = 'SELECT * FROM rpg_wagers WHERE id = '.(int) $targetID;
  $template->assign_vars(array(
    'S_IS_SINGLE' => true
  ));
} else {
  $sql = 'SELECT * FROM rpg_wagers';
}

$result = $db->sql_query($sql);
while ($wager = $db->sql_fetchrow($result)) {
  $script = 'OP_HASH160 ' .
  $wager['challenge'] .' OP_EQUAL
  OP_IF
  04781ef6e459113b9def5fe3a59a8390c44b615dbee4850161ec70d2571fe72dba55c1145d8a5ffd09de3e1b773b0a65e12fd2d51d9fd18d319b782f906413dd1c
OP_ELSE
  144 OP_CHECKLOCKTIME
  049a85cdc3f6547bed4b1873cb72042eca6814c099f69ea94c61273defe474bc4774306d79fca920a716ee5acfd50b44349a2fd991af40b4b50826612a8f361879
OP_ENDIF
OP_CHECKSIG
';

  $template->assign_block_vars('wagers', array(
    'ID' => $wager['id'],
    'DEPOSIT' => $wager['deposit'],
    'LOCKTIME' => $wager['expires'],
    'ASSET_ID' => $wager['asset'],
    'AMOUNT' => $wager['amount'],
    'ANCHOR' => 'unanchored',
    'SCRIPT' => $script,
    'CHALLENGE' => $wager['challenge'],
    'SIGNATURE' => 'unsigned',
  ));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
  'S_PAGE_ONLY' => true
));

page_header('Wagers | Roleplay on ' . $config['sitename']);

$template->set_filenames(array(
  'body' => 'rpg_wagers.html'
));

page_footer();

?>
