<?php
/**
*
* @package prs
* @version 1.0.0 2007/12/23 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
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
// Module
	'ACP_PRS'	=> '(Topic &) Post Rating System',
	'ACP_CAT_PRS'	=> 'Post Rating System',
	'PRS_OVERVIEW'	=> 'Overview',
	'PRS_DETAILS'	=> 'Details',
	'PRS_UPDATE'	=> 'Update',
	'PRS_STATS'	=> 'Stats',

// General
	'001_PROCENT'	=> '0.99%',
	'0025_PROCENT'	=> '0.975%',
	'005_PROCENT'	=> '0.95%',
	'095_PROCENT'	=> '0.05%',
	'0975_PROCENT'	=> '0.025%',
	'099_PROCENT'	=> '0.01%',

// Legends
	'PRS_ON_OFF_SWITCHES'		=> 'Switches',
	'PRS_BASIS_CONFIGURATION'	=> 'Basis configuration',
	'PRS_KARMA_CONFIGURATION'	=> 'Karma configuration',
	'PRS_SHADOW_CONFIGURATION'	=> 'Shadow configuration',
	'PRS_PENALTY_CONFIGURATION'	=> 'Penalty configuration',
	'PRS_MODPOINTS_CONFIGURATION'	=> 'Modpoints configuration',
	'PRS_EXTRA_CONFIGURATION'	=> 'Extra options',

// Legends Explained
	'PRS_ON_OFF_SWITCHES_EXPLAIN'		=> 'With the section \'switches\' you are able to enable or disable major modules. For your convenience the \'overview\' page contains a simplified way to configure these modules. Please go to the \'details\' page if you need to configure these modules in more details.',
	'PRS_BASIS_CONFIGURATION_EXPLAIN'	=> 'This section contains the basic stuff required for the Post Rating System.',
	'PRS_KARMA_CONFIGURATION_EXPLAIN'	=> 'The karma module provides the (weighted) average rating over a period that fades out by dividing this period in to intervals. This way the youngest interval has a greater influence athen the oldest. I.e. from now to 30 days ago has a greater influence than 30 days ago to 60 days ago.',
	'PRS_SHADOW_CONFIGURATION_EXPLAIN'	=> 'The shadow module is a system that figures out what your users would have voted, by comparing voting history of users.',
	'PRS_PENALTY_CONFIGURATION_EXPLAIN'	=> 'The penalty module is a system that blocks users who give scores that lay out of the normal spectrum. Users who do this stuctualy lose there voting rights.',
	'PRS_MODPOINTS_CONFIGURATION_EXPLAIN'	=> 'The modpoints module contains a way for you to rewarding users who post. Users need these points to be able to vote and can collect them by posting. The number of modpoints one gains by writing a post can (optionaly) be based on the rate their post get.',
	'PRS_EXTRA_CONFIGURATION_EXPLAIN'	=> 'This section contains a number of options that make the mod flexible.',

// On Off Swithces
	'PRS_ENABLED'			=> 'Module enabled (main switch)',
	'PRS_KARMA_ENABLED'		=> 'Karma enabled',
	'PRS_SHADOW_VOTES_ENABLED'	=> 'Shadow votes enabled',
	'PRS_PENALTY_ENABLED'		=> 'Penalties enabled',
	'PRS_MODPOINTS_ENABLED'	=> 'Modpoints enabled',

// Overview
	'PRS_OVERVIEW_CONFIGURATION'		=> 'Simple configuration',
	'PRS_OVERVIEW_CONFIGURATION_NONSTRICT'	=> 'Easy',
	'PRS_OVERVIEW_CONFIGURATION_AVERAGE'	=> 'Average',
	'PRS_OVERVIEW_CONFIGURATION_STRICT'	=> 'Strict',
	'PRS_OVERVIEW_CONFIGURATION_CUSTOM'	=> 'Custom',

// Basic Configuration
	'PRS_VOTES_PERIOD'		=> 'Period posts are open for voting (days)',
	'PRS_VOTES_MEMBERSHIP_PERIOD'	=> 'Required membership period (days)',
	'PRS_VOTES_MIN_POSTS'		=> 'Voting requires post count',
	'PRS_DEFAULT_RATING'		=> 'Default rating',
	'PRS_DEFAULT_RATING_EXPLAIN'	=> 'This value is used for posts and ratings that have received no votes at all. The range of this field is 0 to 50.',

// Karma configuration
	'PRS_KARMA_PERIOD'		=> 'Interval',
	'PRS_KARMA_N'			=> 'Number of intervals',

	// Shadow Configuration
	'PRS_SHADOW_CHI_CHANCE'	=> 'The chance the shadow vote is correct',
	'PRS_SHADOW_CHI_CHANCE_EXPLAIN'	=> 'The systems tries to match non-voters to voters in order to determine what someone would have voted. A higher value gives a higher chance that matches are found. A lower value gives more guaranties about the accuracy when a matches are found.',
	'PRS_SHADOW_CHI_REFRESH'	=> 'Shadow algorithm interval',
	'PRS_SHADOW_MIN_VOTES'		=> 'Minimum votes casted per user requirement',
	'PRS_SHADOW_MIN_VOTES_EXPLAIN'	=> 'The shadow algorithm works faster with a higher value because it eliminates from consideration the users who are relative pore candidates for the shadow system.',

// Penalty Configuration
	'PRS_PENALTY_BORDER'		=> 'Penalty permillage (of votes)',
	'PRS_PENALTY_MINIMUM_VOTES'	=> 'Minimum ammount of votes required',
	'PRS_PENALTY_PERIOD'		=> 'Examined period',
	'PRS_PENALTY_USER_OVERALL'	=> 'Percentage users that will lose voting rights',
	'PRS_PENALTY_USER_POSTER'	=> 'Percentage users that will lose partial voting rights',

// Modpoints
	'PRS_MODPOINTS_NEWPOST'	=> 'Amount of modpoints recieved for each new post',
	'PRS_MODPOINTS_PERIOD'	=> 'Period the modpoints stay valid',
	'PRS_MODPOINTS_KARMA'		=> 'Base modpoints on karma',
	'PRS_MODPOINTS_KARMA_EXPLAIN'	=> 'When enabled, the modpoints a user will receive will be in the range %1$s and %2$s and depends on the karma the user has. By lowering the default rating one raises the range and by raising it ones lowers the range.',

// Extra options
	'PRS_EXTRA_FIRST_POST_ONLY'	=> 'Can users only vote on the first post in a topic',
	'PRS_EXTRA_FIRST_POST_ONLY_EXPLAIN'	=> 'This also prevents users from seeing ratings of other post, but doesn\'t effect the way the topic rating is determined.',
	'PRS_EXTRA_VOTE_ONLY_ONES'	=> 'Deny users to change their vote',
	'PRS_EXTRA_OWN_VOTE'		=> 'Can users vote on their own posts?',
	'PRS_EXTRA_OWN_VOTE_EXPLAIN'	=> 'Enable the shadow system instead if you want to avoid subjective votes but want to include what the poster whould have objectively voted.',
	'PRS_EXTRA_ALWAYS_SHOW_RATING'	=> 'Show rating when voting round is open',
	'PRS_EXTRA_ALWAYS_SHOW_RATING_EXPLAIN'	=> 'Enabling this allows for strategical votes, an alternative option is to shorten the voting period. (See the details page)',
	'PRS_EXTRA_TOPIC_RATING'	=> 'Topic rating based on',

	'FIRST_POST'			=> 'First post',
	'ALL_POSTS'			=> 'All posts',

// Votes Information
	'PRS_AVAILABLE_VOTES'	=> 'Available votes',
	'PRS_STARS'		=> 'Number of stars',
	'PRS_STARS_EXPLAIN'	=> 'Explaination',

// Update
	'PRS_UPDATE_CHECK_UNAVAILABLE'	=> 'Update check unavailable',
	'PRS_VERSION_UP2DATE'		=> 'Version is up to date',
	'PRS_VERSION_NOT_UP2DATE'	=> 'Version is not up to date',
	'PRS_VERSION'			=> 'Version',
	'PRS_LATEST_VERSION'		=> 'Latest version',
	'PRS_MD5'			=> 'Fingerprint',
	'PRS_MD5_EXPLAIN'		=> 'The root source files are up to date when both fingerprints are the same.',
	'PRS_LATEST_MD5'		=> 'Remote / package fingerprint',
	'PRS_CREDITS_INSTRUCTIONS'	=> '<h1>Update instructions</h1>

<p>Please include the copyright notice in to <i>all</i> the overall_footer.html template file. With this you give credit for the work given freely by the developer(s) <i>and</i> help build interest, traffic and use of this mod.</p>

<p>Please open the files: %1$s<br />find the string \'%2$s\'<br />and add on the new line \'%3$s\'.</p>',
	'PRS_UPDATE_INSTRUCTIONS'	=> '<h1>Release announcement</h1>

		<p>Please read <a href="%1$s" title="%1$s"><strong>the release announcement for the latest version</strong></a> before you continue your update process, it may contain useful information. It also contains full download links as well as the change log.</p>',

// Stats
	'PRS_STATS_USERS'		=> 'Users',
	'PRS_STATS_VOTERS'		=> 'Voters',
	'PRS_STATS_VOTERS_EXPLAIN'	=> 'Users who have been a memeber for %1$s day(s) and have written %2$s post(s).',
	'PRS_STATS_SHADOWERS'		=> 'Shadowers',
	'PRS_STATS_SHADOWERS_EXPLAIN'	=> 'Voters who have voted at least %1$s time(s).',
	'PRS_STATS_AVG_RATING'		=> 'Average rating',
	'PRS_STATS_AVG_KARMA'		=> 'Average karma',
	'PRS_STATS_AVG_PENALTY'		=> 'Average penalty',
	'PRS_STATS_ROWS_VOTES'		=> 'Number of rows of table VOTES',
	'PRS_STATS_ROWS_VOTES_CHI'	=> 'Number of rows of table VOTES_CHI',
	'PRS_STATS_ROWS_PENALTY'	=> 'Number of rows of table PENALTY',
	'PRS_STATS_ROWS_MODPOINTS'	=> 'Number of rows of table MODPOINTS',

// Loggin
	'LOG_PRS_INSTALLED'		=> '<strong>Mod PRS installed</strong>',
	'LOG_PRS_UPDATED'		=> '<strong>Mod PRS updated</strong>',
	'LOG_PRS_CHANGED_SETTINGS'	=> '<strong>Changed setting of the Post Rating System.</strong>',

/* This is obsolite, if this option returns then the cache will be ajusted makeing this unnecessary! (Don't include this in other language files.)

Please remember, when you change the star details, then you also need to refresh the template (styles -> templates -> refresh) in order for your changes to reflect to template data.',
	'PRS_EXTRA_STARS_SMALL'		=> 'Small stars score detail',
	'FULL_STAR'				=> 'Full star',
	'HALF_STAR'				=> 'Half a star',
	'TENTH_STAR'				=> 'Tenth of a star',

	'PRS_IGNORE_VOTES'		=> 'Ignored permillage of votes (on the edges)',
// Meta
	'PRS_VALUES'		=> 'Values',

// Votes
	'PRS_NO_VOTES'		=> 'No votes available',
	'PRS_EXTRA_STARS'		=> 'How many stars will be shown per rating',
	'PRS_EXTRA_STARS_BIG'		=> 'Big stars score detail',

// Stars

// ---------------------


// adm/style/acp_prs.html
	'PRS_SETTINGS'	=> 'Post Rating System',
	'PRS_EXPLAIN'		=> 'I got some explaining to do!',


*/
));
?>
