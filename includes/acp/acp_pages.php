<?php
/**
*
* Static Pages MOD acp module file
*
* @version $Id$
* @copyright (c) 2009 Vojtěch Vondra
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
class acp_pages
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpEx;

    include($phpbb_root_path . 'includes/acp/info/acp_pages.' . $phpEx);
    
    // MOD language file gets added automatically
		$user->add_lang(array('posting'));

		// Set up general vars
		$action 	= request_var('action', '');
		$action	 = (isset($_POST['add'])) ? 'add' : $action;
		$action	 = (isset($_POST['save'])) ? 'save' : $action;
		$page_id = request_var('id', 0);

		// Handle install and update through ACP file.
    $module_info = new acp_pages_info();
    $module_info_array = $module_info->module();
		$mod_version = $module_info_array['version'];

		if (!isset($config['static_pages_version']))
		{
			$module_info->install($this->u_action);
		}
		else if (version_compare($config['static_pages_version'], $mod_version, '<'))
		{
			$module_info->update($this->u_action);
		}

		// Template and page title
		$this->tpl_name = 'acp_pages';
		$this->page_title = 'ACP_MANAGE_PAGES';

		$form_name = 'acp_pages';
		add_form_key($form_name);

		// Quick link to enable/disable page
		if ($action == 'display')
		{
      // Get the page info from the database
			$sql = 'SELECT page_title, page_display FROM ' . PAGES_TABLE . ' WHERE page_id = ' . $page_id;
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult();
			
			// If displayed hide, if hidden display
			if ($row['page_display'] == 0)
			{
				$page_display = 1;
				$message = $user->lang['PAGE_VISIBLE'];
			}
			else if ($row['page_display'] == 1)
			{
        $page_display = 0;
        $message = $user->lang['PAGE_NOT_VISIBLE'];
			}
			
			// Make the switch in the display
			$sql = 'UPDATE ' . PAGES_TABLE . ' SET page_display = ' . $page_display . ' WHERE page_id = ' . $page_id;
			$db->sql_query($sql);

			trigger_error($message . adm_back_link($this->u_action));
		}

		switch ($action)
		{
			case 'save':
				// Check the form key, set at the beginning
				if (!check_form_key($form_name))
				{
					trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				
				// Get all the data
				$page_title 	= utf8_normalize_nfc(request_var('title', '', true));
				$page_desc 		= utf8_normalize_nfc(request_var('desc', '', true));
				$page_content = utf8_normalize_nfc(request_var('content', '', true));
				$page_url		 	= trim(utf8_strtolower(utf8_normalize_nfc(request_var('url', '', true))));
				$page_order 	= request_var('order', 0);
				$page_display = (bool) request_var('display', 0);
				$page_display_guests = (bool) request_var('display_guests', 0);

				// Check the fields, we don't want empty ones
				if (!$page_title)
				{
					trigger_error($user->lang['NO_PAGE_TITLE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				
				if (!$page_desc)
				{
					trigger_error($user->lang['NO_PAGE_DESC'] . adm_back_link($this->u_action), E_USER_WARNING);
				}
				
				if (!$page_content)
				{
					trigger_error($user->lang['NO_PAGE_CONTENT'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				// If custom URL not entered or not without special characters generate it from the clean version of the page title, whitespaces are replaced with a comma
				if (!$page_url || strspn($page_url, 'abcdefghijklmnopqrstuvwxyz0123456789-+') !== strlen($page_url))
				{
					if (!$page_url)
					{
						$page_url = $page_title;
					}
					
					//Replace ',' '&', '/', ' ' and '_' with '-'
					$search = array(',', '/', '&', ' ', '_');
					$page_url = str_replace($search, '-', utf8_strtolower($page_url));

          $page_url = $this->remove_accents($page_url);

					// If something slipped pass the check we delete it...
					$page_url = preg_replace('/[^a-z0-9+-]/', '', $page_url);

					// Remove multiple conjunctions.
					while (strpos($page_title, '--') !== false)
					{
						$page_url = str_replace('--', '-', $page_url);
					}
				}
				
				// Check for a duplicate page URL, if duplicate add suffix
				// See if we are updating a page
				$where_sql = '';
				if ($page_id)
				{
					$where_sql = ' AND page_id <> ' . $page_id;
				}
				
				// Grab pages with same URL
				$sql = 'SELECT page_url FROM ' . PAGES_TABLE . " WHERE page_url = '" . $page_url . "'" . $where_sql;
				$result = $db->sql_query($sql);

				// If we have result go and add a number as a suffix
				if ($row = $db->sql_fetchrow($result))
				{
				  $db->sql_freeresult($result);
					$suffix = 2;
					
					// Loop until we find a unique name - query is in a loop, but I do not expect to have this loop more than two or three times
					do
					{
						$alt_page_url = $page_url . '-' . $suffix;
						$sql = 'SELECT page_url FROM ' . PAGES_TABLE . " WHERE page_url = '" . $alt_page_url . "'" . $where_sql;
						$result = $db->sql_query($sql);
						
						$suffix++;
					}
					while($row = $db->sql_fetchrow($result));
					$page_url = $alt_page_url;
				}

				// Prepare storage of content
				$uid = $bitfield = $options = '';
				$allow_bbcode = $allow_urls = $allow_smilies = true; // The function doesn't have the last three optional, we'll just feed it so it's happy
				generate_text_for_storage($page_content, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
				
				// Prepare the storage SQL array
				$sql_ary = array(
					'page_title'				=> $page_title,
					'page_desc'					=> $page_desc,
					'page_content'			=> $page_content,
					'page_url'					=> $page_url,
					'bbcode_uid'        => $uid,
					'bbcode_bitfield'   => $bitfield,
					'page_author'       => $user->data['user_id'],
					'page_display'			=> (int) $page_display,
					'page_display_guests'			=> (int) $page_display_guests,
					'page_order'				=> $page_order,
				);
				
				// Update or new page?
				if ($page_id)
				{
					$sql = 'UPDATE ' . PAGES_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE page_id = $page_id";
					$message = $user->lang['PAGE_UPDATED'];

					add_log('admin', 'LOG_PAGE_UPDATED', $page_title);
				}
				else
				{
					$sql = 'INSERT INTO ' . PAGES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
					$message = $user->lang['PAGE_ADDED'];

					add_log('admin', 'LOG_PAGE_ADDED', $page_title);
				}
				$db->sql_query($sql);

				trigger_error($message . adm_back_link($this->u_action));

			break;

			case 'delete':

				// Check if there is somethign to delete
				if (!$page_id)
				{
					trigger_error($user->lang['MUST_SELECT_PAGE'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				// Confirm deletion
				if (confirm_box(true))
				{
					$sql = 'SELECT page_title
						FROM ' . PAGES_TABLE . '
						WHERE page_id = ' . $page_id;
					$result = $db->sql_query($sql);
					$page_title = (string) $db->sql_fetchfield('page_title');
					$db->sql_freeresult($result);

					$sql = 'DELETE FROM ' . PAGES_TABLE . "
						WHERE page_id = $page_id";
					$db->sql_query($sql);

					add_log('admin', 'LOG_PAGE_REMOVED', $page_title);
				}
				else
				{
					confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
						'i'			=> $id,
						'mode'		=> $mode,
						'page_id'	=> $page_id,
						'action'	=> 'delete',
					)));
				}

			break;

			case 'edit':
			case 'add':
			
				if (!function_exists('display_custom_bbcodes'))
				{
					include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
				}
				
				$data = $pages = array();
				
				$sql = 'SELECT *
								FROM ' . PAGES_TABLE . '
								ORDER BY page_order ASC, page_id ASC';
				$result = $db->sql_query($sql);

				// If editing page, get the page's data and decode the bbcode
				while ($row = $db->sql_fetchrow($result))
				{
					if ($action == 'edit' && $page_id == $row['page_id'])
					{
						$pages = $row;
						decode_message($pages['page_content'], $pages['bbcode_uid']);
					}
				}
				$db->sql_freeresult($result);

				// Assign already existing data
				$template->assign_vars(array(
					'S_EDIT'			=> true,
					'S_ADD'       => ($action == 'add') ? true : false,
					'U_BACK'			=> $this->u_action,
					'U_ACTION'		=> $this->u_action . '&amp;id=' . $page_id,

					'PAGE_EDIT_TITLE'		=> (isset($pages['page_title'])) ? $pages['page_title'] : '',
					'PAGE_DESC'					=> (isset($pages['page_desc'])) ? $pages['page_desc'] : '',
					'PAGE_CONTENT'			=> (isset($pages['page_content'])) ? $pages['page_content'] : '',
					'PAGE_URL'					=> (isset($pages['page_url'])) ? $pages['page_url'] : '',
					'PAGE_ORDER'				=> (isset($pages['page_order'])) ? $pages['page_order'] : 0,
					'S_PAGE_DISPLAY'		=> (!empty($pages['page_display'])) ? true : false,
					'S_PAGE_DISPLAY_GUESTS'		=> (!empty($pages['page_display_guests'])) ? true : false,
					
					'U_PAGE'					=> ($action == 'add') ? '' : append_sid("{$phpbb_root_path}page.$phpEx", 'p=' . $pages['page_url']),
				));
				
				// Assigning custom bbcodes
				display_custom_bbcodes();
				
				return;
			break;
		}
	
		$template->assign_vars(array(
			'U_ACTION'		=> $this->u_action)
		);

		// Get all pages
		$sql = 'SELECT p.page_id, p.page_title, p.page_desc, p.page_author, p.page_display, p.page_url, u.username, u.user_colour
						FROM ' . PAGES_TABLE . ' p, ' . USERS_TABLE . ' u
						WHERE u.user_id = p.page_author
						ORDER BY p.page_order ASC, p.page_id ASC';
						
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			// Now we know this, add template vars for pagelist
			$template->assign_block_vars('pages', array(
				'PAGE_TITLE'					=> $row['page_title'],
				'PAGE_DESC'						=> $row['page_desc'],
				'PAGE_AUTHOR'					=> get_username_string('full', $row['page_author'], $row['username'], $row['user_colour']),
				'PAGE_SWITCH_DISPLAY' => ($row['page_display']) ? $user->lang['PAGE_MAKE_HIDDEN'] : $user->lang['PAGE_MAKE_VISIBLE'],
				
				'U_EDIT'				=> $this->u_action . '&amp;action=edit&amp;id=' . $row['page_id'],
				'U_DELETE'			=> $this->u_action . '&amp;action=delete&amp;id=' . $row['page_id'],
				'U_DISPLAY'			=> $this->u_action . '&amp;action=display&amp;id=' . $row['page_id'],
				'U_PAGE'				=> append_sid("{$phpbb_root_path}page.$phpEx", 'p=' . $row['page_url']),
			));
		}
		$db->sql_freeresult($result);
	}
	
	/**
	* Helper function for page_url sanitization
	*
	* @param string $string The string to be returned without accents and special characters
	*
	* @return string The clean string
	*/
	function remove_accents($string)
	{
	  // No problems with plain alphanumerics
		if (!preg_match('/[\x80-\xff]/', $string))
		{
			return $string;
		}

		// Translation table
		$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => ''
		);

		$string = strtr($string, $chars);

		return $string;
	}
}

?>
