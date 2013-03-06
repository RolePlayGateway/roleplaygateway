<?php
/**
*
* @FILENAME  : includes\functions_phpBBTags.php
* @DATE      : 19 Jan 2009
* @VERSION   : 1.0
* @COPYRIGHT : (c) 2009 phpbb3-mods
* @Website   : http://www.phpbb3-mods.com/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*/

/**
* @ignore
*/
function insert_tags($tags, $topic_id){
	
	$outcome 	= array('added' => 0, 'dups' => 0);
	$tag_array  = split(',', $tags);
	
	for($i = 0; $i < sizeof($tag_array); $i++)
	{
		if(insert_tag(str_replace('"', '', trim($tag_array[$i])), $topic_id)){
			$outcome['added']++;
		}else{
			$outcome['dups']++;
		}
		
	}
	
	return $outcome;
	
}

function insert_tag($tag, $topic_id){
	global $db;

	$sql_array = array('tag' 		=> $tag,
					   'topic_id' 	=> $topic_id);
	
	if(trim($tag) == '')
	{
	 	//exit right away
	 	return false;
	}
	
	$sql = 'SELECT *
	FROM ' . TAGS_TABLE . " WHERE " . $db->sql_build_array('SELECT', $sql_array);
	
	$result = $db->sql_query($sql);

	$row = $db->sql_fetchrow($result);
	if($row != false)
	{
		return false;
	}
	else
	{
			
		$sql = "INSERT INTO " . TAGS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_array);
		
		//echo $sql;
		
		$db->sql_query($sql);
		
		return true;
	}
}
function get_board_tags($limit){

	global $db, $config;

	$sql = "SELECT t.tag, COUNT(*) tag_count
			FROM " . TAGS_TABLE . " t, " . TOPICS_TABLE . " topics
			GROUP BY t.tag";
	if($limit > 0){
		$result = $db->sql_query_limit($sql, $limit, 0);
	}else{
		$result = $db->sql_query($sql);
	}
	
	$result_set = $db->sql_fetchrowset($result);

	$tag_array	= array();
	
	for($i = 0; $i < sizeof($result_set); $i++){
		$tag_array[$result_set[$i]['tag']] = $result_set[$i]['tag_count'];
	}

	return $tag_array;
}

function get_topic_tags($topic_id, $limit){

	global $db, $config; //TESTING NEEDS TO BE DOEN ON THESE NEW FUNCTIONS!!!

	$sql = "SELECT t.tag, COUNT(*) tag_count
			FROM " . TAGS_TABLE . " t, " . TOPICS_TABLE . " topics
			WHERE t.topic_id = $topic_id
			GROUP BY t.tag";

	if($limit > 0){
		$result = $db->sql_query_limit($sql, $limit, 0);
	}else{
		$result = $db->sql_query($sql);
	}
	
	$result_set = $db->sql_fetchrowset($result);			

	$tag_array	= array();
	
	for($i = 0; $i < sizeof($result_set); $i++){
		$tag_array[$result_set[$i]['tag']] = $result_set[$i]['tag_count'];
	}

	return $tag_array;
}
function get_tag_cloud($min_size, $max_size, $col1, $col2, $limit){
	
	global $phpEx, $user, $config;
	
	$tags = get_board_tags($limit);
	
	if(sizeof($tags) > 0){
		$min_count = min(array_values($tags));
		$max_count = max(array_values($tags));
		$spread = $max_count - $min_count;
			
		if($spread == 0){
			$spread = 1;
		}
		
		$cTools 	= new ColourTools();
		$gradient	= $cTools->gradient($col1, $col2, $max_count);
		foreach ($tags as $tag => $count)	{
			$size = $min_size + ($count - $min_count) * ($max_size - $min_size) / $spread;
			
			$tag_param = $tag;
			
			if(strpos($tag, ' ') !== false){
				$tag_param = '&quot;' . $tag . '&quot;';
			}
			
			$tag_cloud .= ' <a style="font-size:'.$size.'px; color:#'.$gradient[$count-1].';" href="'
							.append_sid($phpbb_root.'phpBBTags.'.$phpEx, 'mode=search&tag='.$tag_param).'">' . $tag . '</a> ';
							
		}
	}else{
		$tag_cloud = false;		
	}
	
	return $tag_cloud;
}

function get_tag_list($topic_id, $limit, $type = 'topic', $admin = false){
	global $phpbb_root_path, $phpEx, $user;
	
	$tag_list = "";
	
	$tags = get_topic_tags($topic_id, $limit);
	if(sizeof($tags) > 0)
	{
		foreach ($tags as $tag => $count)
		{
			$tag_param = $tag;
			
			if(strpos($tag, ' ') !== false)
			{
				$tag_param = '&quot;' . $tag . '&quot;';
			}
			if($admin){
			//$tag_list .= '<a class="tag-list-del" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
			}
			$tag_list .= '<a href="'.append_sid($phpbb_root_path.'phpBBTags.'.$phpEx, 'mode=search&tag='.$tag_param).'">' .
							$tag . '</a>, ';
		}
		$tag_list = substr($tag_list, 0, -2);
	}
	else
	{
		
		$tag_list = false;		
	}
		
	return $tag_list;
}

/*
USED FOR DIFFERENT TAGS DEPENDING ON WETHER IT SHOULD BE BOARD, TOPIC, FROUM. NEEDS RE WORKING

function get_topic_tags($type = 'board', $id = 0, $limit = 60){
	//TODO: santize input
	global $db;
		
	switch($type){
		case 'forum':
			//forum sql
			$sql = "SELECT t.tag, COUNT(*) tag_count
					FROM " . TAGS_TABLE . " t, " . TOPICS_TABLE . " topics
					WHERE topics.forum_id = $id AND t.topic_id = topics.topic_id
					GROUP BY t.tag";
		break;
		
		case 'topic':
			//topic sql
			$sql =  "SELECT t.tag, COUNT(*) tag_count
					 FROM " . TAGS_TABLE . " t 
					 WHERE t.topic_id = $id 
					 GROUP BY t.tag";
		break;
		
		case 'board':
		default:
			//board sql			 
			$sql =  "SELECT t.tag, COUNT(*) tag_count
					 FROM " . TAGS_TABLE . " t 
				     GROUP BY t.tag";
	}
	
	if($limit > 0){							
		$result = $db->sql_query_limit($sql, $limit, 0);
	}else{
		$result = $db->sql_query($sql);
	}
	$result_set = $db->sql_fetchrowset($result);			
	
	//echo '<pre>';
	//print_r($result_set);
	//echo "_________________________";
	$tag_array	= array();
	
	for($i = 0; $i < sizeof($result_set); $i++)
	{
		$tag_array[$result_set[$i]['tag']] = $result_set[$i]['tag_count'];
	}
	//echo '<pre>';
	//print_r($tag_array);
	return $tag_array;
}

function get_tag_list($topic_id, $limit, $type = 'topic', $admin = false){
	global $phpbb_root_path, $phpEx, $user;
	
	$tag_list = "";
	
	$tags = get_topic_tags($type, $topic_id, $limit);
	if(sizeof($tags) > 0)
	{
		foreach ($tags as $tag => $count)
		{
			$tag_param = $tag;
			
			if(strpos($tag, ' ') !== false)
			{
				$tag_param = '&quot;' . $tag . '&quot;';
			}
			if($admin){
			$tag_list .= '<a class="tag-list-del" href="#">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
			}
			$tag_list .= '<a href="'.append_sid($phpbb_root_path.'phpBBTags.'.$phpEx, 'mode=search&tag='.$tag_param).'">' .
							$tag . '</a>, ';
		}
		$tag_list = substr($tag_list, 0, -2);
	}
	else
	{
		
		$tag_list = false;		
	}
		
	return $tag_list;
}

function get_tag_cloud($type = 'board', $id = -1, $min_size = 8, $max_size = 26, $limit = 60){
	
	global $phpEx, $user, $config;
	
	$tags = get_topic_tags($type, $id, $limit);
	
	if(sizeof($tags) > 0)
	{
		$min_count = min(array_values($tags));
		$max_count = max(array_values($tags));
		$spread = $max_count - $min_count;
			
		if($spread == 0)
		{
			$spread = 1;
		}
		
		$cTools 	= new ColourTools();
		$gradient	= $cTools->gradient($config['pbt_colour1'], $config['pbt_colour2'], $max_count);
				
		foreach ($tags as $tag => $count)
		{
			$size = $min_size + ($count - $min_count) 
					* ($max_size - $min_size) / $spread;
			
			$tag_param = $tag;
			
			if(strpos($tag, ' ') !== false)
			{
				$tag_param = '&quot;' . $tag . '&quot;';
			}
			
			$tag_cloud .= ' <a style="font-size:'.$size.'px; color:#'.$gradient[$count-1].';" href="'.append_sid($phpbb_root.'phpBBTags.'.$phpEx, 'mode=search&tag='.$tag_param).'">' . $tag . '</a> ';
		}
	}
	else
	{
		
		$tag_cloud = false;		
	}
	
	
	return $tag_cloud;
}
*/
function get_num_rows($tags){

	global $db;

	$end = $config['topics_per_page'];
	$tag_array = filter_tags($tags);
	
	
	$sql = "SELECT topi.topic_id,
			topi.forum_id,
			topi.topic_type,
			topi.topic_replies_real,
			topi.topic_replies,
			topi.topic_status,
			topi.topic_moved_id,
			topi.topic_last_post_time,
			topi.topic_approved,
			topi.topic_poster,
			topi.topic_first_poster_name,
			topi.topic_time,
			topi.topic_last_post_subject,
			topi.topic_last_post_time,
			topi.topic_last_poster_id,
			topi.topic_views,
			topi.topic_title,
			topi.icon_id,
			topi.topic_attachment,
			topi.topic_first_poster_name,
			COUNT(topi.topic_id) count
			FROM ". TAGS_TABLE ." t, ". TOPICS_TABLE ." topi";
	
	if(!empty($tag_array['include'])){		
		$sql .=	" WHERE (t.tag IN (";
		$sql .= prepare_search_string($tag_array['include']);
		$sql .= "))";
	}
	
	if(!empty($tag_array['include']) && !empty($tag_array['exclude'])){
		$sql .= " AND ";
	}else if(empty($tag_array['include']) && !empty($tag_array['exclude'])){
		$sql .= " WHERE ";
	}
	
	if(!empty($tag_array['exclude'])){
		$sql .= " (topi.topic_id NOT IN ( 
						SELECT top2.topic_id
						FROM ". TAGS_TABLE ." t2, ". TOPICS_TABLE ." top2
						WHERE t2.topic_id = top2.topic_id";
		$sql .= prep_exclusion_string($tag_array['exclude']);
		$sql .= "))";
	}

	$sql .= "AND topi.topic_id = t.topic_id
			 GROUP BY topi.topic_id,
			 topi.forum_id,
			 topi.topic_type,
			 topi.topic_replies_real,
			 topi.topic_replies,
			 topi.topic_status,
			 topi.topic_moved_id,
			 topi.topic_last_post_time,
			 topi.topic_approved,
			 topi.topic_poster,
			 topi.topic_first_poster_name,
			 topi.topic_time,
			 topi.topic_last_post_subject,
			 topi.topic_last_post_time,
			 topi.topic_last_poster_id,
			 topi.topic_views,
			 topi.topic_title,
			 topi.icon_id,
			 topi.topic_attachment,
			 topi.topic_first_poster_name
			 ORDER BY count DESC";
	
	//echo '<pre>';
	//echo $sql;
			
	if(!($result = $db->sql_query($sql)))
	{
		message_die(GENERAL_ERROR, 'Error retrieving search results', '', __LINE__, __FILE__, $sql);
	}
	
	$result_set = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	return sizeof($result_set);
	
	
	
}

function search_tags($tags, $start = 0, $end = false){
	
	global $db, $config;
	
	$topics_count = (int) $db->sql_fetchfield('num_topics');

	if($end === false){
		$end = $config['topics_per_page'];	
	}
	
	$tag_array = filter_tags($tags);

	$sql = "SELECT topi.topic_id,
			topi.forum_id,
			topi.topic_type,
			topi.topic_replies_real,
			topi.topic_replies,
			topi.topic_status,
			topi.topic_moved_id,
			topi.topic_last_post_time,
			topi.topic_approved,
			topi.topic_poster,
			topi.topic_first_poster_name,
			topi.topic_time,
			topi.topic_last_post_subject,
			topi.topic_last_post_time,
			topi.topic_last_poster_id,
			topi.topic_views,
			topi.topic_title,
			topi.icon_id,
			topi.topic_attachment,
			topi.topic_first_poster_name,
			topi.topic_last_post_id,
			topi.topic_last_poster_id,
			topi.topic_last_poster_name,
			topi.topic_last_poster_colour,
			topi.topic_last_post_subject,
			topi.topic_last_post_time,
			topi.topic_last_view_time,
			COUNT(topi.topic_id) count
			FROM ". TAGS_TABLE ." t, ". TOPICS_TABLE ." topi";
	
	if(!empty($tag_array['include'])){		
		$sql .=	" WHERE (t.tag IN (";
		$sql .= prepare_search_string($tag_array['include']);
		$sql .= "))";
	}
	
	if(!empty($tag_array['include']) && !empty($tag_array['exclude'])){
		$sql .= " AND ";
	}else if(empty($tag_array['include']) && !empty($tag_array['exclude'])){
		$sql .= " WHERE ";
	}
	
	
	if(!empty($tag_array['exclude'])){
		$sql .= "(topi.topic_id NOT IN ( 
						SELECT top2.topic_id
						FROM ". TAGS_TABLE ." t2, ". TOPICS_TABLE ." top2
						WHERE t2.topic_id = top2.topic_id";
		$sql .= prep_exclusion_string($tag_array['exclude']);
		$sql .= "))";
	}

	$sql .= "AND topi.topic_id = t.topic_id
			 GROUP BY topi.topic_id,
			 topi.forum_id,
			 topi.topic_type,
			 topi.topic_replies_real,
			 topi.topic_replies,
			 topi.topic_status,
			 topi.topic_moved_id,
			 topi.topic_last_post_time,
			 topi.topic_approved,
			 topi.topic_poster,
			 topi.topic_first_poster_name,
			 topi.topic_time,
			 topi.topic_last_post_subject,
			 topi.topic_last_post_time,
			 topi.topic_last_poster_id,
			 topi.topic_views,
			 topi.topic_title,
			 topi.icon_id,
			 topi.topic_attachment,
			 topi.topic_first_poster_name,
			 topi.topic_last_post_id,
			 topi.topic_last_poster_id,
			 topi.topic_last_poster_name,
			 topi.topic_last_poster_colour,
			 topi.topic_last_post_subject,
			 topi.topic_last_post_time,
			 topi.topic_last_view_time
			 ORDER BY count DESC";
		
	if(!($result = $db->sql_query_limit($sql, $end, $start)))
	{
		message_die(GENERAL_ERROR, 'Error retrieving search results', '', __LINE__, __FILE__, $sql);
	}
	
	$result_set = $db->sql_fetchrowset($result);


	//echo '<pre>';
	//echo var_dump($result_set);
		
	return $result_set;
	
}

function prep_exclusion_string($exclude_array){
	$string = '';
	
	for($i = 0; $i < sizeof($exclude_array); $i++){
		$string .= ' AND t2.tag = \'' . $exclude_array[$i] . '\'';
	}
	
	return $string;
}

function filter_tags($tags){
	$tag_array = tags_to_array($tags);
	$incl = array();
	$excl = array();
	$filtered_array = array('include' => $incl, 'exclude' => $excl);
	
	for($i = 0; $i < sizeof($tag_array); $i++){
		if($tag_array[$i][0] == '-'){
			array_push($filtered_array['exclude'], substr($tag_array[$i], 1));
		}else{
			array_push($filtered_array['include'], $tag_array[$i]);
		}
	}
	
	//echo '<pre>';
	//print_r($filtered_array);
	return $filtered_array;
}

function tags_to_array($string){
	$str_array 	= array();
	$qoute  	= false;
	$str_buffer = "";
	$tags 		= html_entity_decode($string);
	//$tags = $string;
	for($i = 0; $i < strlen($tags); $i++ )
	{		
		
		if(($tags[$i] == '"') && $qoute === true)
		{
			$qoute = false;
		}
		else if(($tags[$i] == '"') && $qoute === false)
		{
			$qoute = true;
		}		

		if($tags[$i] == ' ' && !$qoute)
		{
		//str_replace(
			$str_buffer = str_replace('"', '', $str_buffer);
						
			array_push($str_array, $str_buffer);
			$str_buffer = "";
		}
		else
		{
			//echo $str_buffer;
			$str_buffer .= $tags[$i];
		}

	}
	$str_buffer = htmlspecialchars(str_replace('"', '', $str_buffer));
	//echo $str_buffer;
	array_push($str_array, $str_buffer);
	
	//echo '<pre>';
	//print_r($str_array);
	
	return $str_array;
	
}

function prepare_search_string($str_array){
	
	global $db;
	
	$prep_str = "";
	for($j = 0; $j < sizeof($str_array); $j++)
	{
		$prep_str .= "'" . $db->sql_escape($str_array[$j]) . "',";
		//echo $prep_str.'-'.$j.'<br />';
	}
	
	//echo $prep_str;
	
	return substr($prep_str, 0, strlen($prep_str)-1);
}

?>