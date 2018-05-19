<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: install.php 204 2009-12-20 12:04:51Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* install [English]
*
*/
/**
* DO NOT CHANGE
*/
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
	// Install
	'SEO_INSTALL_PANEL'	=> 'Gym Sitemaps &amp; RSS Installation Panel',
	'CAT_INSTALL_GYM_SITEMAPS' => 'Install GYM Sitemaps',
	'CAT_UNINSTALL_GYM_SITEMAPS' => 'Un-install GYM Sitemaps',
	'CAT_UPDATE_GYM_SITEMAPS' => 'Update GYM Sitemaps',
	'SEO_ERROR_INSTALL'	=> 'An error occurred during the installation process. If you want to retry the installation, uninstall first.',
	'SEO_ERROR_INSTALLED'	=> 'The %s module is already installed.',
	'SEO_ERROR_ID'	=> 'The %1$ module had no ID.',
	'SEO_ERROR_UNINSTALLED'	=> 'The %s module is already uninstalled.',
	'SEO_ERROR_INFO'	=> 'Information :',
	'SEO_FINAL_INSTALL_GYM_SITEMAPS'	=> 'Login to ACP',
	'SEO_FINAL_UPDATE_GYM_SITEMAPS'	=> 'Login to ACP',
	'SEO_FINAL_UNINSTALL_GYM_SITEMAPS'	=> 'Return to forum index',
	'SEO_OVERVIEW_TITLE'	=> 'GYM sitemaps &amp; RSS Overview',
	'SEO_OVERVIEW_BODY'	=> '<p>Welcome to the phpBB SEO GYM sitemaps &amp; RSS %1$s installer.</p><p>Please read <a href="%3$s" title="Check the release thread" target="_phpBBSEO"><b>the release thread</b></a> for more information</p><p><strong style="text-transform: uppercase;">Note:</strong> You must have already performed the required code changes and uploaded all the new files before you can proceed with this install wizard.</p><p>This installation system will guide you through the process of installing the GYM sitemaps &amp; RSS admin control panel (ACP). It will allow you generate efficient and Search Engine Optimized Google Sitemaps and RSS feeds. Its modular design will allow you to generate Google Sitemaps and RSS feeds for any php/SQL application installed on your site, using dedicated plug-ins. Let’s meet in the <a href="%3$s" title="Support forum" target="_phpBBSEO"><b>support forum</b></a> for anything regarding the GYM Sitemaps &amp; RSS module.</p> ',
	'CAT_SEO_PREMOD'	=> 'GYM Sitemaps &amp; RSS',
	'SEO_INSTALL_INTRO'		=> 'Welcome to the phpBB SEO GYM sitemaps &amp; RSS installer.',
	'SEO_INSTALL_INTRO_BODY'	=> '<p>You are about to install the %1$s %2$s mod. This install tool will activate the GYM Sitemaps &amp; RSS admin control panel in the phpBB ACP.</p><p>Once installed, you will need to go to the ACP to choose the appropriate settings.</p>
	<p><strong>Note:</strong>If it’s the first time you have installed this mod, we strongly encourage you to take the time to test the various features of the module on a local or private test server before going online.</p><br/>
	<p>Requirements :</p>
	<ul>
		<li>Apache server (Linux OS) with mod_rewrite for the URL rewriting features of the module.</li>
		<li>IIS server (Windows OS) with isapi_rewrite for the URL rewriting features of the module, but you will need to adapt the rewriterules in the httpd.ini</li>
	</ul>',
	'SEO_INSTALL'		=> 'Install',
	'UN_SEO_INSTALL_INTRO'		=> 'Welcome to the GYM Sitemaps &amp; RSS un-installer',
	'UN_SEO_INSTALL_INTRO_BODY'	=> '<p>You are about to uninstall the %1$s %2$s mod.</p>
	<p><strong>Note:</strong> Sitemaps and feeds will no longer be available once you have uninstalled the module.</p>',
	'UN_SEO_INSTALL'		=> 'Uninstall',
	'SEO_INSTALL_CONGRATS'		=> 'Congratulations!',
	'SEO_INSTALL_CONGRATS_EXPLAIN'	=> '<p>You have now successfully installed the %1$s %2$s mod. Go to phpBB ACP and proceed with the mod settings.<p>
	<p>It will show up in the phpBB SEO Category; among many other things you will be able to :</p>
	<h2>Accurately configure you Google Sitemaps and RSS feeds</h2>
	<p>Google sitemaps and RSS feeds supports advanced XSLt styling, phpBB’s CSS will even be applied to these without editing a single line of code.</p>
	<p>Google sitemaps and RSS feeds will auto detect the phpBB SEO mod rewrites and their settings; using other URL rewriting mod is made easy.</p>
	<h2>Generate a personalized .htaccess</h2>
	<p>With the phpBB SEO mod rewrite and once you will have set up the above options, you will be able to generate a personalized .htaccess quickly and save it directly on the server.</p><br/><h3>Install Report :</h3>',
	'UN_SEO_INSTALL_CONGRATS'	=> 'The GYM Sitemaps &amp; RSS ACP module was removed.',
	'UN_SEO_INSTALL_CONGRATS_EXPLAIN'	=> '<p>You have now successfully uninstalled the %1$s %2$s mod.<p>
	<p> You Google sitemaps and RSS feeds are not available any more.</p>',
	'SEO_VALIDATE_INFO'	=> 'Validation Info :',
	'SEO_LICENCE_TITLE'	=> 'GNU LESSER GENERAL PUBLIC LICENSE',
	'SEO_LICENCE_BODY'	=> 'The phpBB SEO GYM Sitemaps &amp; RSS is released under the GNU LESSER GENERAL PUBLIC LICENSE.',
	'SEO_SUPPORT_TITLE'	=> 'Support',
	'SEO_SUPPORT_BODY'	=> 'Full support will be given in the <a href="%1$s" title="Visit the %2$s forum" target="_phpBBSEO"><b>%2$s forum</b></a>. We will provide answers to general setup questions, configuration problems, and support for determining common problems.</p><p>Be sure to visit our <a href="http://www.phpbb-seo.com/boards/" title="SEO Forum" target="_phpBBSEO"><b>Search Engine Optimization forums</b></a>.</p><p>You should <a href="http://www.phpbb-seo.com/boards/profile.php?mode=register" title="Register to phpBB SEO" target="_phpBBSEO"><b>register</b></a>, log in and <a href="%3$s" title="Be notified about updates" target="_phpBBSEO"><b>subscribe to the release thread</b></a> to be notified by mail upon each update.',
	// Security
	'SEO_LOGIN'		=> 'The board requires you to be registered and logged in to view this page.',
	'SEO_LOGIN_ADMIN'	=> 'The board requires you to be logged in as admin to view this page.<br/>Your session has been purged for security purposes.',
	'SEO_LOGIN_FOUNDER'	=> 'The board requires you to be logged in as the founder to view this page.',
	'SEO_LOGIN_SESSION'	=> 'Session Check failed.<br/>The Settings were not altered.<br/>Your session has been purged for security purposes.',
	// Cache status
	'SEO_CACHE_FILE_TITLE'	=> 'Cache file status',
	'SEO_CACHE_STATUS'	=> 'The cache folder configured is : <b>%s</b>',
	'SEO_CACHE_FOUND'	=> 'The cache folder was successfully found.',
	'SEO_CACHE_NOT_FOUND'	=> 'The cache folder was not found.',
	'SEO_CACHE_WRITABLE'	=> 'The cache folder is writable.',
	'SEO_CACHE_not writeable'	=> 'The cache folder is not writeable. You need to CHMOD it to 0777.',
	'SEO_CACHE_FORUM_NAME'	=> 'Forum name',
	'SEO_CACHE_URL_OK'	=> 'URL Cached',
	'SEO_CACHE_URL_NOT_OK'	=> 'This Forum URL is not cached',
	'SEO_CACHE_URL'		=> 'Final URL',
	'SEO_CACHE_MSG_OK'	=> 'The cache file was updated successfully.',
	'SEO_CACHE_MSG_FAIL'	=> 'An error occurred while updating the cache file.',
	'SEO_CACHE_UPDATE_FAIL'	=> 'The URL you entered cannot be used, the cache was left untouched.',
	// Update
	'UPDATE_SEO_INSTALL_INTRO'		=> 'Welcome to the phpBB SEO GYM sitemaps &amp; RSS updater.',
	'UPDATE_SEO_INSTALL_INTRO_BODY'	=> '<p>You are about to update the %1$s module to %2$s. This script will update the phpBB data base.<br/>Your current settings won’t be affected.</p>
	<p><strong>Note:</strong> This script will not update GYM Sitemaps &amp; RSS physical files.<br/><br/>To update from all 2.0.x (phpBB3) versions you <b>must</b> upload all files in the <b>root/</b> directory of the archive to your phpBB/ ftp directory, after you will have taken care manually of the eventual code change you would have implemented in the template files (directory phpBB/styles/, .html, .js and .xsl) added by the module.<br/><br/>You <b>can</b> restart this update script when you want, for example if you did not upload the required files or simply to display the update code changes for phpBB3 files again.</p>',
	'UPDATE_SEO_INSTALL'		=> 'Update',
	'SEO_ERROR_NOTINSTALLED'	=> 'GYM Sitemaps &amp; RSS is not installed!',
	'SEO_UPDATE_CONGRATS_EXPLAIN'	=> '<p>You have now successfully updated %1$s to %2$s.<p>
	<p><strong>Note:</strong> This script does not update GYM Sitemaps &amp; RSS physical files.</p><br/><b>Please</b> implement the code changes listed  below.<br/><h3>Update report :</h3>',
));
?>