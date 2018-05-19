<?php
/**
*
* @FILENAME  : language\en\mods\phpBBTags_lang.php
* @DATE      : 19 Jan 2009
* @VERSION   : 1.0
* @COPYRIGHT : (c) 2009 phpbb3-mods
* @Website   : http://www.phpbb3-mods.com/
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
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
	'PBT_ADD_TAGS'				=> 'Add Tags',
	'PBT_TOPIC_TAGS'			=> 'Topic Tags',
	'PBT_ADD_TAGS_DESCRIPTION'	=> 'Add as many tags as you wish seperated by a comma',
	'PBT_ADD_TAGS_DONE'			=> '<strong>%1$s</strong> added, <strong>%2$s</strong> not added. Tags are not added if they are not unique',
	'PBT_ADD_TAGS_RETURN'		=> '%s Click here to return to the topic %s',
	'PBT_ADD_TAGS_NO_TAGS'		=> 'You must supply some tags for the topic',
	'PBT_TAG_CLOUD_TITLE'		=> 'Tag Cloud',
	'PBT_TAGS_RESULT_TITLE'		=> 'Tag Search Results',
	'PBT_TAG_SEARCH_TEXT'		=> 'Search Tags',
	'PBT_NUM_TOPIC'				=> '1 topic',
	'PBT_NUM_TOPICS'			=> '%s topics',
	'PBT_NO_RESULTS'			=> 'No posts have been tagged with any of: <strong> %s </strong>',
	'PBT_RETURN_TO_SEARCH'		=> '%s Click here to return to the tag search %s',
	'PBT_ENTER_SEARCH_TEXT'		=> 'Enter the tags you wish to search for',
	'PBT_SEARCH_PAGE_TITLE'		=> 'Tag Search',
	'PBT_TAGS_LABEL'			=> 'Tags',
	'PBT_NO_SEARCH_CRIT'		=> 'You must enter a tag to search for',
	'PBT_ENTER_TAGS'			=> 'Enter your tags',
	'PFB_INPUT_TAGS_EXPLANATION'=> 'You can add tags to your post to help other users find your post, you may add as many tags as you wish. Seperate the tags using a comma',
	'PBT_TAGS'					=> 'Tags',
	'PBT_ERRORS'				=> 'Errors',
	'PBT_SUGGESTIONS'			=> 'Suggestions',
	'PBT_SUGGESTIONS_EXPLANATION'=> 'To keep things organised, here are some tags that have been used by other users, you can click the tag to insert it.',
	'PBT_TAGGING'				=> 'Tagging',
	'PBT_ENABLE_TAGGING'		=> 'Click here to enable tagging',
	'PBT_NOT_REPLY'				=> 'Please, note this is NOT a quick reply box, this is used for tagging posts. Click above to enable',

	'PNF_JS_WARN'				=> 'Javascript must be enabled to use the tagging feature',
		
	//acp language tags
	'PBT_ACP_DELETE'				=> 'Delete',
	'PBT_ACP_MANAGE_DESCRIPTION'	=> 'Welcome to the phpBB Topic Tags management area, here you can remove tags associated with topics that are not relevant or you find offensive.',
	'PBT_ACP_SEARCH_TAG'			=> 'Use this search box to find tags you wish to delete',
	'PBT_ACP_MANAGE_TITLE'			=> 'Manage Tags',
	'PBT_ACP_RESULTS'				=> 'Results',
	'PBT_ACP_RESULTS_DESC'			=> 'To remove a tag associated with a topic click the tag in the table below. To view the topic you can clikc the topic\'s title',
	'PBT_ACP_TABLE_TITLE'			=> 'Topic title and tags',
	'PBT_ACP_CONFIGURE_TITLE'		=> 'Configure',
	'PBT_ACP_CONFIGURE_DESCRIPTION' => 'Welcome to the phpBB Topic Tags configuration area, here you can adjust how phpBB Topic Tags interacts with your board',
	'PBT_ACP_OPTIONS'				=> 'Options',
	'PBT_ACP_ON'					=> 'On',
	'PBT_ACP_OFF'					=> 'Off',
	'PBT_ACP_SWITCH_ON'				=> 'Turn tagging on or off',
	'PBT_ACP_SWITCH_ON_EXPLAIN'		=> 'Turning tagging off stops users from adding tags and removes all tag clouds from the board',
	'PBT_ACP_TAG_AMOUNT'			=> 'The number of tags to show in the tag cloud',
	'PBT_ACP_ALL'					=> 'All',
	'PBT_ACP_MODS'					=> 'Moderators',
	'PBT_ACP_ADMIN'					=> 'Administrators',
	'PBT_ACP_TAGGERS'				=> 'Who is allowed to tag?',
	'PBT_ACP_TAGGERS_EXPLAIN'		=> 'Choose who is allowed to tag topics. This uses a hierarchy of permissions i.e. choosing moderators will only allow mods and admins to tag topics',
	'PBT_ACP_MIN_SIZE'				=> 'Minimum font size',
	'PBT_ACP_MIN_SIZE_EXPLAIN'		=> 'The minimum size of the fonts in the tag clouds',
	'PBT_ACP_MAX_SIZE'				=> 'Maximum font size',
	'PBT_ACP_MAX_SIZE_EXPLAIN'		=> 'The maximum size of the fonts in the tag clouds',
	'PBT_ACP_FORUM_TG'				=> 'Show tag cloud on forum view',
	'PBT_ACP_INDEX_TG'				=> 'Show tag cloud on forum index',
	'PFB_ACP_REMOVE_CONF'			=> 'Are you sure you wish to remove this tag?',
	'PBT_ACP_REMOVE_SUCCESSFUL'		=> 'Tag was successfully removed',
	'PBT_ACP_NO_TOPICS'				=> 'No topics returned',
	'PBT_ACP_CONF_UPDATE_SUCCESSFUL'=> 'phpBB Topic Tags configuration updated',
	'PBT_ACP_COLOUR1'				=> 'First colour',
	'PBT_ACP_COLOUR1_EXPLAIN'		=> 'The starting colour of the gradient. Please enter a valid hex number',
	'PBT_ACP_COLOUR2'				=> 'Second colour',
	'PBT_ACP_COLOUR2_EXPLAIN'		=> 'The ending colour of the gradient. Please enter a valid hex number',
	'PBT_ACP_CLEAR_ORPHANS'			=> 'Clear Orphans',
	'PBT_ACP_ORPHAN_SUCCESS'		=> 'Operation successfull. %s orphans deleted.',
	'PFB_ACP_ORPHAN_CONF'			=> 'Are you sure you want to remove all %s orphans?',
	
	//error messages
	'TOO_SMALL_TAGS'				=> 'The number of tags in the cloud is to small. Must be greater than 0',
	'TOO_SMALL_PBT_MIN_FONT'		=> 'The minimum font size is too small. Must be greater than 0',
	'PBT_ACP_COLOUR1_INVALID'		=> 'The colour you have chosen for the start of the gradient is invalid. Please choose a valid hex number.',
	'PBT_ACP_COLOUR2_INVALID'		=> 'The colour you have chosen for the end of the gradient is invalid. Please choose a valid hex number.',
	'TOO_SHORT_PBT_COLOUR1'			=> 'The colour you have chosen for the start of the gradient is invalid. Please choose a valid hex number.',
    'TOO_SHORT_PBT_COLOUR2'			=> 'The colour you have chosen for the end of the gradient is invalid. Please choose a valid hex number.',
	'PBT_TAGS_NOT_NUM'				=> 'Number of tags is not a number.',
	'PBT_MAX_FONT_NOT_NUM'			=> 'Maximum font size is not a number.',
	'PBT_MIN_FONT_NOT_NUM'			=> 'Minimum font size is not a number.',
	'PBT_ACTION_CANCELLED'			=> 'Action Cancelled',
));
?>