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
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

// Start session management
$user->session_begin();         
$auth->acl($user->data);

// Initial var setup   
$score     		= request_var('s', 0);
$forum_id       = request_var('f', 0);
$topic_id       = request_var('t', 0);
$post_id        = request_var('p', 0);
$lastclick      = request_var('lastclick', 0);
$mode      		= request_var('mode', '');
$comment   		= request_var('comment', '');
$ajax			= request_var('ajax', false);
// modes: vote, (faq, overview)

// Setup look and feel
$user->setup('mods/prs');

/*
// User is anaonymous
if ($user->data['user_id'] == ANONYMOUS)
{
   login_box('', $user->lang['LOGIN_VIEWFORUM']);
}
*/


// Validate input
if (!$config['prs_enabled'])
{
   trigger_error('PRS_DISABLED');
}
switch($mode)
{
	case 'vote':
		prs_is_votable($post_id, $score, TRUE);
	break;
}

$data = array(
   'user_id'   => $user->data['user_id'],
   'post_id'   => (int) $post_id,
   'score'      => 10 * (int) $score,
   'forum_id'   => (int) $forum_id,
   'topic_id'   => (int) $topic_id,
);



switch($mode)
{
	case 'vote':
		// This means it's an actual rating
		if ($data['score'] > 0) {
			$redirect_url = prs_submit_vote($mode, $data);

			$message = ($mode == 'vote') ? 'PRS_VOTE_CAST' : 'PRS_VOTE_EDIT';

			$message = $user->lang[$message];
		}

		if ($ajax != true) {

			meta_refresh(3, $redirect_url);

			$message .= '<br /><br />' . sprintf($user->lang['PRS_VIEW_MESSAGE'], '<a href="' . $redirect_url . '">', '</a>');
			$message .= '<br /><br />' . sprintf($user->lang['RETURN_FORUM'], '<a href="' . append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $data['forum_id']) . '">', '</a>');
			trigger_error($message);

		} else {
				
			if (strlen($comment) <= 0) {
				//echo $message;
				echo $message.' <a href="javascript:toggleDiv(\'comment'.$post_id.'\');">Comment?</a> <div id="comment'.$post_id.'" style="display:none;"><form action="http://www.roleplaygateway.com/prs.php?mode=vote&p='.$post_id.'&t='.$topic_id.'&f='.$forum_id.'&ajax=true" method="post"><input name="comment" maxlength="140" size="35" /><input type="submit" /></form></div>';
			} else {
				
				// I hate how this SQL query looks. Ugly.  TODO: change SQL structure to give votes an id, or further evaluate the schema
				$sql = 'UPDATE gateway_prs_votes SET comment = "'.$db->sql_escape($comment).'" WHERE user_id = '.$user->data['user_id'].' AND post_id = '.$db->sql_escape($post_id).' ';
				
				if ($db->sql_query($sql)) {
					echo "You successfully submitted the following comment with your rating:<blockquote>".$comment."</blockquote>";
				} else {
					echo "Something went wrong in the voting process.";
				}
			}

		}
	break;
}

// Output the page
if ($ajax != true) {
	page_header();
	$template->set_filenames(array(
	   'body' => 'prs_body.html'
	));
	page_footer();
}


?>