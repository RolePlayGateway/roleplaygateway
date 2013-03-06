<?php
/**
*
* @package Ultimate SEO URL phpBB SEO
* @version $Id: acp_phpbb_seo.php 237 2010-03-03 17:04:35Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl1.5.txt Reciprocal Public License 1.5
*
*/
/**
* @ignore
*/
if (!defined('IN_PHPBB')) {
	exit;
}
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Ultimate SEO URL phpBB SEO
*/
class acp_phpbb_seo {
	var $u_action;
	var $new_config = array();
	var $dyn_select = array();
	var $forum_ids = array();
	var $array_type_cfg = array();
	var $multiple_options = array();
	var $modrtype_lang = array();
	var $write_type = 'forum';
	var $lengh_limit = 20;
	var $word_limit = 3;
	var $seo_unset_opts = array();

	function main($id, $mode) {
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix, $phpbb_seo;
		// Start the phpbb_seo class
		if ( empty($phpbb_seo) ) {
			include_once($phpbb_root_path . 'phpbb_seo/phpbb_seo_class.' . $phpEx);
			$phpbb_seo = new phpbb_seo();
		}
		$user->add_lang('mods/acp_phpbb_seo');
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;
		$form_key = 'acp_board';
		add_form_key($form_key);
		$display_vars = array();
		// --> Zero Dupe
		if (@isset($phpbb_seo->seo_opt['zero_dupe']) ) {
			$this->multiple_options['zero_dupe']['post_redir_values'] = array('off' => 'off', 'post' => 'post', 'guest' => 'guest', 'all' => 'all'); // do not change
			$this->multiple_options['zero_dupe']['post_redir_lang'] = array('off' => $user->lang['ACP_ZERO_DUPE_OFF'], 'post' => $user->lang['ACP_ZERO_DUPE_MSG'], 'guest' => $user->lang['ACP_ZERO_DUPE_GUEST'], 'all' => $user->lang['ACP_ZERO_DUPE_ALL']); // do not change
		}
		// <-- Mod rewrite selector
		if ($phpbb_seo->modrtype == 1) {
			$this->seo_unset_opts = array('cache_layer', 'rem_ids');
		} elseif (!$phpbb_seo->seo_opt['cache_layer']) {
			$this->seo_unset_opts = array('rem_ids');
		}
		$this->modrtype_lang = $this->set_phpbb_seo_links();
		$this->multiple_options['modrtype_lang'] = $this->modrtype_lang['titles'];
		if (@isset($phpbb_seo->seo_opt['modrtype']) ) {
			$this->multiple_options['modrtype_values'] = array( 1 => 1, 2 => 2, 3 => 3 ); // do not change;
		}
		// <-- Mod rewrite selector
		foreach ( $this->seo_unset_opts as $opt ) {
			if ( $optkey = array_search($opt, $phpbb_seo->cache_config['dynamic_options']) ) {
				unset($phpbb_seo->cache_config['dynamic_options'][$optkey]);
			}
		}
		// We need shorter URLs with Virtual Folder Trick
		if ($phpbb_seo->seo_opt['virtual_folder']) {
			$this->lengh_limit = 20;
			$this->word_limit = 3;
		} else {
			$this->lengh_limit = 30;
			$this->word_limit = 5;
		}
		$related_installed = false;
		switch ($mode) {
			case 'settings':
				$this->write_type = 'forum';
				$display_vars['title'] = 'ACP_PHPBB_SEO_CLASS';
				$user->lang['ACP_PHPBB_SEO_CLASS_EXPLAIN'] = sprintf($user->lang['ACP_PHPBB_SEO_CLASS_EXPLAIN'], $this->modrtype_lang['ulink'], $this->modrtype_lang['uforumlink'], '</p><hr/><p><b>' . $user->lang['ACP_PHPBB_SEO_MODE'] . ' : ' . $this->modrtype_lang['link'] . ' - ' . $phpbb_seo->version . ' ( ' . $this->modrtype_lang['forumlink'] . ' )</b></p><hr/><p>');
				$display_vars['vars'] = array();
				$i = 2;
				$display_vars['vars']['legend1'] = 'ACP_PHPBB_SEO_CLASS';
				foreach($phpbb_seo->cache_config['dynamic_options'] as $optionname => $optionvalue) {
					if ( @is_bool($phpbb_seo->seo_opt[$optionvalue]) ) {
						if ($optionvalue == 'virtual_root' && !$phpbb_seo->seo_path['phpbb_script']) {
							continue;
						}
						$display_vars['vars'][$optionvalue] = array('lang' => $optionvalue, 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'lang_explain' => $optionvalue . '_explain');
						$this->new_config[$optionvalue] = $phpbb_seo->seo_opt[$optionvalue];
					} elseif ( @isset($this->multiple_options[$optionvalue . '_values']) ) {
						$this->dyn_select[$optionvalue] = $this->multiple_options[$optionvalue . '_values'];
						$display_vars['vars'][$optionvalue] = array('lang' => $optionvalue, 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'lang_explain' => $optionvalue . '_explain');
						$this->new_config[$optionvalue] = $phpbb_seo->seo_opt[$optionvalue];
					} elseif ( is_array($optionvalue)) {
						$display_vars['vars']['legend' . $i] = $optionname;
						$i++;
						foreach ($optionvalue as $key => $value) {
							$this->array_type_cfg[$optionname . '_' . $key] = array('main' => $optionname, 'sub' => $key);
							if ( is_bool($value) ) {
								$display_vars['vars'][$optionname . '_' . $key] = array('lang' => $optionname . '_' . $key, 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'lang_explain' => $optionname . '_' . $key . '_explain');
								$this->new_config[$optionname . '_' . $key] = $phpbb_seo->seo_opt[$optionname][$key];
							} elseif ( @isset($this->multiple_options[$optionname][$key . '_values'] )) {
  								$this->dyn_select[$optionname . '_' . $key] = $this->multiple_options[$optionname][$key . '_values'];
								$display_vars['vars'][$optionname . '_' . $key] = array('lang' => $optionname . '_' . $key, 'validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true, 'lang_explain' => $optionname . '_' . $key . '_explain');
								$this->new_config[$optionname . '_' . $key] = $phpbb_seo->seo_opt[$optionname][$key];
							} else {
								$display_vars['vars'][$optionname . '_' . $key] = array('lang' => $optionname . '_' . $key, 'validate' => 'string:0:50', 'type' => 'text:50:50', 'explain' => true, 'lang_explain' => $optionname . '_' . $key . '_explain');
								$this->new_config[$optionname . '_' . $key] = $phpbb_seo->seo_opt[$optionname][$key];
							}

						}
					}
				}
				break;
			case 'forum_url':
				// used for cache
				$this->write_type = 'forum';
				$forbidden = array($phpbb_seo->seo_static['forum'], $phpbb_seo->seo_static['global_announce'], $phpbb_seo->seo_static['user'], $phpbb_seo->seo_static['topic'], $phpbb_seo->seo_static['atopic'], $phpbb_seo->seo_static['utopic'], $phpbb_seo->seo_static['leaders'], $phpbb_seo->seo_static['post'], $phpbb_seo->seo_static['group'], $phpbb_seo->seo_static['npost'], $phpbb_seo->seo_static['index']);
				if ( $phpbb_seo->modrtype == 1 || !$phpbb_seo->seo_opt['cache_layer'] ) {
					trigger_error($user->lang['ACP_NO_FORUM_URL'] . preg_replace('`(&amp;|&|\?)mode=forum_url`i', '', adm_back_link($this->u_action)));
					break;
				}
				$display_vars['title'] = 'ACP_FORUM_URL';
				$user->lang['ACP_FORUM_URL_EXPLAIN'] .= '</p><hr/><p><b>' . $user->lang['ACP_PHPBB_SEO_VERSION'] . ' : ' . $this->modrtype_lang['link'] . ' - ' . $phpbb_seo->version . ' ( ' . $this->modrtype_lang['forumlink'] . ' )</b></p><hr/><p>';
				$display_vars['vars'] = array();
				$display_vars['vars']['legend1'] = 'ACP_FORUM_URL';
				$sql = "SELECT forum_id, forum_name
					FROM " . FORUMS_TABLE . "
					ORDER BY left_id ASC";
				$result = $db->sql_query($sql);
				$forum_url_title = $error_cust = '';
				while( $row = $db->sql_fetchrow($result) ) {
					$this->forum_ids[$row['forum_id']] = $row['forum_name'];
				}
				$db->sql_freeresult($result);
				// take care of deleted forums
				foreach ($phpbb_seo->cache_config['forum'] as $fid => $null) {
					if (!isset($this->forum_ids[$fid])) {
						unset($phpbb_seo->cache_config['forum'][$fid]);
					}
				}
				foreach ($this->forum_ids as $forum_id => $forum_name) {
					$error_cust = '';
					// Is the URL cached already ?
					if ( empty($phpbb_seo->cache_config['forum'][$forum_id]) ) {
						// Suggest the one from the title
						$forum_url_title = $phpbb_seo->format_url($forum_name, $phpbb_seo->seo_static['forum']);
						if (!in_array($forum_url_title, $forbidden)) {
							if (array_search($forum_url_title, $phpbb_seo->cache_config['forum'])) {
								$this->new_config['forum_url' . $forum_id] = $forum_url_title .  $phpbb_seo->seo_delim['forum'] . $forum_id;
								$error_cust = '<li>&nbsp;' . sprintf($user->lang['SEO_ADVICE_DUPE'], $forum_url_title) . '</li>';
							} else {
								$this->new_config['forum_url' . $forum_id] = $forum_url_title . (@$phpbb_seo->cache_config['settings']['rem_ids'] ? '': $phpbb_seo->seo_delim['forum'] . $forum_id);
			}
						} else {
							$this->new_config['forum_url' . $forum_id] = $forum_url_title . $phpbb_seo->seo_delim['forum'] . $forum_id;
							$error_cust = '<li>&nbsp;' . sprintf($user->lang['SEO_ADVICE_RESERVED'], $forum_url_title) . '</li>';
						}
						$title = '<b style="color:red">' . $forum_name . ' - ID ' . $forum_id . '</b>';
						$status_msg = '<b style="color:red">' . $user->lang['SEO_CACHE_URL_NOT_OK'] . '</b>';
						$status_msg .= '<br/><span style="color:red">' . $user->lang['SEO_CACHE_URL'] . '&nbsp;:</span>&nbsp;' . $this->new_config['forum_url' . $forum_id] . $phpbb_seo->seo_ext['forum'];
						$display_vars['vars']['forum_url' . $forum_id] = array('lang' => $title, 'validate' => 'string', 'type' => 'custom', 'method' => 'forum_url_input', 'explain' => true, 'lang_explain_custom' => $status_msg, 'append' => $this->seo_advices($this->new_config['forum_url' . $forum_id], $forum_id,  false, $error_cust));
					} else { // Cached
						$this->new_config['forum_url' . $forum_id] = $phpbb_seo->cache_config['forum'][$forum_id];
						$title = '<b style="color:green">' . $forum_name . ' - ID ' . $forum_id . '</b>';
						$status_msg = '<span style="color:green">' . $user->lang['SEO_CACHE_URL_OK'] . '&nbsp;:</span>&nbsp;<b style="color:green">' . $this->new_config['forum_url' . $forum_id] . '</b>';
						$status_msg .= '<br/><span style="color:green">' . $user->lang['SEO_CACHE_URL'] . '&nbsp;:</span>&nbsp;' . $this->new_config['forum_url' . $forum_id] . $phpbb_seo->seo_ext['forum'];
						$display_vars['vars']['forum_url' . $forum_id] = array('lang' => $title, 'validate' => 'string:0:100', 'type' => 'custom', 'method' => 'forum_url_input', 'explain' => true, 'lang_explain_custom' => $status_msg,'append' => $this->seo_advices($this->new_config['forum_url' . $forum_id], $forum_id, true));
					}
				}
				break;
			case 'htaccess':
				$this->write_type = 'htaccess';
				$display_vars['title'] = 'ACP_HTACCESS';
				$user->lang['ACP_HTACCESS_EXPLAIN'] .= '</p><hr/><p><b>' . $user->lang['ACP_PHPBB_SEO_VERSION'] . ' : ' . $this->modrtype_lang['link'] . ' - ' . $phpbb_seo->version . ' ( ' . $this->modrtype_lang['forumlink'] . ' )</b></p><p>';
				$display_vars['vars'] = array();
				$display_vars['vars']['legend1'] = 'ACP_HTACCESS';
				$display_vars['vars']['save'] = array('lang' => 'SEO_HTACCESS_SAVE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,);
				$display_vars['vars']['more_options'] = array('lang' => 'SEO_MORE_OPTION', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,);
				$this->new_config['save'] = false;
				$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
				$this->new_config['more_options'] = isset($cfg_array['more_options']) ? $cfg_array['more_options'] : false;
				$this->new_config['slash'] = isset($cfg_array['slash']) ? $cfg_array['slash'] : false;
				$this->new_config['wslash'] = isset($cfg_array['wslash']) ? $cfg_array['wslash'] : false;
				$this->new_config['rbase'] = isset($cfg_array['rbase']) ? $cfg_array['rbase'] : false;

				if ($this->new_config['more_options']) {
					$display_vars['vars']['slash'] = array('lang' => 'SEO_HTACCESS_SLASH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,);
					$display_vars['vars']['wslash'] = array('lang' => 'SEO_HTACCESS_WSLASH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,);
					if ($phpbb_seo->seo_path['phpbb_script'] && !$phpbb_seo->seo_opt['virtual_root']) {
						$display_vars['vars']['rbase'] = array('lang' => 'SEO_HTACCESS_RBASE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true,);
					}
				}
				// Dirty yet simple templating
				$user->lang['ACP_HTACCESS_EXPLAIN'] .= $this->seo_htaccess();

				break;
			case 'extended':
				$display_vars = array(
					'title'	=> 'ACP_SEO_EXTENDED',
					'vars'	=> array(
						'legend1' => 'SEO_EXTERNAL_LINKS',
						'seo_ext_links' => array('lang' => 'SEO_EXTERNAL_LINKS', 'validate' => 'bool', 'type' => 'radio:enabled_disabled', 'explain' => true, 'default' => 1),
						'seo_ext_subdomain' => array('lang' => 'SEO_EXTERNAL_SUBDOMAIN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'default' => 0),
						'seo_ext_classes' =>  array('lang' => 'SEO_EXTERNAL_CLASSES', 'validate' => 'string', 'type' => 'text:25:150', 'explain' => true, 'default' => ''),
					),
				);
				// Related topics
				if (file_exists($phpbb_root_path . "phpbb_seo/phpbb_seo_related.$phpEx")) {
					$related_installed = true;
					$user->add_lang('mods/phpbb_seo_related_install');
					$display_vars['vars'] += array(
						'legend2' => 'RELATED_TOPICS',
						'seo_related' => array('lang' => 'SEO_RELATED', 'validate' => 'bool', 'type' => 'radio:enabled_disabled', 'explain' => true, 'append' => !empty($config['seo_related']) ? '<br/>' . (!empty($config['seo_related_fulltext']) ? $user->lang['FULLTEXT_INSTALLED'] : $user->lang['FULLTEXT_NOT_INSTALLED']) : '', 'default' => 0),
						'seo_related_check_ignore' => array('lang' => 'SEO_RELATED_CHECK_IGNORE', 'validate' => 'bool', 'type' => 'radio:enabled_disabled', 'explain' => true, 'default' => 0),
						'seo_related_limit' => array('lang' => 'SEO_RELATED_LIMIT', 'validate' => 'int:2:25', 'type' => 'text:3:4', 'explain' => true, 'default' => 5),
						'seo_related_allforums' => array('lang' => 'SEO_RELATED_ALLFORUMS', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'default' => 0),
					);
				}
				// dynamic meta tag mod
				if (class_exists('seo_meta')) {
					$display_vars['vars'] += array(
						'legend3' => 'SEO_META',
						'seo_meta_title' =>  array('lang' => 'SEO_META_TITLE', 'validate' => 'string:0:225', 'type' => 'text:25:150', 'explain' => true, 'default' => $config['sitename']),
						'seo_meta_desc' =>  array('lang' => 'SEO_META_DESC', 'validate' => 'string:0:225', 'type' => 'text:40:255', 'explain' => true, 'default' => $config['site_desc']),
						'seo_meta_desc_limit' => array('lang' => 'SEO_META_DESC_LIMIT', 'validate' => 'int:5:40', 'type' => 'text:3:4', 'explain' => true, 'default' => 25),
						'seo_meta_bbcode_filter' =>  array('lang' => 'SEO_META_BBCODE_FILTER', 'validate' => 'string:0:225', 'type' => 'text:25:150', 'explain' => true, 'default' => 'img|url|flash|code'),
						'seo_meta_keywords' =>  array('lang' => 'SEO_META_KEYWORDS', 'validate' => 'string:0:225', 'type' => 'text:40:150', 'explain' => true, 'default' => $config['site_desc']),
						'seo_meta_keywords_limit' => array('lang' => 'SEO_META_KEYWORDS_LIMIT', 'validate' => 'int:5:40', 'type' => 'text:3:4', 'explain' => true, 'default' => 15),
						'seo_meta_min_len' => array('lang' => 'SEO_META_MIN_LEN', 'validate' => 'int:0:10', 'type' => 'text:3:4', 'explain' => true, 'default' => 2),
						'seo_meta_check_ignore' => array('lang' => 'SEO_META_CHECK_IGNORE', 'validate' => 'bool', 'type' => 'radio:enabled_disabled', 'explain' => true, 'default' => 0),
						'seo_meta_lang' =>  array('lang' => 'SEO_META_LANG', 'validate' => 'lang', 'type' => 'select', 'method' => 'language_select', 'params' => array('{CONFIG_VALUE}'), 'explain' => true,  'default' => $config['default_lang']),
						'seo_meta_copy' =>  array('lang' => 'SEO_META_COPY', 'validate' => 'string:0:225', 'type' => 'text:25:150', 'explain' => true, 'default' => $config['sitename']),
						'seo_meta_file_filter' =>  array('lang' => 'SEO_META_FILE_FILTER', 'validate' => 'string:0:225', 'type' => 'text:25:150', 'explain' => true, 'default' => 'ucp'),
						'seo_meta_get_filter' =>  array('lang' => 'SEO_META_GET_FILTER', 'validate' => 'string:0:225', 'type' => 'text:25:150', 'explain' => true, 'default' => 'style,hilit,sid'),
						'seo_meta_robots' =>  array('lang' => 'SEO_META_ROBOTS', 'validate' => 'string:0:225', 'type' => 'text:25:150', 'explain' => true, 'default' => 'index,follow'),
						'seo_meta_noarchive' =>  array('lang' => 'SEO_META_NOARCHIVE', 'validate' => 'string:0:225', 'multiple_validate' => 'int', 'type' => 'custom', 'method' => 'select_multiple', 'params' => array('{CONFIG_VALUE}', '{KEY}', $this->forum_select()), 'explain' => true, 'default' => ''),
					);
				}
				// Optimal title
				if (isset($user->lang['Page'])) {
					$display_vars['vars'] += array(
						'legend4' => 'SEO_PAGE_TITLES',
						'seo_append_sitename' =>  array('lang' => 'SEO_APPEND_SITENAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true, 'default' => 0),
					);
				}
				// install if necessary
				foreach ($display_vars['vars'] as $config_name => $config_setup) {
					if (strpos($config_name, 'legend') !== false) {
						continue;
					}
					if (!isset($config[$config_name])) {
						set_config($config_name, $config_setup['default']);
						unset($display_vars['vars'][$config_name]['default']);
					}
				}
				$this->new_config = $config;
				break;
			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
		$error = array();
		$seo_msg = array();
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		if ($submit && !check_form_key($form_key)) {
			$error[] = $user->lang['FORM_INVALID'];
		}
		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);
		// Do not write values if there is an error
		if (!empty($error)) {
			$submit = false;
		}
		$additional_notes = '';
		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $cfg_setup) {
			if ((!isset($cfg_array[$config_name]) && @$cfg_setup['method'] != 'select_multiple') || strpos($config_name, 'legend') !== false) {
				continue;
			}
			// Handle multiple select options
			if (!empty($cfg_setup['method']) && $cfg_setup['method'] == 'select_multiple') {
				if (isset($_POST['multiple_' . $config_name])) {
					$m_values = utf8_normalize_nfc(request_var('multiple_' . $config_name, array('' => '')));
					$validate_int = !empty($cfg_setup['multiple_validate']) && $cfg_setup['multiple_validate'] == 'int' ? true : false;
					foreach($m_values as $k => $v) {
						if ($validate_int) {
							$v = max(0, (int) $v);
						}
						if (empty($v)) {
							unset($m_values[$k]);
						} else {
							$m_values[$k] = $v;
						}
					}
					sort($m_values);
					$this->new_config[$config_name] = $m_values;
					$config_value = implode(',', $m_values);
					if ( strlen($config_value) > 255 ) {
						$error[] = sprintf($user->lang['SETTING_TOO_LONG'], $user->lang[$cfg_setup['lang']], 255);
					}
					$submit = empty($error);
				} else {
					if ($submit) {
						$this->new_config[$config_name] = array();
						$config_value = '';
					} else {
						$config_value = $this->new_config[$config_name];
						$this->new_config[$config_name] = !empty($config_value) ? explode(',', $config_value) : array();
					}
				}
			} else {
				$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];
			}
			if ($submit) {
				// In case we deal with forum URLs
				if ($mode == 'forum_url' && preg_match('`^forum_url([0-9]+)$`', $config_name, $matches)) {
					// Check if this is an actual forum_id
					if ( isset($this->forum_ids[$matches[1]]) ) {
						$forum_id = intval($matches[1]);
						$config_value = $phpbb_seo->format_url($config_value, $phpbb_seo->seo_static['forum']);
						// Remove delim if required
						while (preg_match('`^[a-z0-9_-]+' . $phpbb_seo->seo_delim['forum'] . '[0-9]+$`i', $config_value)) {
							$config_value = preg_replace('`^([a-z0-9_-]+)' . $phpbb_seo->seo_delim['forum'] . '[0-9]+$`i', '\\1', $config_value);
							if (@$phpbb_seo->cache_config['settings']['rem_ids']) {
								$seo_msg['SEO_ADVICE_DELIM_REM'] = '<li>&nbsp;' . $user->lang['SEO_ADVICE_DELIM_REM'] . '</li>';
							}
						}
						// Forums cannot end with the pagination param
						while (preg_match('`^[a-z0-9_-]+' . $phpbb_seo->seo_delim['start'] . '[0-9]+$`i', $config_value)) {
							$config_value = preg_replace('`^([a-z0-9_-]+)' . $phpbb_seo->seo_delim['start'] . '[0-9]+$`i', "\\1", $config_value);
							$seo_msg['SEO_ADVICE_START'] = '<li>&nbsp;' . $user->lang['SEO_ADVICE_START'] . '</li>';
						}
						// Only update if the value is not a static one for forums
						if (!in_array($config_value, $forbidden)) {
							// and updated (sic)
							if ($config_value != @$phpbb_seo->cache_config['forum'][$forum_id]) {
								// and if not already set
								if (!array_search($config_value, $phpbb_seo->cache_config['forum'])) {
								$phpbb_seo->cache_config['forum'][$forum_id] = $config_value . (@$phpbb_seo->cache_config['settings']['rem_ids'] ? '': $phpbb_seo->seo_delim['forum'] . $forum_id);
								} else {
									$seo_msg['SEO_ADVICE_DUPE_' . $forum_id] = '<li>&nbsp;' . sprintf($user->lang['SEO_ADVICE_DUPE'], $config_value) . '</li>';
								}
							}
						} else {
							$seo_msg['SEO_ADVICE_RESERVED_' . $forum_id] = '<li>&nbsp;' . sprintf($user->lang['SEO_ADVICE_RESERVED'], $config_value) . '</li>';
						}
					}
				} elseif ($mode == 'settings') {
					if (isset($this->array_type_cfg[$config_name]) && isset($phpbb_seo->seo_opt[$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']])) {
						if ( is_bool($phpbb_seo->seo_opt[$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']]) ) {
							$phpbb_seo->cache_config['settings'][$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']] = ($config_value == 1) ? true : false;
						} elseif (is_numeric($phpbb_seo->seo_opt[$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']])) {
							$phpbb_seo->cache_config['settings'][$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']] = intval($config_value);
						} elseif (is_string($phpbb_seo->seo_opt[$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']])) {
							$phpbb_seo->cache_config['settings'][$this->array_type_cfg[$config_name]['main']][$this->array_type_cfg[$config_name]['sub']] = $config_value;
						}
					} elseif ( isset($phpbb_seo->seo_opt[$config_name]) ) {
						if ( is_bool($phpbb_seo->seo_opt[$config_name]) ) {
							$phpbb_seo->cache_config['settings'][$config_name] = ($config_value == 1) ? true : false;
						} elseif ( is_numeric($phpbb_seo->seo_opt[$config_name]) ) {
							$phpbb_seo->cache_config['settings'][$config_name] = intval($config_value);
						} elseif ( is_string($phpbb_seo->seo_opt[$config_name]) ) {
							$phpbb_seo->cache_config['settings'][$config_name] = $config_value;
						}
					}
					// Let's make sure that the proper field was added to the topic table
					if ($config_name === 'sql_rewrite' && $config_value == 1 && !$phpbb_seo->seo_opt['sql_rewrite']) {
						if (!class_exists('phpbb_db_tools')) {
							include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
						}
						$db_tools = new phpbb_db_tools($db);
						$db_tools->db->sql_return_on_error(true);
						if (!$db_tools->sql_column_exists(TOPICS_TABLE, 'topic_url')) {
							$db_tools->sql_column_add(TOPICS_TABLE, 'topic_url', array('VCHAR', ''));
						}
						$additional_notes = sprintf($user->lang['SYNC_TOPIC_URL_NOTE'], '<a href="' . $phpbb_seo->seo_path['phpbb_url'] . 'phpbb_seo/sync_url.' . $phpEx . '" onclick="window.open(this.href); return false;">', '</a>');
						if ($db_tools->db->sql_error_triggered) {
							$error[] = '<b>' . $user->lang['sql_rewrite'] . '</b> : ' . $user->lang['SEO_SQL_ERROR'] . ' [ ' . $db_tools->db->sql_layer . ' ] : ' . $db_tools->db->sql_error_returned['message'] . ' [' . $db_tools->db->sql_error_returned['code'] . ']' . '<br/>' . $user->lang['SEO_SQL_TRY_MANUALLY'] . '<br/>' . $db_tools->db->sql_error_sql;
							$submit = false;
						}
						$db_tools->db->sql_return_on_error(false);
					}
					// Let's make sure the proper index is added for the no dupe (in case it is installed and activated)
					if ($config_name === 'no_dupe_on' && $config_value == 1 && !$phpbb_seo->seo_opt['no_dupe']['on']) {
						if (!class_exists('phpbb_db_tools')) {
							include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
						}
						// in case we already started the phpbb_db_tools class above
						if (empty($db_tools)) {
							$db_tools = new phpbb_db_tools($db);
						}
						$db_tools->db->sql_return_on_error(true);
						$indexes = $db_tools->sql_list_index(TOPICS_TABLE);
						$drop_index_name = 'topic_last_post_id';
						$add_index_name = 'topic_lpid';
						if (in_array($drop_index_name, $indexes)) {
							$db_tools->sql_index_drop(TOPICS_TABLE, $drop_index_name);
						}
						if (!in_array($add_index_name, $indexes)) {
							// Try to override some limits - maybe it helps some...
							@set_time_limit(0);
							@ini_set('memory_limit', '128M');
							$db_tools->sql_create_index(TOPICS_TABLE, $add_index_name, array('topic_last_post_id'));
						}
						if ($db_tools->db->sql_error_triggered) {
							$error[] = '<b>' . $user->lang['no_dupe'] . '</b> : ' . $user->lang['SEO_SQL_ERROR'] . ' [ ' . $db_tools->db->sql_layer . ' ] : ' . $db_tools->db->sql_error_returned['message'] . ' [' . $db_tools->db->sql_error_returned['code'] . ']' . '<br/>' . $user->lang['SEO_SQL_TRY_MANUALLY'] . '<br/>' . $db_tools->db->sql_error_sql;
							$submit = false;
						}
						$db_tools->db->sql_return_on_error(false);
					}
				} elseif ($mode == 'extended') {
					if ($related_installed && $config_name === 'seo_related') {
						$fulltext = 0;
						$nothing_to_do = false;
						if ($db->sql_layer == 'mysql4' || $db->sql_layer == 'mysqli') {
							$add = $remove = $alter = false;
							if ($config_value && !$config['seo_related']) {
								$alter = $add = true;
							}
							if (!$config_value && $config['seo_related']) {
								$alter = $remove = true;
							}
							// let's go
							if ($alter) {
								// Try to override some limits - maybe it helps some...
								@set_time_limit(0);
								@ini_set('memory_limit', '128M');
								// use db_tools to check the index
								if (!class_exists('phpbb_db_tools')) {
									include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
								}
								if (empty($db_tools)) {
									$db_tools = new phpbb_db_tools($db);
								}
								$indexes = $db_tools->sql_list_index(TOPICS_TABLE);
								if (in_array('topic_tft', $indexes)) {
									$nothing_to_do = $add ? true : false;
									$fulltext = 1;
								} else {
									$nothing_to_do = $remove ? true : false;
									$fulltext = 0;
								}
								// do not use db_tools since it does not support to add FullText indexes
								if (!$nothing_to_do) {
									// Here we use quite a basic approach to make sure that the index is not refused for bad reasons
									if ($add) {
										$sql = 'ALTER TABLE ' . TOPICS_TABLE . '
											ADD FULLTEXT topic_tft (topic_title)';
									} else {
										$sql = 'ALTER TABLE ' . TOPICS_TABLE . '
											DROP INDEX topic_tft';
									}
									$db->sql_return_on_error(true);
									$db->sql_query($sql);
									if ($db->sql_error_triggered) {
										$error[] = '<b>' . $user->lang['RELATED_TOPICS'] . '</b> : ' . $user->lang['SEO_SQL_ERROR'] . ' [ ' . $db->sql_layer . ' ] : ' . $db->sql_error_returned['message'] . ' [' . $db->sql_error_returned['code'] . ']' . '<br/>' . $user->lang['SEO_SQL_TRY_MANUALLY'] . '<br/>' . $db->sql_error_sql;
										$submit = false;
										$config_value = 0;
									}
									// make *sure* about the index !
									$indexes = $db_tools->sql_list_index(TOPICS_TABLE);
									$fulltext = in_array('topic_tft', $indexes) ? 1 : 0;
									$db->sql_return_on_error(false);
								}
							}
						}
						if ($alter) {
							set_config('seo_related_fulltext', $fulltext);
						}
					}
					set_config($config_name, $config_value);
				}
			}
		}
		if (sizeof($error)) {
			$submit = false;
		}
		if ($submit) {
			if ($mode == 'htaccess') {
				if ($this->new_config['save']) {
					$this->write_cache($this->write_type);
					add_log('admin', 'SEO_LOG_CONFIG_' . strtoupper($mode));
				}
			} elseif ($mode == 'extended') {
				add_log('admin', 'SEO_LOG_CONFIG_' . strtoupper($mode));
				trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
			} else {
				if ( $this->write_cache($this->write_type) ) {
					ksort($phpbb_seo->cache_config[$this->write_type]);
					add_log('admin', 'SEO_LOG_CONFIG_' . strtoupper($mode));
					$msg = !empty($seo_msg) ? '<br /><h1 style="color:red;text-align:left;">' . $user->lang['SEO_VALIDATE_INFO'] . '</h1><ul style="text-align:left;">' . implode(' ',$seo_msg) . '</ul><br />' : '';
					global $msg_long_text;
					$msg_long_text = $user->lang['SEO_CACHE_MSG_OK'] . $msg . adm_back_link($this->u_action);
					if ($additional_notes) {
						$msg_long_text .= "<br/><br/>$additional_notes";
					}
					trigger_error(false);
				} else {
					trigger_error($user->lang['SEO_CACHE_MSG_FAIL'] . adm_back_link($this->u_action));
				}
			}
		}
		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];
		$phpbb_seo->seo_end();
		$l_title_explain = $user->lang[$display_vars['title'] . '_EXPLAIN'];
		if ($mode != 'extended') {
			$l_title_explain .= $mode == 'htaccess' ? '' : $this->check_cache_folder($phpbb_root_path . $phpbb_seo->seo_opt['cache_folder']);
		}
		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $l_title_explain,

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);
		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars) {
			if (!is_array($vars) && strpos($config_key, 'legend') === false) {
				continue;
			}
			if (strpos($config_key, 'legend') !== false) {
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);
				continue;
			}
			$type = explode(':', $vars['type']);
			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain'])) {
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			} elseif ($vars['explain'] && isset($vars['lang_explain_custom'])) {
				$l_explain = $vars['lang_explain_custom'];
			} elseif ($vars['explain']) {
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}
			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
				)
			);
			unset($display_vars['vars'][$config_key]);
		}
	}
	/**
	*  forum_url_check validation
	*/
	function forum_url_input($value, $key) {
		global $user, $phpbb_seo;

		return '<input id="' . $key . '" type="text" size="40" maxlength="255" name="config[' . $key . ']" value="' . $value . '" /> ';
	}
	/**
	*  select_string custom select string
	*/
	function select_string($value, $key) {
		global $phpbb_seo;
		$select_ary = $this->dyn_select[$key];
		$html = '';
		foreach ($select_ary as $sel_value) {
			if ( @isset($this->array_type_cfg[$key]) ) {
				$selected = ($sel_value == @$phpbb_seo->seo_opt[$this->array_type_cfg[$key]['main']][$this->array_type_cfg[$key]['sub']]) ? ' selected="selected"' : '';
				$sel_title = @isset($this->multiple_options[$this->array_type_cfg[$key]['main']][$this->array_type_cfg[$key]['sub'] . '_lang'][$sel_value]) ? $this->multiple_options[$this->array_type_cfg[$key]['main']][$this->array_type_cfg[$key]['sub'] . '_lang'][$sel_value] : $sel_value;
			} else {
				$selected = ($sel_value == @$phpbb_seo->cache_config['settings'][$key]) ? ' selected="selected"' : '';
				$sel_title = @isset($this->multiple_options[$key . '_lang'][$sel_value]) ? $this->multiple_options[$key . '_lang'][$sel_value] : $sel_value;
			}
			$html .= '<option value="' . $sel_value . '"' . $selected . '>' . $sel_title . '</option>';
		}
		return $html;
	}
	/**
	*  seo_advices Always needed :-)
	*/
	function seo_advices($url, $forum_id, $cached = FALSE, $error_cust = '') {
		global $phpbb_seo, $user;
		$seo_advice = '';
		// Check how well is the URL SEO wise
		if ( !empty($error_cust) ) {
			$seo_advice .= $error_cust;
		}
		if (strlen($url) > $this->lengh_limit) { // Size
			$seo_advice .= '<li>&nbsp;' . $user->lang['SEO_ADVICE_LENGTH'] . '</li>';
		}
		if (preg_match('`^[a-z0-9_-]+' . $phpbb_seo->seo_delim['forum'] . '[0-9]+$`i', $url)) { // With delimiter and id
			if (@$phpbb_seo->cache_config['settings']['rem_ids']) {
				$seo_advice .= '<li style="color:red">&nbsp;' . $user->lang['SEO_ADVICE_DELIM'] . '</li>';
			}
		}
		if ($phpbb_seo->seo_static['forum'] == $url) { // default
			$seo_advice .= '<li>&nbsp;' . $user->lang['SEO_ADVICE_DEFAULT'] . '</li>';
		}
		// Check the number of word
		$url_words = explode('-', $url);
		if (count($url_words) > $this->word_limit) {
			$seo_advice .= '<li>&nbsp;' . $user->lang['SEO_ADVICE_WORDS'] . '</li>';
		}
		return $seo_advice ? '<ul  style="color:red">' . $seo_advice . '</ul>' : '';
	}
	/**
	*  seo_htaccess The evil one ;-)
	*/
	function seo_htaccess($html = true) {
		global $phpbb_seo, $user, $error, $phpEx, $config, $phpbb_root_path, $config, $phpbb_admin_path;
		static $htaccess_code = '';
		$htaccess_tpl = '';
		// GYM Sitemaps & RSS
		$gym_installed = (boolean) (!empty($config['gym_installed']) && file_exists($phpbb_root_path . 'gym_sitemaps/includes/gym_sitemaps.' . $phpEx));
		$rss_path = $google_path = $html_path = '';
		$rss_commpat_note = $google_commpat_note = $html_commpat_note = $compat_path_note = '';
		$rss_commpat_pre = $html_commpat_pre = $google_commpat_pre = '<b style="color:blue"># RewriteRule';
		$rss_commpat_post = $html_commpat_post = $google_commpat_post = '</b>';
		$google_comp_path = $rss_comp_path = $html_comp_path = false;
		if ($gym_installed) {
			$compat_path_note = '<b style="color:red"># NOTE : THE FOLLOWING REWRITERULE IS LEFT COMMENTED BECAUSE IT CANNOT' . "\n";
			$compat_path_note .= '# BE IMPLEMENTED IN THIS .HTACCESS, BUT RATHER IN AN ABOVE ONE' . "\n";
			$compat_path_note .= '# WITH PROPER SLASHES AND PATHS</b>' . "\n";
			$rss_commpat_note = $google_commpat_note = $html_commpat_note = $compat_path_note;
			require_once($phpbb_root_path . 'gym_sitemaps/includes/gym_common.' . $phpEx);
			obtain_gym_config('main', $gym_config);
			$google_url = trim($gym_config['google_url'], '/') . '/';
			if (utf8_strpos($google_url, $phpbb_seo->seo_path['phpbb_url']) !== false) {
				$google_path = trim(str_replace($phpbb_seo->seo_path['root_url'], '', $google_url), '/');
				$google_comp_path = true;
				$google_commpat_pre = '<b style="color:green">RewriteRule</b>';
				$google_commpat_post = $google_commpat_note = '';
			}
			$rss_url = trim($gym_config['rss_url'], '/') . '/';
			if (utf8_strpos($rss_url, $phpbb_seo->seo_path['phpbb_url']) !== false) {
				$rss_path = trim(str_replace($phpbb_seo->seo_path['root_url'], '', $rss_url), '/');
				$rss_comp_path = true;
				$rss_commpat_pre = '<b style="color:green">RewriteRule</b>';
				$rss_commpat_post = $rss_commpat_note = '';
			}
			$html_url = trim($gym_config['html_url'], '/') . '/';
			if (utf8_strpos($html_url, $phpbb_seo->seo_path['phpbb_url']) !== false) {
				$html_path = trim(str_replace($phpbb_seo->seo_path['root_url'], '', $html_url), '/');
				$html_comp_path = true;
				$html_commpat_pre = '<b style="color:green">RewriteRule</b>';
				$html_commpat_post = $html_commpat_note = '';
			}
		}
		if ( empty($htaccess_code) ) {
			// get mods .htaccess tpls
			$mods_ht = $this->get_mods_ht();
			$default_slash = '/';
			$wierd_slash = '';
			$phpbb_path = trim($phpbb_seo->seo_path['phpbb_script'], '/');
			$show_rewritebase_opt = false;
			$rewritebase = '';
			$wierd_slash = $this->new_config['wslash'] ? '<b style="color:red">/</b>' : '';
			$default_slash = $this->new_config['slash'] ? '' : '/';
			if (!empty($phpbb_path )) {
				$phpbb_path = $phpbb_path . '/';
				if ($this->new_config['rbase']) {
					$rewritebase = $phpbb_path;
					$default_slash = $this->new_config['slash'] ? '/' : '';
				}
				$rewritebase = $this->new_config['rbase'] ? $phpbb_path : '';
				$show_rewritebase_opt = $phpbb_seo->seo_opt['virtual_root'] ? false : true;
			}
			if (!empty($rewritebase)) {
				$rss_path = trim(str_replace(trim($phpbb_path, '/'), '', $rss_path), '/');
				$google_path = trim(str_replace(trim($phpbb_path, '/'), '', $google_path), '/');
				$html_path = trim(str_replace(trim($phpbb_path, '/'), '', $html_path), '/');
			}
			$colors = array( 'color' => '<b style="color:%1$s">%2$s</b>',
				'static' => '#A020F0',
				'ext' => '#6A5ACD',
				'delim' => '#FF00FF',
			);
			$tpl = array('paginpage' => '/?(<b style="color:#A020F0">%1$s</b>([0-9]+)<b style="color:#6A5ACD">%2$s</b>)?',
				'pagin' => '(<b style="color:#FF00FF">%1$s</b>([0-9]+))?<b style="color:#6A5ACD">%2$s</b>',
				'static' => sprintf($colors['color'] , $colors['static'], '%1$s'),
				'ext' => sprintf($colors['color'] , $colors['ext'], '%1$s'),
				'delim' => sprintf($colors['color'] , $colors['delim'], '%1$s'),
			);
			$modrtype = array( 1 => 'SIMPLE', 2 => 'MIXED', 1 => 'SIMPLE', 3 => 'ADVANCED', 'type' => intval($phpbb_seo->modrtype));
			$htaccess_tpl = '<b style="color:blue"># Lines That should already be in your .htacess</b>' . "\n";
			$htaccess_tpl .= '<b style="color:brown">&lt;Files</b> <b style="color:#FF00FF">"config.{PHP_EX}"</b><b style="color:brown">&gt;</b>' . "\n";
			$htaccess_tpl .= "\t" . 'Order Allow,Deny' . "\n";
			$htaccess_tpl .= "\t" . 'Deny from All' . "\n";
			$htaccess_tpl .= '<b style="color:brown">&lt;/Files&gt;</b>' . "\n";
			$htaccess_tpl .= '<b style="color:brown">&lt;Files</b> <b style="color:#FF00FF">"common.{PHP_EX}"</b><b style="color:brown">&gt;</b>' . "\n";
			$htaccess_tpl .= "\t" . 'Order Allow,Deny' . "\n";
			$htaccess_tpl .= "\t" . 'Deny from All' . "\n";
			$htaccess_tpl .= '<b style="color:brown">&lt;/Files&gt;</b>' . "\n\n";
			$htaccess_tpl .= '<b style="color:blue"># You may need to un-comment the following lines' . "\n";
			$htaccess_tpl .= '# Options +FollowSymlinks' . "\n";
			$htaccess_tpl .= '# To make sure that rewritten dir or file (/|.html) will not load dir.php in case it exist' . "\n";
			$htaccess_tpl .= '# Options -MultiViews' . "\n";
			$htaccess_tpl .= '# REMEBER YOU ONLY NEED TO STARD MOD REWRITE ONCE</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteEngine</b> <b style="color:#FF00FF">On</b>' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># Uncomment the statement below if you want to make use of' . "\n";
			$htaccess_tpl .= '# HTTP authentication and it does not already work.' . "\n";
			$htaccess_tpl .= '# This could be required if you are for example using PHP via Apache CGI.' . "\n";
			$htaccess_tpl .= '# RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]</b>' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># REWRITE BASE</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteBase</b> <b>/{REWRITEBASE}</b>' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># HERE IS A GOOD PLACE TO FORCE CANONICAL DOMAIN</b>' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># RewriteCond %{HTTP_HOST} !^' . str_replace(array('https://', 'http://', '.'), array('', '', '\\.'), trim($phpbb_seo->seo_path['root_url'], '/ ')) . '$ [NC]</b>' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># RewriteRule ^(.*)$ ' . $phpbb_seo->seo_path['root_url'] . '{REWRITEBASE}$1 [QSA,L,R=301]</b>' . "\n\n";
			$htaccess_tpl .= '<b style="color:blue"># DO NOT GO FURTHER IF THE REQUESTED FILE / DIR DOES EXISTS</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} -f' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} -d' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> . - [L]' . "\n";
			$htaccess_tpl .= '<b style="color:blue">#####################################################' . "\n";
			$htaccess_tpl .= '# PHPBB SEO REWRITE RULES ALL MODES' . "\n";
			$htaccess_tpl .= '#####################################################' . "\n";
			$htaccess_tpl .= '# AUTHOR : dcz www.phpbb-seo.com' . "\n";
			$htaccess_tpl .= '# STARTED : 01/2006' . "\n";
			$htaccess_tpl .= '#################################' . "\n";
			$htaccess_tpl .= '# FORUMS PAGES' . "\n";
			$htaccess_tpl .= '###############</b>' . "\n";
			if (!empty($phpbb_seo->seo_static['index'])) {
				$htaccess_tpl .= '<b style="color:blue"># FORUM INDEX</b>' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_INDEX}{EXT_INDEX}$ {DEFAULT_SLASH}{PHPBB_RPATH}index.{PHP_EX} [QSA,L,NC]' . "\n";
			} else {
				$htaccess_tpl .= '<b style="color:blue"># FORUM INDEX REWRITERULE WOULD STAND HERE IF USED. "forum" REQUIRES TO BE SET AS FORUM INDEX' . "\n";
				$htaccess_tpl .= '# RewriteRule ^{WIERD_SLASH}{PHPBB_LPATH}<b style="color:#A020F0">forum</b>\.<b style="color:#6A5ACD">html</b>$ {DEFAULT_SLASH}{PHPBB_RPATH}index.{PHP_EX} [QSA,L,NC]</b>' . "\n";
			}
			$htaccess_common_tpl = '<b style="color:blue"># PHPBB FILES ALL MODES</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_FILE_INDEX}{DELIM_FILE}[a-z0-9_-]+{DELIM_FILE}({STATIC_THUMB}{DELIM_FILE})?([0-9]+)$ {DEFAULT_SLASH}{PHPBB_RPATH}download/file.{PHP_EX}?id=$2&amp;t=$1 [QSA,L,NC]' . "\n";
			if ( $phpbb_seo->seo_opt['profile_noids'] ) {
				$htaccess_common_tpl .= '<b style="color:blue"># PROFILES THROUGH USERNAME</b>' . "\n";
				$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_USER}/([^/]+)/?$ {DEFAULT_SLASH}{PHPBB_RPATH}memberlist.{PHP_EX}?mode=viewprofile&amp;un=$1 [QSA,L,NC]' . "\n";
				$htaccess_common_tpl .= '<b style="color:blue"># USER MESSAGES THROUGH USERNAME</b>' . "\n";
				$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_USER}/([^/]+)/(topics|posts){USER_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}search.{PHP_EX}?author=$1&amp;sr=$2&amp;start=$4 [QSA,L,NC]' . "\n";
			} else {
				$htaccess_common_tpl .= '<b style="color:blue"># PROFILES ALL MODES WITH ID</b>' . "\n";
				$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}({STATIC_USER}|[a-z0-9_-]*{DELIM_USER})([0-9]+){EXT_USER}$ {DEFAULT_SLASH}{PHPBB_RPATH}memberlist.{PHP_EX}?mode=viewprofile&amp;u=$2 [QSA,L,NC]' . "\n";
				$htaccess_common_tpl .= '<b style="color:blue"># USER MESSAGES ALL MODES WITH ID</b>' . "\n";
				$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}({STATIC_USER}|[a-z0-9_-]*{DELIM_USER})([0-9]+){DELIM_SR}(topics|posts){USER_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}search.{PHP_EX}?author_id=$2&amp;sr=$3&amp;start=$5 [QSA,L,NC]' . "\n";
			}
			$htaccess_common_tpl .= '<b style="color:blue"># GROUPS ALL MODES</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}({STATIC_GROUP}|[a-z0-9_-]*{DELIM_GROUP})([0-9]+){GROUP_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}memberlist.{PHP_EX}?mode=group&amp;g=$2&amp;start=$4 [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># POST</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_POST}([0-9]+){EXT_POST}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.{PHP_EX}?p=$1 [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># ACTIVE TOPICS</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_ATOPIC}{ATOPIC_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}search.{PHP_EX}?search_id=active_topics&amp;start=$2&amp;sr=topics [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># UNANSWERED TOPICS</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_UTOPIC}{UTOPIC_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}search.{PHP_EX}?search_id=unanswered&amp;start=$2&amp;sr=topics [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># NEW POSTS</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_NPOST}{NPOST_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}search.{PHP_EX}?search_id=newposts&amp;start=$2&amp;sr=topics [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># UNREAD POSTS</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_URPOST}{URPOST_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}search.{PHP_EX}?search_id=unreadposts&amp;start=$2 [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># THE TEAM</b>' . "\n";
			$htaccess_common_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_LEADERS}{EXT_LEADERS}$ {DEFAULT_SLASH}{PHPBB_RPATH}memberlist.{PHP_EX}?mode=leaders [QSA,L,NC]' . "\n";
			$htaccess_common_tpl .= '<b style="color:blue"># HERE IS A GOOD PLACE TO ADD OTHER PHPBB RELATED REWRITERULES</b>' . "\n\n";
			if ($gym_installed) {
				$htaccess_common_tpl .= '<b style="color:blue">#####################################################' . "\n";
				// RSS
				$htaccess_common_tpl .= '# GYM Sitemaps &amp; RSS' . "\n";
				$htaccess_common_tpl .= '# Global channels</b>' . "\n";
				$htaccess_common_tpl .= $rss_commpat_note;
				$htaccess_common_tpl .= $rss_commpat_pre . ' ^{WIERD_SLASH}{RSS_LPATH}rss(/(news)+)?(/(digest)+)?(/(short|long)+)?/?$ {DEFAULT_SLASH}{RSS_RPATH}gymrss.{PHP_EX}?channels&amp;$2&amp;$4&amp;$6 [QSA,L,NC]' . $rss_commpat_post . "\n";
				// HTML
				$htaccess_common_tpl .= '<b style="color:blue"># HTML Global news &amp; maps</b>' . "\n";
				$htaccess_common_tpl .= $html_commpat_note;
				$htaccess_common_tpl .= $html_commpat_pre . ' ^{WIERD_SLASH}{HTML_LPATH}(news|maps){PAGE_PAGINATION}$ {DEFAULT_SLASH}{HTML_RPATH}map.{PHP_EX}?$1&amp;start=$3 [QSA,L,NC]' . $html_commpat_post . "\n";
				$htaccess_common_tpl .= '<b style="color:blue"># END GYM Sitemaps &amp; RSS' . "\n";
				$htaccess_common_tpl .= '#####################################################</b>' . "\n\n";
			}
			// We now handle all modes at once (simple / mixed / advanced)
			$htaccess_tpl .= '<b style="color:blue"># FORUM ALL MODES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}({STATIC_FORUM}|[a-z0-9_-]*{DELIM_FORUM})([0-9]+){FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.{PHP_EX}?f=$2&amp;start=$4 [QSA,L,NC]' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># TOPIC WITH VIRTUAL FOLDER ALL MODES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}({STATIC_FORUM}|[a-z0-9_-]*{DELIM_FORUM})([0-9]+)/({STATIC_TOPIC}|[a-z0-9_-]*{DELIM_TOPIC})([0-9]+){TOPIC_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.{PHP_EX}?f=$2&amp;t=$4&amp;start=$6 [QSA,L,NC]' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># GLOBAL ANNOUNCES WITH VIRTUAL FOLDER ALL MODES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_GLOBAL_ANNOUNCE}{EXT_GLOBAL_ANNOUNCE}({STATIC_TOPIC}|[a-z0-9_-]*{DELIM_TOPIC})([0-9]+){TOPIC_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.{PHP_EX}?t=$2&amp;start=$4 [QSA,L,NC]' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># TOPIC WITHOUT FORUM ID &amp; DELIM ALL MODES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}([a-z0-9_-]*)/?({STATIC_TOPIC}|[a-z0-9_-]*{DELIM_TOPIC})([0-9]+){TOPIC_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.{PHP_EX}?forum_uri=$1&amp;t=$3&amp;start=$5 [QSA,L,NC]' . "\n";
			$htaccess_tpl .= $htaccess_common_tpl;
			// mods .htaccess pos1
			if (!empty($mods_ht['pos1'])) {
				$htaccess_tpl .= $mods_ht['pos1'];
			}
			$htaccess_tpl .= '<b style="color:blue"># FORUM WITHOUT ID &amp; DELIM ALL MODES (SAME DELIM)</b>' . "\n";
			if ($phpbb_seo->seo_ext['forum'] != '/') {
				$htaccess_tpl .= '<b style="color:blue"># THESE FOUR LINES MUST BE LOCATED AT THE END OF YOUR HTACCESS TO WORK PROPERLY</b>' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-f' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}([a-z0-9_-]+)(-([0-9]+)){EXT_FORUM}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.{PHP_EX}?forum_uri=$1&amp;start=$3 [QSA,L,NC]' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-f' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}([a-z0-9_-]+){EXT_FORUM}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.{PHP_EX}?forum_uri=$1 [QSA,L,NC]' . "\n";
			} else {
				$htaccess_tpl .= '<b style="color:blue"># THESE THREE LINES MUST BE LOCATED AT THE END OF YOUR HTACCESS TO WORK PROPERLY</b>' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-f' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-d' . "\n";
				$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}([a-z0-9_-]+){FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.{PHP_EX}?forum_uri=$1&amp;start=$3 [QSA,L,NC]' . "\n";
			}
			// fix for dumb clients unable to deal with base href
			$htaccess_tpl .= '<b style="color:blue"># FIX RELATIVE PATHS : FILES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_RPATH}.+/(style\.{PHP_EX}|ucp\.{PHP_EX}|mcp\.{PHP_EX}|faq\.{PHP_EX}|download/file.{PHP_EX})$ {DEFAULT_SLASH}{PHPBB_RPATH}$1 [QSA,L,NC,R=301]' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># FIX RELATIVE PATHS : IMAGES</b>' . "\n";
			$htaccess_tpl .= '<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_RPATH}.+/(styles/.*|images/.*)/$ {DEFAULT_SLASH}{PHPBB_RPATH}$1 [QSA,L,NC,R=301]' . "\n";
			$htaccess_tpl .= '<b style="color:blue"># END PHPBB PAGES' . "\n";
			$htaccess_tpl .= '#####################################################</b>' . "\n\n";
			// mods .htaccess pos2
			if (!empty($mods_ht['pos2'])) {
				$htaccess_tpl .= $mods_ht['pos2'];
			}
			if ($gym_installed) {
				$htaccess_tpl .= '<b style="color:blue">#####################################################' . "\n";
				$htaccess_tpl .= '# GYM Sitemaps &amp; RSS</b>' . "\n";
				// HTML
				$htaccess_tpl .= '<b style="color:blue"># HTML Module additional modes</b>' . "\n";
				$htaccess_tpl .= $html_commpat_note;
				$htaccess_tpl .= $html_commpat_pre . ' ^{WIERD_SLASH}{HTML_LPATH}(news|maps)/([a-z0-9_-]+)(/([a-z0-9_-]+))?{PAGE_PAGINATION}$ {DEFAULT_SLASH}{HTML_RPATH}map.{PHP_EX}?$2=$4&amp;$1&amp;start=$6 [QSA,L,NC]' . $html_commpat_post . "\n";
				// RSS
				$htaccess_tpl .= '<b style="color:blue"># Main feeds &amp; channels</b>' . "\n";
				$htaccess_tpl .= $rss_commpat_note;
				$htaccess_tpl .= $rss_commpat_pre . ' ^{WIERD_SLASH}{RSS_LPATH}rss(/(news)+)?(/(digest)+)?(/(short|long)+)?(/([a-z0-9_-]+))?/([a-z0-9_]+)\.xml(\.gz)?$ {DEFAULT_SLASH}{RSS_RPATH}gymrss.{PHP_EX}?$9=$8&amp;$2&amp;$4&amp;$6&amp;gzip=$10 [QSA,L,NC]' . $rss_commpat_post . "\n";
				$htaccess_tpl .= '<b style="color:blue"># Module feeds</b>' . "\n";
				$htaccess_tpl .= $rss_commpat_note;
				$htaccess_tpl .= $rss_commpat_pre . ' ^{WIERD_SLASH}{RSS_LPATH}[a-z0-9_-]*-[a-z]{1,2}([0-9]+)(/(news)+)?(/(digest)+)?(/(short|long)+)?/([a-z0-9_]+)\.xml(\.gz)?$ {DEFAULT_SLASH}{RSS_RPATH}gymrss.{PHP_EX}?$8=$1&amp;$3&amp;$5&amp;$7&amp;gzip=$9 [QSA,L,NC]' . $rss_commpat_post . "\n";
				$htaccess_tpl .= '<b style="color:blue"># Module feeds without ids</b>' . "\n";
				$htaccess_tpl .= $rss_commpat_note;
				$htaccess_tpl .= $rss_commpat_pre . ' ^{WIERD_SLASH}{RSS_LPATH}([a-z0-9_-]+)(/(news)+)?(/(digest)+)?(/(short|long)+)?/([a-z0-9_]+)\.xml(\.gz)?$ {DEFAULT_SLASH}{RSS_RPATH}gymrss.{PHP_EX}?nametoid=$1&amp;$3&amp;$5&amp;$7&amp;modulename=$8&amp;gzip=$9 [QSA,L,NC]' . $rss_commpat_post . "\n";
				// Google
				$htaccess_tpl .= '<b style="color:blue"># Google SitemapIndex</b>' . "\n";
				$htaccess_tpl .= $google_commpat_note;
				$htaccess_tpl .= $google_commpat_pre . ' ^{WIERD_SLASH}{GOOGLE_LPATH}sitemapindex\.xml(\.gz)?$ {DEFAULT_SLASH}{GOOGLE_RPATH}sitemap.{PHP_EX}?gzip=$1 [QSA,L,NC]' . $google_commpat_post . "\n";
				$htaccess_tpl .= '<b style="color:blue"># Module cat sitemaps</b>' . "\n";
				$htaccess_tpl .= $google_commpat_note;
				$htaccess_tpl .= $google_commpat_pre . ' ^{WIERD_SLASH}{GOOGLE_LPATH}[a-z0-9_-]+-([a-z]{1,2})([0-9]+)\.xml(\.gz)?$ {DEFAULT_SLASH}{GOOGLE_RPATH}sitemap.{PHP_EX}?module_sep=$1&amp;module_sub=$2&amp;gzip=$3 [QSA,L,NC]' . $google_commpat_post . "\n";
				$htaccess_tpl .= '<b style="color:blue"># Module sitemaps</b>' . "\n";
				$htaccess_tpl .= $google_commpat_note;
				$htaccess_tpl .= $google_commpat_pre . ' ^{WIERD_SLASH}{GOOGLE_LPATH}([a-z0-9_]+)-([a-z0-9_-]+)\.xml(\.gz)?$ {DEFAULT_SLASH}{GOOGLE_RPATH}sitemap.{PHP_EX}?$1=$2&amp;gzip=$3 [QSA,L,NC]' . $google_commpat_post . "\n";
				$htaccess_tpl .= '<b style="color:blue"># END GYM Sitemaps &amp; RSS' . "\n";
				$htaccess_tpl .= '#####################################################</b>' . "\n";
			}

			if (!empty($default_slash) && $this->new_config['more_options']) {
				$default_slash = '<b style="color:red">' . $default_slash . '</b>';
			}
			// The tpl array
			$htaccess_tpl_vars = array();
			if ($phpbb_seo->seo_opt['virtual_folder']) {
				$phpbb_seo->seo_ext['forum'] = '/';
			}
			// handle the suffixes proper in the RegEx
			// set up pagination reg ex
			// set up ext bits
			$seo_ext = array('pagination' => str_replace('.', '\\.', $phpbb_seo->seo_ext['pagination']));
			$reg_ex_page = sprintf($tpl['paginpage'], $phpbb_seo->seo_static['pagination'], $seo_ext['pagination']);
			foreach ( $phpbb_seo->seo_ext as $type => $value) {
				$seo_ext[$type] = str_replace('.', '\\.', $value);
				$htaccess_tpl_vars['{' . strtoupper($type) . '_PAGINATION}'] = ($phpbb_seo->seo_ext[$type] === '/') ? $reg_ex_page : sprintf($tpl['pagin'], $phpbb_seo->seo_delim['start'], $seo_ext[$type]);
				$htaccess_tpl_vars['{EXT_' . strtoupper($type) . '}'] = sprintf($tpl['static'] , $seo_ext[$type]);

			}
			$htaccess_tpl_vars['{PAGE_PAGINATION}'] = sprintf($tpl['paginpage'], $phpbb_seo->seo_static['pagination'], $seo_ext['pagination']);
			// static bits
			foreach ( $phpbb_seo->seo_static as $type => $value) {
				$htaccess_tpl_vars['{STATIC_' . strtoupper($type) . '}'] = sprintf($tpl['static'] , $phpbb_seo->seo_static[$type]);
			}
			// delim bits
			foreach ( $phpbb_seo->seo_delim as $type => $value) {
				$htaccess_tpl_vars['{DELIM_' . strtoupper($type) . '}'] = sprintf($tpl['delim'] , $phpbb_seo->seo_delim[$type]);
			}

			// common .htaccess vars
			$htaccess_tpl_vars += array(
				'{REWRITEBASE}' => $rewritebase,
				'{PHP_EX}' => $phpEx,
				'{PHPBB_LPATH}' => ($this->new_config['rbase'] || $phpbb_seo->seo_opt['virtual_root']) ? '' : $phpbb_path,
				'{PHPBB_RPATH}' => $this->new_config['rbase'] ? '' : $phpbb_path,
				'{RSS_LPATH}' => $rss_path ? $rss_path . '/' : '',
				'{RSS_RPATH}' => $rss_path ? $rss_path . '/' : '',
				'{GOOGLE_LPATH}' => $google_path ? $google_path . '/' : '',
				'{GOOGLE_RPATH}' => $google_path ? $google_path . '/' : '',
				'{HTML_LPATH}' => $html_path ? $html_path . '/' : '',
				'{HTML_RPATH}' => $html_path ? $html_path . '/' : '',
				'{DEFAULT_SLASH}' => $default_slash,
				'{WIERD_SLASH}' => $wierd_slash,
				'{MOD_RTYPE}' => $modrtype[$modrtype['type']],
			);
			// Parse .htaccess
			$htaccess_code = str_replace(array_keys($htaccess_tpl_vars), array_values($htaccess_tpl_vars), $htaccess_tpl);
		} // else the .htaccess is already generated
		if ( $html ) { // HTML output
			$htaccess_output = "\n" . '<script type="text/javascript">' . "\n";
			$htaccess_output .= '// <![CDATA[' . "\n";
			$htaccess_output .= 'function selectCode(a) {' . "\n";
			$htaccess_output .= "\t" . 'var e = a.parentNode.parentNode.getElementsByTagName(\'CODE\')[0]; // Get ID of code block' . "\n";
			$htaccess_output .= "\t" . 'if (window.getSelection) { // Not IE' . "\n";
			$htaccess_output .= "\t\t" . 'var s = window.getSelection();' . "\n";
			$htaccess_output .= "\t\t" . 'if (s.setBaseAndExtent) { // Safari' . "\n";
			$htaccess_output .= "\t\t\t" . 's.setBaseAndExtent(e, 0, e, e.innerText.length - 1);' . "\n";
			$htaccess_output .= "\t\t" . '} else { // Firefox and Opera' . "\n";
			$htaccess_output .= "\t\t\t" . 'var r = document.createRange();' . "\n";
			$htaccess_output .= "\t\t\t" . 'r.selectNodeContents(e);' . "\n";
			$htaccess_output .= "\t\t\t" . 's.removeAllRanges();' . "\n";
			$htaccess_output .= "\t\t\t" . 's.addRange(r);' . "\n";
			$htaccess_output .= "\t\t" . '}' . "\n";
			$htaccess_output .= "\t" . '} else if (document.getSelection) { // Some older browsers' . "\n";
			$htaccess_output .= "\t\t" . 'var s = document.getSelection();' . "\n";
			$htaccess_output .= "\t\t" . 'var r = document.createRange();' . "\n";
			$htaccess_output .= "\t\t" . 'r.selectNodeContents(e);' . "\n";
			$htaccess_output .= "\t\t" . 's.removeAllRanges();' . "\n";
			$htaccess_output .= "\t\t" . 's.addRange(r);' . "\n";
			$htaccess_output .= "\t" . '} else if (document.selection) { // IE' . "\n";
			$htaccess_output .= "\t\t" . 'var r = document.body.createTextRange();' . "\n";
			$htaccess_output .= "\t\t" . 'r.moveToElementText(e);' . "\n";
			$htaccess_output .= "\t\t" . 'r.select();' . "\n";
			$htaccess_output .= "\t" . '}' . "\n";
			$htaccess_output .= '}' . "\n";
			$htaccess_output .= '// ]]>' . "\n";
			$htaccess_output .= '</script>' . "\n";
			// build location message
			if ($show_rewritebase_opt && $this->new_config['rbase']) {
				$msg_loc = sprintf($user->lang['SEO_HTACCESS_FOLDER_MSG'], '<em style="color:#000">' . $phpbb_seo->seo_path['phpbb_url'] . '</em>');
			} else {
				$msg_loc = sprintf($user->lang['SEO_HTACCESS_ROOT_MSG'], '<em style="color:#000">' . $phpbb_seo->seo_path['root_url'] . '</em>');
			}
			$htaccess_output .= '</p><div class="content"><hr/>' . "\n" . '<b style="color:red">&rArr;&nbsp;' . $msg_loc . '</b><br/><br/><hr/>' . "\n";
			$htaccess_output .= '<b>.htaccess :&nbsp;<a href="#" onclick="dE(\'htaccess_code\',1); return false;">' . $user->lang['SEO_SHOW'] . '</a>&nbsp;/&nbsp;<a href="#" onclick="dE(\'htaccess_code\',-1); return false;">' . $user->lang['SEO_HIDE'] . '</a></b>' . "\n";
			$htaccess_output .= '<div id="htaccess_code"><dl style="padding:5px;background-color:#FFFFFF;border:1px solid #d8d8d8;font-size:12px;"><dt style="border-bottom:1px solid #CCCCCC;margin-bottom:3px;font-weight:bold;display:block;">&nbsp;<a href="#" onclick="selectCode(this); return false;">' . $user->lang['SEO_SELECT_ALL'] . '</a></dt>' . "\n";
			$htaccess_output .= '<dd ><code style="padding-top:5px;line-height:1.3em;color:#8b8b8b;font-weight:bold"><br/><br/>' . str_replace( "\n", '<br/>', $htaccess_code) . '</code></dd>' . "\n";
			$htaccess_output .= '</dl>' . "\n";
			$htaccess_output .= '<div style="padding:5px;margin-top:10px;background-color:#FFFFFF;border:1px solid #d8d8d8;font-size:12px;"><b>' . $user->lang['SEO_HTACCESS_CAPTION'] . ':</b><ul style="margin-left:30px;margin-top:10px;font-weight:bold;font-size:12px;">' . "\n";
			$htaccess_output .= '<li style="color:blue">&nbsp;' . $user->lang['SEO_HTACCESS_CAPTION_COMMENT'] . '</li>' . "\n";
			$htaccess_output .= '<li style="color:#A020F0">&nbsp;' . $user->lang['SEO_HTACCESS_CAPTION_STATIC'] . '</li>' . "\n";
			$htaccess_output .= '<li style="color:#6A5ACD">&nbsp;' . $user->lang['SEO_HTACCESS_CAPTION_SUFFIX'] . '</li>' . "\n";
			$htaccess_output .= '<li style="color:#FF00FF">&nbsp;' . $user->lang['SEO_HTACCESS_CAPTION_DELIM'] . '</li>' . "\n";
			if ($this->new_config['more_options']) {
				$htaccess_output .= '<li style="color:red">&nbsp;' . $user->lang['SEO_HTACCESS_CAPTION_SLASH'] . '</li>&nbsp;' . "\n";
			}
			$htaccess_output .= '</ul></div>' . "\n" . '</div></div><p>' . "\n";
		} else { // File output
			$htaccess_output = str_replace(array('&lt;', '&gt;', '&amp;'), array('<', '>', '&'), strip_tags($htaccess_code));
		}
		return $htaccess_output;
	}
	/**
	*  get_mods_ht Get all mods htaccess tpls
	*/
	function get_mods_ht() {
		global $phpEx, $config, $phpbb_root_path, $phpbb_seo;
		$all_ht_tpl = array('pos1' => '', 'pos2' => '');
		$path = $phpbb_root_path . 'phpbb_seo/includes/htmods';
		if (!($dir = @opendir($path))) {
			return false;
		}
		while( ($file = @readdir($dir)) !== false ) {
			if (!trim($file, '. ')) {
				continue;
			}
			if(preg_match('`^ht_([a-z0-9_-]+)\.' . $phpEx . '$`i', $file, $match)) {
				$mode = $match[1];
				$class = 'ht_' . $mode;
				require_once("$path/$file");
				$module = new $class();
				if ($tpl = $module->get_tpl()) {
					if (!empty($tpl['pos1'])) {
						$all_ht_tpl['pos1'] .= $tpl['pos1'] . "\n";
					}
					if (!empty($tpl['pos2'])) {
						$all_ht_tpl['pos2'] .= $tpl['pos2'] . "\n";
					}
				}
			}
		}
		return !empty($all_ht_tpl['pos1']) || !empty($all_ht_tpl['pos2']) ? $all_ht_tpl : false;

	}
	/**
	*  set_phpbb_seo_links Builds links to support threads
	*/
	function set_phpbb_seo_links() {
		global $user, $phpbb_seo, $config;
		$modrtype_lang = array();
		$phpbb_seo->version = htmlspecialchars($phpbb_seo->version);
		$phpbb_seo->modrtype = intval($phpbb_seo->modrtype);
		if ($phpbb_seo->modrtype < 1 || $phpbb_seo->modrtype > 3) {
			$phpbb_seo->modrtype = 1;
		}
		$modrtype_lang['titles'] = array( 1 => $user->lang['ACP_SEO_SIMPLE'], 2 =>  $user->lang['ACP_SEO_MIXED'], 3 =>  $user->lang['ACP_SEO_ADVANCED'], 'u' => $user->lang['ACP_ULTIMATE_SEO_URL']);
		$modrtype_lang['title'] = $modrtype_lang['titles'][$phpbb_seo->modrtype];
		$modrtype_lang['utitle'] = $modrtype_lang['titles']['u'];
		$modrtype_lang['types'] = array( 1 => 'SIMPLE', 2 => 'MIXED', 1 => 'SIMPLE', 3 => 'ADVANCED');
		$modrtype_lang['type'] = $modrtype_lang['types'][$phpbb_seo->modrtype];
		$modrtype_lang['modrlinks_en'] = array( 1 =>  'http://www.phpbb-seo.com/en/simple-seo-url/simple-phpbb-seo-url-t1566.html', 2 =>  'http://www.phpbb-seo.com/en/mixed-seo-url/mixed-phpbb-seo-url-t1565.html', 3 =>  'http://www.phpbb-seo.com/en/advanced-seo-url/advanced-phpbb-seo-url-t1219.html', 'u' => 'http://www.phpbb-seo.com/en/phpbb-mod-rewrite/ultimate-seo-url-t4608.html' );
		$modrtype_lang['modrlinks_fr'] = array( 1 =>  'http://www.phpbb-seo.com/fr/reecriture-url-simple/seo-url-phpbb-simple-t1945.html', 2 =>  'http://www.phpbb-seo.com/fr/reecriture-url-intermediaire/seo-url-intermediaire-t1946.html', 3 =>  'http://www.phpbb-seo.com/fr/reecriture-url-avancee/seo-url-phpbb-avance-t1501.html', 'u' => 'http://www.phpbb-seo.com/fr/mod-rewrite-phpbb/ultimate-seo-url-t4489.html' );
		$modrtype_lang['modrforumlinks_en'] = array( 1 =>  'http://www.phpbb-seo.com/en/simple-seo-url/', 2 =>  'http://www.phpbb-seo.com/en/mixed-seo-url/', 3 =>  'http://www.phpbb-seo.com/en/advanced-seo-url/', 'u' => 'http://www.phpbb-seo.com/en/phpbb-mod-rewrite/' );
		$modrtype_lang['modrforumlinks_fr'] = array( 1 =>  'http://www.phpbb-seo.com/fr/reecriture-url-simple/', 2 =>  'http://www.phpbb-seo.com/fr/reecriture-url-intermediaire/', 3 =>  'http://www.phpbb-seo.com/fr/reecriture-url-avancee/', 'u' => 'http://www.phpbb-seo.com/fr/mod-rewrite-phpbb/' );
		if (strpos($config['default_lang'], 'fr') !== false ) {
			$modrtype_lang['linkurl'] = $modrtype_lang['modrlinks_fr'][$phpbb_seo->modrtype];
			$modrtype_lang['forumlinkurl'] = $modrtype_lang['modrforumlinks_fr'][$phpbb_seo->modrtype];
			$modrtype_lang['ulinkurl'] = $modrtype_lang['modrlinks_fr']['u'];
			$modrtype_lang['uforumlinkurl'] = $modrtype_lang['modrforumlinks_fr']['u'];
		} else {
			$modrtype_lang['linkurl'] = $modrtype_lang['modrlinks_en'][$phpbb_seo->modrtype];
			$modrtype_lang['forumlinkurl'] = $modrtype_lang['modrforumlinks_en'][$phpbb_seo->modrtype];
			$modrtype_lang['ulinkurl'] = $modrtype_lang['modrlinks_en']['u'];
			$modrtype_lang['uforumlinkurl'] = $modrtype_lang['modrforumlinks_en']['u'];
		}
		$modrtype_lang['link'] = '<a href="' . $modrtype_lang['linkurl'] . '" title="' . $user->lang['ACP_PHPBB_SEO_VERSION'] . ' ' . $modrtype_lang['title'] . '" onclick="window.open(this.href); return false;"><b>' . $modrtype_lang['title'] . '</b></a>';
		$modrtype_lang['forumlink'] = '<a href="' . $modrtype_lang['forumlinkurl'] . '" title="' . $user->lang['ACP_SEO_SUPPORT_FORUM'] . '" onclick="window.open(this.href); return false;"><b>' . $user->lang['ACP_SEO_SUPPORT_FORUM'] . '</b></a>';
		$modrtype_lang['ulink'] = '<a href="' . $modrtype_lang['ulinkurl'] . '" title="' . $user->lang['ACP_PHPBB_SEO_VERSION'] . ' ' . $modrtype_lang['utitle'] . '" onclick="window.open(this.href); return false;"><b>' . $modrtype_lang['utitle'] . '</b></a>';
		$modrtype_lang['uforumlink'] = '<a href="' . $modrtype_lang['uforumlinkurl'] . '" title="' . $user->lang['ACP_SEO_SUPPORT_FORUM'] . '" onclick="window.open(this.href); return false;"><b>' . $user->lang['ACP_SEO_SUPPORT_FORUM'] . '</b></a>';
		return $modrtype_lang;
	}
	/**
	*  check_cache_folder Validates the cache folder status
	*/
	function check_cache_folder($cache_dir, $msg = true) {
		global $user, $phpEx;
		$exists = $write = $inner_write = false;
		$cache_msg = '';
		if (file_exists($cache_dir) && is_dir($cache_dir)) {
			$exists = true;
			if (!is_writeable($cache_dir)) {
				phpbb_chmod($cache_dir, CHMOD_READ | CHMOD_WRITE);
				$fp = @fopen($cache_dir . 'test_lock', 'wb');
				if ($fp !== false) {
					$write = true;
				}
				@fclose($fp);
				@unlink($phpbb_root_path . $dir . 'test_lock');
			} else {
				$write = true;
			}
			// check if the config cache file is here already and writeable
			$check = $cache_dir . "phpbb_cache.$phpEx";
			$checks = array("$check.old", "$check.current", "$cache_dir.htaccess", "$cache_dir.htaccess.old", "$cache_dir.htaccess.current");
			// let's check all files
			$inner_write = true;
			foreach($checks as $check) {
				if (file_exists($check)) {
					if (!is_writeable($check)) {
						$inner_write = false;
						phpbb_chmod($check, CHMOD_READ | CHMOD_WRITE);
						$fp = @fopen($check, 'wb');
						if ($fp !== false) {
							$inner_write = true;
						}
						@fclose($fp);
					}
				}
			}
		}
		if ($msg) {
			$exists = ($exists) ? '<b style="color:green">' . $user->lang['SEO_CACHE_FOUND'] . '</b>' : '<b style="color:red">' . $user->lang['SEO_CACHE_NOT_FOUND'] . '</b>';
			$write = ($write) ? '<br/> <b style="color:green">' . $user->lang['SEO_CACHE_WRITABLE'] . '</b>' : (($exists) ? '<br/> <b style="color:red">' . $user->lang['SEO_CACHE_UNWRITABLE'] . '</b>' : '');
			$inner_write = $inner_write ? '' : '<br/> <b style="color:red">' . $user->lang['SEO_CACHE_INNER_UNWRITABLE'] . '</b>';
			$cache_msg = sprintf($user->lang['SEO_CACHE_STATUS'], $cache_dir) . '<br/>' . $exists . $write . $inner_write;
			return '<br/><b>' . $user->lang['SEO_CACHE_FILE_TITLE'] . ':</b><br/>' . $cache_msg . '<br/><br/>';
		} else {
			return ($exists && $write);
		}
	}
	/**
	* write_cache( ) will write the cached file and keep backups.
	*/
	function write_cache( $type = 'forum' ) {
		global $phpbb_seo;
		if(!$phpbb_seo->cache_config['cache_enable'] || (!@is_array($phpbb_seo->cache_config[$type]) && $type != 'htaccess' ) || !array_key_exists($type, $phpbb_seo->cache_config['files'])) {
			return FALSE;
		}
		$cache_tpl = '<'.'?php' . "\n" . '/**' . "\n" . '* phpBB_SEO Class' . "\n" . '* www.phpBB-SEO.com' . "\n" . '* @package Advanced phpBB3 SEO mod Rewrite' . "\n" . '*/' . "\n" . 'if (!defined(\'IN_PHPBB\')) {' . "\n\t" . 'exit;' . "\n" . '}' . "\n";
		if ($type == 'forum') { // Add the phpbb_seo_config
			$update = '$this->cache_config[\'settings\'] = ' . preg_replace('`[\s]+`', ' ', var_export($phpbb_seo->cache_config['settings'], true)) . ';'. "\n";
			$update .= '$this->cache_config[\'forum\'] = ' . preg_replace('`[\s]+`', ' ', var_export($phpbb_seo->cache_config['forum'], true)) . ';'. "\n";
			$update = $cache_tpl . $update . '?'.'>';
		} elseif ($type == 'htaccess') { // .htaccess case
			$update = $this->seo_htaccess(false);
		} else { // Allow additional types
			$update = '$this->cache_config[\'' . $type . '\'] = ' . preg_replace('`[\s]+`', ' ', var_export($phpbb_seo->cache_config[$type], true)) . ';'. "\n";
			$update = $cache_tpl . $update . '?'.'>';
		}
		$file = SEO_CACHE_PATH . $phpbb_seo->cache_config['files'][$type];
		// Keep a backup of the previous settings
		@copy($file, $file . '.old');
		$handle = @fopen($file, 'wb');
		@fputs($handle, $update);
		@fclose ($handle);
		unset($update);
		@umask(0000);
		phpbb_chmod($file, CHMOD_READ | CHMOD_WRITE);
		// Keep a backup of the current settings
		@copy($file, $file . '.current');
		return true;
	}
	/**
	*  select_multiple($value, $key, $select_ary)
	*/
	function select_multiple($value, $key, $select_ary) {
		$size = min(12,count($select_ary));
		$html = '<select multiple="multiple" id="' . $key . '" name="multiple_' . $key . '[]" size="' . $size . '">';
		foreach ($select_ary as $sel_key => $sel_data) {
			if (empty($sel_data['disabled'])) {
				$selected = array_search($sel_key, @$this->new_config[$key]) !== false ? 'selected="selected"' : '';
				$disabled = '';
			} else {
				$disabled = 'disabled="disabled" class="disabled-option"';
				$selected = '';
			}
			$sel_title = $sel_data['title'];
			$html .= "<option value=\"$sel_key\" $disabled $selected>$sel_title</option>";
		}
		return $html . '</select>';
	}
	/**
	*  forum_select() // custom forum select setup
	*/
	function forum_select($ignore_acl = true, $ignore_nonpost = false, $ignore_emptycat = false, $only_acl_post = false) {
		$select_ary = make_forum_select(false, false, $ignore_acl, $ignore_nonpost, $ignore_emptycat, $only_acl_post, true);
		foreach($select_ary as $f_id => $f_data) {
			$select_ary[$f_id] = array(
				'title' => $f_data['padding'] . $f_data['forum_name'],
				'disabled' => $f_data['disabled'],
			);
		}
		return $select_ary;
	}
	/**
	* Pick a language, any language ... or no language
	*/
	function language_select($default = '') {
		global $user;
		return '<option value="">' . $user->lang['DISABLED'] . '</option>' . language_select($default);
	}
} // End of acp class
?>