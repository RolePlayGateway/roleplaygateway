<?php
/** 
*
* seo_cache [English]
*
* @package language
* @version $Id: seo_cache.php,v 1.17 2007/05/10 15:31:21 acydburn Exp $
* @copyright (c) 2007 phpBB SEO
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
	'SEO_MAIN_TITLE'	=> 'phpBB SEO Settings',
	'SEO_MAIN_MSG'		=> 'You can from here set up various phpBB SEO Class parameters.<br/>You can as well set up personalized urls for each forums and categories with or without delimiters and IDs and generate an .htaccess file.',
	'SEO_FORUM_TITLE'	=> 'Forum URL Management',
	'SEO_FORUM_MSG'		=> 'You can here see what\'s in the cache file containing the forum title to inject in their URLs.<br/>Forum in green colors are cached, the one in red are not yet.<br/><b style="color:red">Please Note</b><ul style="margin-left:20px;font-size:12px;"><b>any-title-fxx/</b> will always be properly redirected but it won\'t be the case if you edit <b>any-title/</b> to <b>something-else/</b>.<br/> In such case, <b>any-title/</b> will for now be treated as a forum that does not exist if you do not set appropriate redirections.</ul><br/>',
	'SEO_FORUM_SMSG'	=> 'You can here see what\'s in the cache file containing the forum title to inject in their URLs.',
	'SEO_SETTINGS_TITLE'	=> 'phpBB SEO Class settings',
	'SEO_SETTINGS_MSG'	=> 'You can here set up various options of the phpBB SEO mod rewrite.<br/>The various default settings such as the delimiters and suffixes still must be set up in phpbb_seo_class.php, since changing these implies an .htaccess update and most likely appropriate redirections.',
	'SEO_SETTINGS_SMSG'	=> 'You can here set up various options of the phpBB SEO mod rewrite.',
	'SEO_HTACCESS_TITLE'	=> 'htaccess',
	'SEO_HTACCESS_MSG'	=> 'This tool will help you out building your .htacess.<br/>The version proposed bellow is based on your phpbb_seo/phpbb_seo_class.php settings.<br/>You can edit the $seo_ext and $seo_static values before you install the .htaccess to get personalized URLs.<br/>You can for example choose to use .htm instead of .html, \'message\' instead of \'post\' \'mysite-team\' instead of \'the-team\' and so on.<br/>If you edit these while they where already indexed in SE, you\'ll need personalized redirections.',
	'SEO_HTACCESS_EXPLAIN'	=> 'Once you have chosen the suffixes and static part of your forum URLs (in the phpbb_seo_class.php file), you should try to use the  suggested .htaccess below.<br/>The default settings are not bad at all, you can skip this step without worries if you prefer.<br/>It\'s though the best time to do it, doing it after a while will require some personalized redirections.<br/>By default the following .htaccess shall be uploaded in the domain\'s root (eg where www.example.com is linked).<br/>If phpBB is installed in a sub folder, hitting the more option below will add an option to upload it in the phpBB folder instead.',
	'SEO_HTACCESS_SMSG'	=> 'This tool will help you out building your .htacess.',
	'SEO_HTACCESS_RBASE'	=> '.htaccess location',
	'SEO_HTACCESS_RBASE_EXPLAIN' => 'Put the .htaccess in the phpBB folder ?<br/>The RewriteBase setting allow to put the forum\'s .htaccess in it\'s folder. It\'s usually more convenient to put the .htaccess in the domain\'s root folder even when phpBB is installed in a sub-folder, but you may prefer to put it in the forum folder instead.',
	'SEO_HTACCESS_SLASH'	=> 'RegEx Right Slash',
	'SEO_HTACCESS_SLASH_EXPLAIN'	=> 'Depending on the specific host you are using, you might have to get rid of or add the slash ("/") at the beginning of the right part of each rewriterules. This particular slash is used by default when .htaccess are located at the root level. It\'s the contrary for when phpBB would be installed in a sub-folder and you\'d want to use an .htaccess in the same folder.<br/>Default settings should generally work, but if it\'s not the case, try regenerating an .htaccess by hitting the "Re-generate" button.',
	'SEO_HTACCESS_WSLASH'	=> 'RegEx Left Slash',
	'SEO_HTACCESS_WSLASH_EXPLAIN'	=> 'Depending on the specific host you are using, you might have to add a slash ("/") at the beginning of the left part of each rewriterules. This particular slash ("/") is never used by default.<br/>Default settings should generally work, but if it\'s not the case, try regenerating an .htaccess by hitting the "Re-generate" button.',
	'SEO_HTACCESS_ROOT_MSG'	=> 'Once you are ready, you should select all the code bellow, and paste it in a .htaccess file.<br/> This .htaccess will have to be uploaded in the domain\'s root (eg : in the folder where www.example.com/ is installed).',
	'SEO_HTACCESS_FOLDER_MSG' => 'Once you are ready, you should select all the code bellow, and paste it in a .htaccess file.<br/> This .htaccess will have to be uploaded in the phpBB folder (eg : in the folder where phpBB is installed www.example.com/phpbb/).',
	'SEO_HTACCESS_CAPTION' => 'Caption',
	'SEO_HTACCESS_CAPTION_COMMENT' => 'Comments',
	'SEO_HTACCESS_CAPTION_STATIC' => 'Static parts, editable in phpbb_seo_class.php',
	'SEO_HTACCESS_CAPTION_DELIM' => 'Delimiters, editable in phpbb_seo_class.php',
	'SEO_HTACCESS_CAPTION_SUFFIX' => 'Suffixes, editable in phpbb_seo_class.php',
	'SEO_HTACCESS_CAPTION_SLASH' => 'Optional slashes',
	'SEO_MOD_TYPE_ER'	=> 'The mod rewrite type is not set up properly in phpbb_seo/phpbb_seo_class.php.', 
	'SEO_FILE_TITLE'	=> 'Cache file status',
	'SEO_VALUES'		=> 'Values',
	'SEO_UPDATE'		=> 'Re-Generate',
	'SEO_MORE_OPTION'	=> 'More Options',
	'SEO_MORE_OPTION_EXPLAIN'	=> 'If the first suggested .htaccess does not work.<br/>First make sure mod_rewrite is activated on your server.<br/>Then, make sure you uploaded it in the right folder, and that another one is not perturbing.<br/>If not enough, hit the "more option" button.',
	'SEO_SLASH_DEFAULT'	=> 'Default',
	'SEO_SLASH_ALT'		=> 'Alternate',
	'SEO_CACHE_MSG_OK'	=> 'The cache file was updated successfully.',
	'SEO_CACHE_MSG_FAIL'	=> 'An error occurred while updating the cache file.',
	'SEO_CACHE_UPDATE_FAIL'	=> 'The URL you entered cannot be used, the cache was left untouched.',
	// Security
	'SEO_LOGIN'		=> 'The board requires you to be registered and logged in to view this page.',
	'SEO_LOGIN_ADMIN'	=> 'The board requires you to be logged in as admin to view this page.<br/>Your session has been destroyed for security purposes.',
	'SEO_LOGIN_FOUNDER'	=> 'The board requires you to be logged in as the founder to view this page.',
	'SEO_LOGIN_SESSION'		=> 'Session Check failed.<br/>The Settings were not altered.<br/>Your session has been destroyed for security purposes.',

	'SEO_CACHE_STATUS'		=> 'The cache folder configured is : <b>%s</b>',
	'SEO_CACHE_FOUND'		=> 'The cache folder was successfully found.',
	'SEO_CACHE_NOT_FOUND'		=> 'The cache folder was not found.',
	'SEO_CACHE_WRITABLE'		=> 'The cache folder is writable.',
	'SEO_CACHE_UNWRITABLE'		=> 'The cache folder is unwritable. You need to CHMOD it to 0777.',
	'SEO_CACHE_FORUM_NAME'		=> 'Forum name',
	'SEO_CACHE_URL_OK'		=> 'URL Cached',
	'SEO_CACHE_URL_NOT_OK'		=> 'URL not cached',
	'SEO_CACHE_URL'			=> 'Final URL',
	'SEO_CACHE_OK'			=> 'SET',
	'SEO_CACHE_NOT_OK'		=> 'NOT SET',
	'SEO_CACHE_STATUS_TITLE'	=> 'STATUS',
	'SEO_CACHE_ID_TITLE'		=> 'ID',
	'SEO_CACHE_DETAIL_TITLE'	=> 'DETAILS',
	'SEO_ADVICE_DUPE'		=> 'A duplicate entry in title was detected for this forum.<br/>You should use different URL and titles for each forums',
	'SEO_ADVICE_LENGTH'	=> 'The URL cached is a bit too long.<br/>Consider using a smaller one',
	'SEO_ADVICE_DELIM'	=> 'The URL cached contains the SEO delimiter and ID.<br/>Consider setting up an original one.',
	'SEO_ADVICE_WORDS'	=> 'The URL cached contains a bit too many words.<br/>Consider setting up an original one.',
	'SEO_ADVICE_DEFAULT'	=> 'The ending URL, after formatting, is the default.<br/>Consider setting up an original one.',
	'SEO_ADVICE_START'	=> 'Forum URLs cannot end with a pagination parameter.<br/>It was thus removed from the one submitted.',
	'SEO_ADVICE_DELIM_REM'	=> 'Submitted forum URLs cannot end with a forum delimiter.<br/>It was thus removed from the one submitted.',
	// phpBB SEO Class option
	'rem_sid' => 'SID Removing',
	'rem_sid_explain' => 'SID will be removed from 100% of the URLs passing through the phpbb_seo class, for guests thus bots.<br/>This ensure bots won\'t see any SID on forum, topic and post URLs, but visitors that do not accept cookies will most likely create more than one session.<br/>The Zero duplicate http 301 redirect url with SID for guest and bots by default.',
	'rem_hilit' => 'Highlights Removing',
	'rem_hilit_explain' => 'Highlights will be removed from 100% of the URLs passing through the phpbb_seo class, for guests thus bots.<br/>This ensure bots won\t see any Highlights on forum, topic and post URLs.<br/>The Zero duplicate will automatically follow this setting, eg http 301 redirect url with highlights for guest and bots.',
	'rem_small_words' => 'Remove small words',
	'rem_small_words_explain' => 'Allow to remove all words of less than three letters in rewritten URLs.<br/><b style="color:red">Please Note</b><br/><ul style="margin-left:20px">The filtering will change potentially a lot of URLs in your web site.<br/>Starting to use it with an already indexed web site should thus be considered	with as much care as when migrating and not to often.<br/>So you\'d better be decided to go for it or not.</ul>',
	'virtual_folder' => 'Virtual Folder',
	'virtual_folder_explain' => 'Allow to add the forum URL as a virtual folder in topic URLs.<br/><u>Example :</u><ul style="margin-left:20px"><b>forum-title-fxx/topic-title-txx.html</b> VS <b>topic-title-txx.html</b><br/>for a topic URL.</ul><br/><b style="color:red">Please Note</b><br/><ul style="margin-left:20px">The Virtual folder injection option can change all your web site\'s URLs almost too easily.<br/>Starting to use it with an already indexed web site should thus be considered with as much care as when migrating and not to often.<br/>So you\'d better be decided to go for it or not.</ul>',
	'virtual_root' => 'Virtual Root',
	'virtual_root_explain' => 'If phpBB is installed in a sud folder (example phpBB3/), you can simulate a root install for rewritten links.<br/><u>Example :</u><ul style="margin-left:20px"><b>phpBB3/forum-title-fxx/topic-title-txx.html</b> VS <b>forum-title-fxx/topic-title-txx.html</b><br/>for a topic URL.</ul><br/>This can be handy to shorten URLs a bit, especially if you are using the "Virtual Folder" feature. UnRewritten links will continue to appear and work in the phpBB folder.<br/><br/><b style="color:red">Please Note :</b><br/><ul style="margin-left:20px">Using this option requires to use a home page for the forum index (like forum.html).<br/> This option can change all your web site\'s URLs almost too easily.<br/>Starting to use it with an already indexed web site should thus be considered with as much care as when migrating and not to often.<br/>So you\'d better be decided to go for it or not.</ul>',
	'cache_layer' => 'Forum URL caching',
	'cache_layer_explain' => 'Turns on the cache for forum URLs and allow to separate forum titles from their URL<br/><u>Example :</u><ul style="margin-left:20px"><b>forum-title-fxx/</b> VS <b>any-title-fxx/</b><br/>for a forum URL.</ul><br/><b style="color:red">Please Note</b><br/><ul style="margin-left:20px">This option will allow you to change your forum URL, thus potentially many topic URLS if you are using the Virtual Folder option.<br/>The topic URLs will always be redirected properly with the Zero Duplicate.<br/>It will as well be the case for forum URL as long as you keep the delimiter and IDs, see below.</ul>',
	'rem_ids' => 'Forum ID Removing',
	'rem_ids_explain' => 'Get rid of the IDs and delimiters in forum URLs. Only apply if Forum URL caching is activated.<br/><u>Example :</u><ul style="margin-left:20px"><b>any-title-fxx/</b> VS <b>any-title/</b><br/>for a forum URL.</ul><br/><b style="color:red">Please Note</b><br/><ul style="margin-left:20px">This option will allow you to change your forum URL, thus potentially many topic URLS if you are using the Virtual Folder option.<br/>The topic URLs will always be redirected properly with the Zero Duplicate.<br/><u>It will not always be the case with the forum URLs :</u><br/><ul style="margin-left:20px"><b>any-title-fxx/</b> will always be properly redirected but it won\'t be the case if you edit <b>any-title/</b> to <b>something-else/</b>.<br/> In such case, <b>any-title/</b> will for now be treated as a forum that does not exist.<br/>So you\'d better be decided to go for it or not, but it can really be powerful SEO wise.</ul></ul>',
	// Zero duplicate
	'zero_dupe' => array('zero_dupe' => 'Zero duplictate',
		'zero_dupe_explain' => 'The following settings concerns the Zero duplicate, you can modify them upon your needs.<br/>These do not imply any .htacess update.',
		'on' => 'Activate the Zero duplictate',
		'on_explain' => 'Allow to activate and desactivate the Zero duplicate redirections.',
		'strict' => 'Strict Mode',
		'strict_explain' => 'When activated, the zero dupe will check if the requested URL exactly matches the one attended.<br/>When set to no, the zero dupe will make sure the attended url is the fist part of the one requested.<br/>The interest is to make it easier to deal with mods that could interfere with the zero dupe by adding GET vars.',
		'post_redir' => 'Posts Redirections',
		'post_redir_explain' => 'This option will determine how to handle post urls; it can take four values :<ul style="margin-left:20px"><li><b>&bull;&nbsp;off</b>, do not redirect post url, whatever the case,</li><li><b>&bull;&nbsp;post</b>, only make sure postxx.html is used for a post url,</li><li><b>&bull;&nbsp;guest</b>, redirect guests if required to the corresponding topic url rather than to the postxx.html, and only make sure postxx.html is used for logged users,<li><b>&bull;&nbsp;all</b>, redirect if required to the corresponding topic url.</li></ul><br/><b style="color:red">Please Note</b><br/><ul style="margin-left:20px">Keeping the <b>postxx.html</b> URLs is harmless SEO wise as long as you keep the disallow on post urls in your robots.txt.<br/>Redirecting them all will most likely produce the most redirections among all.<br/>If you redirect postxx.html in all cases, this as well mean that a message that would be posted in a thread and then moved in another one will see it\'s url changing, which thanks to the zero duplicate mod is of no harm SEO wise, but the previous link to the post won\'t link to it anymore in such case.</ul>.
',),
));
?>
