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
// Errors
	'PRS_NO_ANONYMOUS_VOTE'	=> 'You need to login to be able to vote.',
	'PRS_NO_OWN_VOTE'	=> 'You are not allowed to rate your own post!',
	'PRS_NO_VOTE'		=> 'Vote isn\'t recorded.',
	'PRS_POST_LOCKED'	=> 'You can\'t vote because the post is locked.',
	'PRS_TOPIC_LOCKED'	=> 'You can\'t vote because the topic is locked.',
	'PRS_USER_REQUIREMENTS'	=> 'You must have registered for %1$s days and have written at least %2$s posts to vote.',
	'PRS_VOTEROUND_CLOSED'	=> 'This post is too old to rate.',
	'PRS_VOTES_DISABLED_MODPOINTS'	=> 'You can not vote because you have no reputation to give.  Make a post somewhere first.',
	'PRS_VOTES_DISABLED_PENALTY_OVERALL'	=> 'Voting has been disabled due to penalties.',
	'PRS_VOTES_DISABLED_PENALTY_POSTER'	=> 'Voting has been disabled on this user due to penalties.',
	'PRS_VOTE_ONLY_ONES'		=> 'You have already voted and can\'t change your vote.',
	'PRS_VOTE_ONLY_FIRST_POST'	=> 'You can only vote on the first post.',

// Stars
	'PRS_STAR1'		=> '1 star',
	'PRS_STAR1_EXPLAIN'	=> 'Meh.',
	'PRS_STAR2'		=> '2 stars',
	'PRS_STAR2_EXPLAIN'	=> 'Okay.',
	'PRS_STAR3'		=> '3 stars',
	'PRS_STAR3_EXPLAIN'	=> 'Average.',
	'PRS_STAR4'		=> '4 stars',
	'PRS_STAR4_EXPLAIN'	=> 'Good.',
	'PRS_STAR5'		=> '5 stars',
	'PRS_STAR5_EXPLAIN'	=> 'Epic.',

// Other
	'PRS_SCORE'		=> 'Score: %1$s # %2$u',
	'PRS_VIEW_MESSAGE'	=> '%1$sView the post you voted on%2$s',
	'PRS_DISABLED'		=> 'Posts Ratings module is disabled.',
	'PRS_VOTE_CAST'		=> 'Your vote is recorded successfully.',
	'PRS_VOTE_EDIT'		=> 'Your vote is chanced successfully.',

	'PRS_SHOW_VOTES'	=> '%1$sShow the votes%2$s',
	'KARMA'			=> 'Reputation',
	'PENALTY'		=> 'Penalty',
	'MODPOINTS'		=> 'Modpoints',
	'SCORE'			=> 'Score',
	'SHADOW'		=> 'Shadow',
	'VOTES_CASED'		=> 'Votes casted',
	'PRS_VOTES_LOCK'	=> 'Close voting rounds',
	'PRS_VOTE_LOCK'		=> 'Close voting round',
	'PRS_VOTES_UNLOCK'	=> 'Open voting rounds',
	'PRS_VOTE_UNLOCK'	=> 'Open voting round',
	'PRS_VOTE_SCORE_1'	=> 'Set score 1',
	'PRS_VOTE_SCORE_2'	=> 'Set score 2',
	'PRS_VOTE_SCORE_3'	=> 'Set score 3',
	'PRS_VOTE_SCORE_4'	=> 'Set score 4',
	'PRS_VOTE_SCORE_5'	=> 'Set score 5',
	'PRS_VOTE_RESET'	=> 'Reset score',
	'PRS_FIRST_POSTS_SCORE'	=> 'First post',
	'PRS_AVERAGE_POSTS_SCORE'	=> 'Average score',

// IMAGE SET
	'IMG_CAT_POSTS_RATINGS'		=> 'Post ratings icons',
	'IMG_PRS_STAR_S_0'		=> 'Preview 0%',
	'IMG_PRS_STAR_S_1'		=> 'Preview 10%',
	'IMG_PRS_STAR_S_2'		=> 'Preview 20%',
	'IMG_PRS_STAR_S_3'		=> 'Preview 30%',
	'IMG_PRS_STAR_S_4'		=> 'Preview 40%',
	'IMG_PRS_STAR_S_5'		=> 'Preview 50%',
	'IMG_PRS_STAR_S_6'		=> 'Preview 60%',
	'IMG_PRS_STAR_S_7'		=> 'Preview 70%',
	'IMG_PRS_STAR_S_8'		=> 'Preview 80%',
	'IMG_PRS_STAR_S_9'		=> 'Preview 90%',
	'IMG_PRS_STAR_S_10'		=> 'Preview 100%',
	'IMG_PRS_STAR_V_0'		=> 'Votable 0%',
	'IMG_PRS_STAR_V_1'		=> 'Votable 10%',
	'IMG_PRS_STAR_V_2'		=> 'Votable 20%',
	'IMG_PRS_STAR_V_3'		=> 'Votable 30%',
	'IMG_PRS_STAR_V_4'		=> 'Votable 40%',
	'IMG_PRS_STAR_V_5'		=> 'Votable 50%',
	'IMG_PRS_STAR_V_6'		=> 'Votable 60%',
	'IMG_PRS_STAR_V_7'		=> 'Votable 70%',
	'IMG_PRS_STAR_V_8'		=> 'Votable 80%',
	'IMG_PRS_STAR_V_9'		=> 'Votable 90%',
	'IMG_PRS_STAR_V_10'		=> 'Votable 100%',
	'IMG_PRS_STAR_UV_0'		=> 'Unvotable 0%',
	'IMG_PRS_STAR_UV_1'		=> 'Unvotable 10%',
	'IMG_PRS_STAR_UV_2'		=> 'Unvotable 20%',
	'IMG_PRS_STAR_UV_3'		=> 'Unvotable 30%',
	'IMG_PRS_STAR_UV_4'		=> 'Unvotable 40%',
	'IMG_PRS_STAR_UV_5'		=> 'Unvotable 50%',
	'IMG_PRS_STAR_UV_6'		=> 'Unvotable 60%',
	'IMG_PRS_STAR_UV_7'		=> 'Unvotable 70%',
	'IMG_PRS_STAR_UV_8'		=> 'Unvotable 80%',
	'IMG_PRS_STAR_UV_9'		=> 'Unvotable 90%',
	'IMG_PRS_STAR_UV_10'		=> 'Unvotable 100%',
));

?>
