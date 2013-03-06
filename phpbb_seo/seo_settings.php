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
$template->assign_block_vars('navlinks', array(
	'S_IS_CAT'	=>  true,
	'S_IS_LINK'	=> false,
	'S_IS_POST'	=> false,
	'FORUM_NAME'	=> $user->lang['SEO_SETTINGS_TITLE'],
	'FORUM_ID'	=> 0,
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_seo->seo_path['phpbb_url']}phpbb_seo.$phpEx?settings"))
);
$template->assign_vars(array(
	'MESSAGE_TITLE' => $user->lang['SEO_SETTINGS_TITLE'],
	'MESSAGE_TEXT' => $user->lang['SEO_SETTINGS_MSG'],
	'STATUS_TITLE' => $user->lang['SEO_CACHE_STATUS_TITLE'],
	'SEO_VALUES' => $user->lang['SEO_VALUES'],
	'U_CACHE_ACTION'	=> $phpbb_seo->seo_path['phpbb_url'] . 'phpbb_seo.php?settings',
	'S_CACHE_SETTINGS_ACTION'	=> true,
	)
);
$settings_update = array();
if ($submit) {
	$update_num = 0;
	$updated_id = 0;
	foreach($phpbb_seo->cache_config['dynamic_options'] as $optionname => $optionvalue) {
		if (@is_bool($phpbb_seo->seo_opt[$optionvalue]) ) {
			if (isset($_POST[$optionvalue]) ) {
				$new_value = $_POST[$optionvalue] == 1 ? true : false;	
				$settings_update[$optionvalue] = $new_value;
				$updated_id = $optionvalue;
				$update_num++;
			}
		} elseif (@is_array($optionvalue)) {
			foreach ($optionvalue as $key => $value) {
				if (isset($_POST[$optionname . '_' . $key]) ) {
					if (is_bool($value) ) {
						$new_value = $_POST[$optionname . '_' . $key] == 1 ? true : false;	
						$settings_update[$optionname][$key] = $new_value;
						$updated_id = $optionname . '_' . $key;
						$update_num++;
					} elseif ( is_string($phpbb_seo->cache_config['dynamic_options'][$optionname][$key]) ) {
						$new_value = request_var($optionname . '_' . $key, '');
						if (!empty($new_value)) {	
							$settings_update[$optionname][$key] = $new_value;
							$updated_id = $optionname . '_' . $key;
							$update_num++;
						}
					}

				}
			}
		}
	}
	if (count($settings_update) > 0) {
		foreach($settings_update as $optionname => $optionvalue) {
			if (is_bool($optionvalue)) {
				$phpbb_seo->cache_config['settings'][$optionname] = $optionvalue;
			} elseif (is_array($optionvalue) ) {
				foreach( $optionvalue as $key => $value ) {
					$phpbb_seo->cache_config['settings'][$optionname][$key] = $value;
				}
			}
		}
		// If the forum cache is not activated, do not remove IDs
		if (@!$phpbb_seo->cache_config['settings']['cache_layer']) {
			$phpbb_seo->cache_config['settings']['rem_ids'] = false;
		}
		$redirect_url = append_sid("{$phpbb_root_path}phpbb_seo.$phpEx?settings&amp;submit") . ($update_num == 1 ? '#' . $updated_id : '#seo_top');
		meta_refresh(3, $redirect_url);
		if ($phpbb_seo->write_cache()) {
			trigger_error($user->lang['SEO_CACHE_MSG_OK'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>'));
		} else {
			trigger_error($user->lang['SEO_CACHE_MSG_FAIL'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>'));
		}
	}
}
// Zero duplicate options
$zd_redir_post_lvl = array('off', 'post', 'guest', 'all');
$zd_redir_post_sel = '';
if (isset($phpbb_seo->seo_opt['zero_dupe'])){
	$zd_redir_post_sel = select_menu($zd_redir_post_lvl, $zd_redir_post_lvl, 'zero_dupe_post_redir', (@in_array($phpbb_seo->cache_config['settings']['zero_dupe']['post_redir'], $zd_redir_post_lvl) ? $phpbb_seo->cache_config['settings']['zero_dupe']['post_redir'] : (in_array($phpbb_seo->seo_opt['zero_dupe']['post_redir'], $zd_redir_post_lvl) ? $phpbb_seo->seo_opt['zero_dupe']['post_redir'] : 'post')));
}
foreach($phpbb_seo->cache_config['dynamic_options'] as $optionname => $optionvalue) {
	$cached_color = 'red';
	$cached_img = 'announce_unread';
	$lang_ok = '_NOT';
	if (@is_bool($phpbb_seo->seo_opt[$optionvalue])) {
		// The virtual root option is only available when phpBB is installed in a sud folder
		if ($optionvalue == 'virtual_root' && empty($phpbb_seo->seo_path['phpbb_script'])) {
			continue;
		}
		if (isset($phpbb_seo->cache_config['settings'][$optionvalue])) {
			$cached_color = 'green';
			$cached_img = 'announce_read';
			$lang_ok = '';
		}
		$title = '<b style="color:' . $cached_color . '">' . $user->lang[$optionvalue] . '</b>';
		$title_explain = $user->lang[$optionvalue . '_explain'];
		$checked = is_bool($phpbb_seo->seo_opt[$optionvalue]) ? $phpbb_seo->seo_opt[$optionvalue] : false;
		$status_img = $user->img($cached_img, '', false, '', 'src');
		$status_msg = '<b style="color:' . $cached_color . '">' . $user->lang['SEO_CACHE' . $lang_ok . '_OK'] . '</b>';
		$template->assign_block_vars('settingrow', array(
			'TITLE' => $title,
			'TITLE_EXPLAIN' => $title_explain,
			'S_CHECKED' => $checked,
			'STATUS_MSG'	=> $status_msg,
			'STATUS_IMG'	=> $status_img,
			'OPTION'	=> $optionvalue,
			'ZD_CODE'	=> false,
			'S_FORM' 	=> true,
			)
		);
	} elseif (is_array($optionvalue) ) {
		$template->assign_block_vars('settingrow', array(
			'TITLE' => '<b>' . $user->lang[$optionname][$optionname] . '</b>',
			'TITLE_EXPLAIN' => $user->lang[$optionname][$optionname . '_explain'],
			'S_CHECKED' => '',
			'STATUS_MSG'	=> '',
			'STATUS_IMG'	=> $user->img('sticky_unread', '', false, '', 'src'),
			'OPTION'	=> $optionname,
			'ZD_CODE'	=> false,
			'S_FORM' 	=> false,
			)
		);
		foreach($optionvalue as $key => $value) {
			if (isset($phpbb_seo->cache_config['settings'][$optionname][$key])) { // Item is cached
				$cached_color = 'green';
				$cached_img = 'announce_read';
				$lang_ok = '';
			} else { // Not in cache
				$cached_color = 'red';
				$cached_img = 'announce_unread';
				$lang_ok = '_NOT';
			}
			$zd_sel = ( $key == 'post_redir' ) ? true : false;
			$title = '<b style="color:' . $cached_color . '">' . $user->lang[$optionname][$key] . '</b>';
			$title_explain = $user->lang[$optionname][$key . '_explain'];
			$checked = is_bool($phpbb_seo->seo_opt[$optionname][$key]) ? $phpbb_seo->seo_opt[$optionname][$key] : false;
			$status_img = $user->img($cached_img, '', false, '', 'src');
			$status_msg = '<b style="color:' . $cached_color . '">' . $user->lang['SEO_CACHE' . $lang_ok . '_OK'] . '</b>';
			$template->assign_block_vars('settingrow', array(
				'TITLE' => $title,
				'TITLE_EXPLAIN' => $title_explain,
				'S_CHECKED' => $checked,
				'STATUS_MSG'	=> $status_msg,
				'STATUS_IMG'	=> $status_img,
				'OPTION'	=> $optionname . '_' . $key,
				'ZD_CODE'	=> $zd_sel ? $zd_redir_post_sel : false,
				'S_FORM' 	=> true,
				)
			);
		}
	}
}
?>
