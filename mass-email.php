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

//$sql = 'SELECT user_id, username, user_email FROM gateway_users WHERE user_id IN (SELECT user_id FROM gateway_users WHERE mass_email_sent = 0)';

require_once 'includes/Mailgun.php';
mailgun_init('key-82yigyrhrbwgyxuggjefxz2pk0nxmb26');

$log = file_get_contents('mailgun.log');

$sql = 'SELECT user_id, username, user_email FROM gateway_users WHERE user_id IN (SELECT user_id FROM gateway_users WHERE user_email IN ("eric@ericmartindale.com"))';
$result = $db->sql_query($sql);
while ($user = $db->sql_fetchrow($result)) {

  $rawMime = 
    "Content-Type: text/html;charset=UTF-8\n".    
    "From: admin@roleplaygateway.com\n".
    "To: ".$user['user_email']."\n".
    "Subject: RolePlayGateway: New Beginnings\n".
    "\n".
    "Hello, <a href=\"http://www.roleplaygateway.com/user-u".$user['user_id'].".html\">".$user['username']."</a>!

<p>You're receiving this message because you are a trusted member of <a href=\"http://www.roleplaygateway.com\">RolePlayGateway.com (RPG)</a>, formerly known as GWing.net.  Our focus has always been on building a <em>great</em> community, <u>not</u> a <em>big</em> community--and as such, we've recently locked the doors to new members and taken some time to reorganize internally.  We'll be re-opening registration early next week on December 5th--but until then, <strong>we need your help</strong>.</p>

<p>When we started this site in 2005, it was a place where positive conflict fostered creativity and friendships were formed rather than cliques. Honesty and transparency were valued, new people were incorporated into the community rather than judged based on what style of writing they preferred, and despite the youthfulness and zeal of the population there prevailed a reasonable degree of common sense.  As our community experienced unprecedented growth, these qualities waned.  We need to restore the intellectual and accepting foundation on which this site was built, and it starts with those of us who remember these beautiful qualities taking action and setting an example for those who do not.</p>

<p>If you haven't visited in a while, now would be the perfect opportunity as we organize member-drive initiatives to create and participate in the wonderful storylines we all hold so dearly.  We're unleashing a hurricane force against things like trolling and bullying, and trimming down the site itself based on <a href=\"http://feedback.roleplaygateway.com\">your feedback</a>.  There are a lot of moving parts, but <strong>the most integral component is <em>your</em> participation,</strong> through action and leadership.</p>

<p>Thank each and every one of you for taking the time to read this -- that <em>alone</em> means the world to us.   I've placed a PM in your site inbox that contains a <em>lot</em> more information, including some of the recent changes and our upcoming plans, and hope you can find some time to read it.  I genuinely look forward to seeing, and hopefully writing with, you sometime soon.</p>

Sincerely,<br />
<br />
<img src=\"http://www.roleplaygateway.com/images/roleplaygateway.png\" height=\"75\" style=\"float: left; margin-right: 10px;\" />
Eric Martindale<br />
Creator and Owner, RolePlayGateway<br />
http://www.roleplaygateway.com<br />
+1 (919) 374-2020";

  if (preg_match('/'.$user['user_email'].'/m', $log)) {
    echo "\nEmail was already sent.";
  } else {
    try {
      $email = MailgunMessage::send_raw("admin@roleplaygateway.com", $user['user_email'], $rawMime); 
    } catch (Exception $e) {
      echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    
    //if ($email === true) {
      echo "\nEmail sent to ".$user['user_email']. " (".$user['username']."): ".json_encode($email);
      $db->sql_query('UPDATE gateway_users SET mass_email_sent = 1 WHERE user_id = '.(int) $user['user_id']);
    //}    
    
    
  }

}


?>
