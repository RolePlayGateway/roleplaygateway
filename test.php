<?php

$phpbb_root_path = '/var/www/vhosts/roleplaygateway.com/httpdocs/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

$sql = "SELECT post_text FROM gateway_posts WHERE post_id = 646813";

$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result)) {


	
	$text = $row['post_text'];
	
	if (!$uid)
	{
		$uid = '[0-9a-z]{5,}';
	}
	
	// Hoorah. 
	$text = preg_replace("/\[quote(?:=&quot;(.*?)&quot;)?(\:$uid)\](.*)\[\/quote?(\:$uid)\]/ie", ' ', $text);
	$text = preg_replace("/\[img](.*)\[\/img]/iUe", ' ', $text);
	$text = preg_replace("/\[url(=(.*))?\](.*)\[\/url?:$uid\]/iUe", ' ', $text);

	echo "Content: <blockquote>".$text."</blockquote>";
	
}

?>