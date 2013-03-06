<?php
/**
*
* @package tracker
* @version $Id: tracker.php 134 2008-09-07 08:29:33Z evil3 $
* @copyright (c) 2008 http://www.jeffrusso.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

// Handle downloads before anything else, more efficient
if (isset($_GET['mode']) && (string) $_GET['mode'] === 'download')
{
	require($phpbb_root_path . 'includes/tracker/tracker_download.' . $phpEx);
	exit;
}

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/tracker/tracker_class.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// Instantiate tracker
$tracker = new tracker();

// Get the varibles we will use to build the tracker pages
$mode					= request_var('mode', '');
$term					= utf8_normalize_nfc(request_var('term', '', true));
$ticket_id				= request_var('t', 0);
$project_id				= request_var('p', 0);
$project_cat_id			= request_var('c', 0);
$version_id				= request_var('vid', 0);
$component_id			= request_var('cid', 0);
$post_id				= request_var('pid', 0);
$user_id				= request_var('u', 0);
$assigned_to_user_id	= request_var('at', 0);
$status_type			= request_var('st', $tracker->api->config['default_status_type']);
$start					= request_var('start', 0);
$subscribe				= request_var('subscribe', '');
$unsubscribe			= request_var('unsubscribe', '');

$submit					= (isset($_POST['submit'])) ? true : false;
$submit_mod				= (isset($_POST['submit_mod'])) ? true : false;
$update					= (isset($_POST['update'])) ? true : false;
$add_attachment			= (isset($_POST['add_attachment'])) ? true : false;
$remove_attachment		= (isset($_POST['delete_attachment'])) ? true : false;
$attachment_data		= (isset($_POST['attachment_data'])) ? request_var('attachment_data', array('' => '')) : array();
$preview				= (isset($_POST['preview'])) ? true : false;

// check permissions
$tracker->check_permission($mode, $project_id);

// Make sure the project exists and enabled...
if (!empty($project_id))
{
	if (!$tracker->check_exists($project_id))
	{
		trigger_error('TRACKER_PROJECT_NO_EXIST');
	}
}

if ($mode == 'statistics')
{
	$tracker->display_statistics($project_id, $project_cat_id);
}

if ($mode == 'changelog' && $project_id && $version_id)
{
	$tracker->display_changelog($project_id, $version_id);
}

if ($project_id && (!$mode || $mode == 'search') && !$ticket_id)
{
	if ($subscribe != '')
	{
		$tracker->api->subscribe('subscribe', $project_id, $ticket_id);
	}
	else if ($unsubscribe != '')
	{
		$tracker->api->subscribe('unsubscribe', $project_id, $ticket_id);
	}

	$is_subscribed = $tracker->api->is_subscribed('project', $project_id);

	$tracker->api->generate_nav($tracker->api->projects[$project_id]);

	$sql_where = 't.project_id = ' . $project_id;
	$sql_where .= (!$tracker->api->can_manage) ? ' AND p.project_enabled = ' . TRACKER_PROJECT_ENABLED : '';
	$sql_where .= (!$tracker->api->can_manage) ? ' AND t.ticket_hidden = ' . TRACKER_TICKET_UNHIDDEN : '';
	$sql_where .= (!$tracker->api->can_manage && $tracker->api->projects[$project_id]['project_security']) ? ' AND t.ticket_user_id = ' . $user->data['user_id'] : '';
	$sql_where .= (!$tracker->api->can_manage && $tracker->api->projects[$project_id]['ticket_security']) ? ' AND (t.ticket_security = ' . TRACKER_TICKET_UNSECURITY . ' OR (t.ticket_security = ' . TRACKER_TICKET_SECURITY . ' AND t.ticket_user_id = ' . $user->data['user_id'] . '))' : '';
	$sql_where .= ($user_id) ? ' AND t.ticket_user_id = ' . $user_id : '';
	$sql_where .= ($assigned_to_user_id) ? ' AND (t.ticket_assigned_to = ' . $assigned_to_user_id . ' OR t.ticket_assigned_to = ' . TRACKER_ASSIGNED_TO_GROUP . ')' : '';
	$sql_where .= $tracker->api->get_filter_sql($status_type, $version_id, $component_id);

	$sql_array = array(
		'SELECT'	=> 't.*,
						u1.user_colour as ticket_user_colour,
						u1.username as ticket_username,
						u2.user_colour as assigned_user_colour,
						u2.username as assigned_username,
						u3.user_colour as last_post_user_colour,
						u3.username as last_post_username',

		'FROM'		=> array(
			TRACKER_TICKETS_TABLE	=> 't',
		),

		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(TRACKER_PROJECT_TABLE => 'p'),
				'ON'	=> 'p.project_id = t.project_id',
			),
			array(
				'FROM'	=> array(USERS_TABLE => 'u1'),
				'ON'	=> 'u1.user_id = t.ticket_user_id',
			),
			array(
				'FROM'	=> array(USERS_TABLE => 'u2'),
				'ON'	=> 'u2.user_id = t.ticket_assigned_to',
			),
			array(
				'FROM'	=> array(USERS_TABLE => 'u3'),
				'ON'	=> 'u3.user_id = t.last_post_user_id',
			),
		),

		'WHERE'		=> $sql_where,

		'ORDER_BY'	=> 't.ticket_time DESC',
	);

	$pagination_mode = '';
	if ($mode == 'search' && !empty($term))
	{
		$template->assign_var('S_IN_SEARCH', true);
		$searchterm = '*' . strtolower($term) . '*';
		if ($searchterm != '**')
		{
			// replace wildcards
			$searchterm = str_replace('*', $db->any_char , $searchterm);
			$searchterm = str_replace('?', $db->one_char , $searchterm);
		}

		switch ($db->sql_layer)
		{
			case 'mssql':
			case 'mssql_odbc':
				$sql_array['WHERE'] .= ' AND (LOWER(t.ticket_title) ' . $db->sql_like_expression($searchterm) . ' OR LOWER(cast(t.ticket_desc as varchar(4000))) ' . $db->sql_like_expression($searchterm) . ')';
			break;

			default:
				$sql_array['WHERE'] .= ' AND (LOWER(t.ticket_title) ' . $db->sql_like_expression($searchterm) . ' OR LOWER(t.ticket_desc) ' . $db->sql_like_expression($searchterm) . ')';
			break;
		}

		$pagination_mode = 'mode=search&amp;term=' . $term;
	}

	$total_tickets = $tracker->api->get_total('tickets', $project_id, $ticket_id, $sql_array['WHERE']);
	$tickets_per_page = $tracker->api->config['tickets_per_page'];

	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query_limit($sql, $tickets_per_page, $start);

	$tickets = $db->sql_fetchrowset($result);
	$db->sql_freeresult($result);

	if ($tickets)
	{
		$sql = 'SELECT component_id, component_name
			FROM ' . TRACKER_COMPONENTS_TABLE . '
			WHERE project_id = ' . $project_id;
		$result = $db->sql_query($sql);

		$components = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$components[$row['component_id']] = $row['component_name'];
		}
		$db->sql_freeresult($result);
	}

	foreach ($tickets as $item)
	{
		$template->assign_block_vars('tickets', array(
			'U_VIEW_TICKET'				=> $tracker->api->build_url('ticket', array($project_id, $item['ticket_id'])),

			'LAST_POST_USERNAME'		=> (!empty($item['last_post_user_id'])) ? get_username_string('full', $item['last_post_user_id'], $item['last_post_username'], $item['last_post_user_colour']) : '',
			'LAST_POST_TIME'			=> $user->format_date($item['last_post_time']),

			'TICKET_HIDDEN'				=> ($item['ticket_hidden'] == TRACKER_TICKET_HIDDEN) ? true : false,
			'TICKET_SECURITY'			=> ($item['ticket_security'] == TRACKER_TICKET_SECURITY) ? true : false,
			'TICKET_ID'					=> $item['ticket_id'],
			'TICKET_TITLE'				=> $item['ticket_title'],
			'TICKET_USERNAME'			=> get_username_string('full', $item['ticket_user_id'], $item['ticket_username'], $item['ticket_user_colour']),
			'TICKET_TIME'				=> $user->format_date($item['ticket_time']),
			'TICKET_COMPONENT'			=> $tracker->api->set_component_name($item['component_id'], $components),
			'TICKET_ASSIGNED_TO'		=> $tracker->api->get_assigned_to($project_id, $item['ticket_assigned_to'], $item['assigned_username'], $item['assigned_user_colour']),
			'TICKET_STATUS'				=> $tracker->api->set_status($item['status_id']),
		));
	}

	$currently_showing = '';
	$sort_type = array();
	if ($user_id)
	{
		$sort_type['u'] = $user_id;
		$filter_username = array();
		$filter_user_id = $user_id;
		user_get_id_name($filter_user_id, $filter_username);
		$currently_showing = sprintf($user->lang['TRACKER_CURRENTLY_SHOWING_USER'], $tracker->api->format_username($filter_username[$user_id]), strtolower($tracker->api->set_status($status_type)));
	}
	else
	{
		$currently_showing = sprintf($user->lang['TRACKER_CURRENTLY_SHOWING'], $tracker->api->set_status($status_type));
	}

	if ($assigned_to_user_id)
	{
		$sort_type['at'] = $assigned_to_user_id;
		$filter_username = array();
		$filter_user_id = $assigned_to_user_id;
		user_get_id_name($filter_user_id, $filter_username);
		$currently_showing = $currently_showing . sprintf($user->lang['TRACKER_ASSIGNED_TO_USERNAME'], $filter_username[$assigned_to_user_id]);
	}

	if ($version_id && $version_name = $tracker->api->get_name('version', $project_id, $version_id))
	{
		$currently_showing .= sprintf($user->lang['TRACKER_FILTER_VERSION'], $version_name);
	}

	if ($component_id && $component_name = $tracker->api->get_name('component', $project_id, $component_id))
	{
		$currently_showing .= sprintf($user->lang['TRACKER_FILTER_COMPONENT'], $component_name);
	}

	$l_total_tickets = false;
	if ($total_tickets == 1)
	{
		$l_total_tickets = $total_tickets . ' ' . $user->lang['TRACKER_TICKET'];
	}
	else if ($total_tickets > 1)
	{
		$l_total_tickets = $total_tickets . ' ' . $user->lang['TRACKER_TICKETS'];
	}

	if ($mode == 'search' && !empty($term))
	{

		$user->add_lang('search');
		$search_matches = ($total_tickets == 1) ? sprintf($user->lang['FOUND_SEARCH_MATCH'], $total_tickets) : sprintf($user->lang['FOUND_SEARCH_MATCHES'], $total_tickets);
		$template->assign_vars(array(
			'S_FOUND_RESULTS' 	=> true,
			'SEARCH_TERM'		=> $term,
			'SEARCH_MATCHES' 	=> $search_matches,
		));

		$pagination_url = $tracker->api->build_url('search_st_at_u', array($project_id, $term, $status_type, isset($sort_type['at']) ? $sort_type['at'] : '', isset($sort_type['u']) ? $sort_type['u'] : '', $version_id, $component_id));
	}
	else
	{
		$pagination_url = $tracker->api->build_url('search_st_at_u', array($project_id, $term, $status_type, isset($sort_type['at']) ? $sort_type['at'] : '', isset($sort_type['u']) ? $sort_type['u'] : '', $version_id, $component_id));
	}

	$template->assign_vars(array(
		'PAGE_NUMBER'	=> ($tickets_per_page > 0) ? on_page($total_tickets, $tickets_per_page, $start) : on_page($total_tickets, $total_tickets, $start),
		'TOTAL_TICKETS'	=> $l_total_tickets,
		'PAGINATION'	=> ($tickets_per_page > 0) ? generate_pagination($pagination_url, $total_tickets, $tickets_per_page, $start) : false,
	));

	// Assign index specific vars
	$template->assign_vars(array(
		'L_TITLE'						=> $tracker->api->projects[$project_id]['project_name'] . ' - ' . $tracker->api->get_type_option('title', $project_id),

		'S_CAN_MANAGE'					=> $tracker->api->can_manage,
		'PROJECT_ID'					=> $project_id,
		'TRACKER_USER_ID'				=> $user_id,
		'TRACKER_ASSIGNED_USER_ID'		=> $assigned_to_user_id,
		'TRACKER_CURRENTLY_SHOWING'		=> $currently_showing,
		'S_CAN_POST_TRACKER'			=> $auth->acl_get('u_tracker_post'),
		'TICKET_IMG'					=> $user->img('button_issue_new', $user->lang['TRACKER_POST_TICKET']),
		'U_POST_NEW_TICKET'				=> $tracker->api->build_url('add', array($project_id, $ticket_id)),
		'U_MY_TICKETS'					=> ($user_id) ? $tracker->api->build_url('project_st_at', array($project_id, $status_type, $assigned_to_user_id, $version_id, $component_id)) : $tracker->api->build_url('project_st_at_u', array($project_id, $status_type, $assigned_to_user_id, $user->data['user_id'], $version_id, $component_id)),
		'TRACKER_MY_TICKETS'			=> ($user_id) ? $user->lang['TRACKER_EVERYONES_TICKETS'] : $user->lang['TRACKER_MY_TICKETS'],

		'U_WATCH_PROJECT'				=> ($is_subscribed) ? $tracker->api->build_url('unsubscribe_p', array($project_id)) : $tracker->api->build_url('subscribe_p', array($project_id)),
		'L_WATCH_PROJECT'				=> ($is_subscribed) ? $user->lang['TRACKER_UNWATCH_PROJECT'] : $user->lang['TRACKER_WATCH_PROJECT'],

		'U_MY_ASSIGNED_TICKETS'			=> ($assigned_to_user_id) ? $tracker->api->build_url('project_st_u', array($project_id, $status_type, $user_id, $version_id, $component_id)) : $tracker->api->build_url('project_st_at_u', array($project_id, $status_type, $user->data['user_id'], $user_id, $version_id, $component_id)),
		'TRACKER_MY_ASSIGNED_TICKETS'	=> ($assigned_to_user_id) ? $user->lang['TRACKER_EVERYONES_ASSIGNED_TICKETS'] : $user->lang['TRACKER_MY_ASSIGNED_TICKETS'],

		'U_ACTION'						=> ($mode == 'search' && !empty($term)) ? $tracker->api->build_url('search', array($project_id, $term)) : $tracker->api->build_url('index'),
		'S_HIDDEN_FIELDS'				=> ($mode == 'search' && !empty($term)) ? build_hidden_fields(array('mode' => 'search', 'term' => $term)): '' ,
		'S_ACTION_SEARCH' 				=> $tracker->api->build_url('project_st_at_u', array($project_id, $status_type, $assigned_to_user_id, $user_id, $version_id, $component_id)),
		'S_HIDDEN_FIELDS_SEARCH' 		=> build_hidden_fields(array('mode' => 'search', 'p' => $project_id)),

		'S_STATUS_OPTIONS'				=> $tracker->api->status_select_options($status_type, true),
		'S_VERSION_OPTIONS'				=> $tracker->api->select_options($project_id, 'version', $version_id, false),
		'S_COMPONENT_OPTIONS'			=> $tracker->api->select_options($project_id, 'component', $component_id),
		'S_LOGIN_ACTION'				=> $tracker->api->build_url('login'),
	));

	// Output page
	page_header($user->lang['TRACKER'] . ' - ' . $tracker->api->get_type_option('title', $project_id) . ' - ' . $tracker->api->projects[$project_id]['project_name'], false);

	$template->set_filenames(array(
		'body' => 'tracker/tracker_tickets_body.html')
	);

	page_footer();
}
else if ($project_id && $ticket_id && ((!$mode || $mode == 'history' || $mode == 'reply' || $mode == 'delete') || ($mode == 'edit' && $post_id)))
{
	if ($subscribe != '')
	{
		$tracker->api->subscribe('subscribe', $project_id, $ticket_id);
	}
	else if ($unsubscribe != '')
	{
		$tracker->api->subscribe('unsubscribe', $project_id, $ticket_id);
	}

	$is_subscribed = $tracker->api->is_subscribed('ticket', $ticket_id);

	if ($mode == 'delete')
	{
		$tracker->display_delete($project_id, $post_id, $ticket_id);
	}

	add_form_key('add_post');

	if ($mode == 'reply' || $mode == 'edit')
	{
		$tracker->api->check_ticket_exists($ticket_id);

		if ($mode == 'edit' && !$preview && !$submit)
		{
			$sql_array = array(
				'SELECT'	=> 'p.*,
							a.attach_id,
							a.poster_id,
							a.is_orphan,
							a.physical_filename,
							a.real_filename,
							a.extension,
							a.mimetype,
							a.filesize,
							a.filetime',

				'FROM'		=> array(
					TRACKER_POSTS_TABLE	=> 'p',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=> array(TRACKER_ATTACHMENTS_TABLE => 'a'),
						'ON'	=> 'p.post_id = a.post_id',
					),
				),

				'WHERE'		=> 'p.post_id = ' . $post_id,
			);

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);

			$post_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$post_data)
			{
				trigger_error('TRACKER_POST_NO_EXIST');
			}

			$tracker->api->check_edit($post_data['post_time'], $post_data['post_user_id'], false);

			if ($post_data['attach_id'])
			{
				$attachment_data = array(
					'poster_id'				=> $post_data['poster_id'],
					'filesize'				=> $post_data['filesize'],
					'mimetype'				=> $post_data['mimetype'],
					'extension'				=> $post_data['extension'],
					'physical_filename'		=> $post_data['physical_filename'],
					'real_filename'			=> $post_data['real_filename'],
					'filetime'				=> $post_data['filetime'],
					'attach_id'				=> $post_data['attach_id'],
				);
			}
		}
		else
		{
			$post_data = array(
				'post_desc'					=> utf8_normalize_nfc(request_var('post_text', '', true)),
				'post_time'					=> time(),
				'post_user_id'				=> $user->data['user_id'],
				'post_desc_bitfield'		=> '',
				'post_desc_options'			=> 7,
				'post_desc_uid'				=> '',
				'ticket_id'					=> $ticket_id,
			);
		}

		if ($mode == 'edit' && ($preview || $submit))
		{
			unset($post_data['post_time'], $post_data['post_user_id']);

			$post_data += array(
				'edit_reason'	=> utf8_normalize_nfc(request_var('edit_reason', '', true)),
				'edit_time'		=> time(),
				'edit_user'		=> $user->data['user_id'],
			);
		}

		if ($add_attachment)
		{
			$filedata = $tracker->api->add_attachment('attachment', $tracker->errors);

			if (sizeof($filedata))
			{
				$tracker->api->posting_gen_attachment_data($filedata);
			}
		}
		else if (sizeof($attachment_data))
		{
			if ($remove_attachment)
			{
				$tracker->api->remove_attachment($attachment_data);
			}
			else
			{
				$tracker->api->posting_gen_attachment_data($attachment_data);
			}
		}

		$data = array(
			'ticket_assigned_to'	=> request_var('au', 0),
			'status_id'				=> request_var('cs', 0),
			'priority_id'			=> request_var('pr', 0),
			'severity_id'			=> request_var('s', 0),
			'ticket_hidden'			=> request_var('ticket_hidden', 0),
			'ticket_status'			=> request_var('ticket_status', 0),
		);

		if ($tracker->api->projects[$project_id]['ticket_security'] && ($auth->acl_get('u_tracker_ticket_security') || $tracker->api->can_manage))
		{
			$data['ticket_security'] = request_var('ticket_security', 0);
		}

		if ($submit)
		{
			if (!check_form_key('add_post'))
			{
				$tracker->errors[] = $user->lang['FORM_INVALID'];
			}

			if ($post_data['post_desc'] && !sizeof($tracker->errors))
			{
				generate_text_for_storage($post_data['post_desc'], $post_data['post_desc_uid'], $post_data['post_desc_bitfield'], $post_data['post_desc_options'], true, true, true);

				if ($mode == 'reply')
				{
					$post_id = $tracker->api->add_post($post_data, $ticket_id);

					if (sizeof($attachment_data))
					{
						$tracker->api->update_attachment($attachment_data, $ticket_id, $post_id);
					}
				}
				else if ($mode == 'edit')
				{
					$tracker->api->update_post($post_data, $post_id);

					if (sizeof($attachment_data))
					{
						$tracker->api->update_attachment($attachment_data, $ticket_id, $post_id);
					}
				}

				$sql_array = array(
					'SELECT'	=> 't.*,
									p.project_group',

					'FROM'		=> array(
						TRACKER_TICKETS_TABLE	=> 't',
					),

					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(TRACKER_PROJECT_TABLE => 'p'),
							'ON'	=> 't.project_id = p.project_id',
						),
					),

					'WHERE'		=> 't.ticket_id = ' . $ticket_id,
				);

				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);

				$row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				// If user can manage project check for updates
				if ($tracker->api->can_manage)
				{
					$tracker->api->update_ticket($data, $ticket_id);
					$tracker->api->process_notification($data, $row, false);

					$tracker->api->manage_hide((($data['ticket_hidden']) ? 'hide' : 'unhide'), $ticket_id);
					$tracker->api->manage_lock((($data['ticket_status']) ? 'lock' : 'unlock'), $ticket_id);

				}

				$tracker->back_link('TRACKER_TICKET_REPLY_SUBMITTED', '', $project_id, $ticket_id);
			}
			else if (!$post_data['post_desc'])
			{
				$tracker->errors[] = $user->lang['TRACKER_TICKET_MESSAGE_ERROR'];
			}
		}

		if ($preview && $post_data['post_desc'])
		{
			$preview_data = array(
				'text'		=> $post_data['post_desc'],
				'uid'		=> $post_data['post_desc_uid'],
				'bitfield'	=> $post_data['post_desc_bitfield'],
				'options'	=> $post_data['post_desc_options'],
			);

			generate_text_for_storage($preview_data['text'], $preview_data['uid'], $preview_data['bitfield'], $preview_data['options'], true, true, true);

			$template->assign_vars(array(
				'S_PREVIEW'			=> true,
				'REPLY_PREVIEW'		=> generate_text_for_display($preview_data['text'], $preview_data['uid'], $preview_data['bitfield'], $preview_data['options']),
			));
		}

		decode_message($post_data['post_desc'], $post_data['post_desc_uid']);

		$template->assign_vars(array(
			'S_EDIT_REASON'			=> ($mode == 'edit') ? true : false,
			'EDIT_REASON_TEXT'		=> ($mode == 'edit') ? $post_data['edit_reason'] : '',
			'REPLY_DESC'			=> $post_data['post_desc'],
			'U_ACTION'				=> ($mode == 'edit') ? $tracker->api->build_url('edit_pid', array($project_id, $ticket_id, $post_id)) : $tracker->api->build_url('reply', array($project_id, $ticket_id)),
		));
	}

	if ($project_id && $ticket_id && !$mode && $tracker->api->can_manage)
	{
		$tracker->api->update_last_visit($ticket_id);
	}

	$sql_array = array(
		'SELECT'	=> 't.*,
						a.attach_id,
						a.is_orphan,
						a.physical_filename,
						a.real_filename,
						a.extension,
						a.mimetype,
						a.filesize,
						a.filetime,
						p.project_id,
						p.project_cat_id,
						pc.project_name,
						p.project_group,
						p.project_security,
						p.ticket_security as project_ticket_security,
						p.project_enabled,
						u1.user_colour as ticket_user_colour,
						u1.username as ticket_username,
						u2.user_colour as assigned_user_colour,
						u2.username as assigned_username,
						c.component_name,
						v.version_name',

		'FROM'		=> array(
			TRACKER_TICKETS_TABLE	=> 't',
		),

		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(TRACKER_PROJECT_TABLE => 'p'),
				'ON'	=> 't.project_id = p.project_id',
			),
			array(
				'FROM'	=> array(TRACKER_PROJECT_CATS_TABLE => 'pc'),
				'ON'	=> 'p.project_cat_id = pc.project_cat_id',
			),
			array(
				'FROM'	=> array(TRACKER_ATTACHMENTS_TABLE => 'a'),
				'ON'	=> 't.ticket_id = a.ticket_id AND a.post_id = 0',
			),
			array(
				'FROM'	=> array(USERS_TABLE => 'u1'),
				'ON'	=> 'u1.user_id = t.ticket_user_id',
			),
			array(
				'FROM'	=> array(USERS_TABLE => 'u2'),
				'ON'	=> 'u2.user_id = t.ticket_assigned_to',
			),
			array(
				'FROM'	=> array(TRACKER_COMPONENTS_TABLE => 'c'),
				'ON'	=> 't.component_id = c.component_id',
			),
			array(
				'FROM'	=> array(TRACKER_VERSION_TABLE => 'v'),
				'ON'	=> 't.version_id = v.version_id',
			),
		),

		'WHERE'		=> 't.ticket_id = ' . $ticket_id,
	);

	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);

	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if (!$row || ($row['ticket_hidden'] == TRACKER_TICKET_HIDDEN && !$tracker->api->can_manage) || ($row['project_enabled'] == TRACKER_PROJECT_DISABLED && !$tracker->api->can_manage) || (($row['project_security'] || ($row['project_ticket_security'] && $row['ticket_security'])) && !$tracker->api->can_manage && $row['ticket_user_id'] != $user->data['user_id']))
	{
		trigger_error('TRACKER_TICKET_NO_EXIST');
	}

	$tracker->api->generate_nav($row, $ticket_id);

	if ($mode != 'reply' || $mode != 'edit')
	{
		if ($tracker->api->can_manage && $update && $user->data['is_registered'] && $auth->acl_get('u_tracker_view') && $auth->acl_get('u_tracker_post'))
		{
			if (!check_form_key('add_post'))
			{
				trigger_error('FORM_INVALID');
			}

			$data = array(
				'ticket_assigned_to'	=> request_var('au', 0),
				'status_id'				=> request_var('cs', 0),
				'priority_id'			=> request_var('pr', 0),
				'severity_id'			=> request_var('s', 0),
			);

			$tracker->api->update_ticket($data, $ticket_id);
			$tracker->api->process_notification($data, $row);

			$tracker->back_link('TRACKER_TICKET_UPDATED', 'TRACKER_UPDATED_RETURN', $project_id, $ticket_id);
		}
	}

	if ($submit_mod && ($tracker->api->can_manage || $auth->acl_get('u_tracker_edit_global')) && (!$mode || $mode == 'history'))
	{
		$action = request_var('action', '');
		switch ($action)
		{
			case 'lock':
			case 'unlock':
				$tracker->api->manage_lock($action, $ticket_id);
				redirect(build_url());
			break;

			case 'security':
			case 'unsecurity':
				$tracker->api->manage_security($action, $ticket_id);
				redirect(build_url());
			break;

			case 'hide':
			case 'unhide':
				if ($tracker->api->can_manage)
				{
					$tracker->api->manage_hide($action, $ticket_id);
					redirect(build_url());
				}
			break;

			case 'move':
				$projects = $tracker->api->get_projects();

				if (sizeof($projects) < 2)
				{
					trigger_error('TRACKER_NOT_ENOUGH_PROJECTS');
				}

				if (!confirm_box(true))
				{
					$template->assign_vars(array(
						'S_PROJECT_SELECT'		=> $tracker->api->project_select_options($projects, $project_id),
					));

					confirm_box(false, '', build_hidden_fields(array(
						'p'					=> $project_id,
						't'					=> $ticket_id,
						'submit_mod'		=> true,
						'action'			=> 'move',
					)), 'tracker/tracker_move.html');
				}

				if ($to_project_id = request_var('to_project_id', 0))
				{
					$tracker->api->move_ticket($project_id, $to_project_id, $ticket_id);
				}
			break;

			default:
			break;
		}

	}

	$ticket_mod = '';
	if ($tracker->api->can_manage || $auth->acl_get('u_tracker_edit_global'))
	{
		$ticket_mod .= ($row['ticket_status'] == TRACKER_TICKET_UNLOCKED) ? '<option value="lock">' . $user->lang['TRACKER_LOCK_TICKET'] . '</option>' : '<option value="unlock">' . $user->lang['TRACKER_UNLOCK_TICKET'] . '</option>';
		$ticket_mod .= ($tracker->api->can_manage && $row['project_ticket_security']) ? (($row['ticket_security'] == TRACKER_TICKET_UNSECURITY) ? '<option value="security">' . $user->lang['TRACKER_SECURITY_TICKET'] . '</option>' : '<option value="unsecurity">' . $user->lang['TRACKER_UNSECURITY_TICKET'] . '</option>') : '';
		$ticket_mod .= ($tracker->api->can_manage) ? (($row['ticket_hidden'] == TRACKER_TICKET_UNHIDDEN) ? '<option value="hide">' . $user->lang['TRACKER_HIDE_TICKET'] . '</option>' : '<option value="unhide">' . $user->lang['TRACKER_UNHIDE_TICKET'] . '</option>') : '';
		$ticket_mod .= '<option value="move">' . $user->lang['TRACKER_MOVE_TICKET'] . '</option>';
	}

	$s_ticket_reply = ($mode == 'reply' || $mode == 'edit') ? true : false;

	if ($row['attach_id'] && $auth->acl_get('u_tracker_download'))
	{
		$tracker->display_ticket_attachment($row);
	}

	$can_attach = false;
	if ($s_ticket_reply)
	{
		$can_attach = (file_exists($phpbb_root_path . $tracker->api->config['attachment_path']) && $config['allow_attachments'] && @is_writable($phpbb_root_path . $tracker->api->config['attachment_path']) && $auth->acl_get('u_tracker_attach') && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;
	}

	$use_data = (sizeof($tracker->errors) || $preview || $add_attachment || $remove_attachment) ? true : false;

	$s_ticket_severity = $tracker->api->get_type_option('severity', $project_id);
	$s_ticket_priority = $tracker->api->get_type_option('priority', $project_id);

	$option_data = array(
		'status_id'				=> ($use_data) ? $data['status_id'] : $row['status_id'],
		'ticket_assigned_to'	=> ($use_data) ? $data['ticket_assigned_to'] : $row['ticket_assigned_to'],
		'severity_id'			=> ($use_data) ? $data['severity_id'] : $row['severity_id'],
		'priority_id'			=> ($use_data) ? $data['priority_id'] : $row['priority_id'],
		'ticket_hidden'			=> ($use_data) ? $data['ticket_hidden'] : $row['ticket_hidden'],
		'ticket_status'			=> ($use_data) ? $data['ticket_status'] : $row['ticket_status'],
	);

	$template->assign_vars(array(
		'S_TICKET_REPLY'			=> $s_ticket_reply,
		'S_MANAGE_TICKET'			=> $tracker->api->can_manage,
		'S_MANAGE_TICKET_MOD'		=> ($tracker->api->can_manage || $auth->acl_get('u_tracker_edit_global')) ? true : false,

		'S_SHOW_PHP'				=> $tracker->api->projects[$project_id]['show_php'],
		'S_SHOW_DBMS'				=> $tracker->api->projects[$project_id]['show_dbms'],

		'L_TRACKER_TICKET_PHP_DETAIL'	=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_php'] . '_DETAIL'),
		'L_TRACKER_TICKET_DBMS_DETAIL'	=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_dbms'] . '_DETAIL'),

		'S_CAN_ATTACH'				=> ($can_attach) ? true : false,
		'S_DISPLAY_NOTICE'			=> (($auth->acl_get('u_tracker_download') && $row['attach_id']) || !$row['attach_id']) ? false : true,
		'S_FORM_ENCTYPE'			=> ($can_attach) ? ' enctype="multipart/form-data"' : '',
		'S_IS_LOCKED'				=> ($option_data['ticket_status'] == TRACKER_TICKET_LOCKED) ? true : false,

		'U_UPDATE_ACTION'			=> ($tracker->api->can_manage) ? $tracker->api->build_url('ticket', array($project_id, $ticket_id)) : '',
		'S_STATUS_OPTIONS'			=> (!$tracker->api->can_manage) ? '' : $tracker->api->status_select_options($option_data['status_id']),
		'S_ASSIGN_USER_OPTIONS'		=> (!$tracker->api->can_manage) ? '' : $tracker->api->user_select_options($option_data['ticket_assigned_to'], $row['project_group'], $project_id),
		'S_SEVERITY_OPTIONS'		=> (!$s_ticket_severity || !$tracker->api->can_manage) ? '' : $tracker->api->select_options($project_id, 'severity', $option_data['severity_id']),
		'S_PRIORITY_OPTIONS'		=> (!$s_ticket_priority || !$tracker->api->can_manage) ? '' : $tracker->api->select_options($project_id, 'priority', $option_data['priority_id']),

		'S_TICKET_MOD' 				=> ($ticket_mod != '') ? '<select name="action">' . $ticket_mod . '</select>' : '',

		'S_CAN_POST_TRACKER'		=> $auth->acl_get('u_tracker_post'),
		'REPLY_IMG'					=> ($row['ticket_status'] == TRACKER_TICKET_LOCKED) ? $user->img('button_topic_locked', 'TOPIC_LOCKED') :$user->img('button_topic_reply', 'POST_REPLY'),
		'EDIT_IMG' 					=> $user->img('icon_post_edit', 'EDIT_POST'),
		'DELETE_IMG' 				=> $user->img('icon_post_delete', 'DELETE_POST'),

		'EDITED_MESSAGE'			=> $tracker->api->fetch_edited_by($row, 'ticket'),
		'EDIT_REASON'				=> $row['edit_reason'],

		'S_CAN_DELETE'				=> $tracker->check_permission('delete', $project_id, true),
		'U_DELETE'					=> $tracker->api->build_url('delete', array($project_id, $ticket_id)),
		'S_CAN_EDIT'				=> $tracker->api->check_edit($row['ticket_time'], $row['ticket_user_id']),
		'U_EDIT'					=> $tracker->api->build_url('edit', array($project_id, $ticket_id)),

		'L_TITLE'					=> $tracker->api->projects[$project_id]['project_name'] . ' - ' .  $tracker->api->get_type_option('title', $project_id),
		'L_TITLE_EXPLAIN'			=> sprintf($user->lang['TRACKER_REPLY_EXPLAIN'], $row['ticket_title']),
		'U_POST_REPLY_TICKET'		=> $tracker->api->build_url('reply', array($project_id, $ticket_id)),
		'U_SEND_PM'					=> $tracker->api->build_url('compose_pm', array($row['ticket_user_id'])),
		'U_REPORTERS_TICKETS'		=> $tracker->api->build_url('project_st_u', array($project_id, TRACKER_ALL, $row['ticket_user_id'], $version_id, $component_id)),

		'U_VIEW_TICKET_HISTORY'		=> ($mode == 'history') ? $tracker->api->build_url('ticket', array($project_id, $ticket_id)) : $tracker->api->build_url('history', array($project_id, $ticket_id)),
		'L_TICKET_HISTORY'			=> ($mode == 'history') ? $user->lang['TRACKER_HIDE_TICKET_HISTORY'] : $user->lang['TRACKER_VIEW_TICKET_HISTORY'],

		'U_WATCH_TICKET'			=> ($is_subscribed) ? $tracker->api->build_url('unsubscribe_t', array($project_id, $ticket_id)) : $tracker->api->build_url('subscribe_t', array($project_id, $ticket_id)),
		'L_WATCH_TICKET'			=> ($is_subscribed) ? $user->lang['TRACKER_UNWATCH_TICKET'] : $user->lang['TRACKER_WATCH_TICKET'],

		'TRACKER_REPLY_DETAIL'		=> $user->lang['TRACKER_REPLY_DETAIL'] . (($tracker->api->config['send_email']) ? $user->lang['TRACKER_REPLY_DETAIL_EMAIL'] : ''),

		'ERROR'						=> (sizeof($tracker->errors)) ? implode('<br />', $tracker->errors) : '',
		'PROJECT_NAME'				=> $row['project_name'],
		'TICKET_ASSIGNED_TO'		=> $tracker->api->get_assigned_to($project_id, $row['ticket_assigned_to'], $row['assigned_username'], $row['assigned_user_colour']),
		'TICKET_REPORTED_BY'		=> get_username_string('full', $row['ticket_user_id'], $row['ticket_username'], $row['ticket_user_colour']),
		'TICKET_ID'					=> $row['ticket_id'],
		'TICKET_TITLE'				=> $row['ticket_title'],
		'TICKET_DESC'				=> generate_text_for_display($row['ticket_desc'], $row['ticket_desc_uid'], $row['ticket_desc_bitfield'], $row['ticket_desc_options']),
		'TICKET_STATUS'				=> '(' . strtolower($tracker->api->set_status($row['status_id'])) . ')',
		'TICKET_STATUS_DETAILS'		=> $tracker->api->set_status($row['status_id']),
		'TICKET_CLOSED'				=> $tracker->api->is_closed($row['status_id']),
		'TICKET_HIDDEN'				=> ($option_data['ticket_hidden'] == TRACKER_TICKET_HIDDEN) ? true : false,
		'TICKET_LAST_VISIT'			=> (!empty($row['last_visit_user_id'])) ? sprintf($user->lang['TRACKER_LAST_VISIT'], get_username_string('full', $row['last_visit_user_id'], $row['last_visit_username'], $row['last_visit_user_colour']), $user->format_date($row['last_visit_time'])) : '',
		'TICKET_TIME'				=> $user->format_date($row['ticket_time']),

		'S_TICKET_COMPONENT'		=> $tracker->api->get_type_option('component', $project_id),
		'S_TICKET_VERSION'			=> $tracker->api->get_type_option('version', $project_id),
		'S_TICKET_PRIORITY'			=> $s_ticket_priority,
		'S_TICKET_SEVERITY'			=> $s_ticket_severity,
		'S_TICKET_ENVIRONMENT'		=> $tracker->api->get_type_option('environment', $project_id),

		'TICKET_COMPONENT'			=> (empty($row['component_name'])) ? $user->lang['TRACKER_UNKNOWN'] : $tracker->api->set_lang_name($row['component_name']),
		'TICKET_VERSION'			=> (empty($row['version_name'])) ? $user->lang['TRACKER_UNKNOWN'] : $tracker->api->set_lang_name($row['version_name']),
		'TICKET_PRIORITY'			=> (!isset($tracker->api->priority[$row['priority_id']])) ? $user->lang['TRACKER_UNKNOWN'] : $tracker->api->set_lang_name($tracker->api->priority[$row['priority_id']]),
		'TICKET_SEVERITY'			=> (!isset($tracker->api->severity[$row['severity_id']])) ? $user->lang['TRACKER_UNKNOWN'] : $tracker->api->set_lang_name($tracker->api->severity[$row['severity_id']]),
		'TICKET_PHP'				=> (empty($row['ticket_php'])) ? $user->lang['TRACKER_UNKNOWN'] : $row['ticket_php'],
		'TICKET_DBMS'				=> (empty($row['ticket_dbms'])) ? $user->lang['TRACKER_UNKNOWN'] : $row['ticket_dbms'],
	));

	if ($row['project_ticket_security'] && ($auth->acl_get('u_tracker_ticket_security') || $tracker->api->can_manage))
	{
		$option_data['ticket_security'] = ($use_data) ? $data['ticket_security'] : $row['ticket_security'];

		$template->assign_vars(array(
			'S_TICKET_SECURITY'			=> true,
			'TICKET_SECURITY'			=> ($option_data['ticket_security'] == TRACKER_TICKET_SECURITY) ? true : false,
		));
	}

	switch ($mode)
	{
		case 'history':
			$tracker->display_history($ticket_id, $project_id);
		break;

		case 'reply':
			$tracker->display_review($ticket_id);
		break;

		default:
			$tracker->display_comments($ticket_id, $project_id, $start);
		break;
	}

	// Output page
	page_header($user->lang['TRACKER'] . ' - ' . $tracker->api->get_type_option('title', $project_id) . ' - ' . $tracker->api->projects[$project_id]['project_name'], false);

	$template->set_filenames(array(
		'body' => 'tracker/tracker_tickets_view_body.html')
	);

	page_footer();
}
else if ($project_id && ($mode == 'add' || $mode == 'edit'))
{
	add_form_key('add_ticket');

	if ($mode == 'edit' && !$preview && !$submit)
	{
		$sql_array = array(
			'SELECT'	=> 't.*,
							a.attach_id,
							a.poster_id,
							a.is_orphan,
							a.physical_filename,
							a.real_filename,
							a.extension,
							a.mimetype,
							a.filesize,
							a.filetime',

			'FROM'		=> array(
				TRACKER_TICKETS_TABLE	=> 't',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(TRACKER_ATTACHMENTS_TABLE => 'a'),
					'ON'	=> 't.ticket_id = a.ticket_id AND a.post_id = 0',
				),
			),

			'WHERE'		=> 't.ticket_id = ' . $ticket_id,
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);

		$ticket_data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$ticket_data)
		{
			trigger_error('TRACKER_TICKET_NO_EXIST');
		}

		$tracker->api->check_edit($ticket_data['ticket_time'], $ticket_data['ticket_user_id'], false);

		if ($ticket_data['attach_id'])
		{
			$attachment_data = array(
				'poster_id'				=> $ticket_data['poster_id'],
				'filesize'				=> $ticket_data['filesize'],
				'mimetype'				=> $ticket_data['mimetype'],
				'extension'				=> $ticket_data['extension'],
				'physical_filename'		=> $ticket_data['physical_filename'],
				'real_filename'			=> $ticket_data['real_filename'],
				'filetime'				=> $ticket_data['filetime'],
				'attach_id'				=> $ticket_data['attach_id'],
			);
		}
	}
	else
	{
		$ticket_data = array(
			'ticket_title'				=> utf8_normalize_nfc(request_var('ticket_title', '', true)),
			'ticket_desc'				=> utf8_normalize_nfc(request_var('ticket_desc', '', true)),
			'ticket_php'				=> utf8_normalize_nfc(request_var('ticket_php', '', true)),
			'ticket_dbms'				=> utf8_normalize_nfc(request_var('ticket_dbms', '', true)),
			'component_id'				=> request_var('component_id', 0),
			'version_id'				=> request_var('version_id', 0),
			'ticket_time'				=> time(),
			'ticket_user_id'			=> $user->data['user_id'],
			'ticket_desc_bitfield'		=> '',
			'ticket_desc_options'		=> 7,
			'ticket_desc_uid'			=> '',
			'status_id'					=> TRACKER_NEW_STATUS,
			'project_id'				=> $project_id,
		);

		if ($tracker->api->can_manage)
		{
			$ticket_data += array(
				'ticket_hidden'				=> request_var('ticket_hidden', 0),
				'ticket_security'			=> request_var('ticket_security', 0),
				'ticket_status'				=> request_var('ticket_status', 0),
			);
		}

		if ($tracker->api->projects[$project_id]['ticket_security'] && ($auth->acl_get('u_tracker_ticket_security') || $tracker->api->can_manage))
		{
			$ticket_data += array(
				'ticket_security'			=> request_var('ticket_security', 0),
			);
		}

	}

	if ($mode == 'edit' && ($preview || $submit || $add_attachment || $remove_attachment))
	{
		unset($ticket_data['ticket_user_id'], $ticket_data['ticket_time'], $ticket_data['status_id']);

		$ticket_data += array(
			'edit_reason'	=> utf8_normalize_nfc(request_var('edit_reason', '', true)),
			'edit_time' 	=> time(),
			'edit_user' 	=> $user->data['user_id'],
		);
	}

	if ($add_attachment)
	{
		$filedata = $tracker->api->add_attachment('attachment', $tracker->errors);

		if (sizeof($filedata))
		{
			$tracker->api->posting_gen_attachment_data($filedata);
		}
	}
	else if (sizeof($attachment_data))
	{
		if ($remove_attachment)
		{
			$tracker->api->remove_attachment($attachment_data);
		}
		else
		{
			$tracker->api->posting_gen_attachment_data($attachment_data);
		}
	}

	if ($submit)
	{
		if (!check_form_key('add_ticket'))
		{
			$tracker->errors[] = $user->lang['FORM_INVALID'];
		}

		if ($ticket_data['ticket_title'] && $ticket_data['ticket_desc'] && !sizeof($tracker->errors))
		{
			generate_text_for_storage($ticket_data['ticket_desc'], $ticket_data['ticket_desc_uid'], $ticket_data['ticket_desc_bitfield'], $ticket_data['ticket_desc_options'], true, true, true);

			if ($mode == 'add')
			{
				$ticket_id = $tracker->api->add_ticket($ticket_data);

				if (sizeof($attachment_data))
				{
					$tracker->api->update_attachment($attachment_data, $ticket_id);
				}
			}
			else if ($mode == 'edit')
			{
				$tracker->api->update_ticket($ticket_data, $ticket_id, true);

				if (sizeof($attachment_data))
				{
					$tracker->api->update_attachment($attachment_data, $ticket_id);
				}
			}
			else
			{
				trigger_error('NO_MODE');
			}

			$tracker->back_link('TRACKER_TICKET_SUBMITTED', 'TRACKER_SUBMITTED_RETURN', $project_id, $ticket_id);
		}
		else
		{
			if (!$ticket_data['ticket_title'])
			{
				$tracker->errors[] = $user->lang['TRACKER_TICKET_TITLE_ERROR'];
			}

			if (!$ticket_data['ticket_desc'])
			{
				$tracker->errors[] = $user->lang['TRACKER_TICKET_DESC_ERROR'];
			}
		}
	}

	if ($preview)
	{
		$preview_data = array(
			'text'		=> $ticket_data['ticket_desc'],
			'uid'		=> $ticket_data['ticket_desc_uid'],
			'bitfield'	=> $ticket_data['ticket_desc_bitfield'],
			'options'	=> $ticket_data['ticket_desc_options'],
		);

		generate_text_for_storage($preview_data['text'], $preview_data['uid'], $preview_data['bitfield'], $preview_data['options'], true, true, true);

		$template->assign_vars(array(
			'S_PREVIEW'			=> true,
			'TICKET_PREVIEW'	=> generate_text_for_display($preview_data['text'], $preview_data['uid'], $preview_data['bitfield'], $preview_data['options']),
		));
	}

	// Assign index specific vars
	$ticket_desc = generate_text_for_edit($ticket_data['ticket_desc'], $ticket_data['ticket_desc_uid'], $ticket_data['ticket_desc_options']);
	$can_attach = (file_exists($phpbb_root_path . $tracker->api->config['attachment_path']) && $config['allow_attachments'] && @is_writable($phpbb_root_path . $tracker->api->config['attachment_path']) && $auth->acl_get('u_tracker_attach') && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;
	$template->assign_vars(array(
		'L_TITLE'					=> $tracker->api->projects[$project_id]['project_name'] . ' - ' . $tracker->api->get_type_option('title', $project_id),
		'L_TITLE_EXPLAIN'			=> sprintf($user->lang['TRACKER_ADD_EXPLAIN'], $tracker->api->projects[$project_id]['project_name'], $tracker->api->get_type_option('title', $project_id)) . (($tracker->api->config['send_email']) ? $user->lang['TRACKER_ADD_EXPLAIN_EMAIL'] : ''),
		'ERROR'						=> (sizeof($tracker->errors)) ? implode('<br />', $tracker->errors) : '',

		'S_EDIT_REASON'				=> ($mode == 'edit') ? true : false,
		'S_FORM_ENCTYPE'			=> ($can_attach) ? ' enctype="multipart/form-data"' : '',
		'S_COMPONENT_OPTIONS'		=> $tracker->api->select_options($project_id, 'component', $ticket_data['component_id']),
		'S_VERSION_OPTIONS'			=> $tracker->api->select_options($project_id, 'version', $ticket_data['version_id']),
		'S_CAN_ATTACH'				=> $can_attach,
		'S_TICKET_COMPONENT'		=> $tracker->api->get_type_option('component', $project_id),
		'S_TICKET_VERSION'			=> $tracker->api->get_type_option('version', $project_id),
		'S_TICKET_ENVIRONMENT'		=> $tracker->api->get_type_option('environment', $project_id),

		'EDIT_REASON_TEXT'			=> ($mode == 'edit') ? $ticket_data['edit_reason'] : '',
		'PROJECT_ID'				=> $project_id,
		'PROJECT_NAME'				=> $tracker->api->projects[$project_id]['project_name'],
		'PROJECT_TYPE'				=> $tracker->api->get_type_option('title', $project_id),

		'S_SHOW_PHP'				=> $tracker->api->projects[$project_id]['show_php'],
		'S_SHOW_DBMS'				=> $tracker->api->projects[$project_id]['show_dbms'],

		'L_TRACKER_TICKET_PHP'					=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_php']),
		'L_TRACKER_TICKET_PHP_EXPLAIN'			=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_php'] . '_EXPLAIN'),
		'L_TRACKER_TICKET_PHP_EXPLAIN_BAD'		=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_php'] . '_EXPLAIN_BAD'),
		'L_TRACKER_TICKET_PHP_EXPLAIN_GOOD'		=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_php'] . '_EXPLAIN_GOOD'),

		'L_TRACKER_TICKET_DBMS'					=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_dbms']),
		'L_TRACKER_TICKET_DBMS_EXPLAIN'			=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_dbms'] . '_EXPLAIN'),
		'L_TRACKER_TICKET_DBMS_EXPLAIN_BAD'		=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_dbms'] . '_EXPLAIN_BAD'),
		'L_TRACKER_TICKET_DBMS_EXPLAIN_GOOD'	=> $tracker->api->set_lang_name($tracker->api->projects[$project_id]['lang_dbms'] . '_EXPLAIN_GOOD'),

		'TICKET_TITLE'				=> $ticket_data['ticket_title'],
		'TICKET_DESC'				=> $ticket_desc['text'],
		'TICKET_PHP'				=> $ticket_data['ticket_php'],
		'TICKET_DBMS'				=> $ticket_data['ticket_dbms'],

		'U_ACTION'					=> ($mode == 'edit') ? $tracker->api->build_url('edit', array($project_id, $ticket_id)) : $tracker->api->build_url('add', array($project_id)),
	));

	if ($tracker->api->can_manage)
	{
		$template->assign_vars(array(
			'S_MANAGE_TICKET'			=> ($tracker->api->can_manage) ? true : false,
			'S_IS_LOCKED'				=> ($ticket_data['ticket_status'] == TRACKER_TICKET_LOCKED) ? true : false,
			'TICKET_HIDDEN'				=> ($ticket_data['ticket_hidden'] == TRACKER_TICKET_HIDDEN) ? true : false,
		));
	}

	if ($tracker->api->projects[$project_id]['ticket_security'] && ($auth->acl_get('u_tracker_ticket_security') || $tracker->api->can_manage))
	{
		$template->assign_vars(array(
			'S_TICKET_SECURITY'			=> true,
			'TICKET_SECURITY'			=> ($ticket_data['ticket_security'] == TRACKER_TICKET_SECURITY) ? true : false,
		));
	}

	// Output page
	page_header($user->lang['TRACKER'] . ' - ' . $tracker->api->get_type_option('title', $project_id) . ' - ' . $tracker->api->projects[$project_id]['project_name'], false);

	$tracker->api->generate_nav($tracker->api->projects[$project_id]);

	$template->set_filenames(array(
		'body' => 'tracker/tracker_tickets_add_body.html')
	);

	page_footer();
}
else
{
	$tracker->display_index($project_cat_id);
}

?>