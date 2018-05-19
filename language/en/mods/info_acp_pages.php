<?php
/**
*
* Static Pages MOD language file
*
* @version $Id$
* @copyright (c) 2009 Vojtěch Vondra
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// Front-end keys
	'PAGE_ID_INVALID'			=> 'The selected page does not exist.',
	'PAGE_NOT_FOUND'			=> 'The selected page was not found.',
	
	// ACP keys
	'ACP_MANAGE_PAGES' => 'Manage Pages',
	'ACP_PAGES' => 'Pages',
	'ACP_PAGES_EXPLAIN' => 'Here you can add and edit static pages on your board.',
	'ADD_PAGE' => 'Add page',
	'GO_TO_PAGE' => 'See the page',
	'MUST_SELECT_PAGE' => 'You must select a page',
	'NO_PAGE_DESC' => 'You have not entered the page\'s description.',
	'NO_PAGE_TITLE' => 'You have not entered the page\'s title.',
	'NO_PAGE_CONTENT' => 'You have not entered the page\'s content.',
	'PAGE'     => 'Page',
	'PAGES'     => 'Pages',
	'PAGE_ADDED' => 'The page was successfully added.',
	'PAGE_AUTHOR' => 'Page author',
	'PAGE_CONTENT' => 'Page content',
	'PAGE_DESC' => 'Description',
	'PAGE_DESC_EXPLAIN' => 'This is used in two places, here in the ACP to identify your pages and in the pagelist while no page is selected.',
	'PAGE_DISPLAY' => 'Display page',
	'PAGE_DISPLAY_EXPLAIN' => 'If set to no, the page will not be accessible to public. Admins and moderators can always access the page directly.',
	'PAGE_DISPLAY_GUESTS' => 'Display page to guests',
	'PAGE_DISPLAY_GUESTS_EXPLAIN' => 'If set to No, only Registered users will be able to see the page.',
	'PAGE_HIDDEN' => 'This page is hidden, only moderators and administrators can see it. You can enable it in the ACP.',
	'PAGE_LINK' => 'Page link',
	'PAGE_MAKE_HIDDEN' => 'Hide',
	'PAGE_MAKE_VISIBLE' => 'Make visible',
	'PAGE_NOT_VISIBLE' => 'The selected page is now hidden from public view.',
	'PAGE_ORDER' => 'Page order',
	'PAGE_ORDER_EXPLAIN' => 'If a list of pages is shown, you can define the order of the pages by setting a number here, pages are sorted ascending by this field.',
	'PAGE_TITLE' => 'Page title',
	'PAGE_UPDATED' => 'The page was successfully updated.',
	'PAGE_URL' => 'URL identifier',
	'PAGE_URL_EXPLAIN' => 'Used in the URL to access the page, use lowercase letters, numbers and hyphens. If not entered, the system will generate it from the page title.',
	'PAGE_VISIBLE' => 'The selected page is now displayed.',
	'STATIC_PAGES_MOD_UPDATED' => '<strong>Static page MOD updated to version » %s</strong>',
	'STATIC_PAGES_MOD_INSTALLED' => '<strong>Static page MOD was installed - MOD version » %s</strong>',
	
	// Log messages
	'LOG_PAGE_ADDED'	=> '<strong>Static page added</strong><br />» %s',
	'LOG_PAGE_UPDATED'	=> '<strong>Static page updated</strong><br />» %s',
	'LOG_PAGE_REMOVED'	=> '<strong>Static page removed</strong><br />» %s',
	
	// Manage pages permission
	'acl_a_manage_pages'			=> array('lang' => 'Can create, edit and delete static pages', 'cat' => 'misc'),
));
?>