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
  $bastions = array();
  
  $sql = 'SELECT id, name, roleplay_id, place_id, creator FROM rpg_bastions WHERE visibility <> "hidden"';
  $result = $db->sql_query($sql);
  while ($bastion = $db->sql_fetchrow($result)) {
    $bastions[] = $bastion;
  }

  header('Content-Type: application/json');
  echo(json_encode($bastions));
  exit();
}

$pagination_url = 'http://www.roleplaygateway.com/bastions';

$roleplayID = $roleplay['id'];
$limit = (isset($limit)) ? $limit : 2000;
//$limit = 2000;

$sql = 'SELECT count(*) as bastionCount FROM rpg_bastions';
$bastionResult = $db->sql_query($sql);
$bastion = $db->sql_fetchrow($bastionResult);
$roleplay['bastionCount'] = $bastion['bastionCount'];
$db->sql_freeresult($bastionResult);

$bastions = array();

$sql = 'SELECT
          b.id, b.place_id,
          p.name, p.url,
          g.name as groupName, g.slug as groupURL,
          r.id as roleplayID, r.url as roleplayURL, r.title as roleplayTitle
          FROM rpg_bastions b
          INNER JOIN rpg_places p ON b.place_id = p.id
          INNER JOIN rpg_groups g ON b.group_id = g.id
          INNER JOIN rpg_roleplays r ON p.roleplay_id = r.id
          WHERE p.visibility NOT IN ("Hidden", "Closed") ORDER BY b.id DESC';
$result = $db->sql_query_limit($sql, 2000, $start);
while ($bastion = $db->sql_fetchrow($result)) {
  $sql = 'SELECT user_id as id, username, user_id FROM gateway_users WHERE user_id = '.(int) $bastion['creator'];
  $userResult = $db->sql_query($sql);
  $creator = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $sql = 'SELECT user_id as id, username, user_id FROM gateway_users WHERE user_id = '.(int) $bastion['owner'];
  $userResult = $db->sql_query($sql);
  $owner = $db->sql_fetchrow($userResult);
  $db->sql_freeresult($userResult);

  $bastion['owner_username'] = get_username_string('full', $owner['user_id'], $owner['username']);

  $sql = 'SELECT count(*) as number FROM rpg_characters WHERE location = '.(int) $bastion['id'];
  $characterResult = $db->sql_query($sql, 3600);
  $character = $db->sql_fetchrow($characterResult);
  $db->sql_freeresult($characterResult);

  $bastion['characterCount'] = $character['number'];

  $bastions[$bastion['id']] = $bastion;

  $template->assign_block_vars('open_bastions', array(
    'BASTION_ID'              => $bastion['id'],
    'BASTION_NAME'            => $bastion['name'],
    'BASTION_URL'             => $bastion['url'],
    'BASTION_OWNER_USERNAME'  => $bastion['owner_username'],
    'BASTION_SYNOPSIS'        => $bastion['synopsis'],
    'BASTION_SYNOPSIS_LENGTH'   => $bastion['synopsisLength'],
    'BASTION_DESCRIPTION'        => $bastion['description'],
    'BASTION_DESCRIPTION_LENGTH'   => $bastion['descriptionLength'],
    
    'GROUP_NAME' => $bastion['groupName'],
    'GROUP_URL' => $bastion['groupURL'],

    'ROLEPLAY_TITLE'    => $bastion['roleplayTitle'],
    'ROLEPLAY_URL'    => $bastion['roleplayURL'],
    
    'CHARACTER_COUNT' => $bastion['characterCount'],
    'S_IS_WARNING' => ($bastion['synopsisLength'] == 120) ? true : false,
    'S_HAS_SYNOPSIS' => ($bastion['synopsisLength'] > 0) ? true : false,
    'S_HAS_DESCRIPTION' => ($bastion['descriptionLength'] > 0) ? true : false,
    'S_IS_LAZY' => ($bastion['synopsis'] == $bastion['description']) ? true : false,
    
    'S_IS_EXCEPTION' => (
      (($bastion['synopsisLength'] <= 12 || $bastion['synopsisLength'] == 75) ? true : false)
      || (($bastion['descriptionLength'] <= 21 || $bastion['synopsisLength'] == 75) ? true : false)
    ),

    'S_CAN_EDIT' > ($owner['id'] == $user->data['user_id']) ? true : false,
    'S_CAN_SELL' > ($owner['id'] == $user->data['user_id']) ? true : false,
  ));

}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_MORE_QUESTS'		=> (@$roleplay['bastionCount'] > $limit) ? true : false,
  'MORE_QUESTS_COUNT'	=> (@$roleplay['bastionCount'] > $limit) ? @$roleplay['openBastionCount'] - $limit : null,
  'PAGINATION'        => generate_pagination($pagination_url, $roleplay['openBastionCount'], $limit, $start),
  'PAGE_NUMBER'       => on_page(count($bastions), $limit, $start),
  'TOTAL_QUESTS'       => (int) $roleplay['bastionCount'],
  'TOTAL_AVAILABLE_QUESTS'       => (int) $roleplay['openBastionCount'],
  'S_PAGE_ONLY'     => true,
));

page_header('Bastions | '.$config['sitename']);

$template->set_filenames(array(
	'body' => 'bastions-list.html'
	)
);

page_footer();
