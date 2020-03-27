<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_money.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$roleplayURL = $_REQUEST['roleplayURL'];
$start      = (int) $_REQUEST['start'];

$sql = 'SELECT id, title, url, require_approval FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!empty($_POST['quest'])) {
  $sql = 'INSERT INTO rpg_quests (roleplay_id, name, creator, synopsis, description, bounty)
    VALUES
    (
      '.(int) $roleplay['id'].',
      "'.$db->sql_escape($_POST['quest']['name']).'",
      '.(int) $user->data['user_id'].',
      "'.$db->sql_escape($_POST['quest']['description']).'",
      "'.$db->sql_escape($_POST['quest']['description']).'",
      '.(float) $db->sql_escape($_POST['quest']['reward']).'
    )';
  $db->sql_query($sql);
  $questID = $db->sql_nextid();

  $spent = spend(0, (float) $_POST['quest']['reward'], '/quests/'.(int) $questID);
  meta_refresh(3, '/universes/'.$roleplay['url'].'/quests/'.$questID);
  trigger_error('Generating quest... ');
}

$pagination_url = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/quests';

$roleplayID = $roleplay['id'];
$limit = 40;

$sql = 'SELECT count(*) as questCount FROM rpg_quests WHERE roleplay_id = '.(int) $roleplayID;
$placeResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($placeResult);
$roleplay['placeCount'] = $row['placeCount'];
$db->sql_freeresult($placeResult);

$sql = 'SELECT id, name, synopsis, bounty FROM rpg_quests
          WHERE roleplay_id = '.(int) $roleplayID .' /* AND synopsis IS NOT NULL */ ORDER BY id DESC';

$result = $db->sql_query_limit($sql, $limit, $start);
while ($row = $db->sql_fetchrow($result)) {
  $sql = 'SELECT count(*) AS characters FROM rpg_quest_characters WHERE quest_id = '.(int) $row['id']. ' /* AND status = "Member" */ ';
  $memberResult = $db->sql_query($sql);
  $row['characters'] = $db->sql_fetchfield('characters');
  $db->sql_freeresult($memberResult);

  $sql = 'SELECT sum(amount) AS reward FROM rpg_ledger WHERE `for` = "'.$db->sql_escape('/quests/'.(int) $row['id']).'"';
  $valueResult = $db->sql_query($sql);
  $row['reward'] = $db->sql_fetchfield('reward');
  $db->sql_freeresult($valueResult);

  $hasBounty = ((float) $row['reward'] > 0) ? true : false;

	$template->assign_block_vars('quests', array(
		'ID' 				=> $row['id'],
		'NAME' 				=> $row['name'],
		'SLUG' 				=> $row['slug'],
		'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username'], $row['user_colour']),
		'SYNOPSIS'			=> $row['synopsis'],
		'REWARD'			=> number_format($row['reward']),
    'POSTS'     => $row['posts'],
    'CHARACTERS'     => $row['characters'],
    'UNIQUE_PLAYERS'     => $row['players'],
    'S_HAS_BOUNTY'  => $hasBounty,
    'BOUNTY_AMOUNT' =>  money_format('%i', $row['bounty']),
	));
}
$db->sql_freeresult($result);

$template->assign_vars(array(
  'S_PAGE_ONLY'       => true,
	'S_MORE_QUESTS'		=> (@$roleplay['questCount'] > $limit) ? true : false,
  'MORE_QUESTS_COUNT'	=> (@$roleplay['questCount'] > $limit) ? @$roleplay['questCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['questCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page($roleplay['questCount'], $limit, $start),
  'TOTAL_QUESTS'       => (int) $roleplay['questCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url']
));

page_header('Quests | '.$roleplay['title'].' | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'roleplay_quests.html'
	)
);

page_footer();
