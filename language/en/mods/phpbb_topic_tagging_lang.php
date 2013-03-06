<?php
/** 
*
* example [English]
*
* @package language
* @version $Id: phpbb_topic_tagging_lang.php,v 0.0.0 2007/09/24 16:13:01 nanothree Exp $
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
	'PTT_ADD_TAGS'				=> 'Add Tags',
	'PTT_TOPIC_TAGS'			=> 'Topic Tags',
	'PTT_ADD_TAGS_DESCRIPTION'	=> 'Add as many tags as you wish seperated by a comma',
	'PTT_ADD_TAGS_DONE'			=> 'Topic Tagged!',
	'PTT_ADD_TAGS_RETURN'		=> '%s Click here to return to the topic %s',
	'PTT_ADD_TAGS_NO_TAGS'		=> 'You must supply some tags for the topic',
	'PTT_TAG_CLOUD_TITLE'		=> 'Tag Cloud',
	'PTT_TAGS_RESULT_TITLE'		=> 'Tag Search Results',
	'PTT_TAG_SEARCH_TEXT'		=> 'Search Tags',
	'PTT_NUM_TOPIC'				=> '1 topic',
	'PTT_NUM_TOPICS'			=> '%s topics',
	'PTT_NO_RESULTS'			=> 'No posts have been tagged with any of: <strong> %s </strong>',
	'PTT_RETURN_TO_SEARCH'		=> '%s Click here to return to the tag search %s',
	'PTT_ENTER_SEARCH_TEXT'		=> 'Enter the tags you wish to search for',
	'PTT_SEARCH_PAGE_TITLE'		=> 'Tag Search',
	'PTT_TAGS_LABEL'			=> 'Tags',
	'PTT_NO_SEARCH_CRIT'		=> 'You must enter a tag to search for',
	'PTT_ENTER_TAGS'			=> 'Enter your tags',
	'PTT_INPUT_TAGS_EXPLANATION'=> 'You can add tags to your post to help other users find your post, you may add as many tags as you wish. Seperate the tags using a comma',
	'PTT_TAGS'					=> 'Tags',
	'PTT_ERRORS'				=> 'Errors',	
	'PTT_SUGGESTIONS'			=> 'Suggestions',
	'PTT_SUGGESTIONS_EXPLANATION'=> 'To keep things organised, here are some tags that have been used by other users, you can click the tag to insert it.',
	'PTT_TAGGING'				=> 'Tagging',
	'PTT_ENABLE_TAGGING'		=> 'Click here to enable tagging',
	'PTT_NOT_REPLY'				=> 'Please, note this is NOT a quick reply box, this is used for tagging posts. Click the link above to enable',
	'PTT_JS_WARN'				=> 'Javascript must be enabled to use teh tagging feature',
		
	//acp language tags
    'PTT_ACP_MODULE_TITLE'          => 'Topic Tagging',
	'PTT_ACP_DELETE'				=> 'Delete',
	'PTT_ACP_MANAGE_DESCRIPTION'	=> 'Welcome to the topic tagging management area, here you can remove tags associated with topics that are not relevant or you find offensive.',
	'PTT_ACP_SEARCH_TAG'			=> 'Use this search box to find tags you wish to delete',
	'PTT_ACP_MANAGE_TITLE'			=> 'Manage Tags',
	'PTT_ACP_RESULTS'				=> 'Results',
	'PTT_ACP_RESULTS_DESC'			=> 'To remove a tag associated with a topic click the tag in the table below. To view the topic you can click the topic\'s title',
	'PTT_ACP_TABLE_TITLE'			=> 'Topic title and tags',
	'PTT_ACP_CONFIGURE_TITLE'		=> 'Configure',
	'PTT_ACP_CONFIGURE_DESCRIPTION' => 'Welcome to the topic tagging configuration area, here you can adjust how the topic tagging mod interacts with your board',
	'PTT_ACP_OPTIONS'				=> 'Options',
	'PTT_ACP_ON'					=> 'On',
	'PTT_ACP_OFF'					=> 'Off',
	'PTT_ACP_SWITCH_ON'				=> 'Turn tagging on or off',
	'PTT_ACP_SWITCH_ON_EXPLAIN'		=> 'Turning tagging off stops users from adding tags and removes all tag clouds from the board',
	'PTT_ACP_TAG_AMOUNT'			=> 'The number of tags to show in the tag cloud',
	'PTT_ACP_ALL'					=> 'All',
	'PTT_ACP_MODS'					=> 'Moderators',
	'PTT_ACP_ADMIN'					=> 'Administrators',
	'PTT_ACP_TAGGERS'				=> 'Who is allowed to tag?',
	'PTT_ACP_TAGGERS_EXPLAIN'		=> 'Choose who is allowed to tag topics. This uses a hierarchy of permissions i.e. choosing moderators will only allow mods and admins to tag topics',
	'PTT_ACP_MIN_SIZE'				=> 'Minimum font size',
	'PTT_ACP_MIN_SIZE_EXPLAIN'		=> 'The minimum size of the tags in the tag clouds',
	'PTT_ACP_MAX_SIZE'				=> 'Maximum font size',
	'PTT_ACP_MAX_SIZE_EXPLAIN'		=> 'The maximum size of the tags in the tag clouds',
	'PTT_ACP_FORUM_TG'				=> 'Show tag cloud on forum view',
	'PTT_ACP_INDEX_TG'				=> 'Show tag cloud on forum index',
	'PTT_ACP_REMOVE_CONF'			=> 'Are you sure you wish to remove this tag?',
	'PTT_ACP_REMOVE_SUCCESSFUL'		=> 'Tag was successfully removed',
	'PTT_ACP_NO_TOPICS'				=> 'No topics returned',
	'PTT_ACP_CONF_UPDATE_SUCCESSFUL'=> 'Topic tagging configuration updated',
	'PTT_ACP_COLOUR1'				=> 'First colour',
	'PTT_ACP_COLOUR1_EXPLAIN'		=> 'The starting colour of the gradient. Please enter a valid hex number',
	'PTT_ACP_COLOUR2'				=> 'Second colour',
	'PTT_ACP_COLOUR2_EXPLAIN'		=> 'The ending colour of the gradient. Please enter a valid hex number',
	'PTT_ACP_CLEAR_ORPHANS'			=> 'Clear Orphans',
	'PTT_ACP_ORPHAN_SUCCESS'		=> 'Operation successfull. %s orphans deleted.',
	'PTT_ACP_ORPHAN_CONF'			=> 'Are you sure you want to remove all %s orphans?',
    'PTT_ACP_VIEW_ALL_TITLE'        => 'View All Tags',
    'PTT_ACP_REMOVE_TITLE'          => 'Remove',
    'PTT_ACP_TAG_SORT'              => 'Tag Cloud Sorting',
    'PTT_ACP_TAG_SORT_EXPLAIN'      => 'You can order the way tags are displayed in the cloud a number of ways',
	'PTT_ACP_TAG_SORT_DEFAULT'      => 'Default',
	'PTT_ACP_TAG_SORT_RANDOM'       => 'Random',
	'PTT_ACP_TAG_SORT_POPULAR'      => 'Most Popular',



	//error messages
	'TOO_SMALL_TAGS'				=> 'The number of tags in the cloud must be at least 1',
	'TOO_SMALL_PTT_MIN_FONT'		=> 'The minimum font size must be at least 1',
	'PTT_ACP_COLOUR1_INVALID'		=> 'The colour you have chosen for the start of the gradient is invalid. Please choose a valid hex number.',
	'PTT_ACP_COLOUR2_INVALID'		=> 'The colour you have chosen for the end of the gradient is invalid. Please choose a valid hex number.',
	'TOO_SHORT_PTT_COLOUR1'			=> 'The colour you have chosen for the start of the gradient is invalid. Please choose a valid hex number.',
    'TOO_SHORT_PTT_COLOUR2'			=> 'The colour you have chosen for the end of the gradient is invalid. Please choose a valid hex number.',
	'PTT_TAGS_NOT_NUM'				=> 'Number of tags is not a number.',
	'PTT_MAX_FONT_NOT_NUM'			=> 'Maximum font size is not a number.',
	'PTT_MIN_FONT_NOT_NUM'			=> 'Minimum font size is not a number.',
	'PTT_ACTION_CANCELLED'			=> 'Action Cancelled',
));
?>