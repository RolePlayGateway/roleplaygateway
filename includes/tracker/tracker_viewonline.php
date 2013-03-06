<?php
/**
*
* @package tracker
* @version $Id: tracker_viewonline.php 157 2008-12-23 20:08:25Z JRSweets $
* @copyright (c) 2008 http://www.JeffRusso.net
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

if (!isset($tracker))
{
	include($phpbb_root_path . 'includes/tracker/tracker_class.' . $phpEx);
	$tracker = new tracker(false);
}

// Grab some common modules
$url_params = array(
	'mode=statistics'	=> 'VIEWING_TRACKER_STATISTICS',
	'mode=add'			=> 'ADDING_TRACKER_TICKET',
	'mode=reply'		=> 'REPLYING_TRACKER_TICKET',
);

preg_match('#p=([0-9]+)#i', $row['session_page'], $project_id);
$project_id = (sizeof($project_id)) ? (int) $project_id[1] : 0;

preg_match('#t=([0-9]+)#i', $row['session_page'], $ticket_id);
$ticket_id = (sizeof($ticket_id)) ? (int) $ticket_id[1] : 0;

$found_tracker = false;
foreach ($url_params as $param => $lang)
{
	if (strpos($row['session_page'], $param) !== false)
	{
		if ($param == 'mode=statistics')
		{
			$found_tracker = true;
			if ($project_id)
			{
				$location = sprintf($user->lang[$lang], $tracker->api->get_type_option('title', $project_id), $tracker->api->projects[$project_id]['project_name']);
				$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx", 'mode=statistics&amp;p=' . $project_id);
			}
			else
			{
				$location = sprintf($user->lang[$lang . '_ALL']);
				$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx", 'mode=statistics');
			}
		}
		else if ($param == 'mode=add' && $project_id)
		{
			$found_tracker = true;
			$location = sprintf($user->lang[$lang], $tracker->api->get_type_option('title', $project_id), $tracker->api->projects[$project_id]['project_name']);
			$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx", 'p=' . $project_id);
		}
		else if ($param == 'mode=reply' && $project_id && $ticket_id)
		{
			$found_tracker = true;
			$location = sprintf($user->lang[$lang], $tracker->api->get_type_option('title', $project_id), $tracker->api->projects[$project_id]['project_name']);
			$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx", 'p=' . $project_id . '&amp;t=' . $ticket_id);
		}
		break;
	}
}

if (!$found_tracker)
{
	$lang = 'VIEWING_TRACKER';
	if ($project_id && $ticket_id)
	{
		$location = sprintf($user->lang[$lang . '_TICKET'], $tracker->api->get_type_option('title', $project_id), $tracker->api->projects[$project_id]['project_name']);
		$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx", 'p=' . $project_id . '&amp;t=' . $ticket_id);
	}
	else if ($project_id && !$ticket_id)
	{
		$location = sprintf($user->lang[$lang . '_PROJECT'], $tracker->api->get_type_option('title', $project_id), $tracker->api->projects[$project_id]['project_name']);
		$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx", 'p=' . $project_id);
	}
	else
	{
		$location = $user->lang[$lang];
		$location_url = append_sid("{$phpbb_root_path}tracker.$phpEx");
	}
}
?>