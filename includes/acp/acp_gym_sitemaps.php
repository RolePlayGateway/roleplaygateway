<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: acp_gym_sitemaps.php 198 2009-12-09 12:15:07Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Advanced phpBB3 SEO mod Rewrite
*/
class acp_gym_sitemaps {
	var $u_action;
	var $new_config = array();
	var $dyn_select = array();
	var $gym_config = array();
	var $gym_modules = array();
	var $gym_modules_acp = array();
	var $mode = 'gym';
	var $modes = array();
	var $module = 'gym';
	var $action = 'main';
	var $maction = '';
	var $support_link = array();
	var $override = array();
	var $override_type = array();
	var $array_type_cfg = array();
	var $multiple_options = array();
	var $modrtype_lang = array();
	var $write_type = 'forum';
	var $lengh_limit = 20;
	var $word_limit = 3;
	var $seo_unset_opts = array();

	/**
	* Constructor
	*/
	function main($id, $mode) {
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix, $phpbb_seo, $_action_types;
		require_once($phpbb_root_path . 'gym_sitemaps/includes/gym_common.' . $phpEx);
		// Start the phpbb_seo class
		if ( !is_object($phpbb_seo) ) {
			if ( file_exists($phpbb_root_path . 'phpbb_seo/phpbb_seo_class.' . $phpEx)) {
				require_once($phpbb_root_path . 'phpbb_seo/phpbb_seo_class.' . $phpEx);
			} else {
				require_once($phpbb_root_path . 'gym_sitemaps/includes/phpbb_seo_class_light.' . $phpEx);
			}
			$phpbb_seo = new phpbb_seo();
		}
		$user->add_lang('gym_sitemaps/acp/gym_common');
		// action=(module|cache|modrewrite...)&amp;module=(main|forum| ...)
		$action	= request_var('action', 'main');
		$module	= request_var('module', 'main');
		// maction =(settings|maintenance|save)&amp;action=(module|cache|modrewrite...)&amp;module=(main|forum| ...)
		$maction = request_var('maction', '');
		$mactions = array('settings', 'maintenance', 'install');
		$maction = in_array($maction, $mactions) ? $maction : '';
		$submit = (isset($_POST['submit'])) ? true : false;
		$this->modes = $_action_types;
		$this->override_type = $_override_types;
		$this->override = array();
		// Filter allowed modes
		$this->mode = $mode;
		$this->module = $module;
		$this->action = $action;
		$this->maction = $maction;
		$this->set_phpbb_seo_links();
		// Get gym config
		obtain_gym_config('main', $this->gym_config);
		$this->new_config = $this->gym_config;
		// define common validation arrays
		$this->dyn_select['modrtype'] =  array( 0 => 'GYM_MODREWRITE_NONE', 1 => 'GYM_MODREWRITE_SIMPLE', 2 => 'GYM_MODREWRITE_MIXED', 3 => 'GYM_MODREWRITE_ADVANCED');
		$this->dyn_select['gzip_level'] =  array( 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9);
		$this->dyn_select['sort'] =  array( 'DESC' => 'GYM_DESC', 'ASC' => 'GYM_ASC');
		$this->dyn_select['override'] =  array( OVERRIDE_GLOBAL => 'GYM_OVERRIDE_GLOBAL', OVERRIDE_OTYPE => 'GYM_OVERRIDE_OTYPE', OVERRIDE_MODULE => 'GYM_OVERRIDE_MODULE');
		$this->dyn_select['sumarize_method'] =  array( 'chars' => 'GYM_METHOD_CHARS', 'words' => 'GYM_METHOD_WORDS', 'lines' => 'GYM_METHOD_LINES');
		$this->dyn_select['gym_auth'] = array(
			'admin' => 'GYM_AUTH_ADMIN',
			'globalmod' => 'GYM_AUTH_GLOBALMOD',
			'reg' => 'GYM_AUTH_REG',
			'guest' => 'GYM_AUTH_GUEST',
			'all' => 'GYM_AUTH_ALL',
			'none' => 'GYM_AUTH_NONE',
		);
		$this->dyn_select['rss_linking_types'] = array('n' => 'RSS_LINKING_NEWS', 'nd' => 'RSS_LINKING_NEWS_DIGEST', 'r' => 'RSS_LINKING_REGULAR', 'rd' => 'RSS_LINKING_REGULAR_DIGEST');
		// Get the module list
		// Populate the $this->gym_modules[$mode][$module] array
		$this->gym_get_modules($mode);
		// Load the relevant modules acp options
		// Populate the $this->gym_modules_acp[$mode][$module] array
		$this->gym_module_acp($mode, $module);
		// Acp options array for this case
		$display_vars = array();
		// Cache management
		if ($maction === 'maintenance') {
			$display_vars = $this->gym_maintenance( $mode, $module, $action, $submit );
			$submit = false;
		} elseif ($maction === 'settings') { // settings management
			$display_vars = $this->gym_set_default( $mode, $module, $action, $submit );
			$submit = false;
		} elseif ($maction === 'install') { // module install
			$display_vars = $this->gym_install( $mode, $module, $action, $submit );
			$submit = false;
		} else {
			if ( !in_array($mode, $this->modes) || !in_array($module, $this->gym_modules[$mode])) {
				trigger_error('NO_MODE', E_USER_ERROR);
			} else {
				if (empty($this->gym_modules_acp[$mode][$module][$action]['display_vars'])) {
					$action = $this->action = 'main';
				}
				$display_vars = $this->gym_modules_acp[$mode][$module][$action]['display_vars'];
				// Check if we do not have a new module needing a new config key
				$clear_cache = false;
				foreach ($display_vars['vars'] as $key => $value) {
					if (!isset($this->gym_config[$key]) && strpos($key, 'legend') === false) {
						$clear_cache = true;
						if(isset($this->gym_modules_acp[$mode][$module][$action]['default'][$key])) {
							$this->new_config[$key] = $this->gym_modules_acp[$mode][$module][$action]['default'][$key];
							set_gym_config($key, $this->new_config[$key], $mode, $this->gym_config);
						}
					}
				}
				if ($clear_cache) {
					$this->remove_cache('config');
				}
			}
		}

		// Load the module's language files
		foreach ($this->gym_modules_acp[$mode] as $active_modules => $null) {
			if (!empty($this->gym_modules_acp[$mode][$active_modules]['info']['lang_file'])) {
				$user->add_lang('gym_sitemaps/acp/' . $this->gym_modules_acp[$mode][$active_modules]['info']['lang_file']);
			}
		}

		$error = array();
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);
		// Do not write values if there is an error
		if (sizeof($error)) {
			$submit = false;
		}
		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $cfg_setup) {
			if ( (!isset($cfg_array[$config_name]) && @$cfg_setup['method'] != 'select_multiple_string') || strpos($config_name, 'legend') !== false) {
				continue;
			}
			// Handle multiple select options
			if (!empty($cfg_setup['method']) && $cfg_setup['method'] == 'select_multiple_string') {
				if (isset($_POST['multiple_' . $config_name])) {
					$m_values = utf8_normalize_nfc(request_var('multiple_' . $config_name, array('' => '')));
					$validate_int = $cfg_setup['multiple_validate'] == 'int' ? true : false;
					foreach($m_values as $k => $v) {
						if ($validate_int) {
							$v = (int) $v;
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
				set_gym_config($config_name, $config_value, $mode, $this->gym_config);
			}
		}
		if ($submit) {
			$this->remove_cache('config');
			add_log('admin', 'GYM_LOG_CONFIG_' . strtoupper($mode));
			trigger_error($user->lang['CONFIG_UPDATED'] . $this->back_to_prev());
		}
		$this->tpl_name = 'acp_gym_sitemaps';
		$this->page_title = $display_vars['title'];
		// add the maitenance links
		$maintenance_links = '';
		$maintenance_links .= '<a href="' . $this->u_action . '&amp;maction=maintenance&amp;action=' . $action . '&amp;module=' . $module . '"><b style="color:red;">' . $user->lang['GYM_MAINTENANCE'] . '</b></a><b> &bull; </b>';
		$maintenance_links .= '<a href="' . $this->u_action . '&amp;maction=settings&amp;action=' . $action . '&amp;module=' . $module . '"><b style="color:red;">' . $user->lang['GYM_SETTINGS'] . '</b></a>';
		$install_link = ($mode !== 'main') ? '<b> &bull; </b><a href="' . $this->u_action . '&amp;maction=install&amp;action=' . $action . '&amp;module=' . $module . '"><b style="color:red;">' . $user->lang['GYM_INSTALL'] . '</b></a>' : '';
		if ($action === 'gzip') {
			// Adjust language variable a bit
			$user->lang['GYM_GZIP_EXPLAIN'] = sprintf( $user->lang['GYM_GZIP_EXPLAIN'], ($config['gzip_compress'] ? $user->lang['GYM_GZIP_FORCED'] : $user->lang['GYM_GZIP_CONFIGURABLE']) );
		}
		$lang_key = 'GYM_' . strtoupper($mode);
		$l_mode_title = $this->safe_lang($lang_key);
		$l_mode_title_explain = $this->safe_lang($lang_key . '_EXPLAIN');
		$lang_key = $this->gym_modules_acp[$mode][$module]['info']['title_lang'];
		$l_module_title = $this->safe_lang($lang_key);
		$l_module_title_explain = $this->safe_lang($lang_key . '_EXPLAIN');
		$l_title = $this->safe_lang($display_vars['title']);
		$l_title_explain = $this->safe_lang($display_vars['title'] . '_EXPLAIN');
		$l_title_explain .= ($action === 'cache' && $mode !== 'html') ? $this->check_cache_folder($phpbb_root_path . 'gym_sitemaps/cache') : '';
		$template->assign_vars(array(
			'L_MODE_TITLE'		=> $l_mode_title,
			'L_MODE_ITLE_EXPLAIN'	=> $l_mode_title_explain,
			'L_MODULE_TITLE'	=> $l_module_title,
			'L_MODULE_ITLE_EXPLAIN'	=> $l_module_title_explain,
			'L_TITLE'		=> $l_title,
			'L_TITLE_EXPLAIN'	=> $l_title_explain,
			'GYM_VERSION'		=> $this->gym_config['gym_version'],
			'INSTALL_LINK'		=> $install_link,
			'MAINTENANCE_LINKS'	=> $maintenance_links,
			'S_ERROR'		=> (sizeof($error)) ? true : false,
			'ERROR_MSG'		=> implode('<br />', $error),
			'S_SUBMIT_BUTTON'	=> !empty($this->gym_modules_acp[$mode][$module][$action]['default']),
			'U_ACTION'		=> $this->u_action . '&amp;maction=' . $maction . '&amp;action=' . $action . '&amp;module=' . $module)
		);

		$this->gym_module_menu( $mode, $module, $action );

		$this->gym_menu( $mode, $module, $action );
		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars) {
			if (!is_array($vars) && strpos($config_key, 'legend') === false) {
				continue;
			}
			if (strpos($config_key, 'legend') !== false) {
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> $this->safe_lang($vars))
				);
				continue;
			}
			$type = explode(':', $vars['type']);
			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain'])) {
				$l_explain = $this->safe_lang($vars['lang_explain']);
			} elseif ($vars['explain']) {
				$l_explain = $this->safe_lang($vars['lang'] . '_EXPLAIN');
			}
			// Add overriding infos
			$form = true;
			if (isset($vars['overriding']) && $vars['overriding']) {
				$vars['append'] = $this->is_overriden($mode, $module, $action, $config_key, $vars);
				//$form = $vars['append'] == $this->safe_lang('GYM_COULD_OVERRIDE') ? true : false;
			}
			$content = '';
			$template->assign_block_vars('options', array(
				'KEY' => $config_key,
				'TITLE' => $this->safe_lang($vars['lang']),
				'S_EXPLAIN' => $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT' =>  $form ? build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars) : $vars['append'],
				)
			);
			unset($display_vars['vars'][$config_key]);
		}
	}
	/**
	* gym_get_modules($mode).
	* Populates $this->gym_modules[$mode][$module]
	* with the acp modules list
	*/
	function gym_get_modules($mode) {
		global $cache, $phpEx, $phpbb_root_path;
		if (($this->gym_modules[$mode] = $cache->get('_gym_modules_' . $mode)) === false) {
			$this->gym_modules[$mode] = array();
			$dir = @opendir( $phpbb_root_path . 'gym_sitemaps/acp' );
			while( ($file = @readdir($dir)) !== FALSE ) {
				if(preg_match('`^' . $mode . '_([a-z0-9_-]+)\.' . $phpEx . '$`i', $file, $matches)) {
					$module = trim(str_replace( $mode . '_', '' , str_replace('.' . $phpEx , '' ,$file)), "/");
					if ($matches[1] == 'main' || (file_exists($phpbb_root_path . 'gym_sitemaps/modules/' .  $file) && !empty($this->gym_config[$mode . '_' . $module . '_installed'])) ) {
						$this->gym_modules[$mode][$module] = $module;
					}
				}
			}
			@closedir($dir);
			// Reorder a bit, put the main panel at the first position, others will keep
			// the file system sorting
			if (!empty($this->gym_modules[$mode]['main'])) {
				$main = $this->gym_modules[$mode]['main'];
				unset($this->gym_modules[$mode]['main']);
				$this->gym_modules[$mode] = array('main' => $main) + $this->gym_modules[$mode];

			}
			$cache->put('_gym_modules_' . $mode, $this->gym_modules[$mode]);
		}
		if (!is_array($this->gym_modules[$mode]) || empty($this->gym_modules[$mode])) {
			$this->remove_cache('acp', $mode);
		}
	}
	/**
	* gym_module_acp($mode, $module)
	* loads acp module options in the $this->gym_modules_acp[$mode][$mode_module] array.
	*/
	function gym_module_acp($mode, $module) {
		global $phpbb_root_path, $phpEx, $cache;
		if (is_array($this->gym_modules[$mode]) && ($this->gym_modules_acp[$mode] = $cache->get('_gym_acp_' . $mode)) === false) {
			foreach ($this->gym_modules[$mode] as $mode_module) {
				$this->gym_pick_module($mode, $mode_module, $this->gym_modules_acp);
			}
			$cache->put('_gym_acp_' . $mode, $this->gym_modules_acp[$mode]);
		}
		if (!@is_array($this->gym_modules_acp[$mode]) || empty($this->gym_modules_acp[$mode])) {
			$this->remove_cache('acp', $mode);
		}
	}
	/**
	* gym_pick_module( $mode, $module, $action)
	* pick a given module data
	*/
	function gym_pick_module( $mode, $mode_module, &$return_array) {
		global $phpbb_root_path, $phpEx;
		$module_class = $mode . '_' . $mode_module;
		$module_file = $phpbb_root_path . 'gym_sitemaps/acp/' . $module_class . '.' . $phpEx;
		if ( file_exists($module_file) ) {
			include_once($module_file);
			if (class_exists($module_class)) {
				$gym_module = new $module_class($this);
				if ( method_exists($gym_module, 'acp_module')) {
					$return_array[$mode][$mode_module] = $gym_module->acp_module();
				}
			}
		}
	}
	/**
	* gym_menu( $mode, $module, $action)
	* Builds the module action links
	*/
	function gym_menu( $mode, $module, $action) {
		global $template, $user;
		foreach ($this->gym_modules_acp[$mode][$module]['info']['actions'] as $module_action) {
				$template->assign_block_vars('menu', array(
					'L_TITLE'	=> $this->safe_lang($this->gym_modules_acp[$mode][$module][$module_action]['display_vars']['title']),
					'S_SELECTED'	=> $action == $module_action ? true : false,
					'U_TITLE'	=> $this->u_action . '&amp;action=' . $module_action . '&amp;module=' . $module,
				));
		}
		$template->assign_vars(array('S_MENU' => empty($this->maction)));
		return;
	}
	/**
	* gym_module_menu( $mode, $module, $action)
	* builds the module ACP links
	*/
	function gym_module_menu( $mode, $module, $action ) {
		global $template, $user;
		foreach ($this->gym_modules_acp[$mode] as $modules) {
				$template->assign_block_vars('menu_module', array(
					'L_TITLE'		=> $this->safe_lang($modules['info']['title_lang']),
					'S_SELECTED'	=> ($module == @$modules['info']['module'] && $this->maction !== 'install') ? true : false,
					'U_TITLE'		=> $this->u_action . '&amp;module=' . @$modules['info']['module'] . (!empty($action) ? "&amp;action=$action" : ''),
				));
		}
		$template->assign_vars(array('S_MENU' => true));
		$template->assign_vars(array('S_MENU_MODULE' => true));
		return;
	}
	/**
	* gym_install( $mode, $module, $action, $submit = false )
	* handle module install / un-install
	*/
	function gym_install( $mode, $module, $action, $submit = false ) {
		global $user, $phpEx, $phpbb_root_path;
		$post_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : array();
		if ($submit) {
			$un_install = $install = array();
			foreach ($this->gym_modules[$mode] as $_module) { // check if we need to uninstall
				$type_module = $mode . '_' . $_module;
				if (isset($post_array[$type_module]) && !$post_array[$type_module] && !empty($this->gym_config[$type_module . '_installed'])) { // Uninstall
					$un_install[$mode][$_module] = $type_module;
				}
			}
			$dir = @opendir( $phpbb_root_path . 'gym_sitemaps/acp' );
			while( ($file = @readdir($dir)) !== false ) { // check if we need to install
				if(preg_match('`^' . $mode . '_([a-z0-9_-]+)\.' . $phpEx . '$`i', $file, $matches)) {
					$type_module = trim(str_replace('.' . $phpEx , '' ,$file), "/");
					$_module = str_replace($mode . '_', '', $type_module);
					if ($matches[1] !== 'main' && file_exists($phpbb_root_path . 'gym_sitemaps/modules/' .  $file) && !isset($this->gym_modules[$mode][$_module]) && !empty($post_array[$type_module])) {
						$install[$mode][$_module] = $type_module;
					}
				}
			}
			// Now un-install
			if (!empty($un_install)) {
				foreach ($un_install[$mode] as $_module => $type_module) {
					// In case we are uninstalling from this module
					// we go back to main
					if ($module == $this->module) {
						$this->module = 'main';
					}
					set_gym_config($type_module . '_installed', 0, 'main', $this->gym_config);
					$this->gym_set_default( $mode, $_module, $action, true, true, true );
				}
			}
			// Now install
			if (!empty($install)) {
				foreach ($install[$mode] as $_module => $type_module) {
					set_gym_config($type_module . '_installed', 1, 'main', $this->gym_config);
				}
				$this->remove_cache('config');
				$this->remove_cache('acp');
				$this->gym_get_modules($mode);
				foreach ($install[$mode] as $_module => $type_module) {
					$this->gym_set_default( $mode, $_module, $action, true, true );
				}
			}
			$this->remove_cache('config');
			$this->remove_cache('acp');
			trigger_error($user->lang['CONFIG_UPDATED'] . $this->back_to_prev());
		}
		// Adjust language variable a bit
		$user->lang['GYM_MODULE_INSTALL'] = sprintf($user->lang['GYM_MODULE_INSTALL'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_MODULE_INSTALL_EXPLAIN'] = sprintf($user->lang['GYM_MODULE_INSTALL_EXPLAIN'], $user->lang[strtoupper($mode)] );
		$display_vars = array( 'title'	=> 'GYM_MODULE_INSTALL');
		$display_vars['vars'] = array();
		$i = 1;
		// Installed modules
		$active = array();
		foreach ($this->gym_modules[$mode] as $_module) {
			if ($_module !== 'main') {
				$active[$mode][$_module] = $mode . '_' . $_module;
			}
		}
		if (!empty($active)){ // Add the active module list
			$display_vars['vars']['legend' . $i] = 'GYM_MODULES_INSTALLED';
			$i++;
			foreach ($active[$mode] as $_module => $type_module) { // Installed modules
				// Grabb the data
				//$this->gym_module_acp($mode, $type_module);
				$this->new_config[$type_module] = 1;
				$display_vars['vars'][$type_module] = array('lang' => strtoupper($type_module), 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false);
			}
		}
		// now check if we have some module to install
		$unactive = array();
		$dir = @opendir( $phpbb_root_path . 'gym_sitemaps/acp' );
		while( ($file = @readdir($dir)) !== FALSE ) {
			if(preg_match('`^' . $mode . '_([a-z0-9_-]+)\.' . $phpEx . '$`i', $file, $matches)) {
				$type_module = trim(str_replace('.' . $phpEx , '' ,$file), "/");
				$_module = str_replace($mode . '_', '', $type_module);
				if ($matches[1] !== 'main' && file_exists($phpbb_root_path . 'gym_sitemaps/modules/' .  $file) && !isset($this->gym_modules[$mode][$_module])) {
					$unactive[$mode][$_module] = $type_module;
					if ($_module != 'main' && !empty($this->gym_config[$type_module . '_installed'])) {
						set_gym_config($type_module . '_installed', 0, 'main', $this->gym_config);
					}
				}
			}
		}
		if (!empty($unactive)){ // Add the not active module list
			$display_vars['vars']['legend' . $i] = 'GYM_MODULES_UNINSTALLED';
			$i++;
			foreach ($unactive[$mode] as $_module => $type_module) { // Uninstalled modules
				// Grabb the data
				$this->gym_pick_module($mode, $_module, $module_data);
				if (!empty($module_data[$mode][$_module]['info']['lang_file'])) {
					$user->add_lang('gym_sitemaps/acp/' . $module_data[$mode][$_module]['info']['lang_file']);
				}
				$this->new_config[$type_module] = 0;
				$display_vars['vars'][$type_module] = array('lang' => strtoupper($type_module), 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false);
			}
		}
		return $display_vars;
	}
	/**
	* gym_set_default( $mode, $module, $action, $submit = false, $silent = false, $uninstall = false )
	* Set default values for modules
	*/
	function gym_set_default( $mode, $module, $action, $submit = false, $silent = false, $uninstall = false ) {
		global $user, $phpbb_root_path, $phpEx;
		$post_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : array();
		$this->new_config['reset_all'] = $reset_all = isset($post_array['reset_all']) ? $post_array['reset_all'] : false;
		if ($silent) {
			$reset_all = true;
		}
		if ($submit) {
			if ($mode === 'main' ) { // Reset all seting for all output and all modules
				foreach ($this->modes as $output_mode) { // List the output modes
					$this->gym_get_modules($output_mode);
					foreach ($this->gym_modules[$output_mode] as $type_module) { // List modules from each output mode
						if (!empty($post_array[$output_mode . '_' . $type_module . '_reset']) || $reset_all) {
							// Grabb the data
							$this->gym_module_acp($output_mode, $type_module);
							foreach($this->gym_modules_acp[$output_mode][$type_module]['info']['actions'] as $module_action) {
								foreach ($this->gym_modules_acp[$output_mode][$type_module][$module_action]['default'] as $module_config => $default_value ) { // In the end list possible options for this module's option set
									// Update config
									if ($uninstall) {
										rem_gym_config($module_config, $this->gym_config);
									} else {
										set_gym_config($module_config, $default_value, $output_mode, $this->gym_config);
									}
								}
							}
						}
					}
				}
			} elseif ($module === 'main') { // Only looking for one output type modules
				foreach ($this->gym_modules[$mode] as $type_module) { // add the output types modules
					if (!empty($post_array[$mode . '_' . $type_module . '_reset']) || $reset_all) {
						// Grabb the data
						$this->gym_module_acp($mode, $type_module);
						foreach($this->gym_modules_acp[$mode][$type_module]['info']['actions'] as $module_action) {
							foreach ($this->gym_modules_acp[$mode][$type_module][$module_action]['default'] as $module_config => $default_value ) {
								// Update config
								if ($uninstall) {
									rem_gym_config($module_config, $this->gym_config);
								} else {
									set_gym_config($module_config, $default_value, $mode, $this->gym_config);
								}
							}
						}
					}
				}
			} else { // Only reset this module config
				$this->gym_module_acp($mode, $module);
				// Allow modules with no acp
				if (!empty($this->gym_modules_acp[$mode][$module]['info'])) {
					foreach ($this->gym_modules_acp[$mode][$module]['info']['actions'] as $module_action ) {
						foreach ($this->gym_modules_acp[$mode][$module][$module_action]['default'] as $module_config => $default_value ) {
							if (!empty($post_array[$mode . '_' . $module . '_' . $module_action . '_reset']) || $reset_all) {
								// Update config
								if ($uninstall) {
									rem_gym_config($module_config, $this->gym_config);
								} else {
									set_gym_config($module_config, $default_value, $mode, $this->gym_config);
								}
							}
						}
					}
				}
			}
			$this->clear_all_cache();
			unset($post_array);
			if (!$silent) {
				trigger_error($user->lang['CONFIG_UPDATED'] . $this->back_to_prev());
			} else {
				return;
			}
		}
		// Adjust language variable a bit
		$user->lang['GYM_RESET'] = sprintf($user->lang['GYM_RESET'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_RESET_EXPLAIN'] = sprintf($user->lang['GYM_RESET_EXPLAIN'], $user->lang[strtoupper($mode)] );
		$display_vars = array( 'title'	=> 'GYM_RESET');
		$i = 1;
		if ($mode === 'main' ) { // Reset all seting for all output and all modules
			foreach ($this->modes as $output_mode) { // List the output types modules
				$this->gym_get_modules($output_mode);
				$display_vars['vars']['legend' . $i] = strtoupper($output_mode);
				$i++;
				foreach ($this->gym_modules[$output_mode] as $type_module) { // Then the modules
					// Grabb the data
					$this->gym_module_acp($output_mode, $type_module);
					// Then the associated language files if any
					if (!empty($this->gym_modules_acp[$output_mode][$type_module]['info']['lang_file'])) {
						$user->add_lang('gym_sitemaps/acp/' . $this->gym_modules_acp[$output_mode][$type_module]['info']['lang_file']);
					}
					$var_key = $output_mode . '_' . $type_module . '_reset';
					$this->new_config[$var_key] = 0;
					$display_vars['vars'][$var_key] = array('lang' => strtoupper($var_key), 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
				}
			}
		} elseif ($module === 'main') { // Only looking for one output type modules

			foreach ($this->gym_modules[$mode] as $type_module) { // add the output types modules
				// Grabb the data
				$this->gym_module_acp($mode, $type_module);
				$display_vars['vars']['legend' . $i] = strtoupper($mode . '_' . $type_module);
				$i++;
				$var_key = $mode . '_' . $type_module . '_reset';
				$this->new_config[$var_key] = 0;
				$display_vars['vars'][$var_key] = array('lang' => strtoupper($var_key), 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
			}
		} else { // Only reset this module config
			$this->gym_module_acp($mode, $module);
			$display_vars['vars']['legend' . $i] = strtoupper($mode . '_' . $module) . '_RESET';
			$i++;
			// Grabb the data
			foreach ($this->gym_modules_acp[$mode][$module]['info']['actions'] as $module_action ) {
				if (!empty($this->gym_modules_acp[$mode][$module][$module_action]['display_vars']['vars'])) {
					$var_key = $mode . '_' . $module . '_' . $module_action . '_reset';
					$this->new_config[$var_key] = 0;
					$display_vars['vars'][$var_key] = array('lang' => strtoupper($var_key), 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
				}
			}
		}
		$display_vars['vars']['legend' . $i] = 'GYM_RESET_ALL';
		$i++;
		$display_vars['vars']['reset_all'] = array('lang' => 'GYM_RESET_ALL', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true);
		return $display_vars;
	}
	/**
	* gym_maintenance( $mode, $module, $action, $submit = false )
	* handle cache (data + module's cache) clearing
	*/
	function gym_maintenance( $mode, $module, $action, $submit = false ) {
		global $user, $phpbb_root_path, $phpEx;
		$post_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : array();
		$this->new_config['cache_action'] = $cache_action = isset($post_array['cache_action']) ? $post_array['cache_action'] : 'all';
		$this->new_config['acp_modules'] = $acp_modules = isset($post_array['acp_modules']) ? $post_array['acp_modules'] : false;
		unset($post_array);
		$regexes = array( 'all' => '[a-z0-9_-]+', 'google' => 'google_', 'rss' => 'rss_', /*'html' => 'html_', 'yahoo' => 'yahoo_'*/);

		$cache_dir = $phpbb_root_path . 'gym_sitemaps/cache/';
		$cache_regex = $style_regex = $regexes['all'];
		if ($cache_action == 'all') {
			if ($mode != 'main') {
				$cache_regex = $style_regex = $mode . '_';
			}
		} else {
			if ($mode != 'main') { // we are at the output type level
				$cache_regex = $style_regex = $mode . '_';
				$cache_regex .= $cache_action . '_';
			} else { // At the global level, we only can delete complete output type cache at once
				$cache_regex = isset($regexes[$cache_action]) ? $regexes[$cache_action] : $regexes['all'];

			}
		}
		if ($submit) {
			$message = '';
			if ($acp_modules) {
				$this->remove_cache('acp', $cache_action);
				$message = $user->lang['MODULE_CACHE_CLEARED'];
			} else {
				$accessed = false;
				$deleted = '';
				$res = opendir($cache_dir);
				if($res) {
					$num_del = 0;
					while(($file = readdir($res))) {
						// includes CSS and XSL cache
						if(preg_match('`^(style_' . $style_regex . '|' . $cache_regex . ')[a-z0-9_-]+\.(xml|xml\.gz|css|xsl)$`i', $file)) {
							@unlink($cache_dir . $file);
							$deleted .=  "<li>$file</li>";
							$num_del++;
						}
					}
					$accessed = true;
				}
				closedir($res);
				if ($accessed) {
					if ($deleted !='') {
						$message = $user->lang['GYM_CACHE_CLEARED'] . $cache_dir . '<br/><br/>';
						$message .= '<div align="left">' . $user->lang['GYM_FILE_CLEARED'] . " $num_del<ul>$deleted</ul></div>";
					} else {
						$message = $user->lang['GYM_CACHE_ACCESSED'] . $cache_dir;
					}
				} else {
					$message = $user->lang['GYM_CACHE_NOT_CLEARED'] . $cache_dir;
				}
			}
			trigger_error($message . $this->back_to_prev());
		}
		// Clear cache type
		if ($mode === 'main') {
			$cache_actions = array( 'gym' => $user->lang['ALL'], 'google' => $user->lang['GOOGLE'], 'rss' => $user->lang['RSS'], /*'yahoo' => $user->lang['YAHOO'], 'html' => $user->lang['HTML']*/);
		} else {
			$cache_actions = array();
			foreach ($this->gym_modules[$mode] as $mode_module) {
				if ($mode_module === 'main') {
					if ($module !== 'main') {
						continue;
					}
					$mode_module = 'all';
				}
				if (!empty($this->gym_modules_acp[$mode][$mode_module]['info']['lang_file'])) {
					$user->add_lang('gym_sitemaps/acp/' . $this->gym_modules_acp[$mode][$mode_module]['info']['lang_file']);
				}
				$cache_actions[$mode_module] = $this->safe_lang(strtoupper($mode . '_' . $mode_module . '_reset'));
			}
		}
		$this->gym_modules_acp[$this->mode][$this->module][$this->action]['select']['cache_action'] = $cache_actions;
		// Adjust language variable a bit
		$user->lang['GYM_MODULE_MAINTENANCE'] = sprintf($user->lang['GYM_CLEAR_CACHE'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_MODULE_MAINTENANCE_EXPLAIN'] = sprintf($user->lang['GYM_MODULE_MAINTENANCE_EXPLAIN'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_CLEAR_CACHE'] = sprintf($user->lang['GYM_CLEAR_CACHE'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_CLEAR_CACHE_EXPLAIN'] = sprintf($user->lang['GYM_CLEAR_CACHE_EXPLAIN'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_CLEAR_ACP_CACHE'] = sprintf($user->lang['GYM_CLEAR_ACP_CACHE'], $user->lang[strtoupper($mode)] );
		$user->lang['GYM_CLEAR_ACP_CACHE_EXPLAIN'] = sprintf($user->lang['GYM_CLEAR_ACP_CACHE_EXPLAIN'], $user->lang[strtoupper($mode)] );
		$display_vars = array( 'title'	=> 'GYM_MODULE_MAINTENANCE',
			'vars'	=> array(
				'legend1'	=> 'GYM_MODULE_MAINTENANCE',
				'cache_action'	=> array('lang' => 'GYM_CLEAR_CACHE','validate' => 'string', 'type' => 'select', 'method' => 'select_string', 'explain' => true,),
				'acp_modules' => array('lang' => 'GYM_CLEAR_ACP_CACHE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			),
		);
		return $display_vars;
	}
	/**
	* is_overriden($mode, $module, $action, $config_key, $vars)
	* tell if an option is overriden
	*/
	function is_overriden($mode, $module, $action, $config_key, $vars) {
		global $user;
		static $override_msg = array();
		if (empty($override_msg)) {
			$override_msg = array( OVERRIDE_GLOBAL => 'GYM_OVERRIDED_GLOBAL', OVERRIDE_OTYPE => 'GYM_OVERRIDED_OTYPE', OVERRIDE_MODULE => 'GYM_OVERRIDED_MODULE');
		}
		// Define overrides if needed
		if (empty($this->override[$mode][$module])) {
			foreach ($this->override_type as $_type) {
				$this->override[$mode][$module][$_type] = $this->_set_override($mode, $module, $_type);
			}
			$this->override[$mode][$module][$mode] = $this->gym_config[$mode . '_override'];
		}
		$override = $overrided = $level = '';
		if ($mode != 'main') {
			// We are setting up an output type
			if ($module != 'main') {
				$option = str_replace($mode . "_$module" . '_', '', $config_key);
				$level = OVERRIDE_MODULE;
			} else { // we are setting up a module
				$option = str_replace($mode . '_', '', $config_key);
				$level = OVERRIDE_OTYPE;
			}
		} else { // Main level
			$option = str_replace('gym_', '', $config_key);
			$level = OVERRIDE_GLOBAL;
		}
		if (in_array($action, $this->override_type)) { // Main overrides
			$override = $this->override[$mode][$module][$action];
		} else {
			$override = $this->override[$mode][$module][$mode];
		}
		$overrided = $this->_overriden_type($mode, $module, $option, $override, $level);
		if ($overrided['override'] == $level) {
			return '<br/><i style="color:green">' . $this->safe_lang('GYM_COULD_OVERRIDE') . '</i>';
		} else {
			$message = '';
			// Check if we should show the value used
			if ($overrided['used_value'] !== 'current') {
				// Check var type
				if ($vars['validate'] == 'bool') {
					$message = $overrided['used_value'] ? $user->lang['YES'] : $user->lang['NO'];

				} elseif (($vars['validate'] == 'int' || $vars['validate'] == 'string') && !@$vars['method'] == 'select_string') {
					$message = htmlspecialchars($overrided['used_value']);
					$message = $message == '' ? $user->lang['GYM_OVERRIDED_VALUE_NOTHING'] : $message;

				} elseif (@$vars['method'] == 'select_string') {
					$select_ary = $this->gym_modules_acp[$mode][$module][$action]['select'][$config_key];
					$message = $this->safe_lang($select_ary[$overrided['used_value']]);
				}
			}
			$message = !empty($message) ? '<br/>' . $user->lang['GYM_OVERRIDED_VALUE'] . $message : '';
			return '<br/><i style="color:red">' . $this->safe_lang($override_msg[$overrided['override']]) . $message . '</i>';
		}
	}

	/**
	* _overriden_type()
	* helper for is_overriden()
	*/
	function _overriden_type($mode, $module, $option, $override, $level ) {
		// module level
		if ( ($override == OVERRIDE_MODULE) && @isset($this->gym_config[$mode . "_$module" . "_$option"])) {
			return array('override' => OVERRIDE_MODULE);
		}
		// Output type level
		if ( $override != OVERRIDE_GLOBAL && @isset($this->gym_config[$mode . "_$option"])) {
			return array('override' => OVERRIDE_OTYPE, 'used_value' => ($level != OVERRIDE_OTYPE ? $this->gym_config[$mode . "_$option"] : 'current' ) );
		}
		// Global level
		if (isset($this->gym_config["gym_$option"])) {
			return  array('override' => OVERRIDE_GLOBAL, 'used_value' => ($level != OVERRIDE_GLOBAL ? $this->gym_config["gym_$option"] : 'current' ) );
		} elseif ( @isset($this->gym_config[$mode . "_$option"]) ) {
			return  array('override' => OVERRIDE_OTYPE, 'used_value' => ($level != OVERRIDE_OTYPE ? $this->gym_config[$mode . "_$option"] : 'current' ) );
		} elseif ( @isset($this->gym_config[$mode . "_$module" . "_$option"]) ) {
			return  array('override' => OVERRIDE_MODULE, 'used_value' => ($level != OVERRIDE_MODULE ? $this->gym_config[$mode . "_$module" . "_$option"] : 'current' ) );
		} else {
			return null;
		}
	}
	/**
	* _set_override()
	* helper for is_overriden()
	*/
	function _set_override($mode, $module, $type) {
		$main_key = 'gym_override_' . $type;
		$mode_key = $mode . '_override_' . $type;
	//	$module_key = $module != 'main' ? $mode . '_' . $module . '_override_' . $type : 0;
		if ($this->gym_config['gym_override']) {
			return ($this->gym_config[$main_key] != OVERRIDE_GLOBAL)  ? ($this->gym_config[$mode_key] != OVERRIDE_GLOBAL ? $this->gym_config[$mode_key] : $this->gym_config[$main_key]) : OVERRIDE_GLOBAL;
		} else {
			return $this->gym_config[$mode_key];
		}
	}
	/**
	* safe_lang($lang_key)
	* Safely set a lang key
	*/
	function safe_lang($lang_key) {
		global $user;
		return isset($user->lang[$lang_key]) ? $user->lang[$lang_key] : htmlspecialchars($lang_key);
	}
	/**
	* back_to_prev()
	* Generate back link for acp pages
	*/
	function back_to_prev() {
		global $user;
		return '<br /><br /><a href="' . $this->u_action . '&amp;maction=' . $this->maction . '&amp;action=' . $this->action . '&amp;module=' . $this->module . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
	}
	/**
	*  module_custom_select($value, $key) to grabb custom select function from modules
	* In the $display_vars array :
	* 'gym_config_key' => array('lang' => 'LANG_TITLE', 'validate' => 'int|bool|string', 'type' => 'custom', 'method' => 'module_custom_select', 'explain' => true),
	* Will build the custom select unsing the module's select_gym_config_key($value, $key) method.
	*/
	function module_custom_select($value, $key) {
		global $phpbb_root_path, $phpEx;
		$method = 'select_' . $key;
		$module_file = $phpbb_root_path . 'gym_sitemaps/acp/modules/' . $this->mode . '_' . $this->module . '.' . $phpEx;
		if ( file_exists($module_file) ) {
			include_once($module_file);
			if (class_exists($module_class)) {
				$gym_module = new $module_class($this);
				if ( method_exists($gym_module, $method)) {
					return $gym_module->$method($value, $key);
				}
			}
		}
		// Error
	}
	/**
	*  select_multiple_string($value, $key) custom select string
	*/
	function select_multiple_string($value, $key) {
		global $phpbb_seo;
		$select_ary = $this->gym_modules_acp[$this->mode][$this->module][$this->action]['select'][$key];
		$size = min(12,count($select_ary));
		$html = '<select multiple="multiple" id="' . $key . '" name="multiple_' . $key . '[]" size="' . $size . '">';
		foreach ($select_ary as $sel_key => $sel_data) {
			if (empty($sel_data['disabled'])) {
				$selected = @array_search($sel_key, @$this->new_config[$key]) !== false ? 'selected="selected"' : '';
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
	*  select_string($value, $key) custom select string
	*/
	function select_string($value, $key) {
		global $phpbb_seo;
		$select_ary = $this->gym_modules_acp[$this->mode][$this->module][$this->action]['select'][$key];
		$html = '';
		foreach ($select_ary as $sel_key => $sel_lang) {
			$selected = ($sel_key == @$this->new_config[$key]) ? ' selected="selected"' : '';
			$sel_title = $this->safe_lang($sel_lang);
			$html .= '<option value="' . $sel_key . '"' . $selected . '>' . $sel_title . '</option>';
		}
		return $html;
	}
	/**
	*  validate_num($value, $key, $num) validate num, 2.3255
	*/
	function validate_num($value, $key, $float = 1, $min = 0 , $max = 4) {
		$float = $float > 0 ? (int) $float : 0;
		$min = $min > 0 ? $min : 0;
		$max = $max > 0 ? $max : 4;
		$value = ($value >= $min && $value <= $max) ? $value : ($max/2);
		$value = $float > 0 ? sprintf('%.' . $float . 'f', $value) : (int) $value;
		return '<input id="' . $key . '" type="text" size="' . (strlen($max) + $float + 1) . '" maxlength="' . (strlen($max) + $float + 1) . '" name="config[' . $key . ']" value="' . $value . '" />';
	}
	/**
	*  forum_select() // custom forum select setup
	*/
	function forum_select() {
		if (empty($this->dyn_select['forums'])) {
			$this->dyn_select['forums'] = make_forum_select(false, false, true, true, true, false, true);
			foreach($this->dyn_select['forums'] as $f_id => $f_data) {
				$this->dyn_select['forums'][$f_id] = array(
					'title' => $f_data['padding'] . $f_data['forum_name'],
					'disabled' => $f_data['disabled'],
				);
			}
		}
	}
	/**
	* clear_all_cache()
	* Clears all the gym sitemaps cache ( acp modules, module lists and config )
	*/
	function clear_all_cache($option = '') {
		global $phpbb_root_path, $phpEx, $acm_type;
		if ($acm_type !== 'file') {
			global $cache;
			// Apparently, we cannot loop through cached variable using cache class in such case, purge all for now
			$cache->purge();
			return;
		}
		$cache_path = $phpbb_root_path . 'cache/';
		$dir = opendir( $cache_path );
		$action_from_file = '';
		while( ($file = @readdir($dir)) !== false ) {
			if(preg_match('`^data_gym_' . $option . '[a-z0-9_-]+\.' . $phpEx . '$`i', $file)) {
				@unlink($cache_path. $file);
			}
		}
		@closedir($dir);
		return;
	}
	/**
	* remove_cache($cache_type, $file_type)
	* Removes/unlinks config cache file(s)
	*/
	function remove_cache($type = 'config', $mode = '') {
		global $phpbb_root_path, $phpEx, $acm_type;
		if ($type == 'all' || $acm_type !== 'file') {
			$this->clear_all_cache();
			return;
		}
		$RegEx = ($type === 'config') ? '(config|links|auth)' : '';
		$RegEx .= (!empty($mode) && in_array($mode, $this->modes) ? "_$mode" : '');
		$this->clear_all_cache($RegEx);
		return;
	}
	/**
	*  set_phpbb_seo_links Builds links to support threads
	*/
	function set_phpbb_seo_links() {
		global $config, $user, $template;
		$this->support_link['links_en'] = array( 'release' =>  'http://www.phpbb-seo.com/en/gym-sitemaps-rss/google-yahoo-msn-sitemaps-rss-t2734.html', 'support' =>  'http://www.phpbb-seo.com/en/gym-sitemaps-rss/', 'seo_forum' =>  'http://www.phpbb-seo.com/en/', 'subscribe' => 'http://www.phpbb-seo.com/boards/viewtopic.php?t=2734&watch=topic' );
		$this->support_link['links_fr'] = array( 'release' =>  'http://www.phpbb-seo.com/fr/gym-sitemaps-rss/sitemaps-rss-google-yahoo-msn-t3136.html', 'support' =>  'http://www.phpbb-seo.com/fr/gym-sitemaps-rss/', 'seo_forum' =>  'http://www.phpbb-seo.com/fr/', 'subscribe' => 'http://www.phpbb-seo.com/forums/viewtopic.php?t=3136&watch=topic' );
		if (strpos($config['default_lang'], 'fr') !== false ) {
			$this->support_link['release'] = $this->support_link['links_fr']['release'];
			$this->support_link['support'] = $this->support_link['links_fr']['support'];
			$this->support_link['seo_forum'] = $this->support_link['links_fr']['seo_forum'];
			$this->support_link['subscribe'] = $this->support_link['links_fr']['subscribe'];
		} else {
			$this->support_link['release'] = $this->support_link['links_en']['release'];
			$this->support_link['support'] = $this->support_link['links_en']['support'];
			$this->support_link['seo_forum'] = $this->support_link['links_en']['seo_forum'];
			$this->support_link['subscribe'] = $this->support_link['links_en']['subscribe'];
		}
		$this->support_link['register'] = $this->support_link['seo_forum'] . 'profile.php?mode=register';
		$this->support_link['update_msg'] = sprintf($user->lang['ACP_SEO_REGISTER_MSG'], sprintf($user->lang['ACP_SEO_REGISTER_TITLE'],$this->support_link['register'] ), sprintf($user->lang['ACP_SEO_REGISTER_UPDATE'], $this->support_link['subscribe'] ) );
		$user->lang['TRANSLATION_INFO'] .= '<br/><a href="http://www.phpbb-seo.com/" title="' . ( strpos($config['default_lang'], 'fr') !== false  ?  'Optimisation du R&eacute;f&eacute;rencement' : 'Search Engine Optimization') . '">phpBB SEO</a>';
		$this->support_link['release_full'] = '<a href="' . $this->support_link['release'] . '" title="' . $user->lang['ACP_SEO_RELEASE_THREAD'] . '">' . $user->lang['ACP_SEO_RELEASE_THREAD'] . '</a>';
		$this->support_link['support_full'] = '<a href="' . $this->support_link['support'] . '" title="' . $user->lang['ACP_SEO_SUPPORT_FORUM'] . '">' . $user->lang['ACP_SEO_SUPPORT_FORUM'] . '</a>';
		$this->support_link['seo_forum_full'] = '<a href="' . $this->support_link['seo_forum'] . '" title ="phpBB SEO">phpBB SEO</a>';
		$template->assign_vars(array(
			'SUPPORT_LINKS' => '<p>' . $this->support_link['release_full'] . '&nbsp;&bull;&nbsp;' . $this->support_link['support_full'] . '&nbsp;&bull;&nbsp;' . $this->support_link['seo_forum_full'] . '</p>',
			)
		);
		if ($this->mode === 'main' && $this->module === 'main' && $this->action === 'main') {
			$user->lang['MAIN_MAIN_EXPLAIN'] = sprintf( $user->lang['MAIN_MAIN_EXPLAIN'], $this->support_link['release_full'], $this->support_link['support_full'], $this->support_link['seo_forum_full'], $this->support_link['update_msg'] );
		}
		return;
	}
	/**
	*  check_cache_folder Validates the cache folder status
	*/
	function check_cache_folder($cache_dir, $msg = true) {
		global $user;
		$exists = $write = false;
		$cache_msg = '';
		$cache_dir = rtrim($cache_dir, '/');
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
		}
		if ($msg) {
			$exists = ($exists) ? '<b style="color:green">' . $user->lang['SEO_CACHE_FOUND'] . '</b>' : '<b style="color:red">' . $user->lang['SEO_CACHE_NOT_FOUND'] . '</b>';
			$write = ($write) ? '<br/> <b style="color:green">' . $user->lang['SEO_CACHE_WRITABLE'] . '</b>' : (($exists) ? '<br/> <b style="color:red">' . $user->lang['SEO_CACHE_UNWRITABLE'] . '</b>' : '');
			$cache_msg = sprintf($user->lang['SEO_CACHE_STATUS'], $cache_dir) . '<br/>' . $exists . $write;
			return '<br/><br/><b>' . $user->lang['SEO_CACHE_FILE_TITLE'] . ':</b><ul>' . $cache_msg . '</ul><br/>';
		} else {
			return ($exists && $write);
		}
	}
} // End of acp class
?>