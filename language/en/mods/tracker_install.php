<?php
/**
*
* install [English]
*
* @package language
* @version $Id: tracker_install.php 190 2008-12-28 19:09:52Z JRSweets $
* @copyright (c) 2005 phpBB Group
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

$lang = array_merge($lang, array(
	'CAT_INSTALL'							=> 'Install',
	'CAT_OVERVIEW'							=> 'Overview',
	'CAT_UNINSTALL'							=> 'Uninstall',
	'CAT_UPDATE'							=> 'Update',
	'CAT_VERIFY'							=> 'Verify',

	'DONE'									=> 'Done',
	'DUPLICATE_AUTH_FOUND'					=> '%s was found %s times',

	'FILES_REQUIRED'						=> 'Files and Directories',
	'FILES_REQUIRED_EXPLAIN'				=> '<strong>Required</strong> - In order to function correctly phpBB Tracker needs to be able to access or write to certain files or directories. If you see “Cannot find” you need to create the relevant file or directory. If you see “Unwritable” you need to change the permissions on the file or directory to allow phpBB Tracker to write to it.',
	'FOUND'									=> 'Found',

	'GPL'									=> 'General Public License',

	'INSTALL_CONGRATS'						=> 'Congratulations!',
	'INSTALL_CONGRATS_EXPLAIN'				=> '
		<p>You have now successfully installed phpBB Tracker %1$s.</p>
		<p>Clicking the button below will take you to your Administration Control Panel (ACP). Take some time to examine the options available to you</p><p><strong>Please now delete, move or rename the install directory before you use your board. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.</strong></p>',
	'INSTALL_INTRO'							=> 'Welcome to phpBB Tracker Installation',
	'INSTALL_INTRO_BODY'					=> 'With this option, it is possible to install phpBB Tracker to your database.',
	'INSTALL_LOGIN'							=> 'Proceed to ACP',
	'INSTALL_PANEL'							=> 'phpBB Tracker Installation Panel',
	'INSTALL_START'							=> 'Start install',
	'INSTALL_TEST'							=> 'Test again',
	'INST_ERR'								=> 'Installation error',
	'INST_ERR_AUTH'							=> 'You are not authorized to use this script.<br /><br />Please note to use the script the following requirements must be met.  First you must be logged in to the site and second you must have the user type founder. If you are logged in and are the founder then you have incorrect cookie settings in the acp. Please check the cookie domain setting. If your site url is <strong>http://www.example.com</strong> then the cookie domain should be <strong>.example.com</strong>.',
	'INST_ERR_FATAL'						=> 'Fatal installation error',
	'INST_ERR_FATAL_DB'						=> 'A fatal and unrecoverable database error has occurred. This may be because the specified user does not have appropriate permissions to <code>CREATE TABLES</code> or <code>INSERT</code> data, etc. Further information may be given below. Please contact your hosting provider in the first instance or the support forums of phpBB for further assistance.',
	'INST_SQL_RESULTS'						=> 'SQL Statements Completed',

	'MODULE_ACP'							=> 'ACP Module',
	'MODULE_MCP'							=> 'MCP Module',
	'MODULE_UCP'							=> 'UCP Module',

	'NEXT_STEP'								=> 'Proceed to next step',
	'NOT_FOUND'								=> 'Cannot find',

	'OVERVIEW_BODY'							=> 'Welcome to phpBB Tracker!<br /><br />phpBB Tracker is feature-rich, user-friendly, and is fully supported.<br /><br />This installation system will guide you through installing phpBB Tracker, updating to the latest version of phpBB Tracker from past releases, uninstalling phpBB Tracker and verifying phpBB Tracker is installed correctly. To read the phpBB Tracker license or learn about obtaining support and our stance on it, please select the respective options from the side menu. To continue, please select the appropriate tab above.',

	'PHPBB_VERSION_REQD'					=> 'phpBB version >= %s',
	'PHP_SETTINGS'							=> 'phpBB version',
	'PHP_SETTINGS_EXPLAIN'					=> '<strong>Required</strong> - You must be running at least version %s of phpBB in order to install phpBB Tracker.',

	'REQUIREMENTS_EXPLAIN'					=> 'Before proceeding with the full installation phpBB Tracker will carry out some tests on your server configuration and files to ensure that you are able to install and run phpBB Tracker. Please ensure you read through the results thoroughly and do not proceed until all the required tests are passed. If you wish to use any of the features depending on the optional tests, you should ensure that these tests are passed also.',
	'REQUIREMENTS_TITLE'					=> 'Installation compatibility',

	'STAGE_INSTALL'							=> 'Install',
	'STAGE_INSTALL_TRACKER'					=> 'Installation of phpBB Tracker',
	'STAGE_INSTALL_TRACKER_EXPLAIN'			=> 'The database tables, modules, permissions and data used by phpBB Tracker have been created.',
	'STAGE_INTRO'							=> 'Introduction',
	'STAGE_REQUIREMENTS'					=> 'Requirements',
	'STAGE_UNINSTALL'						=> 'Uninstall',
	'STAGE_UNINSTALL_TRACKER'				=> 'Uninstallation of phpBB Tracker',
	'STAGE_UNINSTALL_TRACKER_EXPLAIN'		=> 'The database tables, modules, permissions and data used by phpBB Tracker have been removed from the database.  To complete the uninstallation you need to reverse all the file edits and remove all the files from your server.',
	'STAGE_UPDATE'							=> 'Update',
	'STAGE_UPDATE_TRACKER'					=> 'Update of phpBB Tracker',
	'STAGE_UPDATE_TRACKER_EXPLAIN'			=> 'The phpBB Tracker has been updated to the latest version.',
	'STAGE_VERIFY'							=> 'Verify',
	'SUB_INTRO'								=> 'Introduction',
	'SUB_LICENSE'							=> 'License',
	'SUB_SUPPORT'							=> 'Support',
	'SUPPORT_BODY'							=> 'Full support will be provided for the current stable and development release of phpBB Tracker, free of charge. This includes:</p><ul><li>installation</li><li>configuration</li><li>technical questions</li><li>problems relating to potential bugs in the software</li><li>updating from older versions to the latest version</li></ul><p>I encourage users still running older versions of phpBB Tracker to update their installation with the latest version.</p><h2>Obtaining Support</h2><p><a href="http://www.jeffrusso.net/forum/viewforum.php?f=21">Main Development Site</a><br /><a href="http://www.phpbb.com/community/viewtopic.php?f=70&amp;t=808995">phpBB.com Development Topic</a><br />User guide (located inside phpBB Tracker ACP)<br /><br />',

	'UNAVAILABLE'							=> 'Unavailable',
	'UNINSTALL_CONGRATS_EXPLAIN'			=> '
		<p>You have now successfully uninstalled phpBB Tracker %1$s.</p>
		<p>Clicking the button below will take you to your Administration Control Panel (ACP). <p><strong>Please now delete, move or rename the install directory before you use your board. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.</strong></p>',
	'UNINSTALL_INTRO'						=> 'Welcome to phpBB Tracker Uninstallation',
	'UNINSTALL_INTRO_BODY'					=> 'With this option, it is possible to uninstall phpBB Tracker from your database.',
	'UNINSTALL_START'						=> 'Start uninstall',
	'UNWRITABLE'							=> 'Unwritable',
	'UPDATE_CONGRATS_EXPLAIN'				=> '
		<p>You have now successfully updated to phpBB Tracker %1$s.</p>
		<p>Clicking the button below will take you to your Administration Control Panel (ACP). Take some time to examine the options available to you.</p><p><strong>Please now delete, move or rename the install directory before you use your board. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.</strong></p>',
	'UPDATE_INTRO'							=> 'Welcome to phpBB Tracker Installation Update',
	'UPDATE_INTRO_BODY'						=> 'With this option, it is possible to update phpBB Tracker to the latest release.',
	'UPDATE_START'							=> 'Start update',

	'VERIFY_ALL_FILES'						=> 'All files found',
	'VERIFY_ALL_FILES_EDITED'				=> 'All files are edited',
	'VERIFY_ALL_MODULES'					=> 'All modules found',
	'VERIFY_ALL_PERMISSIONS'				=> 'All permissions found',
	'VERIFY_ALL_TABLES'						=> 'All tables found',
	'VERIFY_CONGRATS_EXPLAIN'				=> '
		<p>You have now successfully verified the installation of phpBB Tracker %1$s.</p>
		<p>Clicking the button below will take you to your Administration Control Panel (ACP).</p><p><strong>Please now delete, move or rename the install directory before you use your board. If this directory is still present, only the Administration Control Panel (ACP) will be accessible.</strong></p>',
	'VERIFY_DUPLICATE_PERMISSIONS'			=> 'Checking if duplicate phpBB permissions exist',
	'VERIFY_ERRORS'							=> 'Unsuccessful!',
	'VERIFY_ERRORS_EXPLAIN'					=> '
		<p>You have not successfully installed phpBB Tracker %1$s.</p>
		<p>Clicking the button below will take you back to verify the installation again.</p><p><strong>Please check the reported errors below.</strong></p>',
	'VERIFY_FILES_EDITED'					=> 'Checking if files are edited',
	'VERIFY_FILES_EXIST'					=> 'Checking if files exist',
	'VERIFY_FOUND_DUPLICATE_PERMISSIONS'	=> 'Duplicate auth values can cause problems with permissions. The following duplicate auth values were found inside the %s table:<br />%s',
	'VERIFY_INTRO'							=> 'Welcome to phpBB Tracker Installation Verfication',
	'VERIFY_INTRO_BODY'						=> 'With this option, it is possible to verify that phpBB Tracker is installed correctly onto your server.',
	'VERIFY_MISSING_FILES'					=> 'The following files were missing:<br />%s',
	'VERIFY_MISSING_FILES_EDITED'			=> 'The following files seem to not be edited:<br />%s',
	'VERIFY_MISSING_MODULES'				=> 'The following modules were missing:<br />%s',
	'VERIFY_MISSING_PERMISSIONS'			=> 'The following permissions were missing:<br />%s',
	'VERIFY_MISSING_TABLES'					=> 'The following tables were missing:<br />%s',
	'VERIFY_MODULES'						=> 'Checking if all modules exist',
	'VERIFY_NO_DUPLICATE_PERMISSIONS'		=> 'No duplicate permissions found',
	'VERIFY_OTHER_DB_DATA'					=> 'Checking other db data',
	'VERIFY_PERMISSIONS'					=> 'Checking if all permissions exist',
	'VERIFY_TABLES_EXIST'					=> 'Checking if tables exist',
	'VERIFY_TABLE_ALTERED'					=> '%s table correctly altered',
	'VERIFY_TABLE_NOT_ALTERED'				=> 'The following columns are missing from the %s table:<br />%s',
	'VERIFY_TRACKER_INSTALLATION'			=> 'Verify tracker installation',
	'VERIFY_TRACKER_INSTALLATION_EXPLAIN'	=> 'This will check to make sure all the tracker is installed correctly.',
	'VERSION'								=> 'Version',

	'WELCOME_INSTALL'						=> 'Welcome to phpBB Tracker Installation',
	'WRITABLE'								=> 'Writable',
));

?>