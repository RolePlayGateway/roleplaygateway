<?php
/**
*
* @FILENAME  : install\includes\acp\acp_phpBBTags.php
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
* @package acp
*/

include($phpbb_root_path . '/includes/functions_user.'.$phpEx);

class acp_phpBBTags
{
	var $u_action;
					
	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
					
		$user->add_lang('mods/phpBBTags_lang');
							
						
		// Set up general vars
						
		switch($mode)
		{
			case 'configure':
				$this->configure();
			break;
			
			case 'manage':
				$this->manage();
			break;
			
			case 'remove':
				$this->remove();
			break;
			
			case 'clear_orphans':
				$this->clear_orphans();
			break;
			
			case 'view_all':
				$this->view_all();
			break;
		}							 
	}
	
	//module functions
	function manage(){
	
		global $template, $user, $phpEx, $phpbb_root_path, $phpbb_admin_path;
		
		$this->page_title 	= 'PBT_ACP_MANAGE_TITLE';
		$this->tpl_name     = 'acp_phpBBTags_manage';

		$tags = utf8_normalize_nfc(request_var('tag', '', true));

		$template->assign_vars(array(
				'S_TAG_SEARCH_ACTION'	=> $this->u_action,
				'S_SHOW_RESULTS'		=> false,
				'SEARCH_TAGS'			=> $tags
			)
		);
		
		if(isset($_POST['search']) || $tags != '')
		{
			
			if($tags == '')
			{
				$message = 	$user->lang['PBT_NO_SEARCH_CRIT'] . '<br /><br />';
							//sprintf($user->lang['RETURN_SEARCH'], '<a href="' . $meta_info . '">', '</a>');
				trigger_error($message . adm_back_link($this->u_action), E_USER_WARNING);

		
			}
			else
			{
				$topics = search_tags($tags);
				$template->assign_vars(array(
						'S_SHOW_RESULTS'	=> true,
					)
				);
				$row_count = 0;
				foreach($topics as $row){
					
					//echo append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&t=' . $row['topic_id']).'<br>';
					$template->assign_block_vars('topicrow', array(
								'TOPIC_TITLE' 	=> $row['topic_title'],
								'TOPIC_TAGS'  	=> $this->get_tag_list($row['topic_id'], $tags),
								'U_TOPIC_LINK'	=> append_sid($phpbb_root_path."viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&t=' . $row['topic_id']),
								'S_ROW_COUNT'	=> $row_count++,
								
							)
					);
				}//end of foreach	
			}
		}		
	}//end of function
	
	function configure(){
		
		global $template, $user, $phpEx, $phpbb_admin_path, $config;
		
		$submit = (isset($_POST['submit']) ? true : false);
		
		if($submit)
		{
		
			$data = request_var('config', array('' => ''));
		
			//make sure that the hex codes are preceded by a # char
			if($data['pbt_colour1'][0] != '#'){
				$data['pbt_colour1'] = '#' . $data['pbt_colour1'];
			}
			if($data['pbt_colour2'][0] != '#'){
				$data['pbt_colour2'] = '#' . $data['pbt_colour2'];
			}
					
			$config_vars = array(
				'vars' => array(
					'pbt_tags' 			 => array('lang' => '', 'validate' => 'int',  	'type' => 'text:0:10', 		'explain' => false),
					'pbt_on' 			 => array('lang' => '', 'validate' => 'bool', 	'type' => 'radio:yes_no', 	'explain' => false),
					'pbt_max_font'		 => array('lang' => '', 'validate' => 'int',  	'type' => 'text:5:5', 		'explain' => false),
					'pbt_min_font'		 => array('lang' => '', 'validate' => 'int',  	'type' => 'text:5:5', 		'explain' => false),
					'pbt_colour1'		 => array('lang' => '', 'validate' => 'string', 'type' => 'text:0:7', 		'explain' => false),
					'pbt_colour2'		 => array('lang' => '', 'validate' => 'string', 'type' => 'text:0:7', 		'explain' => false),
				)
			);
			
			$error = array();
			validate_config_vars($config_vars, $data, $error);

			$validate = array(
				'pbt_tags' 		=> array('num'),
				'pbt_max_font'	=> array('num'),
				'pbt_min_font'	=> array('num'),
				'pbt_colour1'	=> array('string', false, 4, 7),
				'pbt_colour2'	=> array('string', false, 4, 7),
			);
			
			$error = validate_data($data, $validate);
			
			//custom validation
			$ct = new ColourTools();
			
			if(!is_numeric($data['pbt_tags'])) 			$error[] = $user->lang['PBT_TAGS_NOT_NUM'];
			if(!is_numeric($data['pbt_max_font'])) 		$error[] = $user->lang['PBT_MAX_FONT_NOT_NUM'];
			if(!is_numeric($data['pbt_min_font'])) 		$error[] = $user->lang['PBT_MIN_FONT_NOT_NUM'];
			
			if(!$ct->check_hex($data['pbt_colour1']))	$error[] = $user->lang['PBT_ACP_COLOUR1_INVALID'];
			if(!$ct->check_hex($data['pbt_colour2']))	$error[] = $user->lang['PBT_ACP_COLOUR2_INVALID'];
		
		
			
			if(!sizeof($error)){
			
				$booleans = array('pbt_on');
				
				foreach ($config_vars['vars'] as $config_name => $null)
				{
					$config_value = $data[$config_name];
					
					if(in_array($config_name, $booleans))
					{
						$config_value = ($config_value == 'yes' ? 1 : 0);
					}
									
					set_config($config_name, $config_value, false);
				}
							
				$message = 	$user->lang['PBT_ACP_CONF_UPDATE_SUCCESSFUL'];
				$link 	 = append_sid("index.php", "i=phpBBTags&mode=configure");
				
				meta_refresh(4, $phpbb_admin_path . $link);	
				trigger_error($message . adm_back_link($link));
			
			}			
					
			$template->assign_vars(array(
						'ERROR'					=> implode('<br />', $error),
						'S_TAGS_ON'				=> ($data['pbt_on'] == 'yes' ? true : false),
						'TAG_AMOUNT'			=>  $data['pbt_tags'],
						'MAX_SIZE'				=>  $data['pbt_max_font'],
						'MIN_SIZE'				=>  $data['pbt_min_font'],
						'COLOUR1'				=>  $data['pbt_colour1'],
						'COLOUR2'				=>  $data['pbt_colour2'],
				)
			);			

			$this->page_title 	= 'PBT_ACP_CONFIGURE_TITLE';
			$this->tpl_name		= 'acp_phpBBTags_configure';

		}
		else
		{
					
			$template->assign_vars(array(
						'S_TAGS_ON'				=> ($config['pbt_on'] == 1 ? true : false),
						'TAG_AMOUNT'			=>  $config['pbt_tags'],
						'MAX_SIZE'				=>  $config['pbt_max_font'],
						'MIN_SIZE'				=>  $config['pbt_min_font'],
						'COLOUR1'				=>  $config['pbt_colour1'],
						'COLOUR2'				=>  $config['pbt_colour2'],

				)
			);

			$this->page_title 	= 'PBT_ACP_CONFIGURE_TITLE';
			$this->tpl_name		= 'acp_phpBBTags_configure';
		}

	}
	
	function remove()
	{
		
		global $template, $user, $phpbb_admin_path;
		
		//$this->page_title 	= 'PBT_ACP_CONFIGURE_TITLE';
		//$this->tpl_name 	= 'acp_phpBBTags_configure';
		
		$topic_id	= request_var('topic_id', 0);
		$tag		= utf8_normalize_nfc(request_var('tag_id', '', true));
		if($tag == '')
		{
			//$tag = request_var('tag_id', array(array()));
			$tag = request_var('tag_id', array('' => array('' => '')), true);
		}
			
		//check where we came from
		if(is_array($tag))
		{
			//if its an array then the user MUST have come form the view all page
			$redirect = 'view_all';
		}
		else
		{
			$redirect 	= 'manage';
		}

		$search_tag = utf8_normalize_nfc(request_var('tag', '', true));

		if (confirm_box(true))
		{
			//echo '<pre>';
			//print_r($_POST['tag_id']);
			//echo '<br /><br />$tag<br /><br />';
			//print_r($tag);
			//echo '<pre>';
			//var_dump($tag);
			if(is_array($tag))
			{
				foreach($tag as $topic_id => $topic_tags){
					foreach($topic_tags as $tag_text){
						$this->remove_tag($tag_text, $topic_id);
					}
				}
			}
			else
			{
				$this->remove_tag($tag, $topic_id);
			}
			//exit();
			//echo $topic_id;
			$message = 	$user->lang['PBT_ACP_REMOVE_SUCCESSFUL'];
			$action = append_sid('index.php?i=phpBBTags&mode='.$redirect);
					
			meta_refresh(3, $phpbb_admin_path . $action);	
			trigger_error($message . adm_back_link($action));
		
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				'tag_id'	=> $tag,
				'topic_id'	=> $topic_id,
				'tag'		=> $search_tag
				)
			);
	
			//display confirm box
			confirm_box(false, $user->lang['PFB_ACP_REMOVE_CONF'], $s_hidden_fields);
		}
		
		$action = append_sid('index.php?i=phpBBTags&mode='.$redirect);
		meta_refresh(3, $phpbb_admin_path . $action);	
		trigger_error($user->lang['PBT_ACTION_CANCELLED'] . adm_back_link($action));
	
	}
	
	function clear_orphans()
	{
		global $db, $user;
		
		if (confirm_box(true))
		{
			$sql = "DELETE FROM " . TAGS_TABLE . " WHERE topic_id
					NOT IN (SELECT topic_id FROM " . TOPICS_TABLE . ")";
			
			$result = $db->sql_query($sql);
			
			$orphans = $db->sql_affectedrows($result);
			
			$message = 	sprintf($user->lang['PBT_ACP_ORPHAN_SUCCESS'], $orphans);
			
			trigger_error($message);			
			
		
		}
		else
		{
			$sql = "SELECT count(*) count FROM " . TAGS_TABLE . " WHERE topic_id
					NOT IN (SELECT topic_id FROM " . TOPICS_TABLE . ")";
			
			$result = $db->sql_query($sql);
					
			$orphans = $db->sql_fetchrow($result);
					
			//display mode
			confirm_box(false, sprintf($user->lang['PFB_ACP_ORPHAN_CONF'], $orphans['count']), $s_hidden_fields);
		}
		
		$link  = append_sid("index.php", "i=phpBBTags&mode=configure");

		redirect($link);
		
		//$this->page_title 	= 'PBT_ACP_CLEAR_ORPHANS';
		//$this->tpl_name		= 'acp_phpBBTags_configure';
		
	}
	
	function view_all()
	{
		global  $db, $template, $user, $phpbb_root_path, $phpbb_admin_path,
				$phpEx;

		$sql = 'SELECT tags.*, topics.topic_title topic_title FROM ' . TAGS_TABLE . ' tags
				JOIN ' . TOPICS_TABLE . ' topics ON tags.topic_id = topics.topic_id';
		
		$result = $db->sql_query($sql);
		$tags = $db->sql_fetchrowset($result);
		
		//var_dump($tags);

		foreach($tags as $row){
			$template->assign_block_vars('tags', array(
						'TOPIC_ID' 		=> $row['topic_id'],
						'TAG'			=> $row['tag'],
						'TOPIC_TITLE' 	=> $row['topic_title'],
						'U_TOPIC_LINK'	=> append_sid($phpbb_root_path."viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&t=' . $row['topic_id']),

				));
		}
		
		$template->assign_vars(array(
						'S_FORM_ACTION' => append_sid($phpbb_admin_path."index.$phpEx", 'i=phpBBTags&mode=remove'),
		));
		
		$this->page_title 	= 'PBT_ACP_VIEW_ALL';	
		$this->tpl_name		= 'acp_phpBBTags_view_all';
	}
	
	//other functions
	function remove_tag($tag, $topic_id)
	{
	
		global $db;
			
		$sql = "DELETE FROM " . TAGS_TABLE . " WHERE tag = '".$db->sql_escape($tag)."' AND topic_id = ".$db->sql_escape($topic_id);
		
		if(!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Error retrieving search results', '', __LINE__, __FILE__, $sql);
		}
		
	}
	
	function search_tags($tags, $start = 0){
		
		global $db, $config;
		
		$topics_count = (int) $db->sql_fetchfield('num_topics');
	
		$end = $config['topics_per_page'];
		$tag_array = $this->prepare_search_string($tags);
		
		$sql = "SELECT top.*, COUNT(top.topic_id) ttid
				FROM ".TAG_MAP_TABLE." tm, ". TAGS_TABLE ." t, ". TOPICS_TABLE ." top
				WHERE tm.tag_id = t.tag_id
				AND (t.tag IN (";
		$sql .= $tag_array;
		$sql .= "))
				AND top.topic_id = tm.topic_id
				GROUP BY top.topic_id
				ORDER BY ttid DESC";
					
		if(!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Error retrieving search results', '', __LINE__, __FILE__, $sql);
		}
		
		$result_set = $db->sql_fetchrowset($result);
		
		return $result_set;
		
		//echo '<pre>';
		//echo var_dump($result_set);
		
	}
	function get_topic_tags($topic_id){
		
		global $db;
		
		$sql =  "SELECT t.tag
				 FROM " . TAGS_TABLE . " t ";
		$sql .=	 ($topic_id == -1 ? "" : "WHERE t.topic_id = $topic_id ");
		$sql .= "GROUP BY t.tag";

		if(!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Error retrieving topic tags', '', __LINE__, __FILE__, $sql);
		}
		
		$result_set = $db->sql_fetchrowset($result);			
		
		$tag_array	= array();
		
		for($i = 0; $i < sizeof($result_set); $i++)
		{
			$tag_array[$result_set[$i]['tag']] = $result_set[$i]['tag'];
		}

		return $tag_array;
	}
	
	function get_tag_list($topic_id, $search_str = ''){
		global $phpEx, $user, $phpbb_admin_path;
		
		$tag_list = "";
		
		$tags = $this->get_topic_tags($topic_id);
		if(sizeof($tags) > 0)
		{
			foreach ($tags as $tag => $tag_id)
			{
				$tag_param = $tag;
				
				if(strpos($tag, ' ') !== false)
				{
					$tag_param = '&quot;' . $tag . '&quot;';
				}
				
				$params = "i=phpBBTags&mode=remove&tag_id={$tag_id}&back_link=acp_phpBBTags&topic_id={$topic_id}";
				
				if($search_str != ''){
					$params .= '&tag=' . $search_str;
				}
				
				$remove_link = append_sid("{$phpbb_admin_path}index.$phpEx", $params);		
				$tag_list .= ' <a href="'.$remove_link.'">' . $tag . '</a>, ';
			}
			$tag_list = substr($tag_list, 0, -2);
		}
		else
		{
			$tag_list = false;		
		}
			
		return $tag_list;
	}	
	function prepare_search_string($string){
		
		global $db;
		
		$str_array = array();
		$qoute  = false;
		
		$tags = html_entity_decode($string);
		
		for($i = 0; $i < strlen($tags); $i++ )
		{		
			
			if(($tags[$i] == '"' || $tags[$i] == '\'') && $qoute === true)
			{
				$qoute = false;
			}
			else if(($tags[$i] == '"' || $tags[$i] == '\'') && $qoute === false)
			{
				$qoute = true;
			}		
	
			if($tags[$i] == ' ' && !$qoute)
			{
				$str_buffer = str_replace('"', '', $str_buffer);
				$str_buffer = str_replace("'", '', $str_buffer);
				array_push($str_array, $str_buffer);
				$str_buffer = "";
			}
			else
			{
				$str_buffer .= $tags[$i];
			}
	
		}
		
		//echo $str_buffer;
		$str_buffer = str_replace('"', '', $str_buffer);
		$str_buffer = str_replace("'", '', $str_buffer);
		array_push($str_array, $str_buffer);
		
		//echo $str_buffer;
		
		$pre_str = "";
		for($j = 0; $j < sizeof($str_array); $j++)
		{
			$prep_str .= "'" . $db->sql_escape($str_array[$j]) . "',";
		}
		
		return substr($prep_str, 0, strlen($prep_str)-1);
	}	
}
			
?>