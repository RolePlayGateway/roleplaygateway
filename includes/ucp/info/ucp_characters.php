<?php
class ucp_characters_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_characters',
			'title'		=> 'UCP_CHARACTERS',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'characters'	=> array('title' => 'My Characters', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'new'			=> array('title' => 'Add New Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'edit'			=> array('title' => 'Edit Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'abandon'		=> array('title' => 'Abandon Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'adopt'			=> array('title' => 'Adopt Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'approve'		=> array('title' => 'Approve Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'reject'		=> array('title' => 'Reject Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
				'duplicate'		=> array('title' => 'Duplicate Character', 'auth' => '', 'cat' => array('UCP_CHARACTERS')),
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