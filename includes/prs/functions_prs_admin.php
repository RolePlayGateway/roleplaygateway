<?php 
// m_lock controlls if moderator can lock / unlock / edit ratings
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

$prs_check_files = array(
	'EDITED'	=> prs_edited_files(),
	'LANGUAGE'      => prs_language_files(),
	'STYLES'	=> prs_styles_files(),
	'COPIED'	=> prs_copied_files(),
);

if (strpos($_SERVER['SERVER_NAME'], 'prs.kruijff.org') !== false)
{
	include($phpbb_root_path . 'includes/prs/prs_md5.' . $phpEx);
}
include($phpbb_root_path . 'prs_check.' . $phpEx);

function &prs_language_files()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
		
	$language_dir = $phpbb_root_path . 'language/';
	$language_files = array('mods/prs.php', 'mods/info_acp_prs.php');   

	$ret = array();
	$dir = @opendir($language_dir);
	if (!$dir)
	{
		return $ret;
	}

	while(($filename = readdir($dir)) !== false)
	{
		if ($filename[0] == '.'
			|| !is_dir($language_dir . $filename))
		{
			continue;
		}
		foreach ($language_files as $file)
		{
			$file = 'language/' . $filename . '/' . $file;
			if (file_exists($phpbb_root_path . $file))
			{
				$ret[] = $file;
			}
		}
	}
	closedir($dir);
	return $ret;
}

function &prs_edited_files()
{
	$arr = array(
		'includes/acp/acp_styles.php',
		'includes/mcp/mcp_forum.php',
		'includes/mcp/mcp_topic.php',
		'includes/mcp/mcp_post.php',
		'includes/mcp/mcp_main.php',
		'includes/constants.php',
		'posting.php',
		'viewtopic.php',
		'cron.php',
		'mcp.php',
		'viewforum.php',
		'search.php',
		'memberlist.php',
		'ucp.php',
	);
	return $arr;
}

function &prs_styles_files()
{
	$arr = array(
		'styles/prosilver/template/prs_body.html',
		'styles/prosilver/theme/prs.css',
		'styles/subsilver2/template/prs_body.html',
	);
	return $arr;
}

function &prs_copied_files()
{
	$arr = array(
		'adm/style/acp_prs.html',
		'includes/acp/acp_prs.php',
		'includes/acp/info/acp_prs.php',
		'includes/functions_prs.php',
		'includes/prs/functions_prs_admin.php',
		'includes/prs/functions_prs_basis.php',
		'includes/prs/functions_prs_compat.php',
		'includes/prs/functions_prs_constants.php',
		'includes/prs/functions_prs_hooks.php',
		'includes/prs/functions_prs_karma.php',
		'includes/prs/functions_prs_moderator.php',
		'includes/prs/functions_prs_modpoints.php',
		'includes/prs/functions_prs_penalty.php',
		'includes/prs/functions_prs_shadow.php',
		'includes/prs/functions_prs_stats.php',
		'includes/prs/functions_prs_support.php',
		'prs.php',
		'prs_info.php',
		'prs_uninstall.php',
	);
	return $arr;
}

function &prs_md5_file(&$file_list)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$total = '';
	$result = array();
	foreach ($file_list as $file)
	{
		$md5 = md5_file($phpbb_root_path . $file);
		$total .= $md5;
		$result[$file] = $md5;
	}
	$total = md5($total);
	$result['ALL'] = $total;
	return $result;
}

function prs_stats()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
		
	$queries = array(
		'PRS_STATS_USERS'	=> 'SELECT count(*) as value
				    FROM ' . GROUPS_TABLE . ' as g,
					' . USERS_TABLE . ' as u
				    WHERE g.group_id = u.group_id
					AND group_name != \'BOTS\'',

		'PRS_STATS_VOTERS'      => 'SELECT count(*) as value
				    FROM ' . GROUPS_TABLE . ' as g,
					' . USERS_TABLE . ' as u
				    WHERE g.group_id = u.group_id
				    	AND user_posts >= ' . $config['prs_votes_min_posts'] . '
					AND user_regdate <= ' . (time() - $config['prs_votes_membership_period']) . '
					AND group_name != \'BOTS\'',
	 
		'PRS_STATS_SHADOWERS'   => 'SELECT count(user_id) as value
				    FROM ' . PRS_VOTES_TABLE . '
				    GROUP BY user_id
				    HAVING count(post_id) >= ' . $config['prs_shadow_min_votes'],

		'PRS_STATS_AVG_RATING'  => 'SELECT avg(score) as value
				    FROM ' . PRS_VOTES_TABLE . '
				    GROUP BY post_id',

		'PRS_STATS_AVG_KARMA'   => 'SELECT avg(score) as value
				    FROM ' . POSTS_TABLE . ' as p,
					' . PRS_VOTES_TABLE . ' as v
				    WHERE p.post_id = v.post_id
				    GROUP BY poster_id',
/*
		'PRS_STATS_AVG_PENALTY' => 'SELECT avg(score) as value
				    FROM ' .
*/
		'PRS_STATS_ROWS_VOTES'  => 'SELECT count(*) as value
				    FROM ' . PRS_VOTES_TABLE,
		'PRS_STATS_ROWS_VOTES_CHI'      => 'SELECT count(*) as value
				    FROM ' . PRS_VOTES_CHI_TABLE,
		'PRS_STATS_ROWS_PENALTY'	=> 'SELECT count(*) as value
				    FROM ' . PRS_PENALTY_TABLE,
		'PRS_STATS_ROWS_MODPOINTS' => 'SELECT count(*) as value
				    FROM ' . PRS_MODPOINTS_TABLE,
	);
	$results = array();
	foreach ($queries as $key => $sql)
	{
		if (strlen($key) >= 14
			&& $key[10] == 'A' && $key[11] == 'V'
			&& $key[12] == 'G' && $key[13] == '_')
		{
			$som = 0;
			$i = 0;
			$result = $db->sql_query($sql, 300);
			while($row = $db->sql_fetchrow())
			{
				$som += $row['value'];
				$i++;
			}
			$db->sql_freeresult($result);
			$avg = round($som / $i / PRS_MULTIPLIER_SCORE, PRS_STATS_PRECISION);
			$results[$key] = $avg;
		}
		elseif (strpos($key, 'PRS_STATS_SHADOWERS') !== FALSE)
		{
			$i = 0;
			$result = $db->sql_query($sql, 300);
			while($row = $db->sql_fetchrow())
			{
				$i++;
			}
			$db->sql_freeresult($result);
			$results[$key] = $i;
		}
		else
		{
			$result = $db->sql_query_limit($sql, 1, 0, 300);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			$results[$key] = $row['value'];
		}
	}
	return $results;
}

function prs_add_module($basename, $parent, $child, $data = NULL)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	// Does the child already exist?
	$sql = 'SELECT module_id
		FROM ' . MODULES_TABLE . "
		WHERE module_langname = '$child'";
	$result = $db->sql_query($sql);
	$found = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);   
					
	// Create or update child
	if (!$found)
	{
		$sql = 'SELECT module_id
			FROM ' . MODULES_TABLE . "
			WHERE module_langname = '$parent'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result); 
    
		$module_data = array(
			'module_enabled'	=> 1,  
			'module_display'	=> 1,
			'module_basename'       => '',
			'module_class'	  => 'acp',
			'parent_id'	     => $row['module_id'],
			'module_langname'       => $child,
		);   
		if (isset($data))
		{
			$module_data['module_basename'] = $basename;
			$module_data['module_mode'] = $data['mode'];
			$module_data['module_auth'] = $data['auth'];
		}
		$acp = new acp_modules();
		$errors = $acp->update_module_data($module_data, TRUE);
	}
}

function prs_add_modules($basename)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$info = new acp_prs_info();
	$modules = $info->module();
	foreach ($modules['modes'] as $key => $arr)
	{
		foreach ($arr['cat'] as $value)
		{
			// Add category PRS
			prs_add_module($basename,
				'ACP_CAT_DOT_MODS',
				$value);
		
			// Add modules
			$data = array(
				'mode'  => $key,
				'auth'  => $arr['auth'],
			);
			prs_add_module($basename,
				$value,
				$arr['title'],
				$data);
		}
	}

	$acp = new acp_modules();
	$acp->remove_cache_file();

/* If the line above doesn't work use this.
	global $cache;
	$cache->destroy('_modules_acp');
	$cache->destroy('sql', MODULES_TABLE);
*/
}

function prs_sql_create_table($table, $columns)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = 'CREATE TABLE ' . $table . '
		( ' . $columns . ' ) COLLATE utf8_bin';
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);
}
 
function prs_sql_alter_table($table, $sql)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = 'ALTER TABLE ' . $table . ' ' . $sql;
	$result = $db->sql_query($sql);
	$db->sql_freeresult($result);
}

function prs_sql_imageset_data($templates) {
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	global $cache;
		 
	$sql = 'INSERT INTO ' . STYLES_IMAGESET_DATA_TABLE . '
		(`image_filename`, `image_name`, `image_height`,
			 `image_width`, `imageset_id`) VALUES ';
 
	$comma = '';
	$run = array();
	foreach($templates as $template => $template_data)
	{
		$tmp = 'SELECT imageset_id
			FROM ' . STYLES_IMAGESET_TABLE . "
			WHERE imageset_name = '$template'";
		$result = $db->sql_query($tmp);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		if (!$row)
		{
			continue;
		}

		foreach($template_data['imageset'] as $base => $arr)
		{
			for ($i = 0 ; $i <= 10; $i++)
			{
				$tmp = 'SELECT image_id
					FROM ' . STYLES_IMAGESET_DATA_TABLE . '
					WHERE image_name = \'' . $base . $i . '\'';
				$result = $db->sql_query($tmp);
				$found = $db->sql_fetchrow($result);
				$found = $db->sql_fetchrow($result);
				$db->sql_freeresult($result); 
					if ($found)
				{
					continue;
				}
				$run[$template] = 1;
		 
				$sql .= $comma . '(\'' . $base;
				if ($arr['small'])
				{
					if ($i < 3)
					{
						$sql .= '00';
					}
						elseif ($i < 8)
					{
							$sql .= '05'; 
					}
					else
					{
						$sql .= '10';
					}
				}
				else
					{
					$sql .= ($i < 10) ? '0' : '';
					$sql .= $i;
				}
				$sql .= '.gif\', \'';

				$sql .= $base . $i . '\', \'';
				$sql .= $arr['height'] . '\', \'';
				$sql .= $arr['width'] . '\', \'';
				$sql .= $row['imageset_id'] . '\')';
				$comma = ', ';
			}
		}
	}

	if (sizeof($run))
	{
		$result = $db->sql_query($sql);
		$db->sql_freeresult($result);
		$cache->destroy('sql', STYLES_IMAGESET_DATA_TABLE);
		foreach (array_keys($run) as $template)
		{
			$cache->destroy('_cfg_imageset_'.$template);
		}
	}
}


 
function prs_sql_table_prs_votes()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = "user_id MEDIUMINT UNSIGNED NOT NULL,
		user_ip VARCHAR(40) NOT NULL,
		post_id MEDIUMINT UNSIGNED NOT NULL,
		score TINYINT UNSIGNED NOT NULL DEFAULT '0',
		standard_diviation SMALLINT UNSIGNED NOT NULL DEFAULT '0',
		shadow BOOL NOT NULL DEFAULT '0',
		time INT UNSIGNED NOT NULL,

		PRIMARY KEY (user_id, post_id),
		INDEX (post_id)";
	prs_sql_create_table(PRS_VOTES_TABLE, $sql);
}

function prs_sql_table_prs_votes_chi()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = "user1_id MEDIUMINT UNSIGNED NOT NULL,
		user2_id MEDIUMINT UNSIGNED NOT NULL,
		time INT UNSIGNED NOT NULL,
		chi TINYINT UNSIGNED NOT NULL,
		diff TINYINT NOT NULL,
		num TINYINT UNSIGNED NOT NULL,

		PRIMARY KEY (user1_id, user2_id),
		INDEX (time, num)";
	prs_sql_create_table(PRS_VOTES_CHI_TABLE, $sql);
}

function prs_sql_table_prs_penalty()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = "user_id MEDIUMINT UNSIGNED NOT NULL,
		poster_id MEDIUMINT UNSIGNED NOT NULL,
		penalty TINYINT UNSIGNED NOT NULL,
 
		PRIMARY KEY (user_id, poster_id)";
	prs_sql_create_table(PRS_PENALTY_TABLE, $sql);
}
 
function prs_sql_table_prs_modpoints()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = "time INT UNSIGNED NOT NULL,
		user_id MEDIUMINT UNSIGNED NOT NULL,
		post_id MEDIUMINT UNSIGNED NOT NULL,
		points TINYINT UNSIGNED NOT NULL,

		PRIMARY KEY (time, user_id),
		INDEX (post_id)";
	prs_sql_create_table(PRS_MODPOINTS_TABLE, $sql);
}

function prs_sql_table_posts()
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;

	$sql = "ADD prs_score TINYINT UNSIGNED NOT NULL DEFAULT 0,
		ADD prs_standard_diviation SMALLINT UNSIGNED NOT NULL DEFAULT 0,
		ADD prs_shadowed BOOL NOT NULL DEFAULT 0,
		ADD prs_penaltized BOOL NOT NULL DEFAULT 0,
		ADD INDEX (prs_score, prs_shadowed)";
	prs_sql_alter_table(POSTS_TABLE, $sql);
}

?>
