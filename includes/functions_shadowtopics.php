<?php
/**
*
* @package phpBB3
* @author mtrs 01.12.08
* @version $Id$ prune shadow topics
* @copyrigh(c) 2008 , mtrs
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
/**
* @ignore
*/

if (!defined('IN_PHPBB'))
{
	exit();
}

function prune_shadow_links()
{
	global $config, $db, $phpbb_root_path, $phpEx;

	if (!function_exists('delete_topics'))
	{
		include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
	} 

	$current_time = time();
	$sql = 'SELECT topic_id
				FROM ' . TOPICS_TABLE . ' 			
				WHERE topic_shadow_time < ' . $current_time . ' 
				AND topic_shadow_time <> 0
				AND topic_status = '. ITEM_MOVED . '';

	$result = $db->sql_query($sql);
	$topic_list[] = 0;
	
	while ($row = $db->sql_fetchrow($result))
	{
		$topic_list[] = (int) $row['topic_id'];
	}
	$db->sql_freeresult($result);
	
	delete_topics('topic_id', $topic_list);
	add_log('admin', 'SHADOW_TOPICS_PRUNED');
	
	$next_prune_time = $current_time + $config['shadow_prune_enable'] * 3600;
	
	set_config('shadow_prune_time', $next_prune_time, true);
}

?>