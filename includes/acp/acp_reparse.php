<?php
/**
*
* @package acp
* @version $Id  $
* @copyright (c) 2008 iWisdom
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

/**
* @package acp
*/
class acp_reparse
{
	var $u_action;

	function main($id, $mode)
	{
		global $user, $db, $phpbb_root_path, $phpEx, $config;
		
		$step = request_var('step', 0);
		
		if ($step < 1)
		{
			if (isset($_POST['cancel']))
			{
				redirect($phpbb_admin_path . 'index.' . $phpEx);
			}
			
			if (confirm_box(true))
			{
				redirect($this->u_action . '&step=1');
			}
			else
			{
				$s_hidden_fields = build_hidden_fields(array(
					'submit'		=> true,
				));
					
				confirm_box(false, $user->lang['BBCODE_REPARSE_CONFIRM'], $s_hidden_fields);
			}
		}
		else
		{
			$start = request_var('start', 0);
			
			if (!class_exists('parse_message'))
			{
				include($phpbb_root_path . "includes/message_parser." . $phpEx);
			}
						
			$bbcode_status = ($config['allow_bbcode']) ? true : false;
			$img_status = ($bbcode_status) ? true : false;
			$flash_status   = ($bbcode_status && $config['allow_post_flash']) ? true : false;
			
			$sql = 'SELECT * FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
				WHERE t.topic_id = p.topic_id
					ORDER BY p.post_id ASC';
			$result = $db->sql_query_limit($sql, 200, $start);
			
			while ($row = $db->sql_fetchrow($result))
			{
				decode_message($row['post_text'], $row['bbcode_uid']);

			   $message_parser = new parse_message();
			   $message_parser->message = str_replace('"', '&quot;', html_entity_decode($row['post_text']));
			   $message_parser->parse((($bbcode_status) ? $row['enable_bbcode'] : false), (($config['allow_post_links']) ? $row['enable_magic_url'] : false), $row['enable_smilies'], $img_status, $flash_status, true, $config['allow_post_links']);

			   if ($row['poll_title'] && $row['post_id'] == $row['topic_first_post_id'])
			   {
				  $row['poll_option_text'] = '';
				  $sql = 'SELECT * FROM ' . POLL_OPTIONS_TABLE . ' WHERE topic_id = ' . $row['topic_id'];
				  $result2 = $db->sql_query($sql);
				  while ($row2 = $db->sql_fetchrow($result2))
				  {
					 $row['poll_option_text'] .= $row2['poll_option_text'] . "\n";
				  }
				  $db->sql_freeresult($result2);

				  $poll = array(
					 'poll_title'      => $row['poll_title'],
					 'poll_length'      => $row['poll_length'],
					 'poll_max_options'   => $row['poll_max_options'],
					 'poll_option_text'   => $row['poll_option_text'],
					 'poll_start'      => $row['poll_start'],
					 'poll_last_vote'   => $row['poll_last_vote'],
					 'poll_vote_change'   => $row['poll_vote_change'],
					 'enable_bbcode'      => $row['enable_bbcode'],
					 'enable_urls'      => $row['enable_magic_url'],
					 'enable_smilies'   => $row['enable_smilies'],
					 'img_status'      => $img_status
				  );

				  $message_parser->parse_poll($poll);
			   }

			   $sql_data = array(
				  'post_text'         => $message_parser->message,
				  'post_checksum'      => md5($message_parser->message),
				  'bbcode_bitfield'   => $message_parser->bbcode_bitfield,
				  'bbcode_uid'      => $message_parser->bbcode_uid,
			   );

			   $sql = 'UPDATE ' . POSTS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_data) . '
				  WHERE post_id = ' . $row['post_id'];
			   $db->sql_query($sql);

			   if ($row['poll_title'] && $row['post_id'] == $row['topic_first_post_id'])
			   {
				  $sql_data = array(
					 'poll_title'      => str_replace($row['bbcode_uid'], $message_parser->bbcode_uid, $poll['poll_title']),
				  );

				  $sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_data) . '
					 WHERE topic_id = ' . $row['topic_id'];
				  $db->sql_query($sql);

				  $sql = 'SELECT * FROM ' . POLL_OPTIONS_TABLE . ' WHERE topic_id = ' . $row['topic_id'];
				  $result2 = $db->sql_query($sql);
				  while ($row2 = $db->sql_fetchrow($result2))
				  {
					 $sql_data = array(
						'poll_option_text'      => str_replace($row['bbcode_uid'], $message_parser->bbcode_uid, $row2['poll_option_text']),
					 );

					 $sql = 'UPDATE ' . POLL_OPTIONS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_data) . '
						WHERE topic_id = ' . $row['topic_id'] . '
						AND poll_option_id = ' . $row2['poll_option_id'];
					 $db->sql_query($sql);
				  }
			   }
			}
			$db->sql_freeresult($result);
			
			$sql = 'SELECT count(post_id) as post_count FROM ' . POSTS_TABLE;
			$result = $db->sql_query($sql);
			$post_count = $db->sql_fetchfield('post_count');
			
			$next_step = $start + 200;
			$step++;
			
			if ($post_count > $next_step)
			{
				meta_refresh(1, $this->u_action . '&start=' . $next_step . '&step=' . $step);
				trigger_error(sprintf($user->lang['BBCODE_REPARSE_PROGRESS'], ($step - 1), $step));
			}
			else
			{
				add_log('admin', 'LOG_BBCODE_REPARSED');
				trigger_error($user->lang['BBCODE_REPARSE_COMPLETE']);
			}
		}
	}
}

?>