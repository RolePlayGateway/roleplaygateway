<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$cacheTTL = 600;

$roleplayURL = $_REQUEST['roleplayURL'];
if (empty($roleplayURL)) { die('lol'); }

$sql = 'SELECT id, title, url FROM rpg_roleplays WHERE url ="'.$db->sql_escape($roleplayURL).'"';
$result = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (empty($roleplay)) { die('also, lol.'); }

$sql = 'SELECT * FROM rpg_content FORCE INDEX(roleplay_written) WHERE roleplay_id = '.(int) $roleplay['id'] . ' ORDER BY written DESC LIMIT 20';
$result = $db->sql_query($sql, $cacheTTL);
while ($row = $db->sql_fetchrow($result)) {

  $place = getPlace($row['place_id']);

  $feedItems['c_'.$row['id']]['headline'] = '[IC] ('.$roleplay['title'].') '.$place['name']; // http://www.roleplaygateway.com/roleplay/the-multiverse/post/1684804/#roleplay168480411
  $feedItems['c_'.$row['id']]['link'] = 'http://www.roleplaygateway.com/roleplay/'.$roleplayURL.'/post/'.$row['id'].'/#roleplay'.$row['id'];
  $feedItems['c_'.$row['id']]['pubDate'] = date("D, d M Y H:i:s T", strtotime($row['written']));
  $feedItems['c_'.$row['id']]['description'] = generate_text_for_display($row['text'], $row['bbcode_uid'], $row['bbcode_bitfield'], 7);
}
$db->sql_freeresult($result);

$sql = 'SELECT * FROM gateway_posts WHERE topic_id IN (SELECT thread_id FROM rpg_roleplay_threads WHERE roleplay_id = '.(int) $roleplay['id'] . ' AND type = "Out Of Character") ORDER BY post_time DESC LIMIT 20';
$result = $db->sql_query($sql, $cacheTTL);
while ($row = $db->sql_fetchrow($result)) {

  $feedItems['o_'.$row['post_id']]['headline'] = '[OOC] ('.$roleplay['title'].'): '.$row['post_subject']; // http://www.roleplaygateway.com/roleplay/the-multiverse/post/1684804/#roleplay168480411
  $feedItems['o_'.$row['post_id']]['link'] = 'http://www.roleplaygateway.com/post'.$row['post_id'].'.html#'.$row['post_id'];
  $feedItems['o_'.$row['post_id']]['pubDate'] = date("D, d M Y H:i:s T", strtotime($row['post_time']));
  $feedItems['o_'.$row['post_id']]['description'] = generate_text_for_display($row['post_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], 7);
}
$db->sql_freeresult($result);

function compareFeedItems($a, $b) {
  if ($a['pubDate'] == $b['pubDate']) {
   return 0;
  }
  return ($a['pubDate'] < $b['pubDate']) ? 1 : -1;
}
uasort($feedItems, 'compareFeedItems');

$now = date("D, d M Y H:i:s T");

$output = "<?xml version=\"1.0\"?>
            <rss version=\"2.0\">
                <channel>
                    <title>".htmlentities(strip_tags($roleplay['title']))." Activity Stream</title>
                    <link>http://www.roleplaygateway.com/roleplay/".$roleplayURL."/feed</link>
                    <description>Latest activity in ".htmlentities(strip_tags($roleplay['title'])).", a work of collaborative fiction on RolePlayGateway.com.</description>
                    <language>en-us</language>
                    <pubDate>$now</pubDate>
                    <lastBuildDate>$now</lastBuildDate>
                    <docs>http://www.roleplaygateway.com</docs>
                    <managingEditor>editors@roleplaygateway.com</managingEditor>
                    <webMaster>roleplay@roleplaygateway.com</webMaster>
            ";
            
foreach ($feedItems as $line)
{
    $output .= "<item><title>".htmlentities($line['headline'])."</title>
                    <link>".htmlentities($line['link'])."</link>
                    <pubDate>".htmlentities($line['pubDate'])."</pubDate>
                    <description>".$line['description']."</description>
                </item>";
}
$output .= "</channel></rss>";
header("Content-Type: text/xml; charset=utf-8");
echo $output;

function getPlace($placeID) {
	global $db,$cache;

	$sql = 'SELECT id, name, description FROM rpg_places WHERE id = '.(int) $placeID;
	$result = $db->sql_query($sql);
	$row = $result->fetch_assoc();
	$result->free();

	return $row;

}

?>
