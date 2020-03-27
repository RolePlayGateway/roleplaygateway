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

$roleplayURL = $_REQUEST['roleplayURL'];
$start      = (int) @$_REQUEST['start'];
$limit      = (int) @$_REQUEST['limit'];

$sql = 'SELECT id, title, url, require_approval, owner FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if ($_REQUEST['format'] == 'json') {
  $quests = array();
  
  $sql = 'SELECT id, name, roleplay_id, place_id, creator FROM rpg_quests WHERE visibility <> "hidden"';
  $result = $db->sql_query($sql);
  while ($quest = $db->sql_fetchrow($result)) {
    $quests[] = $quest;
  }

  header('Content-Type: application/json');
  echo(json_encode($quests));
  exit();
}

$pagination_url = 'http://www.roleplaygateway.com/quests';

$roleplayID = $roleplay['id'];
$limit = (isset($limit)) ? $limit : 2000;
//$limit = 2000;

$sql = 'SELECT count(*) as questCount FROM rpg_quests WHERE status NOT IN ("Hidden")';
$questResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($questResult);
$roleplay['questCount'] = $row['questCount'];
$db->sql_freeresult($questResult);

$sql = 'SELECT count(*) as openQuestCount FROM rpg_quests WHERE status NOT IN ("Hidden", "Closed")';
$questResult = $db->sql_query($sql);
$row = $db->sql_fetchrow($questResult);
$roleplay['openQuestCount'] = $row['openQuestCount'];
$db->sql_freeresult($questResult);

$quests = array();

$sql = 'SELECT
          q.id, q.name, q.synopsis, q.description, q.creator, length(q.synopsis) as synopsisLength, length(q.description) as descriptionLength,
          r.title as roleplayTitle, r.url as roleplayURL
          FROM rpg_quests q
          INNER JOIN rpg_roleplays r ON q.roleplay_id = r.id
          WHERE q.status NOT IN ("Hidden", "Closed") ORDER BY q.id DESC';
$result = $db->sql_query_limit($sql, 2000, $start);
while ($row = $db->sql_fetchrow($result)) {

  $sql = 'SELECT user_id as id, username, user_id FROM gateway_users WHERE user_id = '.(int) $row['creator'];
  $userResult = $db->sql_query($sql);
  $creator = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $sql = 'SELECT user_id as id, username, user_id FROM gateway_users WHERE user_id = '.(int) $row['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $row['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $sql = 'SELECT count(*) as number FROM rpg_characters WHERE location = '.(int) $row['id'];
  $characterResult = $db->sql_query($sql, 3600);
  $character = $db->sql_fetchrow($characterResult);
  $db->sql_freeresult($characterResult);

  $row['characterCount'] = $character['number'];

  $quests[ $row['id']] = $row;

  $template->assign_block_vars('open_quests', array(
    'QUEST_ID'              => $row['id'],
    'QUEST_NAME'            => $row['name'],
    'QUEST_URL'             => $row['url'],
    'QUEST_OWNER_USERNAME'  => $row['owner_username'],
    'QUEST_SYNOPSIS'        => $row['synopsis'],
    'QUEST_SYNOPSIS_LENGTH'   => $row['synopsisLength'],
    'QUEST_DESCRIPTION'        => $row['description'],
    'QUEST_DESCRIPTION_LENGTH'   => $row['descriptionLength'],

    'ROLEPLAY_TITLE'    => $row['roleplayTitle'],
    'ROLEPLAY_URL'    => $row['roleplayURL'],
    
    'CHARACTER_COUNT' => $row['characterCount'],
    'S_IS_WARNING' => ($row['synopsisLength'] == 120) ? true : false,
    'S_HAS_SYNOPSIS' => ($row['synopsisLength'] > 0) ? true : false,
    'S_HAS_DESCRIPTION' => ($row['descriptionLength'] > 0) ? true : false,
    'S_IS_LAZY' => ($row['synopsis'] == $row['description']) ? true : false,
    
    'S_IS_EXCEPTION' => (
      (($row['synopsisLength'] <= 12 || $row['synopsisLength'] == 75) ? true : false)
      || (($row['descriptionLength'] <= 21 || $row['synopsisLength'] == 75) ? true : false)
    ),

    'S_CAN_EDIT' > ($owner['id'] == $user->data['user_id']) ? true : false,
    'S_CAN_SELL' > ($owner['id'] == $user->data['user_id']) ? true : false,
  ));

}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_MORE_QUESTS'		=> (@$roleplay['questCount'] > $limit) ? true : false,
  'MORE_QUESTS_COUNT'	=> (@$roleplay['questCount'] > $limit) ? @$roleplay['openQuestCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['openQuestCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page(count($quests), $limit, $start),
  'TOTAL_QUESTS'       => (int) $roleplay['questCount'],
  'TOTAL_AVAILABLE_QUESTS'       => (int) $roleplay['openQuestCount'],
  'ROLEPLAY_NAME'     => $roleplay['title'],
  'ROLEPLAY_URL'    => $roleplay['url'],
  'ROLEPLAY_ID'    => $roleplay['id'],
  'S_PAGE_ONLY'     => true,
));

page_header('Quests | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'ambassadors.html'
	)
);

page_footer();
