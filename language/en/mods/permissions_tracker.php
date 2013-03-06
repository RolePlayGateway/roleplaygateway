<?php
/**
*
* acp_permissions_tracker [English]
*
* @package language
* @version $Id: permissions_tracker.php 191 2008-12-28 21:18:33Z JRSweets $
* @copyright (c) 2008 http://www.jeffrusso.net
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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

/**
*	MODDERS PLEASE NOTE
*
*	You are able to put your permission sets into a seperate file too by
*	prefixing the new file with permissions_ and putting it into the acp
*	language folder.
*
*	An example of how the file could look like:
*
*	<code>
*
*	if (empty($lang) || !is_array($lang))
*	{
*		$lang = array();
*	}
*
*	// Adding new category
*	$lang['permission_cat']['tracker'] = 'Bugs';
*
*	// Adding new permission set
*	$lang['permission_type']['bug_'] = 'Bug Permissions';
*
*	// Adding the permissions
*	$lang = array_merge($lang, array(
*		'acl_bug_view'		=> array('lang' => 'Can view bug reports', 'cat' => 'tracker'),
*		'acl_bug_post'		=> array('lang' => 'Can post tracker', 'cat' => 'post'), // Using a phpBB category here
*	));
*
*	</code>
*/

// Define categories and permission types
$lang['permission_cat']['tracker'] = 'phpBB Tracker';

$lang = array_merge($lang, array(
	'acl_a_tracker'					=> array('lang' => 'Can manage tracker', 											'cat' => 'tracker'),
	'acl_u_tracker_attach'			=> array('lang' => 'Can attach files to tracker tickets/posts', 					'cat' => 'tracker'),
	'acl_u_tracker_download'		=> array('lang' => 'Can download files from tracker tickets/posts', 				'cat' => 'tracker'),
	'acl_u_tracker_view'			=> array('lang' => 'Can view tracker tickets', 										'cat' => 'tracker'),
	'acl_u_tracker_post'			=> array('lang' => 'Can post tracker tickets', 										'cat' => 'tracker'),
	'acl_u_tracker_edit'			=> array('lang' => 'Can edit own tracker tickets/posts', 							'cat' => 'tracker'),
	'acl_u_tracker_edit_all'		=> array('lang' => 'Can edit all tracker tickets/posts if in project group', 		'cat' => 'tracker'),
	'acl_u_tracker_delete_all'		=> array('lang' => 'Can delete all tracker tickets/posts if in project group', 		'cat' => 'tracker'),
	'acl_u_tracker_edit_global'		=> array('lang' => 'Can edit all tracker tickets/posts in any project', 			'cat' => 'tracker'),
	'acl_u_tracker_delete_global'	=> array('lang' => 'Can delete all tracker tickets/posts in any project', 			'cat' => 'tracker'),
	'acl_u_tracker_ticket_security'	=> array('lang' => 'Can specify security tickets if enabled for project',			'cat' => 'tracker'),
));

?>