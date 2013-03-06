<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_install.php 226 2010-03-01 10:11:29Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/*
 * Based on the phpBB3 install package / www.phpBB.com
 */
define('IN_PHPBB', true);
define('IN_INSTALL', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
@define('GYM_VERSION', '2.0.1');
// Try to override some limits - maybe it helps some...
@set_time_limit(0);
$mem_limit = @ini_get('memory_limit');
if (!empty($mem_limit)) {
	$unit = strtolower(substr($mem_limit, -1, 1));
	$mem_limit = (int) $mem_limit;
	if ($unit == 'k') {
		$mem_limit = floor($mem_limit / 1024);
	} else if ($unit == 'g') {
		$mem_limit *= 1024;
	} else if (is_numeric($unit)) {
		$mem_limit = floor((int) ($mem_limit . $unit) / 1048576);
	}
	$mem_limit = max(128, $mem_limit) . 'M';
} else {
	$mem_limit = '128M';
}
@ini_set('memory_limit', $mem_limit);
include($phpbb_root_path . 'common.' . $phpEx);
// Include essential scripts
require($phpbb_root_path . 'includes/functions_install.' . $phpEx);
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('gym_sitemaps/install');
// Security check
// Circumvent a potential phpbb bug with paths
$redirect = append_sid(generate_board_url() . "/gym_sitemaps/gym_install.$phpEx");
if (!$user->data['is_registered']) {
	login_box($redirect, $user->lang['SEO_LOGIN'],'', false, false);
}
if (!$auth->acl_get('a_')) {
	$user->session_kill(true);
	login_box($redirect, $user->lang['SEO_LOGIN_ADMIN'],'', false, false);
}
if ($user->data['user_type'] != USER_FOUNDER) {
	login_box($redirect, $user->lang['SEO_LOGIN_FOUNDER'],'', false, false);
}
$user->add_lang(array('acp/common', 'acp/board', 'install', 'posting', 'acp/modules'));
$mode = request_var('mode', 'overview');
$sub = request_var('sub', '');
// Set some standard variables we want to force
$config['load_tplcompile']	= '1';
$template->set_custom_template('../adm/style', '../admin');
$template->assign_var('T_TEMPLATE_PATH', '../adm/style');
// the acp template is never stored in the database
$user->theme['template_storedb'] = false;
// Start the installer
$install = new module();
$install->create('install', "gym_install.$phpEx", $mode, $sub);
$install->load();
// Generate the page
$install->page_header();
$install->generate_navigation();
$template->set_filenames(array(
	'body' => $install->get_tpl_name())
);
$install->page_footer();
/**
* @package install
*/
class module {
	var $id = 0;
	var $type = 'install';
	var $module_ary = array();
	var $filename;
	var $module_url = '';
	var $tpl_name = '';
	var $mode;
	var $sub;
	/**
	* Private methods, should not be overwritten
	*/
	function create($module_type, $module_url, $selected_mod = false, $selected_submod = false) {
		global $db, $config, $phpEx, $phpbb_root_path, $user;
		$module = array(
			array(
				'module_type'		=> 'install',
				'module_title'		=> 'OVERVIEW',
				'module_filename'	=> 'overview_gym_sitemaps',
				'module_order'		=> 0,
				'module_subs'		=> array('INTRO', 'LICENSE', 'SUPPORT'),
				'module_stages'		=> '',
				'module_reqs'		=> ''
			),
			array(
				'module_type'		=> 'install',
				'module_title'		=> 'INSTALL_GYM_SITEMAPS',
				'module_filename'	=> 'install_gym_sitemaps',
				'module_order'		=> 1,
				'module_subs'		=> '',
				'module_stages'		=> array('INTRO', 'FINAL'),
				'module_reqs'		=> ''
			),
			array(
				'module_type'		=> 'uninstall',
				'module_title'		=> 'UNINSTALL_GYM_SITEMAPS',
				'module_filename'	=> 'install_gym_sitemaps',
				'module_order'		=> 2,
				'module_subs'		=> '',
				'module_stages'		=> array('INTRO', 'FINAL'),
				'module_reqs'		=> ''
			),
			array(
				'module_type'		=> 'update',
				'module_title'		=> 'UPDATE_GYM_SITEMAPS',
				'module_filename'	=> 'install_gym_sitemaps',
				'module_order'		=> 3,
				'module_subs'		=> '',
				'module_stages'		=> array('INTRO', 'FINAL'),
				'module_reqs'		=> ''
			),
		);
		// Order to use and count further if modules get assigned to the same position or not having an order
		$max_module_order = 1000;
		foreach ($module as $row) {
			// Module order not specified or module already assigned at this position?
			if (!isset($row['module_order']) || isset($this->module_ary[$row['module_order']])) {
				$row['module_order'] = $max_module_order;
				$max_module_order++;
			}
			$this->module_ary[$row['module_order']]['name'] = $row['module_title'];
			$this->module_ary[$row['module_order']]['filename'] = $row['module_filename'];
			$this->module_ary[$row['module_order']]['subs'] = $row['module_subs'];
			$this->module_ary[$row['module_order']]['stages'] = $row['module_stages'];
			if (strtolower($selected_mod) == strtolower($row['module_title'])) {
				$this->id = (int) $row['module_order'];
				$this->filename = (string) $row['module_filename'];
				$this->module_url = (string) $module_url;
				$this->mode = (string) $selected_mod;
				// Check that the sub-mode specified is valid or set a default if not
				if (is_array($row['module_subs'])) {
					$this->sub = strtolower((in_array(strtoupper($selected_submod), $row['module_subs'])) ? $selected_submod : $row['module_subs'][0]);
				} else if (is_array($row['module_stages'])) {
					$this->sub = strtolower((in_array(strtoupper($selected_submod), $row['module_stages'])) ? $selected_submod : $row['module_stages'][0]);
				} else {
					$this->sub = '';
				}
			}
		} // END foreach
	} // END create
	/**
	* Load and run the relevant module if applicable
	*/
	function load($mode = false, $run = true) {
		global $phpbb_root_path, $phpEx;
		if ($run) {
			if (!empty($mode)) {
				$this->mode = $mode;
			}
			$module = $this->filename;
			if (!class_exists($module)) {
				$this->error('Module "' . htmlspecialchars($module) . '" not accessible.', __LINE__, __FILE__);
			}
			$this->module = new $module($this);
			if (method_exists($this->module, 'main')) {
				$this->module->main($this->mode, $this->sub);
			}
		}
	}
	/**
	* Output the standard page header
	*/
	function page_header() {
		if (defined('HEADER_INC')) {
			return;
		}
		define('HEADER_INC', true);
		global $template, $user, $stage, $phpbb_root_path;
		$template->assign_vars(array(
			'L_INSTALL_PANEL'	=> $user->lang['SEO_INSTALL_PANEL'],
			'L_SKIP'		=> $user->lang['SKIP'],
			'PAGE_TITLE'		=> $this->get_page_title(),
			'T_IMAGE_PATH'		=> $phpbb_root_path . 'adm/images/',
			'S_CONTENT_DIRECTION' 	=> $user->lang['DIRECTION'],
			'S_CONTENT_ENCODING' 	=> 'UTF-8',
			'S_USER_LANG'		=> $user->lang['USER_LANG'],
			)
		);
		header('Content-type: text/html; charset=UTF-8');
		header('Cache-Control: private, no-cache="set-cookie"');
		header('Expires: 0');
		header('Pragma: no-cache');
		return;
	}
	/**
	* Output the standard page footer
	*/
	function page_footer() {
		global $db, $template, $phpbb_seo;
		$template->display('body');
		// Close our DB connection.
		if (!empty($db) && is_object($db)) {
			$db->sql_close();
		}
		exit;
	}
	/**
	* Returns desired template name
	*/
	function get_tpl_name() {
		return $this->module->tpl_name . '.html';
	}
	/**
	* Returns the desired page title
	*/
	function get_page_title() {
		global $user;
		if (!isset($this->module->page_title)) {
			return '';
		}
		return (isset($user->lang[$this->module->page_title])) ? $user->lang[$this->module->page_title] : $this->module->page_title;
	}
	/**
	* Generate an HTTP/1.1 header to redirect the user to another page
	* This is used during the installation when we do not have a database available to call the normal redirect function
	* @param string $page The page to redirect to relative to the installer root path
	*/
	function redirect($page) {
		$server_name = (!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME');
		$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');
		$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

		$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
		if (!$script_name) {
			$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
		}
		// Replace backslashes and doubled slashes (could happen on some proxy setups)
		$script_name = str_replace(array('\\', '//'), '/', $script_name);
		$script_path = trim(dirname($script_name));
		$url = (($secure) ? 'https://' : 'http://') . $server_name;
		if ($server_port && (($secure && $server_port <> 443) || (!$secure && $server_port <> 80))) {
			$url .= ':' . $server_port;
		}
		$url .= $script_path . '/' . $page;
		header('Location: ' . $url);
		exit;
	}
	/**
	* Generate the navigation tabs
	*/
	function generate_navigation() {
		global $user, $template, $phpEx;
		if (is_array($this->module_ary)) {
			@ksort($this->module_ary);
			foreach ($this->module_ary as $cat_ary) {
				$cat = $cat_ary['name'];
				$l_cat = (!empty($user->lang['CAT_' . $cat])) ? $user->lang['CAT_' . $cat] : preg_replace('#_#', ' ', $cat);
				$cat = strtolower($cat);
				$url = $this->module_url . "?mode=$cat";
				if ($this->mode == $cat) {
					$template->assign_block_vars('t_block1', array(
						'L_TITLE'		=> $l_cat,
						'S_SELECTED'	=> true,
						'U_TITLE'		=> $url,
					));
					if (is_array($this->module_ary[$this->id]['subs'])) {
						$subs = $this->module_ary[$this->id]['subs'];
						foreach ($subs as $option) {
							$l_option = (!empty($user->lang['SUB_' . $option])) ? $user->lang['SUB_' . $option] : preg_replace('#_#', ' ', $option);
							$option = strtolower($option);
							$url = $this->module_url . '?mode=' . $this->mode . "&amp;sub=$option";
							$template->assign_block_vars('l_block1', array(
								'L_TITLE'		=> $l_option,
								'S_SELECTED'	=> ($this->sub == $option),
								'U_TITLE'		=> $url,
							));
						}
					}
					if (is_array($this->module_ary[$this->id]['stages'])) {
						$subs = $this->module_ary[$this->id]['stages'];
						$matched = false;
						foreach ($subs as $option) {
							$l_option = (!empty($user->lang['STAGE_' . $option])) ? $user->lang['STAGE_' . $option] : preg_replace('#_#', ' ', $option);
							$option = strtolower($option);
							$matched = ($this->sub == $option) ? true : $matched;

							$template->assign_block_vars('l_block2', array(
								'L_TITLE'		=> $l_option,
								'S_SELECTED'	=> ($this->sub == $option),
								'S_COMPLETE'	=> !$matched,
							));
						}
					}
				} else {
					$template->assign_block_vars('t_block1', array(
						'L_TITLE'		=> $l_cat,
						'S_SELECTED'	=> false,
						'U_TITLE'		=> $url,
					));
				}
			}
		}
	}
	/**
	* Output an error message
	* If skip is true, return and continue execution, else exit
	*/
	function error($error, $line = '', $file = '', $skip = false, $title = '') {
		global $user, $db, $template, $phpbb_seo;
		$title = !empty($title) ? $title : $user->lang['INST_ERR_FATAL'];
		$file = !empty($file) ? basename($file) . ' [ ' . $line . ' ]' : '';
		if ($skip) {
			$template->assign_block_vars('checks', array(
				'S_LEGEND'	=> true,
				'LEGEND'	=> $user->lang['INST_ERR'],
			));
			$template->assign_block_vars('checks', array(
				'TITLE'		=> basename($file) . ' [ ' . $line . ' ]',
				'RESULT'	=> '<b style="color:red">' . $error . '</b>',
			));
			return;
		}
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">';
		echo '<head>';
		echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
		echo '<title>' . $title . '</title>';
		echo '<link href="../adm/style/admin.css" rel="stylesheet" type="text/css" media="screen" />';
		echo '</head>';
		echo '<body id="errorpage">';
		echo '<div id="wrap">';
		echo '	<div id="page-header">';
		echo '	</div>';
		echo '	<div id="page-body">';
		echo '		<div id="acp">';
		echo '		<div class="panel">';
		echo '			<span class="corners-top"><span></span></span>';
		echo '			<div id="content">';
		echo '				<h1>' . $title . '</h1>';
		echo '				<p>' . $file . '</p>' . "\n";
		echo '				<p><b>' . $error . "</b></p>\n";
		echo '			</div>';
		echo '			<span class="corners-bottom"><span></span></span>';
		echo '		</div>';
		echo '		</div>';
		echo '	</div>';
		echo '	<div id="page-footer">';
		echo '		Powered by phpBB &copy; 2000, 2002, 2005, 2007, 2008 <a href="http://www.phpbb.com/">phpBB Group</a>';
		echo '	</div>';
		echo '</div>';
		echo '</body>';
		echo '</html>';
		if (!empty($db) && is_object($db)) {
			$db->sql_close();
		}
		exit;
	}
	/**
	* Output an error message for a database related problem
	* If skip is true, return and continue execution, else exit
	*/
	function db_error($error, $sql, $line, $file, $skip = false) {
		global $user, $db, $template;
		if ($skip) {
			$template->assign_block_vars('checks', array(
				'S_LEGEND'	=> true,
				'LEGEND'	=> $user->lang['INST_ERR_FATAL'],
			));
			$template->assign_block_vars('checks', array(
				'TITLE'		=> basename($file) . ' [ ' . $line . ' ]',
				'RESULT'	=> '<b style="color:red">' . $error . '</b><br />&#187; SQL:' . $sql,
			));
			return;
		}
		$template->set_filenames(array(
			'body' => 'install_error.html')
		);
		$this->page_header();
		$this->generate_navigation();
		$template->assign_vars(array(
			'MESSAGE_TITLE'		=> $user->lang['INST_ERR_FATAL_DB'],
			'MESSAGE_TEXT'		=> '<p>' . basename($file) . ' [ ' . $line . ' ]</p><p>SQL : ' . $sql . '</p><p><b>' . $error . '</b></p>',
		));
		// Rollback if in transaction
		if ($db->transaction) {
			$db->sql_transaction('rollback');
		}
		$this->page_footer();
	}
}
/**
* Installation Tabs
*/
class install_gym_sitemaps extends module {
	var $errors = array();
	var $uninst_prefix = '';
	var $modrtype_lang = array();
	var $action_types = array();
	var $version = '(not set)';
	var $old_config = array();
	var $config_report = array();
	function install_gym_sitemaps(&$p_master) {
		global $user, $phpbb_seo, $config, $phpbb_root_path, $phpEx, $_action_types;
		$this->p_master = &$p_master;
		$this->version = GYM_VERSION;
		require_once($phpbb_root_path . 'gym_sitemaps/includes/gym_common.' . $phpEx);
		// For Compatibility with the phpBB SEO mod rewrites
		if (empty($phpbb_seo)) {
			require_once($phpbb_root_path . 'gym_sitemaps/includes/phpbb_seo_class_light.' . $phpEx);
			$phpbb_seo = new phpbb_seo();
			define('STARTED_LIGHT', true);
		}
		$this->action_types = $_action_types;
		$this->modrtype_lang = set_phpbb_seo_links();
	}
	function main($mode, $sub) {
		global $user, $template, $phpbb_root_path, $phpbb_seo, $phpEx, $cache, $db;
		switch ($mode) {
		case 'install_gym_sitemaps':
			$this->uninst_prefix = '';
			break;
		case 'update_gym_sitemaps':
			obtain_gym_config('main', $this->old_config);
			if (!empty($this->old_config)) {
				$this->uninst_prefix = 'UPDATE_';
			} else {
				$this->p_master->error($user->lang['SEO_ERROR_NOTINSTALLED']. '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $this->p_master->module_url . '">', '</a>'), '', '', false, $user->lang['SEO_ERROR_INFO']);
				exit;
			}
			break;
		case 'uninstall_gym_sitemaps':
			$this->uninst_prefix = 'UN_';
			break;
		}
		switch ($sub) {
			case 'intro':
				$this->page_title = $user->lang['SUB_INTRO'];
				$template->assign_vars(array(
					'TITLE'			=> $user->lang[$this->uninst_prefix . 'SEO_INSTALL_INTRO'],
					'BODY'			=> sprintf($user->lang[$this->uninst_prefix . 'SEO_INSTALL_INTRO_BODY'], @$this->modrtype_lang['link'], $this->version),
					'L_SUBMIT'		=> $user->lang[$this->uninst_prefix . 'SEO_INSTALL'],
					'S_LANG_SELECT'	=> '',
					'U_ACTION'		=> $this->p_master->module_url . "?mode=$mode&amp;sub=final",
				));
			break;
			case 'final':
				if ($mode != 'uninstall_gym_sitemaps') {
					$update = $mode == 'update_gym_sitemaps' ? true : false;
					if(!$update) {
						$this->add_modules($mode, $sub);
						$this->install_tables($mode);
					}
					$gym_modules = $gym_modules_acp = array();
					foreach ($this->action_types as $otype) { // List all output types (sitemaps, rss, html, yahoo ...)
						$dir = opendir( $phpbb_root_path . 'gym_sitemaps/acp' );
						while( ($file = readdir($dir)) !== FALSE ) { // Look for available mocules
							if(preg_match('`^' . $otype . '_([a-z0-9_-]+)\.' . $phpEx . '$`i', $file, $matches)) {
								$type_module = trim(str_replace('.' . $phpEx , '' ,$file), "/");
								$_module = str_replace($otype . '_', '', $type_module);
								if ($matches[1] === 'main' || (file_exists($phpbb_root_path . 'gym_sitemaps/modules/' .  $file) ) ) {
									$gym_modules[$otype][$_module] = $type_module;
								}
							}
						}
						closedir($dir);
						foreach ($gym_modules[$otype] as $_module => $type_module) { // List all available modules
							$module_file = $phpbb_root_path . 'gym_sitemaps/acp/' . $type_module . '.' . $phpEx;

							if ( file_exists($module_file) ) {
								include_once($module_file);
								if (class_exists($type_module)) {
									$gym_module = new $type_module($this);
									if ( method_exists($gym_module, 'acp_module')) { // Looks like we match
										$gym_modules_acp[$otype][$_module] = $gym_module->acp_module();
										foreach ($gym_modules_acp[$otype][$_module]['info']['actions'] as $module_action) { // list the module's options sets
											foreach ($gym_modules_acp[$otype][$_module][$module_action]['default'] as $module_config => $default_value ) { // In the end list possible options for this module for this module's options set
												if (!isset($this->old_config[$module_config])) {
													// Update config
													set_gym_config($module_config, $default_value, $otype, $this->old_config);
													$this->config_report[] = "SET <b>$module_config</b> to $default_value";
												}
											}
										}
										if (!isset($this->old_config[$type_module . '_installed'])) {
											// Set the module as installed
											set_gym_config($type_module . '_installed', 1, 'main', $this->old_config);
											$this->config_report[] = "ACTIVATED <b>$type_module module</b>";
										}
									}
								}
							}
						}
					}
				} else {
					$this->remove_modules($mode, $sub);
					$this->install_tables($mode);
				}
				if (@method_exists($cache, 'purge')) {
					$cache->purge();
				}
				$this->final_stage($mode, $sub);
			break;
		}
		$this->tpl_name = 'install_install';
	}
	/**
	* Populate the module tables
	*/
	function add_modules($mode, $sub) {
		global $db, $user, $phpbb_root_path, $phpEx;
		include_once($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);
		$_module = new acp_modules();
		if ( $this->get_module_id('ACP_GYM_SITEMAPS')  > 0 ) {
			$url_mod = !empty($sub) ? '?mode=' . $mode : '';

			$this->p_master->error(sprintf($user->lang['SEO_ERROR_INSTALLED'], $user->lang['ACP_CAT_PHPBB_SEO'] ) . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $this->p_master->module_url . $url_mod . '">', '</a>'), '', '', false, $user->lang['SEO_ERROR_INFO']);
		}
		$module_classes = array('acp');
		// Add categories
		foreach ($module_classes as $module_class) {
			$categories = array();
			// Set the module class
			$_module->module_class = $module_class;
			foreach ($this->module_categories[$module_class] as $cat_name => $subs) {
				$module_data = array(
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'		=> 0,
					'module_class'		=> $module_class,
					'module_langname'	=> $cat_name,
					'module_mode'		=> '',
					'module_auth'		=> '',
				);
				if ( $this->get_module_id('ACP_CAT_PHPBB_SEO')  < 1 ) {
					// Add category
					$_module->update_module_data($module_data, true);
				} else {
					$module_data['module_id'] = $this->check_module_id('ACP_CAT_PHPBB_SEO');
				}
				// Check for last sql error happened
				if ($db->sql_error_triggered) {
					$error = $db->sql_error($db->sql_error_sql);
					$this->p_master->db_error($error['message'], $db->sql_error_sql, __LINE__, __FILE__);
				}
				$categories[$cat_name]['id'] = (int) $module_data['module_id'];
				$categories[$cat_name]['parent_id'] = 0;
				// Create sub-categories...
				if (is_array($subs)) {
					foreach ($subs as $level2_name) {
						$module_data = array(
							'module_basename'	=> '',
							'module_enabled'	=> 1,
							'module_display'	=> 1,
							'parent_id'			=> (int) $categories[$cat_name]['id'],
							'module_class'		=> $module_class,
							'module_langname'	=> $level2_name,
							'module_mode'		=> '',
							'module_auth'		=> '',
						);
						$_module->update_module_data($module_data, true);
						// Check for last sql error happened
						if ($db->sql_error_triggered) {
							$error = $db->sql_error($db->sql_error_sql);
							$this->p_master->db_error($error['message'], $db->sql_error_sql, __LINE__, __FILE__);
						}
						$categories[$level2_name]['id'] = (int) $module_data['module_id'];
						$categories[$level2_name]['parent_id'] = (int) $categories[$cat_name]['id'];
					}
				}
			}
			// Get the modules we want to add... returned sorted by name
			$module_info = $_module->get_module_infos('gym_sitemaps', $module_class);
			foreach ($module_info as $module_basename => $fileinfo) {
				foreach ($fileinfo['modes'] as $module_mode => $row) {
					foreach ($row['cat'] as $cat_name) {
						if (!isset($categories[$cat_name])) {
							continue;
						}
						$module_data = array(
							'module_basename'	=> $module_basename,
							'module_enabled'	=> 1,
							'module_display'	=> (isset($row['display'])) ? (int) $row['display'] : 1,
							'parent_id'			=> (int) $categories[$cat_name]['id'],
							'module_class'		=> $module_class,
							'module_langname'	=> $row['title'],
							'module_mode'		=> $module_mode,
							'module_auth'		=> $row['auth'],
						);
						$_module->update_module_data($module_data, true);
						// Check for last sql error happened
						if ($db->sql_error_triggered) {
							$error = $db->sql_error($db->sql_error_sql);
							$this->p_master->db_error($error['message'], $db->sql_error_sql, __LINE__, __FILE__);
						}
					}
				}
			}
		}
	}
	/**
	* remove_modules
	*/
	function remove_modules($mode, $sub) {
		global $db, $user, $phpbb_root_path, $phpEx;
		include_once($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);
		$_module = new acp_modules();
		// Set the module class
		$module_classes = array_keys($this->module_categories);
		$_module->u_action = "phpbb_seo_install.$phpEx";
		$cat_module_data = array();
		$module_data = array();
		$delete_module_data = array();
		foreach ($module_classes as $module_class) {
			$_module->module_class = $module_class;
			foreach ($this->module_categories[$module_class] as $cat_name => $subs) {
				// If the cat is already uninstalled break for now
				if ( $this->get_module_id($cat_name) < 1 ) {
					$url_mod = !empty($this->sub) ? '?mode=' . $this->mode : '';
					$this->p_master->error(sprintf($user->lang['SEO_ERROR_UNINSTALLED'], $user->lang[$cat_name] ). '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $this->p_master->module_url . $url_mod . '">', '</a>'), '', '', false, $user->lang['SEO_ERROR_INFO']);
				}
				$cat_module_data[$cat_name] = array(
					'module_id'		=> $this->check_module_id($cat_name),
					'module_basename'	=> '',
					'module_enabled'	=> 1,
					'module_display'	=> 1,
					'parent_id'		=> 0,
					'module_class'		=> $module_class,
					'module_langname'	=> $cat_name,
					'module_mode'		=> '',
					'module_auth'		=> '',
				);
				if (is_array($subs)) {
					foreach ($subs as $sub_cat) {
						$sub_cat_module_data[$sub_cat] = array(
							'module_id'		=> $this->check_module_id($sub_cat),
							'module_basename'	=> '',
							'module_enabled'	=> 1,
							'module_display'	=> 1,
							'parent_id'		=> (int) $cat_module_data[$cat_name]['module_id'],
							'module_class'		=> $module_class,
							'module_langname'	=> $sub_cat,
							'module_mode'		=> '',
							'module_auth'		=> '',
						);
						$branch = $_module->get_module_branch($sub_cat_module_data[$sub_cat]['module_id'],'children', 'descending', false);
						if (sizeof($branch)) {
							foreach ($branch as $module) {
								$error = $_module->delete_module($module['module_id']);
								if (!sizeof($error)) {
									$_module->remove_cache_file();
									$delete_module_data[$module['module_id']] = $module['module_langname'] . ' - id : ' . $module['module_id'];
								} else {
									$this->errors[] = implode(' ', $error);
								}
							} // End modules
						}
						if (!sizeof($this->errors)) {
							$error = $_module->delete_module($sub_cat_module_data[$sub_cat]['module_id']);
							if (!sizeof($error)) {
								$_module->remove_cache_file();
								$delete_module_data[$sub_cat_module_data[$sub_cat]['module_id']] = $sub_cat_module_data[$sub_cat]['module_langname'] . ' - id : ' . $sub_cat_module_data[$sub_cat]['module_id'];
							} else {
								$this->errors[] = implode(' ', $error);
							}
						}
					}
				} // End sub categories
				if (!sizeof($this->errors)) {
				 	$branch = $_module->get_module_branch($cat_module_data[$cat_name]['module_id'],'children', 'descending', false);
					if (empty($branch)) {
						$error = $_module->delete_module($cat_module_data[$cat_name]['module_id']);
					}
					if (!sizeof($error)) {
						$_module->remove_cache_file();
						$delete_module_data[$cat_module_data[$cat_name]['module_id']] = $cat_module_data[$cat_name]['module_langname'] . ' - id : ' . $cat_module_data[$cat_name]['module_id'];
					} else {
						$this->errors[] = implode(' ', $error);
					}
				}
			} // End categories
		} // End classes
		return;
	}
	/**
	* install_tables
	*/
	function install_tables($mode) {
		global $db;
		if ( $mode === 'install_gym_sitemaps') {
			//$sql = "DROP TABLE IF EXISTS " . GYM_CONFIG_TABLE;
			//$db->sql_query($sql);
			$sql = array();
			switch( $db->sql_layer ) {
				case 'oracle':
					$sql[] = "CREATE TABLE " . GYM_CONFIG_TABLE . " (
						config_name varchar(255) DEFAULT '',
						config_value varchar(255) DEFAULT '',
						config_type varchar(10) DEFAULT 'gym' NOT NULL,
						CONSTRAINT pk_gym_config PRIMARY KEY (config_name)
					)";
					$sql[] = "CREATE INDEX gym_config_type ON " . GYM_CONFIG_TABLE . " (config_type)";
					break;
				case 'firebird':
					$sql[] = "CREATE TABLE " . GYM_CONFIG_TABLE . " (
						config_name VARCHAR(255) CHARACTER SET NONE DEFAULT '' NOT NULL,
						config_value VARCHAR(255) CHARACTER SET UTF8 DEFAULT '' NOT NULL COLLATE UNICODE,
						config_type VARCHAR(10) DEFAULT 'gym' NOT NULL
					)";
					$sql[] = "ALTER TABLE " . GYM_CONFIG_TABLE . " ADD PRIMARY KEY (config_name)";
					$sql[] = "CREATE INDEX gym_config_type ON " . GYM_CONFIG_TABLE . "(config_type)";
					break;
				case 'sqlite':
					$sql[] = "CREATE TABLE " . GYM_CONFIG_TABLE . " (
						config_name varchar(255) DEFAULT '',
						config_value varchar(255) DEFAULT '',
						config_type varchar(10) DEFAULT 'gym',
						PRIMARY KEY (config_name)
					)";
					$sql[] = "CREATE INDEX gym_config_type ON " . GYM_CONFIG_TABLE . " (config_type)";
					break;
				case 'postgres':
					$sql[] = "CREATE TABLE " . GYM_CONFIG_TABLE . " (
						config_name varchar(255) DEFAULT '' NOT NULL,
						config_value varchar(255) DEFAULT '' NOT NULL,
						config_type varchar(10) DEFAULT 'gym' NOT NULL,
						PRIMARY KEY (config_name)
					)";
					$sql[] = "CREATE INDEX gym_config_type ON " . GYM_CONFIG_TABLE . " (config_type)";
					break;
				case 'mssql':
				case 'mssql_odbc':
					$sql[] = "CREATE TABLE [" . GYM_CONFIG_TABLE . "] (
							[config_name] [varchar] (255) DEFAULT ('') NOT NULL ,
							[config_value] [varchar] (255) DEFAULT ('') NOT NULL ,
							[config_type] [varchar] (10) DEFAULT ('') NOT NULL
						) ON [PRIMARY] ";
					$sql[] = "ALTER TABLE [" . GYM_CONFIG_TABLE . "] WITH NOCHECK ADD
							CONSTRAINT [PK_gym_config] PRIMARY KEY  CLUSTERED
							(
								[config_name]
							)  ON [PRIMARY] ";
					$sql[] = "CREATE  INDEX [config_type] ON [" . GYM_CONFIG_TABLE . "]([config_type]) ON [PRIMARY] ";
					break;
				case 'mysql':
				case 'mysql4':
				case 'mysqli':
				default:
					$sqlt = "CREATE TABLE " . GYM_CONFIG_TABLE . " (
						config_name varchar(255) DEFAULT '' NOT NULL,
						config_value varchar(255) DEFAULT '' NOT NULL,
						config_type varchar(10) DEFAULT 'gym' NOT NULL,
						PRIMARY KEY (config_name),
						KEY config_type (config_type)
					)";
					if ($db->sql_layer == 'mysqli' || version_compare($db->sql_server_info(true), '4.1.3', '>=')) {
						$sqlt .= ' CHARACTER SET `utf8` COLLATE `utf8_bin`';
					}
					$sql[] = $sqlt;
					break;
			}
			foreach ($sql as $query ) {
				$db->sql_query($query);
			}
		} else {
			$sql = "DROP TABLE IF EXISTS " . GYM_CONFIG_TABLE;
			$db->sql_query($sql);
		}
	}
	/**
	* check_module_id by title
	*/
	function check_module_id($title) {
		global $user;
		if ( $module_id = $this->get_module_id($title)) {
			return $module_id;
		} else {
			$url_mod = !empty($this->sub) ? '?mode=' . $this->mode : '';
			$this->p_master->error(sprintf($user->lang['SEO_ERROR_ID'], $title ) . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $this->p_master->module_url . $url_mod . '">', '</a>'), '', '', false, $user->lang['SEO_ERROR_INFO']);
		}
	}
	/**
	* get_module_id by title
	*/
	function get_module_id($title) {
		global $db, $user, $phpEx;
		$sql = 'SELECT module_id
			FROM ' . MODULES_TABLE . "
			WHERE module_langname = '" . $db->sql_escape($title) . "'";
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		if ($row['module_id'] > 1) {
			return intval($row['module_id']);
		}
		return 0;
	}
	/**
	* Sends an email to the board administrator with their password and some useful links
	*/
	function final_stage($mode, $sub) {
		global $auth, $config, $db, $user, $template, $user, $phpbb_root_path, $phpEx, $phpbb_seo, $cache;
		$update_info = '';
		if (!sizeof($this->errors) ) {
			if ($mode != 'uninstall_gym_sitemaps') {
				set_gym_config('gym_version', $this->version, 'main', $this->old_config);
				$this->config_report[] = "SET <b>gym_version</b> to $this->version";
				set_config('gym_installed', 1);
			} else {
				set_config('gym_installed', 0);
			}
			add_log('admin', 'SEO_LOG_' . strtoupper($mode), $this->version );
		} else {
			set_config('gym_installed', 0);
			add_log('admin', 'SEO_LOG_' . strtoupper($mode) . '_FAIL', $this->errors);
			$cache->purge();
			$this->p_master->error($user->lang['SEO_ERROR_INSTALL'] . '<br/><pre>' . implode('<br/>', $this->errors) . '</pre>', __LINE__, __FILE__);
		}
		$cache->purge();
		$this->page_title = $user->lang['STAGE_FINAL'];
		if (  $mode != 'uninstall_gym_sitemaps' ) {
			if ($mode == 'update_gym_sitemaps') {
				$key = 'UPDATE';
				$lang_key = strpos($user->lang_name, 'fr') !== false ? 'FR' : '';
				if ($update_infos = @file("./docs/update_from_last$lang_key.txt")) {
					foreach ($update_infos as $line) {
						$line = str_replace(array("\r", "\n"), '', utf8_htmlspecialchars(is_utf8($line) ? $line : utf8_recode($line, 'iso-8859-1')));
						$update_info .= (preg_match('`^#`', $line) ? "<b style=\"color:blue;\">$line</b>" : $line) . '<br/>';
					}
				}
			} else {
				$key = 'INSTALL';
			}
			$submit_action = append_sid($phpbb_root_path . 'adm/index.' . $phpEx . '?sid=' . $user->session_id);
			$title = $user->lang['SEO_INSTALL_CONGRATS'];
			$body =  sprintf($user->lang["SEO_{$key}_CONGRATS_EXPLAIN"], $this->modrtype_lang['link'], $this->version) . '<br/>' . implode('<br/>', $this->config_report) . "<br/><br/><hr/><pre>$update_info</pre>";
		} else {
			$submit_action = append_sid($phpbb_root_path . 'index.' . $phpEx);
			$title = $user->lang['UN_SEO_INSTALL_CONGRATS'];
			$body = sprintf($user->lang['UN_SEO_INSTALL_CONGRATS_EXPLAIN'], $this->modrtype_lang['link'], $this->version);
		}
		$template->assign_vars(array(
			'TITLE'		=> $title,
			'BODY'		=> $body,
			'L_SUBMIT'	=> $user->lang['SEO_FINAL_' . strtoupper($mode)],
			'U_ACTION'	=> $submit_action,
		));
	}
	var $module_categories = array(
		'acp'	=> array(
			'ACP_CAT_PHPBB_SEO'		=> array(
				'ACP_GYM_SITEMAPS',
			),
		),
	);
}
/**
* is_utf8($string)
* Borrowed from php.net : http://www.php.net/mb_detect_encoding (detectUTF8)
*/
function is_utf8($string) {
	// non-overlong 2-byte|excluding overlongs|straight 3-byte|excluding surrogates|planes 1-3|planes 4-15|plane 16
	return preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF] |\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string);
}
function set_phpbb_seo_links() {
	global $user, $config;
	$modinfo_lang = array();
	$modinfo_lang['title'] = $user->lang['CAT_SEO_PREMOD'];
	$modinfo_lang['modlinks_en'] = 'http://www.phpbb-seo.com/en/gym-sitemaps-rss/google-yahoo-msn-sitemaps-rss-t2734.html';
	$modinfo_lang['modlinks_fr'] = 'http://www.phpbb-seo.com/fr/gym-sitemaps-rss/sitemaps-rss-google-yahoo-msn-t3136.html';
	$modinfo_lang['modforumlinks_en'] = 'http://www.phpbb-seo.com/en/gym-sitemaps-rss/';
	$modinfo_lang['modforumlinks_fr'] = 'http://www.phpbb-seo.com/fr/gym-sitemaps-rss/';
	if (strpos($config['default_lang'], 'fr') !== false ) {
		$modinfo_lang['linkurl'] = $modinfo_lang['modlinks_fr'];
		$modinfo_lang['link'] = '<a href="' . $modinfo_lang['modlinks_fr'] . '" title="' . $modinfo_lang['title'] . '" target="_phpBBSEO"><b>' . $modinfo_lang['title'] . '</b></a>';
		$modinfo_lang['forumlinkurl'] = $modinfo_lang['modforumlinks_fr'];
	} else {
		$modinfo_lang['linkurl'] = $modinfo_lang['modlinks_en'];
		$modinfo_lang['link'] = '<a href="' . $modinfo_lang['modlinks_en'] . '" title="' . $modinfo_lang['title'] . '" target="_phpBBSEO"><b>' . $modinfo_lang['title'] . '</b></a>';
		$modinfo_lang['forumlinkurl'] = $modinfo_lang['modforumlinks_en'];
	}
	return $modinfo_lang;
}
/**
* Main Tab - Overview
*/
class overview_gym_sitemaps extends module {
	var $modrtype_lang = array();
	var $version = '(not set)';
	function overview_gym_sitemaps(&$p_master) {
		$this->modrtype_lang = set_phpbb_seo_links();
		$this->p_master = &$p_master;
		$this->version = GYM_VERSION;
	}
	function main($mode, $sub) {
		global $lang, $template, $language, $user, $phpbb_seo;
		switch ($sub) {
			case 'intro' :
				$title = $user->lang['SEO_OVERVIEW_TITLE'];
				$body = sprintf($user->lang['SEO_OVERVIEW_BODY'], $this->version, @$this->modrtype_lang['linkurl'], @$this->modrtype_lang['forumlinkurl']);
			break;
			case 'license' :
				$title = $user->lang['SEO_LICENCE_TITLE'];
				$body = '<p>' . $user->lang['SEO_LICENCE_BODY'] . '</p><br/><hr/>' . implode("<br/>\n", file('./docs/COPYING'));
			break;
			case 'support' :
				$title = $user->lang['SEO_SUPPORT_TITLE'];
				$body = sprintf($user->lang['SEO_SUPPORT_BODY'],$this->modrtype_lang['forumlinkurl'], $this->modrtype_lang['title'], @$this->modrtype_lang['linkurl'] );
			break;
		}
		$this->tpl_name = 'install_main';
		$this->page_title = $title;
		$template->assign_vars(array(
			'TITLE'		=> $title,
			'BODY'		=> $body,

			'S_LANG_SELECT'	=> '',
		));
	}
}
/**
* Quick fix for using the module class outside ACP.
*/
function adm_back_link($u_action) {
	global $user, $install;
	$url_mod = !empty($install->sub) ? '?mode=' . $install->mode : '';
	return '<br /><br /><a href="' . $install->module_url . $url_mod . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
}
?>