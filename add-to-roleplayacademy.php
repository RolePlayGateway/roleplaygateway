<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

error_reporting(E_ALL);
ini_set('display_errors',true);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

if (!$topic_id = (int) $_REQUEST['topic_id']) {
	trigger_error('You must specify a topic ID.');
}

if (!($auth->acl_get('a_'))) {
	trigger_error('hell no.');
}

$sql = 'SELECT t.topic_title,t.topic_id,t.topic_poster,post_text,bbcode_uid,bbcode_bitfield,topic_first_poster_name FROM gateway_topics t INNER JOIN gateway_posts p ON t.topic_first_post_id = p.post_id WHERE t.topic_id = '.$topic_id;
$result = $db->sql_query($sql);
$topic = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$title = $topic['topic_title'];
$body = '<em>Originally posted on <a href="http://www.roleplaygateway.com/">RolePlayGateway.com</a> as &ldquo;<a href="http://www.roleplaygateway.com/viewtopic.php?t='.$topic['topic_id'].'">'.$topic['topic_title'].'</a>&rdquo;, by <a href="http://www.roleplaygateway.com/memberlist.php?mode=viewprofile&u='.$topic['topic_poster'].'">'.$topic['topic_first_poster_name'].'</a>:</em><hr />'. generate_text_for_display($topic['post_text'], $topic['bbcode_uid'], $topic['bbcode_bitfield'], 7);
$url = 'http://www.roleplayacademy.com/xmlrpc.php';

$username = "RolePlayGateway.com";
$password = "puq3seVU";

wpPostXMLRPC($title,$body,$url,$username,$password);

echo "\nSuccessfully posted this topic to RolePlayAcademy.com!";




function wpPostXMLRPC($title,$body,$rpcurl,$username,$password,$categories=array(1)){
	$categories = implode(",", $categories);
	$XML = "<title>$title</title>".
	"<category>$categories</category>".
	$body;
	$params = array('','',$username,$password,$XML,1);
	$request = xmlrpc_encode_request('blogger.newPost',$params);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	curl_setopt($ch, CURLOPT_URL, $rpcurl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_exec($ch);
	curl_close($ch);
}


?>