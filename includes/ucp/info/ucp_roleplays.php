<?php
class ucp_roleplays_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_roleplays',
			'title'		=> 'UCP_ROLEPLAYS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'roleplays'				=> array('title' => 'My Roleplays', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'new'					=> array('title' => 'Add New Roleplay', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'edit'					=> array('title' => 'Edit Roleplay', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'view'					=> array('title' => 'View Roleplay', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_player'			=> array('title' => 'Add Player', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'invite_player'			=> array('title' => 'Invite Player', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'approve_player'		=> array('title' => 'Approve Player', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'remove_player'			=> array('title' => 'Remove Player', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_place'				=> array('title' => 'Add Place', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'edit_place'			=> array('title' => 'Edit Place', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_tag'				=> array('title' => 'Add Tag', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'remove_tag'				=> array('title' => 'Remove Tag', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_thread'			=> array('title' => 'Add Thread', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'remove_thread'			=> array('title' => 'Remove Thread', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'write'					=> array('title' => 'Write', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'edit_content'			=> array('title' => 'Edit Content', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'review'				=> array('title' => 'Review', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'tag_character'			=> array('title' => 'Tag Character', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_group'				=> array('title' => 'Add Group', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'join_group'				=> array('title' => 'Join Group', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'edit_group'			=> array('title' => 'Edit Group', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_event'				=> array('title' => 'Add Event', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'edit_event'			=> array('title' => 'Edit Event', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'add_news'				=> array('title' => 'Add News', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'edit_news'			=> array('title' => 'Edit News', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'flag'				=> array('title' => 'Flag Roleplay', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
				'abandon'				=> array('title' => 'Abandon Roleplay', 'auth' => '', 'cat' => array('UCP_ROLEPLAYS')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>
