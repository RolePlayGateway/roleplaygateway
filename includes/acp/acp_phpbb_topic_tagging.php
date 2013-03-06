<?php
/** 
*
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

include($phpbb_root_path . '/includes/functions_user.'.$phpEx);

class acp_phpbb_topic_tagging
{
	var $u_action;
					
	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
					
		$user->add_lang('mods/phpbb_topic_tagging_lang');
							
						
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
		
		$this->page_title 	= 'PTT_ACP_MANAGE_TITLE';	
		$this->tpl_name     = 'acp_phpbb_topic_tagging_manage';

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
				$message = 	$user->lang['PTT_NO_SEARCH_CRIT'] . '<br /><br />';
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
			if($data['ptt_colour1'][0] != '#'){
				$data['ptt_colour1'] = '#' . $data['ptt_colour1'];
			}
			if($data['ptt_colour2'][0] != '#'){
				$data['ptt_colour2'] = '#' . $data['ptt_colour2'];
			}
					
			$config_vars = array(
				'vars' => array(
					'ptt_tags' 			 => array('lang' => '', 'validate' => 'int',  	'type' => 'text:0:10', 		'explain' => false),
					'ptt_on' 			 => array('lang' => '', 'validate' => 'bool', 	'type' => 'radio:yes_no', 	'explain' => false),
					'ptt_max_font'		 => array('lang' => '', 'validate' => 'int',  	'type' => 'text:5:5', 		'explain' => false),
					'ptt_min_font'		 => array('lang' => '', 'validate' => 'int',  	'type' => 'text:5:5', 		'explain' => false),
					'ptt_colour1'		 => array('lang' => '', 'validate' => 'string', 'type' => 'text:0:7', 		'explain' => false),
					'ptt_colour2'		 => array('lang' => '', 'validate' => 'string', 'type' => 'text:0:7', 		'explain' => false),
					'ptt_tag_sort'		 => array('lang' => '', 'validate' => 'string', 'type' => 'text',    		'explain' => false),

				)
			);
			
			$error = array();
			validate_config_vars($config_vars, $data, $error);

			$validate = array(
				'ptt_tags' 		=> array('num'),
				'ptt_max_font'	=> array('num'),
				'ptt_min_font'	=> array('num'),
				'ptt_colour1'	=> array('string', false, 4, 7),
				'ptt_colour2'	=> array('string', false, 4, 7),
			);
			
			$error = validate_data($data, $validate);
			
			//custom validation
			$ct = new ColourTools();
			
			if(!is_numeric($data['ptt_tags'])) 			$error[] = $user->lang['PTT_TAGS_NOT_NUM'];			
			if(!is_numeric($data['ptt_max_font'])) 		$error[] = $user->lang['PTT_MAX_FONT_NOT_NUM'];			
			if(!is_numeric($data['ptt_min_font'])) 		$error[] = $user->lang['PTT_MIN_FONT_NOT_NUM'];			
			
			if(!$ct->check_hex($data['ptt_colour1']))	$error[] = $user->lang['PTT_ACP_COLOUR1_INVALID'];
			if(!$ct->check_hex($data['ptt_colour2']))	$error[] = $user->lang['PTT_ACP_COLOUR2_INVALID'];
		
		
			
			if(!sizeof($error)){
			
				$booleans = array('ptt_on');
				
				foreach ($config_vars['vars'] as $config_name => $null)
				{
					$config_value = $data[$config_name];
					
					if(in_array($config_name, $booleans))
					{
						$config_value = ($config_value == 'yes' ? 1 : 0);
					}
									
					set_config($config_name, $config_value, false);
				}
							
				$message = 	$user->lang['PTT_ACP_CONF_UPDATE_SUCCESSFUL'];
				$link 	 = append_sid("index.php", "i=phpbb_topic_tagging&mode=configure");
				
				meta_refresh(4, $phpbb_admin_path . $link);	
				trigger_error($message . adm_back_link($link));
			
			}			
					
			$template->assign_vars(array(
						'ERROR'					=> implode('<br />', $error),
						'S_TAGS_ON'				=> ($data['ptt_on'] == 'yes' ? true : false),
						'TAG_AMOUNT'			=>  $data['ptt_tags'],
						'MAX_SIZE'				=>  $data['ptt_max_font'],
						'MIN_SIZE'				=>  $data['ptt_min_font'],
						'COLOUR1'				=>  $data['ptt_colour1'],
						'COLOUR2'				=>  $data['ptt_colour2'],
				)
			);			

            $tag_sort_options = array(
                array('name' => $user->lang['PTT_ACP_TAG_SORT_DEFAULT'], 'value' => 'alphabetical'),
                array('name' => $user->lang['PTT_ACP_TAG_SORT_POPULAR'], 'value' => 'popular'),
                array('name' => $user->lang['PTT_ACP_TAG_SORT_RANDOM'],  'value' => 'random'),
            );

            foreach($tag_sort_options as $row){
                $template->assign_block_vars('tag_sort_options', array(
                            'NAME'          => $row['name'],
                            'VALUE'			=> $row['value'],
                            'SELECTED'   	=> $config['ptt_tag_sort'] == $row['value'] ? ' selected' : '',

                    ));
            }

			$this->page_title 	= 'PTT_ACP_CONFIGURE_TITLE';	
			$this->tpl_name		= 'acp_phpbb_topic_tagging_configure';

		}
		else
		{

            $tag_sort_options = array(
                array('name' => $user->lang['PTT_ACP_TAG_SORT_DEFAULT'], 'value' => 'alphabetical'),
                array('name' => $user->lang['PTT_ACP_TAG_SORT_POPULAR'], 'value' => 'popular'),
                array('name' => $user->lang['PTT_ACP_TAG_SORT_RANDOM'],  'value' => 'random'),
            );

            foreach($tag_sort_options as $row){
                $template->assign_block_vars('tag_sort_options', array(
                            'NAME'          => $row['name'],
                            'VALUE'			=> $row['value'],
                            'SELECTED'   	=> $config['ptt_tag_sort'] == $row['value'] ? ' selected' : '',

                    ));
            }

			$template->assign_vars(array(
						'S_TAGS_ON'				=> ($config['ptt_on'] == 1 ? true : false),
						'TAG_AMOUNT'			=>  $config['ptt_tags'],
						'MAX_SIZE'				=>  $config['ptt_max_font'],
						'MIN_SIZE'				=>  $config['ptt_min_font'],
						'COLOUR1'				=>  $config['ptt_colour1'],
						'COLOUR2'				=>  $config['ptt_colour2'],

				)
			);

			$this->page_title 	= 'PTT_ACP_CONFIGURE_TITLE';	
			$this->tpl_name		= 'acp_phpbb_topic_tagging_configure';
		}

	}
	
	function remove()
	{
		
		global $template, $user, $phpbb_admin_path;
		
		//$this->page_title 	= 'PTT_ACP_CONFIGURE_TITLE';	
		//$this->tpl_name 	= 'acp_phpbb_topic_tagging_configure';
		
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

			$message = 	$user->lang['PTT_ACP_REMOVE_SUCCESSFUL'];
			$action = append_sid('index.php?i=phpbb_topic_tagging&mode='.$redirect);
					
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
			confirm_box(false, $user->lang['PTT_ACP_REMOVE_CONF'], $s_hidden_fields);
		}
		
		$action = append_sid('index.php?i=phpbb_topic_tagging&mode='.$redirect);
		meta_refresh(3, $phpbb_admin_path . $action);	
		trigger_error($user->lang['PTT_ACTION_CANCELLED'] . adm_back_link($action));
	
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
			
			$message = 	sprintf($user->lang['PTT_ACP_ORPHAN_SUCCESS'], $orphans);
			
			trigger_error($message);			
			
		
		}
		else
		{
			$sql = "SELECT count(*) count FROM " . TAGS_TABLE . " WHERE topic_id
					NOT IN (SELECT topic_id FROM " . TOPICS_TABLE . ")";
			
			$result = $db->sql_query($sql);
					
			$orphans = $db->sql_fetchrow($result);
					
			//display mode
			confirm_box(false, sprintf($user->lang['PTT_ACP_ORPHAN_CONF'], $orphans['count']));
		}
		
		$link  = append_sid("index.php", "i=phpbb_topic_tagging&mode=configure");

		redirect($link);
		
		//$this->page_title 	= 'PTT_ACP_CLEAR_ORPHANS';	
		//$this->tpl_name		= 'acp_phpbb_topic_tagging_configure';
		
	}
	
	function view_all()
	{
		global  $db, $template, $user, $phpbb_root_path, $phpbb_admin_path,
				$phpEx;

		$sql = 'SELECT tags.*, topics.topic_title topic_title, topics.forum_id FROM ' . TAGS_TABLE . ' tags
				JOIN ' . TOPICS_TABLE . ' topics ON tags.topic_id = topics.topic_id';
		
		$result = $db->sql_query($sql);
		$tags = $db->sql_fetchrowset($result);
		
		foreach($tags as $row){
			$template->assign_block_vars('tags', array(
						'TOPIC_ID' 		=> $row['topic_id'],
						'TAG'			=> $row['tag'],
						'TOPIC_TITLE' 	=> $row['topic_title'],
						'U_TOPIC_LINK'	=> append_sid($phpbb_root_path."viewtopic.$phpEx", 'f=' . $row['forum_id'] . '&t=' . $row['topic_id']),

				));
		}
		
		$template->assign_vars(array(
						'S_FORM_ACTION' => append_sid($phpbb_admin_path."index.$phpEx", 'i=phpbb_topic_tagging&mode=remove'),		
		));
		
		$this->page_title 	= 'PTT_ACP_VIEW_ALL_TITLE';
		$this->tpl_name		= 'acp_phpbb_topic_tagging_view_all';
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
				
				$params = "i=phpbb_topic_tagging&mode=remove&tag_id={$tag_id}&back_link=acp_phpbb_topic_tagging&topic_id={$topic_id}";
				
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
		
		$str_buffer = str_replace('"', '', $str_buffer);
		$str_buffer = str_replace("'", '', $str_buffer);
		array_push($str_array, $str_buffer);
				
		$pre_str = "";
		for($j = 0; $j < sizeof($str_array); $j++)
		{
			$prep_str .= "'" . $db->sql_escape($str_array[$j]) . "',";
		}
		
		return substr($prep_str, 0, strlen($prep_str)-1);
	}	
}
			
?>