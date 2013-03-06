<?php

/**
*
* Static Pages MOD display page file
*
* @version $Id$
* @copyright (c) 2009 Vojtěch Vondra
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/info_acp_pages');

$page_id = request_var('id', 0);
$page_url = request_var('p', '');

// Check if valid page ID entered, if not, show list of pages, else show page
if ($page_id <= 0 && $page_url == '')
{
	// Check if user is anonymous, if yes, server only pages he can se
	$where_sql = ($user->data['user_id'] == ANONYMOUS) ? ' AND p.page_display_guests = 1 ' : '';

	// Get pages from the database
	$sql = 'SELECT p.page_url, p.page_title, p.page_desc, p.page_author, u.username, u.user_colour
					FROM ' . PAGES_TABLE . ' p, ' . USERS_TABLE . " u
					WHERE u.user_id = p.page_author
						AND p.page_display = 1
						$where_sql
					ORDER BY p.page_order ASC, p.page_id ASC";
					
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('pagelist', array(
			'PAGE_TITLE' => $row['page_title'],
			'PAGE_DESC' => $row['page_desc'],
			'PAGE_AUTHOR' => get_username_string('full', $row['page_author'], $row['username'], $row['user_colour']),
			'PAGE_LINK'   => append_sid("{$phpbb_root_path}" . $row['page_url']),
		));
	}
	$db->sql_freeresult();
	
	// Send the page header
	page_header($user->lang['PAGES']);
	
	$template->set_filenames(array(
	    'body' => 'page_list.html',
	));
}
else
{
	// See whether the request is defined by a page id or page name
	if ($page_id <= 0)
	{
		$sql_where = "page_url = '" . $db->sql_escape($page_url) . "'";
	}
	else
	{
    $sql_where = 'page_id = ' . $page_id;
	}
	
	// Get the page info from the database
	$sql = 'SELECT page_id, page_title, page_desc, page_content, page_display, page_display_guests, page_url, bbcode_uid, bbcode_bitfield
					FROM ' . PAGES_TABLE . '
					WHERE ' . $sql_where;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult();

	// See if the page exists or if is hidden (to guests)
	if (!sizeof($row) || ($row['page_display'] == 0 && !$auth->acl_getf_global('m_') && !$auth->acl_get('a_')) || ($row['page_display_guests'] == 0 && $user->data['user_id'] == ANONYMOUS))
	{
		trigger_error('PAGE_NOT_FOUND');
	}

	// Parse the page content to transform BBCode
	$content = generate_text_for_display($row['page_content'], $row['bbcode_uid'], $row['bbcode_bitfield'], 7);

	// Push the data to the templates
	$template->assign_vars(array(
		'PAGE_TITLE' => $row['page_title'],
		'PAGE_DESC' => $row['page_desc'],
		'PAGE_CONTENT' => $content,
		'S_PAGE_HIDDEN' => (empty($row['page_display'])) ? true : false,
	));

  // Send the page header with the page title
	page_header($row['page_title']);

	$template->set_filenames(array(
	    'body' => 'page_body.html',
	));
}

// Send the page footer
page_footer();
?>
