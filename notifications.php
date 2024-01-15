<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin(false);
$auth->acl($user->data);
$user->setup('viewforum');

$target = request_var('target', 0);
$start = request_var('limit', 0);
$limit = request_var('limit', 100);
$mode = request_var('mode', 'default');
$format = request_var('format', 'html');

if ($mode == 'all') {
  $limit = 1000;
}

switch ($mode) {
  case 'single':
    $sql = "SELECT *
      FROM arrowchat_notifications
      WHERE to_id = '" . $db->sql_escape($user->data['user_id']) . "'
      AND id = ".(int) $target;
    break;
  case 'unread':
    $sql = "SELECT *
      FROM arrowchat_notifications
      WHERE to_id = '" . $db->sql_escape($user->data['user_id']) . "'
      AND user_read = 0
      ORDER BY alert_time DESC
      LIMIT ".(int) $start.", ".(int) $limit;
    break;
  case 'all':
    $sql = "SELECT *
      FROM arrowchat_notifications
      WHERE to_id = '" . $db->sql_escape($user->data['user_id']) . "'
      ORDER BY alert_time DESC
      LIMIT ".(int) $start.", ".(int) $limit;
    break;
  default:
    $sql = "SELECT *
      FROM arrowchat_notifications
      WHERE to_id = '" . $db->sql_escape($user->data['user_id']) . "'
      ORDER BY alert_time DESC
      LIMIT ".(int) $start.", ".(int) $limit;
    break;
}


// ########################### INITILIZATION #############################
$response = array();
$notifications = array();
$unread = array();
$ids = array();

// ###################### START NOTIFICATION RECEIVE ######################
$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result))
{
  $ids[] = $row['id'];
  
  $alert_id   = $row['id'];
  $author_id    = $row['author_id'];
  $author_name  = $db->sql_escape(strip_tags($row['author_name']));
  $type       = $row['type'];
  $message_time = $row['alert_time'];
  $misc1      = $row['misc1'];
  $misc2      = $row['misc2'];
  $misc3      = $row['misc3'];
  $data       = $row['data'];
  
  $markup = get_markup($author_id, $author_name, $type, $message_time, $misc1, $misc2, $misc3, $data);
  
  $notifications[] = array('alert_id' => $alert_id, 'markup' => $markup, 'type' => $type);

  if ($row['user_read'] != 1) {
    $unread[] = $row;
  }

  $template->assign_block_vars('notifications', array(
    'ID' => $alert_id,
    'DATA' => $data,
    'MARKUP' => $markup,
    'STATUS' => ($row['user_read'] == 1) ? 'read' : 'unread'
  ));
}

if ($format == 'json') {
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'success',
    'meta' => array(
      'unread' => count($unread)
    )
  ));
} else {
  if (count($ids) > 0) {
    $sql = 'UPDATE arrowchat_notifications SET user_read = 1 WHERE id IN ('.implode($ids, ',').')';
    $db->sql_query($sql);
  }

  page_header('Notifications | '.$roleplay['title'].' | '.$config['sitename']);

  $template->set_filenames(array(
  	'body' => 'notifications.html'
  	)
  );

  $template->assign_vars(array(
    'S_PAGE_ONLY'     => true,
    'MODE'     => $mode,
    'NEXT'     => $start + $limit,
  ));

  page_footer();
}
