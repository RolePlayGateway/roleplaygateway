<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'config.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

if ($user->data['user_id'] == 4) {
	error_reporting(0);
	ini_set('display_errors',true);
}

page_header($config['sitename']);

	$suggestions = json_decode(file_get_contents('http://rpg.uservoice.com/api/v1/forums/22249/suggestions.json?&per_page=25&client=joFtOhtVK6ZcJd36b0aQ'),true);

	foreach ($suggestions['suggestions'] as $suggestion) {
		//trigger_error("<br />".json_encode($suggestion));
		$suggestion = (array) $suggestion;
		$template->assign_block_vars('suggestions', array(
			'TITLE' 		=> (string) $suggestion['title'],
			'TEXT' 			=> (string) $suggestion['text'],
			'PERMALINK' 		=> (string) $suggestion['url'],
			'CREATOR_NAME' 		=> (string) $suggestion['creator']['name'],
			'SUPPORTERS_COUNT' 	=> $suggestion['supporters_count'],
			'COMMENTS_COUNT' 	=> $suggestion['comments_count'],
			'STATUS' 		=> $suggestion['status']['name'],
			'STATUS_COLOR' 		=> $suggestion['status']['hex_color']
		));
	}


#and finally, specifying which template file is to be used
#these can be found in /styles/RolePlayGateway/templates/
$template->set_filenames(array(
  'body' => 'feedback.html')
);

page_footer();

?>
