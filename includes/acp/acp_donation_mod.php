<?php
/**
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package acp
 * @version $Id: acp_donation_mod.php 9 2008-04-08 19:42:39Z Highway of Life $
 * @copyright (c) 2008 Star Trek Guide Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	// Avoid Hacking attempts.
	exit;
}

/**
* @package acp
*/
class acp_donation_mod
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $user, $template, $config, $phpbb_root_path, $phpEx;

		$user->add_lang('mods/paypal_donation_mod');

		if (!class_exists('paypal_class'))
		{
			$filename = $phpbb_root_path . 'donate/functions_paypal.' . $phpEx;
			if (file_exists($filename))
			{
				global $table_prefix;

				include($filename);
			}
			else
			{
				global $table_prefix;

				include('./.' . $filename);
			}
		}

		if (!class_exists('acp_donation_mod_info'))
		{
			include($phpbb_root_path . 'includes/acp/info/acp_donation_mod.' . $phpEx);

			$module = new acp_donation_mod_info();
			$module->install();
		}

		if ($config['paypal_founder_manage'] && $user->data['user_type'] != USER_FOUNDER)
		{
			trigger_error('NOT_AUTHORISED');
		}

		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_paypal_mod';
		add_form_key($form_key);

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'settings':

				$display_vars = array(
					'title'	=> 'ACP_DONATION_MOD_SETTINGS',
					'vars'	=> array(
						'legend1'				=> 'ACP_DONATION_MOD_SETTINGS',
						'paypal_founder_manage'	=> array('lang' => 'FOUNDER_MANAGE',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_send_pm'		=> array('lang'	=> 'SEND_CONFIRM_PM',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_address'		=> array('lang' => 'PAYPAL_ADDRESS',	'validate' => 'string',	'type' => 'text:40:255', 'explain' => false),
						'paypal_debug'			=> array('lang' => 'PAYPAL_DEBUG',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_logging'		=> array('lang' => 'ERROR_LOGGING',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'paypal_sandbox'		=> array('lang' => 'SANDBOX_TESTING',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
						'paypal_sandbox_address' => array('lang' => 'SANDBOX_ADDRESS',	'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),
						'paypal_supporters_group_id' => array('lang' => 'SUPPORTERS_GROUP',	'validate' => 'int','type' => 'select', 'method' => 'group_select', 'params' => array('{CONFIG_VALUE}', false), 'explain' => true),
						'paypal_style'			=> array('lang' => 'PAYPAL_STYLE',		'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),
						'paypal_default_currency' => array('lang' => 'DEFAULT_CURRENCY','validate' => 'string',	'type' => 'select', 'function' => 'currency_options', 'explain' => true),
						'paypal_donate_minimum'	=> array('lang' => 'DONATE_MINIMUM',	'validate' => 'int',	'type' => 'text:3:5', 'explain' => true),
						'paypal_convert_percentage' => array('lang' => 'CONVERT_PERCENTAGE', 'validate' => 'string', 'type' => 'select', 'method' => 'percentage_select', 'explain' => true),
						'paypal_default_country' => array('lang' => 'DEFAULT_COUNTRY',	'validate' => 'string',	'type' => 'select', 'function' => 'country_options', 'explain' => true),
					)
				);
			break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		// prevent CSRF attacks
		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}

		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($config_name == 'paypal_supporters_group_id' && $submit)
			{
				if (!function_exists('get_group_name'))
				{
					include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
				}

				$this->new_config['paypal_supporters_group'] = get_group_name($config_value);

				set_config('paypal_supporters_group', $this->new_config['paypal_supporters_group']);
			}

			if ($submit)
			{
				set_config($config_name, $config_value);
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_CONFIG_' . strtoupper($mode));

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'donate/acp_paypal_mod';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
			));

			unset($display_vars['vars'][$config_key]);
		}
	}

	/**
	 * Select list of groups
	 *
	 * @param int $default
	 * @return option list of groups
	 */
	function group_select($default = 0)
	{
		global $db, $user;

		$sql_where = ($user->data['user_type'] == USER_FOUNDER) ? '' : 'WHERE group_founder_manage = 0';
		$sql = 'SELECT group_id, group_name, group_type
				FROM ' . GROUPS_TABLE . "
				$sql_where
				ORDER BY group_name";
		$result = $db->sql_query($sql);

		$group_options = '';
		while ($row = $db->sql_fetchrow($result))
		{
			$selected = ($row['group_id'] == $default) ? ' selected="selected"' : '';
			$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];
			$group_options .= '<option value="' . $row['group_id'] . '"' . $selected . '>' . $group_name . '</option>';
		}
		$db->sql_freeresult($result);

		return $group_options;
	}

	/**
	 * list percentage values for currency conversion
	 *
	 * @param float $default
	 * @return option list
	 */
	function percentage_select($default = 0)
	{
		global $config;

		$options = '';

		$default = ($default) ? $default : $config['paypal_convert_percentage'];

		for ($i = 0; $i < 50; $i++)
		{
			$selected = (($default * 1000) == $i) ? ' selected="selected"' : '';
			$options .= '<option value="' . ($i * 0.001) . '"' . $selected . '>' . ($i * 0.1) . "%</option>\n";
		}

		return $options;
	}
}

?>