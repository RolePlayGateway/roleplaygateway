<?php
/** 
*
* @package Advanced phpBB SEO mod Rewrite
* @version $Id: phpbb_seo_class.php 2007/05/26 13:48:48 dcz Exp $
* @copyright (c) 2006, 2007 dcz - www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl.php RPL Public License 
*
*/
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Advanced phpBB3 SEO mod Rewrite
*/
if (!defined('IN_PHPBB')) {
	exit;
}
function seo_advices($url, &$url_color, &$seo_advice, $forum_id) {
	global $word_limit, $lengh_limit, $phpbb_seo, $user;
	// Check how well is the URL SEO wise
	if (strlen($url) > $lengh_limit) { // Size
		$url_color = 'red';
		@$seo_advice[$forum_id] .= '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_LENGTH'] . '</li>';
	}
	if (preg_match('`^[a-z0-9_-]+' . $phpbb_seo->seo_delim['forum'] . '[0-9]+$`i', $url)) { // With delimiter and id
		$url_color = 'red';
		if ($phpbb_seo->cache_config['settings']['rem_ids']) {
			@$seo_advice[$forum_id] .= '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_DELIM'] . '</li>';
		}
	}
	if ($phpbb_seo->seo_static['forum'] == $url) { // default
		$url_color = 'red';
		@$seo_advice[$forum_id] .= '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_DEFAULT'] . '</li>';
	}
	// Check the number of word
	$url_words = explode('-', $url);
	if (count($url_words) > $word_limit) {
		$url_color = 'red';
		@$seo_advice[$forum_id] .= '<li style="color:red">&bull;&nbsp;' . $user->lang['SEO_ADVICE_WORDS'] . '</li>';
	}
}
function check_cache_folder($cache_dir, $msg = TRUE) {
	global $user;
	$exists = $write = FALSE;
	$cache_msg = '';
	if (file_exists($cache_dir) && is_dir($cache_dir)) {
		$exists = TRUE;
		if (!is_writeable($cache_dir)) {
			@chmod($cache_dir, 0777);
				$fp = @fopen($cache_dir . 'test_lock', 'wb');
				if ($fp !== false) {
					$write = true;
				}
				@fclose($fp);
				@unlink($phpbb_root_path . $dir . 'test_lock');
		} else {
			$write = true;
		}
	}
	if ($msg) {
		$exists = ($exists) ? '<b style="color:green">' . $user->lang['SEO_CACHE_FOUND'] . '</b>' : '<b style="color:red">' . $user->lang['SEO_CACHE_NOT_FOUND'] . '</b>';
		$write = ($write) ? '<br/> <b style="color:green">' . $user->lang['SEO_CACHE_WRITABLE'] . '</b>' : (($exists) ? '<br/> <b style="color:red">' . $user->lang['SEO_CACHE_UNWRITABLE'] . '</b>' : '');
		$cache_msg = sprintf($user->lang['SEO_CACHE_STATUS'], $cache_dir) . '<br/>' . $exists . $write;
		return $cache_msg;
	} else {
		return ($exists && $write);
	}
}
// Functionn select_menu
function select_menu($levels = array(), $level_txt = array(), $submit = '', $select = '') {
	$menu = '<select id="' . $submit . '" name="' . $submit . '">';
	foreach ($levels as $key => $level) {
		$selected = ( $level == $select ) ? ' selected="selected"' : '';
		$menu .= '<option title="' . $level_txt[$key] . '" value="' . $level . '"' . $selected . '>' . $level_txt[$key] . '</option>';
	}
	$menu .= "</select>";
	return $menu;
}
?>
