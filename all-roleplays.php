<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

?>
<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript" src="http://www.roleplaygateway.com/includes/jquery-latest.min.js"></script>
  <script type="text/javascript" src="http://www.roleplaygateway.com/includes/jquery.tablesorter.min.js"></script>
  <link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css">
  <style type="text/css">
    a:visited {
      color: purple;
    }
  </style>
  
  <script type="text/javascript">
    $(document).ready(function() { 
      $("#roleplays").tablesorter(); 
    });
  </script>
  
</head>
<body>
<table id="roleplays">
  <thead>
    <th>ID</th>
    <th>Tools</th>
    <th>Created</th>
    <th>Creator</th>
    <th>Name</th>
    <th>Status</th>
  </thead>
  <tbody>
<?php

$sql = 'SELECT id, status, title, url, owner, created FROM rpg_roleplays ORDER BY created DESC';
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result)) {

  $sql = 'SELECT username FROM gateway_users WHERE user_id = '.$row['owner'];
  $ownerResult = $db->sql_query($sql);
  $row['user'] = $db->sql_fetchrow($ownerResult);
  $db->sql_freeresult($ownerResult);

  switch ($row['status']) {
    case 'Open':
      $row['statusClass'] = 'success';
    break;
    case 'Closed':
      $row['statusClass'] = 'warning';
    break;
    case 'Completed':
      $row['statusClass'] = 'important';
    break;
    default:
      $row['statusClass'] = 'notice';
    break;
    
  }
  
  $row['status'] = '<span class="label '.$row['statusClass'].'">'.$row['status'] . '</span>';

	echo '<tr><td>'.$row['id'].'</td><td>[<a href="http://gwing.net/ucp.php?i=roleplays&mode=edit&id='.$row['id'].'">EDIT</a>]</td><td>'.$row['created'].'</td><td><a href="http://gwing.net/member/'.$row['user']['username'].'/">'.$row['user']['username'].'</a></td><td><a href="http://gwing.net/roleplay/'.$row['url'].'/">'.$row['title'].'</a></td><td>'.$row['status'].'</td></tr>';	
}
$db->sql_freeresult($result);

?>
  </tbody>
</table>
</body>
</html>
