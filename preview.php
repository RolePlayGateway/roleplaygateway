<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$input = $text = $_REQUEST['text'];

$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
$allow_bbcode = $allow_urls = $allow_smilies = true;
generate_text_for_storage($text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

$output = generate_text_for_display($text, $uid, $bitfield, 7);

echo $output;