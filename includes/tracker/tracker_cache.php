<?php
/**
*
* @package tracker
* @version $Id: tracker_cache.php 114 2008-05-05 20:07:38Z evil3 $
* @copyright (c) 2008 http://www.jeffrusso.net
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

/**
* Class for grabbing/handling cached entries for the tracker, extends cache.php
* @package acm
*/
class tracker_cache extends cache
{
	/**
	* Obtain tracker config
	*/
	public function obtain_tracker_config()
	{
		if (($config = $this->get('_tracker')) === false)
		{
			global $db;

			// tracker Config
			$sql = 'SELECT *
				FROM ' . TRACKER_CONFIG_TABLE;
			$result = $db->sql_query($sql);

			$config = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$config[$row['config_name']] = $row['config_value'];
			}
			$db->sql_freeresult($result);

			$this->put('_tracker', $config);
		}

		return $config;
	}

	public function obtain_tracker_project_cats()
	{
		if (($projects = $this->get('_tracker_project_cats')) === false)
		{
			global $db;

			$sql = 'SELECT *
				FROM ' . TRACKER_PROJECT_CATS_TABLE . '
				 ORDER BY project_name_clean ASC';
			$result = $db->sql_query($sql);

			$projects = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$projects[$row['project_cat_id']] = array(
					'project_cat_id'		=> $row['project_cat_id'],
					'project_name'			=> $row['project_name'],
					'project_name_clean'	=> $row['project_name_clean'],
				);
			}
			$db->sql_freeresult($result);
			$this->put('_tracker_project_cats', $projects);
		}
		return $projects;
	}

	/**
	* Obtain tracker projects
	* The array returned is indexed by the project id
	* this allows you to simply use isset on the array
	* index to check if a project exists
	*/
	public function obtain_tracker_projects()
	{
		if (($projects = $this->get('_tracker_projects')) === false)
		{
			global $db;

			// Get tracker projects
			$sql_array = array(
				'SELECT'	=> 'p.*,
								pc.*,
								g.group_name,
								g.group_colour',

				'FROM'		=> array(
					TRACKER_PROJECT_TABLE	=> 'p',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(TRACKER_PROJECT_CATS_TABLE => 'pc'),
						'ON'	=> 'p.project_cat_id = pc.project_cat_id',
					),
					array(
						'FROM'	=> array(GROUPS_TABLE => 'g'),
						'ON'	=> 'p.project_group = g.group_id',
					),
				),

				'ORDER_BY'	=> 'pc.project_name_clean ASC, p.project_type ASC',
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			$projects = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$projects[$row['project_id']] = array(
					'project_id'				=> $row['project_id'],
					'project_name'				=> $row['project_name'],
					'project_name_clean'		=> $row['project_name_clean'],
					'project_desc'				=> $row['project_desc'],
					'project_enabled'			=> $row['project_enabled'],
					'project_type'				=> $row['project_type'],
					'project_security'			=> $row['project_security'],
					'ticket_security'			=> $row['ticket_security'],
					'project_group'				=> $row['project_group'],
					'project_cat_id'			=> $row['project_cat_id'],
					'show_php'					=> $row['show_php'],
					'lang_php'					=> $row['lang_php'],
					'show_dbms'					=> $row['show_dbms'],
					'lang_dbms'					=> $row['lang_dbms'],
					'group_name'				=> $row['group_name'],
					'group_colour'				=> $row['group_colour'],
				);
			}
			$db->sql_freeresult($result);

			// Get project components
			$sql_array = array(
				'SELECT'	=> 'c.component_id,
								c.component_name,
								c.project_id',

				'FROM'		=> array(
					TRACKER_COMPONENTS_TABLE	=> 'c',
				),

				'ORDER_BY'	=> 'c.component_id ASC',
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$id = $row['component_id'];
				$name = $row['component_name'];
				$project_id = $row['project_id'];

				if (isset($projects[$project_id]))
				{
					$projects[$project_id]['components'][$id] = array(
						'component_id'		=> $id,
						'component_name'	=> $name,
					);
				}
			}
			$db->sql_freeresult($result);

			// Get project versions
			$sql_array = array(
				'SELECT'	=> 'c.version_id,
								c.version_name,
								c.project_id',

				'FROM'		=> array(
					TRACKER_VERSION_TABLE	=> 'c',
				),

				'ORDER_BY'	=> 'c.version_id ASC',
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$id = $row['version_id'];
				$name = $row['version_name'];
				$project_id = $row['project_id'];

				if (isset($projects[$project_id]))
				{
					$projects[$project_id]['versions'][$id] = array(
						'version_id'		=> $id,
						'version_name'		=> $name,
					);
				}
			}
			$db->sql_freeresult($result);

			$this->put('_tracker_projects', $projects);
		}

		return $projects;
	}
}

?>