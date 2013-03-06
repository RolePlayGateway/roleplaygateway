<?php
/** 
*
* @package prs
* @version 1.0.0 2007/12/23 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);
include($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);
include($phpbb_root_path . 'includes/acp/info/acp_prs.' . $phpEx);

// Setup look and feel
//$user->setup('mods/prs');

// wat is de kans dat iemand een beoordeling geeft met een hogere o dan 3,
// met oog op het recente verleden?
// P = (

/**
* @package acp
*/
class acp_prs
{
	var $meta;
	var $u_action;
	var $info;

	function acp_prs()
	{
		$this->info = new acp_prs_info();
	}

	function main_cfg($meta, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
  
		// Initail general vars
		$submit = isset($_POST['submit']) ? true : false;
		$do_log = FALSE;

		$data = array();
		$overview_cfg = ($submit)
			? request_var('prs_overview_configuration', 0)
			: 15;
		foreach ($meta as $key => $arr)
		{
			$tmp = isset($config[$key])
			     ? $config[$key]
			     : $arr[0][1];
			$x = 1;
			$offset = strlen($key) - 7;
			if ($offset >= 0 && strpos($key, '_period', $offset))
			{
				$x = 86400;
			}
			if (isset($config[$key]) && is_int($tmp))
			{
				$tmp /= $x;
			}
			if (!$submit)
			{
				for ($i = 0; $i < 3; $i++)
				{
					if ($overview_cfg & (1 << $i) &&
					    $tmp != $arr[0][$i] &&
					    $key != 'prs_enabled')
					{
						$overview_cfg ^= (1 << $i);
					}
				}
			}
			else
			{
				$offset = strlen($key) - 8;
				if ($overview_cfg < 1 || $overview_cfg >= 16)
				{
					$tmp = request_var($key, 0);
				}
				elseif (in_array($mode, $arr[2]))
				{
			     		$tmp = request_var($key, 0);
				}
				else
				{
					for ($i = 0; $i < 3; $i++)
					{
						if ($overview_cfg & (1 << $i))
						{
							$tmp = $arr[0][$i];
						}
					}
				}
			}
			if (is_int($tmp))
			{
				$tmp *= $x;
			}

			switch($arr[1][0])
			{
				case 'num':
					$data[$key] =  (int) $tmp;
				break;
				case 'string':
				default:
					$data[$key] =  (string) $tmp;
				break;
			}
		}
		for ($i = 0; $i < 3; $i++)
		{
			if ($overview_cfg & (1 << $i))
			{
				$overview_cfg = 1 << $i;
			}
		}
		if ($submit)
		{
			$check = array();
			foreach($meta as $key => $arr)
			{
				$check[$key] = $arr[1];
			}
			$error = validate_data($data, $check);
//			trigger_error($error);

			// Save the data to the database
			$refresh_template = FALSE;
			foreach($data as $key => $value)
			{
				if (!isset($config[$key]) || 
				   $config[$key] != $value) 
				{
					set_config($key, $value);
					$do_log = TRUE;
/*
					switch($key)
					{
						case 'prs_extra_stars_big':
							if  ($value > 10)
							{
								$value = 10;
							}
							elseif ($value < 1)
							{
								$value = 1;
							}
							$this->update_stars('prs_star_v_', $value);
							$this->update_stars('prs_star_uv_', $value);
							$refresh_template = TRUE;
							
						break;
						case 'prs_extra_stars_small':
							$this->update_stars('prs_star_s_', ($value < 8) ? 5 : 10, 9, 8);
							$refresh_template = TRUE;
						break;
					}
*/
				}
			}
		}

		// Setup template variables.
		$arr = array(
			'S_PRS_OVERVIEW_CONFIGURATION'	=> $overview_cfg,
		);
		foreach ($meta as $key => $value)
		{
			$upper = strtoupper($key);
			$offset = strlen($key) - 7;
			if ($offset >= 0 && strpos($key, '_period', $offset))
			{
				$arr['S_' . $upper] = $data[$key] / 86400;
			}
			else
			{
				$arr['S_' . $upper] = $data[$key];
			}
		}
		$template->assign_vars($arr);

		// Log the action
		if ($do_log)
		{
			add_log('admin', 'LOG_PRS_CHANGED_SETTINGS');
		}
	}

	function &check()
	{
		global $phpbb_root_path;

		$styles_dir = $phpbb_root_path . 'styles/';
		$check = '<!-- IF S_PRS_ENABLED -->|&nbsp;<a href="http://prs.kr
uijff.org/">PRS</a>&nbsp;&copy;&nbsp;2008&nbsp;PRS&nbsp;Team</a><!-- ENDIF -->';
		$filelist = array($check);

		$dir = @opendir($phpbb_root_path . $styles_dir);
		if (!$dir)
		{
			$filelist[] = $styles_dir;
			return $filelist;
		}
			  
		while (($template_dir = readdir($dir)) !== false)
		{
			if ($template_dir[0] == '.'
				|| !is_dir($styles_dir . $template_dir))
			{
				continue;
			} 
			$filename = $styles_dir. $template_dir;
			if (!is_dir($filename))
			{
				continue;
			} 
			$filename .= '/template/overall_footer.html';
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);

			$pos = strpos($contents, $check);
			if ($pos === FALSE)
			{
				$filelist[] = $filename;
			}
		}
		closedir($dir);
		return $filelist;
	}

	function mode_update()
	{
		global $config, $db, $user, $auth, $template, $cache,
			$phpbb_root_path, $phpbb_admin_path, $phpEx,
			$prs_md5_copied;
  
		$now = time();

		// Does this forum contain the credits?
		$filelist = $this->check();
		$copyright_check = array_shift($filelist);
		$fetch = sizeof($filelist);
		$filenames = '';
		foreach ($filelist as $filename)
		{
			if (strlen($filename))
			{
				$filenames .= ' ';
			}
			$filenames .= $filename;
		}
	 
		if ($config['prs_release_date'] < $now && !defined('PRS_QA'))
		{
			// At what interval should we check?
			$interval = $now - $config['prs_release_date'];
			$interval = ($interval < 604800) ? 86400 : 604800;

			$last_time = isset($config['prs_remote_date']) ?
				(int) $config['prs_remote_date'] : 0;
			$check_remote = ($now - $last_time - $interval > 0);
		}
		else
		{
			$check_remote = 1;
		}


		if ($check_remote
			|| !isset($config['prs_remote_version'])
			|| !isset($config['prs_remote_date'])
		)
		{
			// Get current and latest version
			$errstr = '';
			$errno = 0;
		
			$info = get_remote_file('prs.kruijff.org', '/updatecheck', ((defined('PRS_QA')) ? '30x_prs_qa.txt' : '30x_prs.txt'), $errstr, $errno);

			if ($info === false)
			{
				trigger_error($errstr, E_USER_WARNING);
			}

		
			$info = explode("\n", $info);
			set_config('prs_remote_date', $now);
			set_config('prs_remote_version', $info[0]);
			set_config('prs_remote_url', $info[1]);
			set_config('prs_remote_md5', $info[2]);
		}

		$prs_version = $this->info->module();
		$prs_version = $prs_version['version'];
		$up2date = !(version_compare($config['prs_version'], $prs_version) < 0);
		$file_list = prs_copied_files();
		$md5 = prs_md5_file($file_list);
		$md5 = $md5['ALL'];

		$package_md5 = $up2date
			? $config['prs_remote_md5']
			: $prs_md5_copied['COPIED']['ALL'];


		if (strcmp($md5, $config['prs_remote_md5']))
		{
			$up2date = 0;
		}

		$copyright_notice = 'Powered by';
		$template->assign_vars(array(
			'S_PRS_UPDATE_AVAILABLE'	=> $fetch,
			'S_UP2DATE'			=> $up2date,
			'PRS_VERSION'			=> $config['prs_version'],
			'PRS_LATEST_VERSION'		=> $config['prs_remote_version'],
			'PRS_MD5'			=> $md5,
			'PRS_LATEST_MD5'		=> $package_md5,
			'PRS_CREDITS_INSTRUCTIONS'	=> sprintf($user->lang['PRS_CREDITS_INSTRUCTIONS'], $filenames, $copyright_notice, $copyright_check),
			'PRS_UPDATE_INSTRUCTIONS'	=> sprintf($user->lang['PRS_UPDATE_INSTRUCTIONS'], $config['prs_remote_url']),
		));
	}

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;
  

		$user->add_lang('mods/prs');

		// Set up the page
		$this->tpl_name 	= 'acp_prs';
		$this->page_title 	= 'ACP_PRS';

		$template->assign_vars(array('MODE' => $mode));

		$prs_version = $this->info->module();
		$prs_version = $prs_version['version'];
		
		if (!isset($config['prs_version']))
		{
			$this->info->install();
		}
		else if (version_compare($config['prs_version'], $prs_version) < 0)
		{
			$this->info->update();
		}


		// Pages
		if (!strcmp($mode, 'update'))
		{
			return $this->mode_update();
		}
		elseif (!strcmp($mode, 'stats'))
		{
			$template->assign_vars(array(
				'PRS_STATS_VOTERS_EXPLAIN'	=> sprintf($user->lang['PRS_STATS_VOTERS_EXPLAIN'], ($config['prs_votes_membership_period'] / 86400), $config['prs_votes_min_posts']),
				'PRS_STATS_SHADOWERS_EXPLAIN'	=> sprintf($user->lang['PRS_STATS_SHADOWERS_EXPLAIN'], $config['prs_shadow_min_votes']),
			));
			$template->assign_vars(prs_stats());
			return;
		}

		$base_modpoints = 14 / get_base_modpoints();
		$template->assign_vars(array(
			'PRS_MODPOINTS_KARMA_EXPLAIN'	=> sprintf($user->lang['PRS_MODPOINTS_KARMA_EXPLAIN'], round($base_modpoints), round(5 * $base_modpoints)),
		));

		/*
		 * $meta[0] hold an array of default values for each profile.
		 * $meta[1] Meta information about the field
	 	 * $meta[1][0] information type (num, string)
	 	 * $meta[1][1] ??
	 	 * $meta[1][2] min value
	 	 * $meta[1][3] max value
		 * $meta[2] The pages where the value is shown.
		 */
		$meta = array(
//					default (easy, avg, strict)
//					check (type, ??, min, max)
//					list of pages it apears in
			// Switches
			'prs_enabled'	
				=> array(array(0, 0, 0),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_karma_enabled'
				=> array(array(1, 1, 1),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_shadow_votes_enabled'
				=> array(array(1, 1, 1),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_penalty_enabled'
				=> array(array(1, 1, 1),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_modpoints_enabled'
				=> array(array(1, 1, 1),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),

			// Basis configuration
			'prs_votes_period'
				=> array(array(7, 14, 30),
					array('num', FALSE, 1),
					array('details')),
			'prs_votes_membership_period'
				=> array(array(14, 30, 60),
					array('num'),
					array('details')),
			'prs_votes_min_posts'
				=> array(array(25, 50, 100),
					array('num'),
					array('details')),
			'prs_default_rating'
				=> array(array(30, 30, 30),
					array('num', FALSE, 0, 50),
					array('details')),
/*
			'prs_ignore_votes'
				=> array(array(3, 184, 456),
					array('num', FALSE, 0, 1000),
					array('details')),
*/

			// Karma configuration
			'prs_karma_period'
				=> array(array(30, 30, 30),
					array('num', FALSE, 1),
					array('details')),
			'prs_karma_n'
				=> array(array(6, 6, 12),
					array('num', FALSE, 1),
					array('details')),

			// Shadow votes
			'prs_shadow_chi_chance'
				=> array(array(50, 50, 50),
					array('num', FALSE, 0, 1000),
					array('details')),
			'prs_shadow_refresh_chi'
				=> array(array(3600, 3600, 3600),
					array('num', FALSE, 1200),
					array('details')),
			'prs_shadow_min_votes'
				=> array(array(20, 20, 20),
					array('num', FALSE, 5, 10),
					array('details')),

			// Penalties
			'prs_penalty_border'
				=> array(array(3, 184, 456),
					array('num', FALSE),
					array('details')),
			'prs_penalty_minimum_votes'
				=> array(array(100, 100, 100),
					array('num', FALSE, 0),
					array('details')),
			'prs_penalty_user_overall'
				=> array(array(30, 30, 20),
					array('num', FALSE, 0, 100),
					array('details')),
			'prs_penalty_user_poster'
				=> array(array(30, 20, 10),
					array('num', FALSE, 0, 10),
					array('details')),

			// Mod points
			'prs_modpoints_newpost'
				=> array(array(30, 14, 7),
					array('num', FALSE, 0),
					array('details')),
			'prs_modpoints_period'
				=> array(array(40, 25, 10),
					array('num', FALSE, 0),
					array('details')),
			'prs_modpoints_karma'
				=> array(array(1, 1, 0),
					array('num', FALSE, 0, 1),
					array('details')),

			// Extra options
			'prs_extra_first_post_only'
				=> array(array(0, 0, 0),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_extra_vote_only_ones'
				=> array(array(0, 0, 0),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_extra_own_vote'
				=> array(array(0, 0, 0),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_extra_always_show_rating'
				=> array(array(0, 0, 0),
					array('num', FALSE, 0, 1),
					array('overview', 'details')),
			'prs_extra_stars'
				=> array(array(5, 5, 5),
					array('num', FALSE, 0, 25),
					array('overview', 'details')),
			'prs_extra_stars_big'
				=> array(array(1, 1, 1),
					array('num', FALSE, 0, 10),
					array('overview', 'details')),
			'prs_extra_stars_small'
				=> array(array(5, 5, 5),
					array('num', FALSE, 5, 10),
					array('overview', 'details')),
			'prs_extra_topic_rating'
				=> array(array(PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST, PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST, PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST),
					array('string', FALSE),
					array('overview', 'details')),
		);
		ksort($meta);

		$this->main_cfg($meta, $mode);
	}

/*
	function set_imageset_data($name, $filename, $height, $width)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		// Is the image_name in the database?
		$sql = 'SELECT image_name
			FROM ' . STYLES_IMAGESET_DATA_TABLE . '
			WHERE image_name = \'' . $name . '\'';
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ($row) // Update the $name with the $filename
		{
			$sql = 'UPDATE ' . STYLES_IMAGESET_DATA_TABLE . '
				SET image_filename = \'' . $filename . '\'
				WHERE image_name = \'' . $name . '\'';
		}
		else	// Insert the image_name with the filename
		{
			$sql_data = array();
			$sql_data[STYLES_IMAGESET_DATA_TABLE]['sql'] = array(
				'image_name'		=> $name,
				'image_filename'	=> $filename,
				'image_height'		=> $height,
				'image_width'		=> $width,
				'imageset_id'		=> 1,
			);
			$sql = 'INSERT INTO ' . STYLES_IMAGESET_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_data[STYLES_IMAGESET_DATA_TABLE]['sql']);

		}
		$db->sql_freeresult($db->sql_query($sql));
	}

	function update_stars($base, $n = 1, $height = 20, $width = 17)
	{
		$img = 0;
		for ($i = 0; $i <= 10; $i++)
		{ 
			$img = round($i / $n) * $n;
			$name = $filename = $base;
			$name .= $i;
			if ($i == 0)
			{
				$filename .= '00';
			}
			elseif ($img < 10)
			{
				$filename .= '0'.$img;
			}
			else
			{
				$filename .= $img;
			}
			$filename .= '.gif';
			$this->set_imageset_data($name, $filename,
				$height, $width
			);
		}
	}
*/

}
?>
