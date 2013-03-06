<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);

// Start session management
$user->session_begin();         
$auth->acl($user->data);

if (!$auth->acl_get('a_'))
{
        trigger_error('NO_ADMIN');
}

$user_id = intval($_REQUEST['user_id']);

// Make sure anonymous users and Rem don't get accidentally banned.
if ($user_id <= 4) { trigger_error('No.'); }

if ($result = $db->sql_query("SELECT * FROM gateway_users WHERE user_id = '". $user_id ."'")) {
  while ($row = $db->sql_fetchrow($result)) {

          $username = $row['username'];
          $email = $row['user_email'];
          $ip_addr = $row['user_ip'];
  
  }
  
  if ($topics_result = $db->sql_query("SELECT topic_id FROM gateway_topics WHERE topic_poster = ".$user_id)) {

    while ($row = $db->sql_fetchrow($topics_result)) {
    
      $spam_topics[] = $row['topic_id'];
            
    }
    
    move_topics($spam_topics,164);
          
  } else {
          $result = "something went wrong with moving topics.<br />";
  }
  
  if ($posts_result = $db->sql_query("SELECT post_id FROM gateway_posts WHERE poster_id = ".$user_id)) {

    while ($row = $db->sql_fetchrow($posts_result)) {
    
            $spam_posts[] = $row['post_id'];
    
    }        

    move_posts($spam_posts,17468);

    $result = "successfully moved posts.<br />";
  } else {
    $result = "something went wrong with moving posts.<br />";
  }
  

  
  if (user_ban("user",$username, 0, null, 0, "Identified by admin as spambot.", "Identified by admin as spambot.")) {
    $result .= "user banned successfully";
  } else {
    $result .= "couldn't ban user";
  }
	
	$sql = 'SELECT msg_id
	FROM ' . PRIVMSGS_TABLE . '
	WHERE author_id = '.$user_id;
	$resultset = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($resultset))
	{
		$msg_id = $row['msg_id'];
		
		$sql = 'DELETE FROM ' . PRIVMSGS_TABLE . "
		WHERE msg_id = $msg_id";
		$result2 = $db->sql_query($sql);
		
		$sql = 'DELETE FROM ' . PRIVMSGS_TO_TABLE . "
		WHERE msg_id = $msg_id";
		$result2 = $db->sql_query($sql);
	}
	
	$db->sql_freeresult($resultset);	

//unset($result);	

  //bot.php
  $url = "http://www.stopforumspam.com/add.php";
  $ch = curl_init();

  $data = array(
          'username' => $username,
          'email' => $email,
          'ip_addr' => $ip_addr,
          'api_key' => 'EzkNFKh4LcnJm3'
  );

  // set the target url
  curl_setopt($ch, CURLOPT_URL,$url);

  // howmany parameter to post
  curl_setopt($ch, CURLOPT_POST, 1);

  curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

  if (curl_exec ($ch)) {
    $result .= "successful submission.";
  } else {
    $result .= "couldn't submit as spambot.";
  }
  curl_close ($ch);
        
} else {
  $result = "fail";
}

trigger_error($result);

?>
