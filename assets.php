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

$cost_to_issue = 100; // 10000;

$postName = request_var('name', '');
$postSymbol = $_POST['symbol'];
$postIssuance = (int) $_POST['issuance'];
$postDeposit = (float) $_POST['deposit'];
$postDemurrage = (float) $_POST['demurrage'];
$postPeriod = $_POST['period'];
$postUniverse = $_POST['universe'];

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $user->data['user_id'];
$creditResult = $db->sql_query($sql);
$credits = $db->sql_fetchrow($creditResult);
$db->sql_freeresult($creditResult);

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $user->data['user_id'];
$debitResult = $db->sql_query($sql);
$debits = $db->sql_fetchrow($debitResult);
$db->sql_freeresult($debitResult);

$userBalance = $credits['total'] - $debits['total'];

if (
  !empty($postSymbol)
  && strlen($postSymbol) == 3
  && strlen($postName) <= 25 && strlen($postName) >= 3
  && $postDeposit >= 100
  && $postDemurrage >= 0.0001
) {

  if ($userBalance < $cost_to_issue) {
    trigger_error('You do not hold enough <code>INK</code> to create an asset, which requires a minimum of '. number_format($cost_to_issue) .' <code>INK</code>.');
  }

  if ($userBalance < $postDeposit) {
    trigger_error('You do not hold enough <code>INK</code> to bond this amount ('. number_format($cost_to_issue) .').');
  }

  if ($userBalance < ($postDeposit + $cost_to_issue)) {
    trigger_error('You do not hold enough <code>INK</code> to both pay the creation fee ('. number_format($cost_to_issue) .' <code>INK</code>) and the initial bonding deposit ('. number_format($cost_to_issue) .' <code>INK</code>).');
  }

  $db->sql_transaction('begin');

  $inputSequence = '"'.$db->sql_escape($postName).'", "'.$db->sql_escape($postIssuance).'", "'.$db->sql_escape($postDeposit).'", "'.$db->sql_escape($postDemurrage).'", "'.$db->sql_escape($postPeriod).'"';
  
  $sql = 'INSERT INTO rpg_assets (symbol, hash, name, issuance, deposit, demurrage, period) VALUES (
    UPPER("'.$db->sql_escape($postSymbol).'"),
    SHA2(CONCAT(CAST(CURTIME() AS CHAR), SHA2(RAND(), 256), UPPER(symbol), '.$inputSequence.'), 256),
    '.$inputSequence.'
  )';
  $db->sql_query($sql);
  $assetID = $db->sql_nextid();

  $result = $db->sql_query('SELECT * FROM rpg_assets WHERE id = '.(int) $assetID);
  $asset = $db->sql_fetchrow($result);

  $sql = 'INSERT INTO rpg_ledger (`from`, `to`, `amount`, `for`)'.
    'VALUES ('.(int) $user->data['user_id'].', 0, '.(int) $cost_to_issue.', "/assets/'.(int) $assetID.'/fee")';
  $db->sql_query($sql);

  $sql = 'INSERT INTO rpg_ledger (`from`, `to`, `amount`, `for`)'.
    'VALUES ('.(int) $user->data['user_id'].', 0, '.(int) $postDeposit.', "/assets/'.(int) $assetID.'/bond")';
  $db->sql_query($sql);

  $db->sql_transaction('commit');
  
  meta_refresh(3, '/assets/' . $asset['symbol']);
  trigger_error('Created asset successfully.  Taking you there now...');
}

$assetSymbol = strtoupper(request_var('assetSymbol', ''));

if (!empty($assetSymbol)) {
  $sql = 'SELECT * FROM rpg_assets WHERE symbol = "'.$db->sql_escape($assetSymbol).'"';
  $result = $db->sql_query($sql);
  $asset = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $sql = 'SELECT count(DISTINCT `to`) as total FROM rpg_ledger';
  $result = $db->sql_query($sql);
  $status = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  $template->assign_vars(array(
    'S_IS_BASE_ASSET' => ($asset['id'] == 1) ? true : false,
  	'ASSET_NAME' => $asset['name'],
  	'ASSET_SYMBOL' => $asset['symbol'],
  	'ASSET_ISSUANCE' => number_format($asset['issuance']),
  	'ASSET_HOLDERS' => number_format($status['total']),
  ));

  $template->assign_vars(array(
  	'S_PAGE_ONLY' => true
  ));

  page_header($config['sitename']);

  $template->set_filenames(array(
  	'body' => 'asset-single.html'
  ));

  page_footer();
} else {

  $sql = 'SELECT * FROM rpg_assets ORDER BY id ASC';
  $result = $db->sql_query($sql);
  while ($asset = $db->sql_fetchrow($result)) {
    $template->assign_block_vars('assets', array(
      'ID' => $asset['id'],
      'NAME' => $asset['name'],
      'SYMBOL' => $asset['symbol'],
      'ISSUANCE' => number_format(money_format($asset['issuance'], 2), 2),
      'DEMURRAGE' => $asset['demurrage'],
      'PERIOD' => $asset['period'],
      // TODO: fix division by zero!
      'RATE' => (!empty($asset['deposit'])) ? @number_format($asset['deposit'] / $asset['issuance'], 8) : 1,
      'ISSUER' => $asset['issuer']
    ));
  }
  $db->sql_freeresult($result);

  $template->assign_vars(array(
    'S_PAGE_ONLY' => true,
    'UNIVERSE_ID' => $postUniverse,
    'COST_TO_ISSUE' => number_format($cost_to_issue)
  ));

  page_header($config['sitename']);

  $template->set_filenames(array(
  	'body' => 'rpg-assets.html'
  ));

  page_footer();
}
?>
