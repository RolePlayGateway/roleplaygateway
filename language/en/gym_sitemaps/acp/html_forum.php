<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: html_forum.php 204 2009-12-20 12:04:51Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* html_forum [English]
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
	'HTML_FORUM' => 'HTML Forum Module',
	'HTML_FORUM_EXPLAIN' => 'These are the settings for the HTML forum module.<br/> Some of them can be overridden depending on the HTML override settings.',
	'HTML_FORUM_EXCLUDE' => 'Forum Exclusions',
	'HTML_FORUM_EXCLUDE_EXPLAIN' => 'You can here exclude one or several forums from the RSS listing.<br /><u>Note :</u> If this field is left empty, all readable forums will be listed.',
	'HTML_FORUM_ALLOW_NEWS' => 'Forum News',
	'HTML_FORUM_ALLOW_NEWS_EXPLAIN' => 'The forum news page is a page displaying one or several topic’s first posts, clipped or not, and coming from one or several forum you may select bellow.',
	'HTML_FORUM_ALLOW_CAT_NEWS' => 'Forum category news',
	'HTML_FORUM_ALLOW_CAT_NEWS_EXPLAIN' => 'Activate, or not, the per forum news pages. If activated, each non excluded forum will have a news page for its topics.',
	'HTML_FORUM_NEWS_IDS' => 'Forum news source',
	'HTML_FORUM_NEWS_IDS_EXPLAIN' => 'You can select one or several forums, even private, as source for your main forum news page.<br /><u>Note</u> :<br />If left empty, all authed forum will be taken as source for the forum news page.',
	'HTML_FORUM_LTOPIC' => 'Optional last active topic list',
	'HTML_FORUM_INDEX_LTOPIC' => 'Display on forum map',
	'HTML_FORUM_INDEX_LTOPIC_EXPLAIN' => 'Display, or not, the last active topic list on the forum map.<br/>Enter the number of topic to display, 0 to deactivate.',
	'HTML_FORUM_CAT_LTOPIC' => 'Display on forum category maps',
	'HTML_FORUM_CAT_LTOPIC_EXPLAIN' => 'Display, or not, the last active topic list on each forum maps.<br/>Enter the number of topic to display, 0 to deactivate.',
	'HTML_FORUM_NEWS_LTOPIC' => 'Display on forum news page',
	'HTML_FORUM_NEWS_LTOPIC_EXPLAIN' => 'Display, or not, the last active topic list on the forum news page.<br/>Enter the number of topic to display, 0 to deactivate.',
	'HTML_FORUM_CAT_NEWS_LTOPIC' => 'Display on forum category news page',
	'HTML_FORUM_CAT_NEWS_LTOPIC_EXPLAIN' => 'Display, or not, the last active topic list on each forum news page.<br/>Enter the number of topic to display, 0 to deactivate.',
	'HTML_FORUM_LTOPIC_PAGINATION' => 'Last active topic pagination',
	'HTML_FORUM_LTOPIC_PAGINATION_EXPLAIN' => 'Display, or not, topic pagination in the last active topic list.',
	'HTML_FORUM_LTOPIC_EXCLUDE' => 'Last active topic list exclusion',
	'HTML_FORUM_LTOPIC_EXCLUDE_EXPLAIN' => 'You can here exclude one or several forum from the last active topic listing.<br /><u>Note :</u> If this field is left empty, all readable forums will be listed.',
	// Pagination
	'HTML_FORUM_PAGINATION' => 'Forum map Pagination',
	'HTML_FORUM_PAGINATION_EXPLAIN' => 'Activate, or not, paginating of forum maps. Turn this on if you want to display more than one page and list all topics in each forum map.',
	'HTML_FORUM_PAGINATION_LIMIT' => 'Topics per page',
	'HTML_FORUM_PAGINATION_LIMIT_EXPLAIN' => 'When Forum map Pagination is activated, you can here define the number of topic displayed per page.',
	// Content
	'HTML_FORUM_CONTENT' => 'Forum Content settings',
	'HTML_FORUM_FIRST' => 'Map sorting',
	'HTML_FORUM_FIRST_EXPLAIN' => 'The forum maps can be sorted against the topic first post date or the topic last post date. This means that you can either use the topic creation or the last replied order.',
	'HTML_FORUM_NEWS_FIRST' => 'News sorting',
	'HTML_FORUM_NEWS_FIRST_EXPLAIN' => 'The forum news pages can be sorted against the topic first post date or the topic last post date. This means that you can either use the topic creation or the last replied order.',
	'HTML_FORUM_LAST_POST' => 'Display last post',
	'HTML_FORUM_LAST_POST_EXPLAIN' => 'Display, or not, the last post information of the topic listed.',
	'HTML_FORUM_POST_BUTTONS' => 'Display post button',
	'HTML_FORUM_POST_BUTTONS_EXPLAIN' => 'Display, or not, the post button such as reply, edit etc ...',
	'HTML_FORUM_RULES' => 'Display forum rules',
	'HTML_FORUM_RULES_EXPLAIN' => 'Display, or not, forum rules in forum news and map pages.',
	'HTML_FORUM_DESC' => 'Display forum rules description',
	'HTML_FORUM_DESC_EXPLAIN' => 'Display, or not, forum description in forum news and map pages.',
	// Reset settings
	'HTML_FORUM_RESET' => 'HTML forum module',
	'HTML_FORUM_RESET_EXPLAIN' => 'Reset all the HTML forum module otpions to default values.',
	'HTML_FORUM_MAIN_RESET' => 'HTML forum Main',
	'HTML_FORUM_MAIN_RESET_EXPLAIN' => 'Reset to default all the options in the "HTML Settings" (main) tab of the HTML forum module.',
	'HTML_FORUM_CONTENT_RESET' => 'HTML forum News',
	'HTML_FORUM_CONTENT_RESET_EXPLAIN' => 'Reset to default all the news options of the HTML forum module.',
	'HTML_FORUM_CACHE_RESET' => 'HTML forum Cache',
	'HTML_FORUM_CACHE_RESET_EXPLAIN' => 'Reset to default all the caching options of the HTML forum module.',
	'HTML_FORUM_MODREWRITE_RESET' => 'HTML forum URL rewriting',
	'HTML_FORUM_MODREWRITE_RESET_EXPLAIN' => 'Reset to default all the URL rewriting options of the HTML forum module.',
	'HTML_FORUM_GZIP_RESET' => 'HTML forum Gunzip',
	'HTML_FORUM_GZIP_RESET_EXPLAIN' => 'Reset to default all the Gunzip options of the HTML forum module.',
	'HTML_FORUM_LIMIT_RESET' => 'HTML forum Limits',
	'HTML_FORUM_LIMIT_RESET_EXPLAIN' => 'Reset to default all the Limits options of the HTML forum module.',
	'HTML_FORUM_SORT_RESET' => 'HTML forum Sorting',
	'HTML_FORUM_SORT_RESET_EXPLAIN' => 'Reset to default all the Sorting options of the HTML forum module.',
	'HTML_FORUM_PAGINATION_RESET' => 'HTML forum Pagination',
	'HTML_FORUM_PAGINATION_RESET_EXPLAIN' => 'Reset to default all the Pagination options of the HTML forum module.',
));
?>