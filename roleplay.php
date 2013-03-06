<?php

define('IN_PHPBB', true);
$phpbb_root_path = './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

ini_set('display_errors', false);
error_reporting(E_ALL & ~E_NOTICE);

$roleplay 		= request_var("roleplay_id",0);
$post_id 		= request_var("post_id", 0);
$location 		= request_var("location",0);
$start 		= request_var("start", -1);
$roleplay_name 	= request_var("roleplay_name","");
$place_name 	= request_var("place_name","");
$character_name = request_var("character_name","");
$limit = 20;

if (strlen($roleplay_name) > 0) {
	$sql = "SELECT id,title,url,description,introduction,introduction_bitfield,introduction_uid,owner,require_approval,type,featured,player_slots,updated,status,created FROM rpg_roleplays
			WHERE url = '".$db->sql_escape($roleplay_name)."'";
	$result = $db->sql_query($sql);
	if (!$row = $db->sql_fetchrow($result)) {
		header("HTTP/1.0 404 Not Found");
		page_header('404 Error: '.$_SERVER['REQUEST_URI'].' Not Found');

		$template->set_filenames(array(
			'body' => '404_body.html',)
		);

		page_footer();
		exit;
	}
	$db->sql_freeresult($result);
	$roleplay_data = $row;
	$roleplay = $row['id'];
} else {
	$sql = "SELECT id,title,url,description,introduction,introduction_bitfield,introduction_uid,owner,require_approval,type,featured,player_slots,updated,status,created FROM rpg_roleplays WHERE id = ".$db->sql_escape(urlify($roleplay));
	$result = 	$db->sql_query($sql);
	$row = 		$db->sql_fetchrow($result);
	$db->sql_freeresult($result);
}

if (strlen($place_name) > 0) {
	$sql 			= "SELECT id, name, synopsis, description, description_uid, description_bitfield, owner, url, parent_id, status FROM rpg_places WHERE url = \"".$db->sql_escape($place_name)."\" AND roleplay_id = ".$roleplay;
	$result 		= 	$db->sql_query($sql);
	$place_row 			= 	$db->sql_fetchrow($result);
	$place_data 	= $place_row;
	$location 		= $place_row['id'];
	$db->sql_freeresult($result);

	if (empty($place_row)) {
		header("HTTP/1.0 404 Not Found");
		page_header('404 Error: '.$_SERVER['REQUEST_URI'].' Not Found');

		$template->set_filenames(array(
			'body' => '404_body.html',)
		);

		page_footer();
		exit;
	} else {

		$sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $place_row['owner'];
		$placeOwnerResult = $db->sql_query($sql);
		$place_owner = $db->sql_fetchrow($placeOwnerResult);
		$db->sql_freeresult($placeOwnerResult);

		$place_row['ownername'] = $place_owner['username'];

	}

}

$game_masters = array();
$sql = 'SELECT user_id FROM gateway_users WHERE user_id = '.(int) $roleplay_data['owner'].' OR user_id IN (SELECT user_id FROM rpg_permissions WHERE roleplay_id = '.(int) $roleplay_data['id'].' AND isCoGM = 1)';
$result = $db->sql_query($sql);
while ($gm_row = $db->sql_fetchrow($result)) {
  $game_masters[] = $gm_row['user_id'];
}
$db->sql_freeresult($result);


//trigger_error(" Roleplay: $roleplay <br />Location: $location<br />Roleplay Name: $roleplay_name<br />Place Name: $place_name<br />Character Name: $character_name ");

if (strlen($character_name) > 0) {

	$sql = "SELECT c.*,u.username,r.title,r.url as roleplay_url,count(c.id) as results FROM rpg_characters c
				INNER JOIN gateway_users u ON c.owner = u.user_id
				INNER JOIN rpg_roleplays r ON c.roleplay_id = r.id
				WHERE c.url = \"".$db->sql_escape($character_name)."\" AND roleplay_id = $roleplay";

	$result = $db->sql_query($sql);
	if (!$character 	= $db->sql_fetchrow($result)) {
		header("HTTP/1.0 404 Not Found");
		page_header('404 Error: '.$_SERVER['REQUEST_URI'].' Not Found');

		$template->set_filenames(array(
			'body' => '404_body.html',)
		);

		page_footer();
		exit;
	}
	$total_options = (int) $db->sql_fetchfield('results');
	$db->sql_freeresult($result);

  $pagination_url = 'http://www.roleplaygateway.com/roleplay/'.$character['roleplay_url'].'/characters/'.$character['url'].'';

  $sql = 'SELECT count(*) as sightings FROM rpg_content_tags WHERE character_id = '.(int) $character['id'];
  $contentResult = $db->sql_query($sql);
  $character['sightings'] = $db->sql_fetchfield('sightings');
  $db->sql_freeresult($contentResult);

  $template->assign_vars(array(
      'PAGINATION'        => generate_pagination($pagination_url, $character['sightings'], $limit, $start),
      'PAGE_NUMBER'       => on_page($character['sightings'], $limit, $start),
      'TOTAL_SIGHTINGS'   => (int) $character['sightings'],
      'ROLEPLAY_NAME'     => $roleplay_data['title'],
      'ROLEPLAY_URL'      => $roleplay_data['url'],
      'URL'               => $roleplay_data['url'],
      'ID'               	=> $roleplay_data['id']
  ));

	$sql = 'SELECT c.*,u.user_id,u.username,p.name as place,p.url FROM rpg_content c
				INNER JOIN rpg_content_tags t ON c.id = t.content_id
				LEFT OUTER JOIN gateway_users u
					ON c.author_id = u.user_id
				LEFT OUTER JOIN rpg_places p
					ON c.place_id = p.id
				WHERE t.character_id = '.(int) $character['id'] . ' ORDER BY c.written ASC';

  $limit = 20;
  /* SPECIAL PAGINATION HACK */
	if ($start == -1) {
		$start = floor($total_activity / $limit) * $limit;
	}

	$result = $db->sql_query_limit($sql, $limit, $start);
	while ($row = $db->sql_fetchrow($result)) {

    $row['content'] = generate_text_for_display($row['text'], $row['bbcode_uid'], $row['bbcode_bitfield'], 7);

    if ($row['type'] == 'Dialogue') {
      $row['tokens'] = explode(' ', $row['content']);
      if ($row['tokens'][0] == '/say') {
        $newContent = array_slice($row['tokens'], 1);
        $row['content'] = implode(' ', $newContent);
      }
    }

		$template->assign_block_vars('activity', array(
			'S_BBCODE_ALLOWED'		=> true,
			'S_SMILIES_ALLOWED'		=> true,
			'S_CAN_EDIT'			=> (($auth->acl_get('m_')) || ($row['author_id'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
			'S_IS_DIALOGUE'			=> ($row['type'] == 'Dialogue') ? true : false,
			'ID'	 				=> $row['id'],
			'AUTHOR'	 			=> get_username_string('full', @$row['user_id'], @$row['username']),
			'PLAYER_ID' 			=> @$row['user_id'],
			'LOCATION' 				=> @$row['place'],
			'LOCATION_ID' 			=> @$row['place_id'],
			'LOCATION_URL' 			=> urlify(@$row['place']),
			'CONTENT'       => $row['content'],
			'TIME_AGO'				=> timeAgo(strtotime($row['written'])),
			'CHARACTER_NAME'		=> $character['name'],
			'CHARACTER_URL'			=> $character['url'],
		));

    $sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $row['id'] . '';
    $tags_result = $db->sql_query($sql);
    while ($tags_row = $db->sql_fetchrow($tags_result)) {
      $template->assign_block_vars('activity.characters', array(
        'ID'        => $tags_row['id'],
        'NAME'      => $tags_row['name'],
        'URL'       => $tags_row['url'],
        'SYNOPSIS'  => $tags_row['synopsis'],
      )); 
    }
    $db->sql_freeresult($tags_result);

	}
	$db->sql_freeresult($result);
	
	$sql= 'SELECT id, name, synopsis, url FROM rpg_places WHERE id = '.(int) $character['location'];
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {

		 $template->assign_vars(array(
      'PLACE_NAME' => $row['name'],
      'PLACE_URL' => $row['url']
		 ));

	}
	$db->sql_freeresult($result);	
	
	
	
	$sql = 'SELECT * FROM rpg_groups WHERE 
	          id IN (SELECT group_id FROM rpg_group_members WHERE character_id = '.(int) $character['id'] . ')';
  $limit = 20;
	$result = $db->sql_query_limit($sql, $limit, $start);
	while ($row = $db->sql_fetchrow($result)) {

		 $template->assign_block_vars('groups', array(
			'ID'		    => $row['id'],
			'NAME'		  => $row['name'],
			'SYNOPSIS'	=> $row['synopsis'],
			'SLUG'	=> $row['slug'],
		 ));

	}
	$db->sql_freeresult($result);
	
	
	$template->assign_vars(array(
	  'S_PAGE_ONLY'   => true,
	  'S_IS_ABANDONED'  => ($character['owner'] < 4) ? true : false,
		'S_CAN_EDIT'				=> (($auth->acl_get('m_')) || ($character['owner'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
		'S_IS_ABANDONED'  => ($character['owner'] > 1) ? false : true,
		'S_IS_ADOPTABLE'  => ($character['isAdoptable'] == 1) ? true : false,		
		'ROLEPLAY_NAME'				=> $character['title'],
		'ROLEPLAY_URL'				=> $character['roleplay_url'],
		'OWNER_USERNAME'            => get_username_string('full', $character['owner'], $character['username']), // don't forget the comma.  we're building an array here.
		'CHARACTER_NAME'            => $character['name'],
		'CHARACTER_ID'              => $character['id'],
		'CHARACTER_SYNOPSIS'        => $character['synopsis'],
		'CHARACTER_URL'             => $character['url'],
		'CHARACTER_DESCRIPTION'     => generate_text_for_display($character['description'], $character['description_uid'], $character['description_bitfield'], 7),
		'CHARACTER_PERSONALITY'     => generate_text_for_display($character['personality'], $character['personality_uid'], $character['personality_bitfield'], 7),
		'CHARACTER_EQUIPMENT'       => generate_text_for_display($character['equipment'], $character['equipment_uid'], $character['equipment_bitfield'], 7),
		'CHARACTER_HISTORY'         => generate_text_for_display($character['history'], $character['history_uid'], $character['history_bitfield'], 7),
	));
	
	page_header($character['name'] . ' | ' . $roleplay_data['title'] . ' | ' . $config['sitename']);

	$template->set_filenames(array(
		'body' => 'characters_profile_body.html')
	);

	page_footer();	
	
} elseif ($roleplay) {


/* 		$allowed_users = array(
			7664,
			7669,
			12793,
			5365,
			12717,
			12609, // kris
			15855, // jyll
			5365, // Kronos
			// Twitter users
			16584, // jvalenti57 
			16593, // sdwrage
			16595, // dylanmcintosh
			16961, // Empererlou
			7687, // Frug
		);
		
		$allowed_usernames = array(
			'Sarcyn',
			'queenofdarkness',
			'SilencexGolden',
			'chicagofats',
			'Omega_Pancake',
			'ldraes',
			'Angel-Chii',
			'dreamsdontlast',
			'Sweet Angel Jocelyn',
			'Aryx Noi',
			'Myth',
			'Law',
			'Ryand-Smith',
			'Ylanne',
			// 'Kouketsu',
			'Kronos',
			'Ottoman',
			'AzricanRepublic',
			'Conumbra',
			'Conquerer_Man',
			'Andreis',
			'ShatteredSoul',
			'admiralmcgregor',
			'CuriousVisitor',
			'Alucroas',
			'Tetrino',
			'dinocular',
			'Mencith',
			'Combatant876',
			'Huge Roach',
		);
		
		if ((!$auth->acl_get('m_')) && (!in_array($user->data['user_id'], $allowed_users) && (!in_array($user->data['username'], $allowed_usernames))))
		{
			trigger_error('NOT_AUTHORISED');
		}
 */

	$sql_where = ($location > 0) ? " AND a.place_id = $location" : "";
	$active_tab = ($location > 0) ? "subPanels('activity');" : "subPanels('introduction')";

	$can_post = false;

	$sql = "SELECT * FROM rpg_roleplays WHERE id = ".(int) $roleplay;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	
	if ($row['status'] == "Closed") {
		trigger_error('This roleplay has been flagged as "Closed".');
	}

	$introduction = generate_text_for_display($row['introduction'], $row['introduction_uid'], $row['introduction_bitfield'], 7);

	$roleplay_name = $row['title'];

	$sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $row['owner'];
	$result = $db->sql_query($sql);
	while ($owner_row = $db->sql_fetchrow($result)) {
		$row['ownername'] = $owner_row['username'];
	}
	$db->sql_freeresult($result);
	
  switch ($row['status']) {
    case 'Open':
      $row['statusColor'] = '#cec';
    break;
    case 'Closed':
      $row['statusColor'] = '#ecc';
    break;
    case 'Completed':
      $row['statusColor'] = '#FFD700';
    break;    
    default:
      $row['statusColor'] = '#ccc';
    break;
  }

	include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);


	$sql = 'SELECT username, user_id FROM gateway_users WHERE user_id IN ('.implode(',', $game_masters).')';
	$gmResult = $db->sql_query($sql);
	while ($gmRow = $db->sql_fetchrow($gmResult)) {
		$gameMasters[] = get_username_string('full', $gmRow['user_id'], $gmRow['username']);
	}
	$db->sql_freeresult($gmResult);
	
	$template->assign_vars(array(
		'S_CAN_EDIT'		=> (($auth->acl_get('a_')) || ($place_row['owner'] == $user->data['user_id']) || ($row['owner'] == $user->data['user_id']) || (group_memberships(2625, $user->data['user_id'], true) )) ? true : false,
		'S_IS_MODERATOR'	=> ($auth->acl_get('m_') || $auth->acl_get('a_')) ? true : false,
		'S_CAN_REVIEW'		=> ($auth->acl_get('m_') || $auth->acl_get('a_') || group_memberships(2629, $user->data['user_id'], true)) ? true : false,
		'S_SINGLE_PLACE'	=> ($location > 0) ? true : false,
		'S_IS_LOCKED'			=> ($place_row['status'] == 'Locked') ? true : false,
		'S_STREAM_ENABLED'	=> (request_var('start',0) > 0) ? false : true,
		'ID' 				=> $row['id'],
		'TITLE' 			=> $roleplay_name,
		'URL' 				=> urlify($roleplay_name),
		'GAME_MASTER' 		=>  get_username_string('full', $row['owner'], $row['ownername']),
		//'GAME_MASTER' 		=>  $row['ownername'],
		'GAME_MASTERS' 		=> implode(", ",$gameMasters),
		'DESCRIPTION'		=> $row['description'],
		'REQUIRE_APPROVAL'	=> ($row['require_approval'] == 1) ? '<span style="color:red;">Yes</span>' : '<span style="color:green;">No</span>',
		'INTRODUCTION'		=> $introduction,
		'ROLEPLAY_CITATIONS'		=> $row['citations'],
		'ACTIVE_TAB'		=> $active_tab,
		'ACTIVITY'			=> @$activity,
		'STATUS' => generateStatusTag($row['status']),
		'S_HAS_NEWS'	=> ($row['id'] == 1) ? true : false,
		'S_PAGE_ONLY'   => true,
		'S_DISPLAY_ORPHANAGE' => ($row['id'] == 1) ? false : true,
	));



	
	$sql = "SELECT id, name, url, synopsis, description, description_uid, description_bitfield, owner, sovereignty FROM rpg_places WHERE roleplay_id = ".(int) $roleplay." AND id > 0 AND id < 5000000 AND url IS NOT NULL AND length(url) > 0";
	$result = $db->sql_query_limit($sql,50);
	while ($row = $db->sql_fetchrow($result)) {
	
		if ($row) {
			$template->assign_vars(array(
				'S_HAS_PLACES'			=> true,
				)
			);
		}
	
	
		if ($location > 0) {
			$description = generate_text_for_display($place_row['description'], $place_row['description_uid'], $place_row['description_bitfield'], 7);

			$template->assign_vars(array(
				'S_HAS_PLACES'				=> true,
				'PLACE_URL'						=> ($location > 0) ? $place_row['url'] : null,
				'PLACE_ID'						=> ($location > 0) ? $location : null,
				'PLACE_NAME'					=> ($location > 0) ? $place_row['name'] : null,
				'PLACE_SYNOPSIS'			=> ($location > 0) ? $place_row['synopsis'] : null,
				'PLACE_DESCRIPTION'		 => ($location > 0) ? $description : null,
				'PLACE_OWNER_USERNAME' => get_username_string('full', $place_row['owner'], $place_row['ownername']),
				'S_CAN_EDIT'	=>	(($auth->acl_get('m_')) || ($place_row['owner'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
			));
			$selected = ($row['id'] == $location) ? ' selected' : '';
		}

	   $template->assign_block_vars('places', array(
		  'NAME' 			=> $row['name'],
		  'SYNOPSIS'		=> $row['synopsis'],
		  'ID'				=> $row['id'],
		  'URL'				=> $row['url'],
	   ));	
	   
	   $template->assign_block_vars('posting_places', array(
		  'NAME' 			=> $row['name'],
		  'ID'				=> $row['id'],
		  'SELECTED'		=> @$selected,
	   ));
	  
	}
	$db->sql_freeresult($result);

	if (!empty($location)) {

		$sql = 'SELECT id, name, synopsis, url, owner FROM rpg_places WHERE id = '.(int) $place_data['parent_id'];
		$parentResult = $db->sql_query($sql);
		$parent = $db->sql_fetchrow($parentResult);
		$db->sql_freeresult($parentResult);

		$template->assign_vars(array(
			'PARENT_ID'				=> $parent['id'],
			'PARENT_SLUG'			=> $parent['url'],
			'PARENT_NAME'			=> $parent['name'],
			'PARENT_SYNOPSIS'	=> $parent['synopsis'],
		));

		$sql = 'SELECT id, name, synopsis, url FROM rpg_places WHERE parent_id = '.(int) $location . ' AND url IS NOT NULL AND length(url) > 0 ORDER BY name ASC';
		$childrenResult = $db->sql_query($sql);
		while ($child = $db->sql_fetchrow($childrenResult)) {
		  $template->assign_block_vars('children', array(
			  'NAME' 			=> $child['name'],
			  'SYNOPSIS'	=> $child['children'],
			  'ID'				=> $child['id'],
			  'SLUG'			=> $child['url'],
		  ));
		}
		$db->sql_freeresult($childrenResult);
	}

	
	// BEGIN REVIEWS
	$sql = 'SELECT r.*,username FROM rpg_reviews r INNER JOIN gateway_users u ON r.author = u.user_id WHERE r.roleplay_id = '.(int) $roleplay .' ';
	$reviews_result = $db->sql_query($sql);
	while ($reviews_row = $db->sql_fetchrow($reviews_result)) {
	
		
		$scores = array(
			scoreRubric($reviews_row['characterization']),
			scoreRubric($reviews_row['plot']),
			scoreRubric($reviews_row['depth']),
			scoreRubric($reviews_row['style']),
			scoreRubric($reviews_row['mechanics']),
			scoreRubric($reviews_row['overall'])
		);
	
		$template->assign_block_vars('reviews', array(
			'USERNAME'      			=> get_username_string('full', $reviews_row['author'], $reviews_row['username']),
			'CHARACTERIZATION'      => $reviews_row['characterization'],
			'PLOT'          		=> $reviews_row['plot'],
			'DEPTH'          		=> $reviews_row['depth'],
			'STYLE'          		=> $reviews_row['style'],
			'MECHANICS'          	=> $reviews_row['mechanics'],
			'OVERALL'          		=> $reviews_row['overall'],
			'COMMENTARY'         	=> $reviews_row['commentary'],
			'SCORE'					=>	array_sum($scores)
		));		
	}
	$db->sql_freeresult($reviews_result);
	// END REVIEWS
	
	// BEGIN EVENTS
	$sql = 'SELECT * FROM rpg_events e WHERE e.roleplay_id = '.(int) $roleplay .' ';
	$events_result = $db->sql_query($sql);
	while ($events_row = $db->sql_fetchrow($events_result)) {
	
		$template->assign_block_vars('events', array(
			'TITLE'				=> $events_row['title'],
			'LOCATION'			=> @$events_row['location'],
			'URL'				=> $events_row['url'],
			'DESCRIPTION'		=> generate_text_for_display($events_row['description'], $events_row['description_uid'], $events_row['description_bitfield'], 7),
			'START_TIME'		=> $user->format_date(strtotime($events_row['start_time'])),
			'START_TIME_ISO'	=> date('c',strtotime($events_row['start_time'])),
			'END_TIME'			=> $user->format_date(strtotime($events_row['end_time'])),
			'END_TIME_ISO'		=> date('c',strtotime($events_row['end_time']))
		));		
	}
	$db->sql_freeresult($events_result);	
	// END EVENTS
	
	// BEGIN QUESTS
	$sql = 'SELECT id, name, synopsis, status, creator, "someone" as username FROM rpg_quests
			  WHERE roleplay_id = '.(int) $roleplay .' /* AND synopsis IS NOT NULL */ AND status <> "Cancelled" ORDER BY id DESC';

	$result = $db->sql_query_limit($sql, $limit, $start);
	while ($quest = $db->sql_fetchrow($result)) {
	  
	  $sql = 'SELECT count(*) AS characters FROM rpg_quest_characters WHERE quest_id = '.(int) $row['id']. ' /* AND status = "Member" */ ';
	  $memberResult = $db->sql_query($sql);
	  $quest['characters'] = $db->sql_fetchfield('characters');
	  $db->sql_freeresult($memberResult);

		$template->assign_block_vars('quests', array(
			'ID' 				=> $quest['id'],
			'NAME' 				=> $quest['name'],
			'SLUG' 				=> @$quest['slug'],
			'STATUS' 			=> $quest['status'],
			'OWNER_USERNAME'	=> get_username_string('full', $quest['creator'], $quest['username']),
			'SYNOPSIS'			=> $quest['synopsis'],
			'POSTS'     		=> $quest['posts'],
			'CHARACTERS'     	=> $quest['characters'],
			'UNIQUE_PLAYERS'    => $quest['players'],
		));
	}
	$db->sql_freeresult($result);
	// END QUESTS
	
	// BEGIN ARCS
	$sql = 'SELECT id, name, description, creator, slug FROM rpg_arcs
			  WHERE roleplay_id = '.(int) $roleplay .' ORDER BY id DESC';

	$result = $db->sql_query_limit($sql, $limit, $start);
	while ($arc = $db->sql_fetchrow($result)) {
	  
	  $sql = 'SELECT count(*) AS posts FROM rpg_arc_content WHERE arc_id = '.(int) $row['id'];
	  $memberResult = $db->sql_query($sql);
	  $arc['posts'] = $db->sql_fetchfield('posts');
	  $db->sql_freeresult($memberResult);

		$template->assign_block_vars('bundles', array(
			'ID' 				=> $arc['id'],
			'NAME' 				=> $arc['name'],
			'SLUG' 				=> @$arc['slug'],
			'OWNER_USERNAME'	=> get_username_string('full', $arc['creator'], $arc['username']),
			'DESCRIPTION'			=> $arc['description'],
			'POSTS'     		=> $arc['posts'],
		));
	}
	$db->sql_freeresult($result);
	// END ARCS
	
	if ($roleplay_data['require_approval'] == 1) {
		$sql = "SELECT id,name,username,user_id,owner,synopsis,url FROM rpg_characters c
				INNER JOIN gateway_users u ON c.owner = u.user_id
			WHERE c.roleplay_id = ".$db->sql_escape($roleplay). " AND c.owner <> 0 AND c.status = 'Approved' AND c.isAdoptable = 0";
	} else {
		$sql = "SELECT id,name,username,user_id,owner,synopsis,url FROM rpg_characters c
					INNER JOIN gateway_users u ON c.owner = u.user_id
				WHERE c.roleplay_id = ".$db->sql_escape($roleplay) . " AND c.owner <> 0 AND isAdoptable = 0 ORDER BY length(image) DESC";
	}
	
	if ($roleplay_data['id'] == 1) {	
		$sql = "SELECT id,name,username,user_id,owner,synopsis,url FROM rpg_characters c
					INNER JOIN gateway_users u ON c.owner = u.user_id
				WHERE c.roleplay_id = ".$db->sql_escape($roleplay). " AND length(synopsis) > 1  AND c.synopsis NOT LIKE \"%http%\" AND length(image) > 1 AND c.isAdoptable = 0
					GROUP BY owner
					ORDER BY id";	

		$sql = "SELECT id,name,username,user_id,owner,synopsis,url FROM rpg_characters c
					INNER JOIN gateway_users u ON c.owner = u.user_id
				WHERE c.roleplay_id = 1 AND length(synopsis) > 1  AND c.synopsis NOT LIKE \"%http%\" AND length(image) > 1 AND c.isAdoptable = 0
					GROUP BY owner
					ORDER BY id";	
		
	}
		
	$limit = 18;
	$template->assign_vars(array(
		'S_MORE_CHARACTERS'		=> (@$roleplay_data['characters'] > $limit) ? true : false,
		'MORE_CHARACTERS_COUNT'	=> (@$roleplay_data['characters'] > $limit) ? @$roleplay_data['characters'] - $limit : null,
		)
	);

	$result = $db->sql_query_limit($sql, $limit);
	while ($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('characters', array(
			'ID' 				=> $row['id'],
			'NAME' 				=> $row['name'],
			'URL' 				=> $row['url'],
			'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
			'SYNOPSIS'			=> $row['synopsis'],
		));
		
		$template->assign_block_vars('taggable_characters', array(
			'ID' 				=> $row['id'],
			'NAME' 				=> $row['name'],
			'URL' 				=> $row['url'],
			'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
			'SYNOPSIS'			=> $row['synopsis'],
		));		
	   
		if (($user->data['user_id'] == $row['owner']) && ($user->data['user_id'] > 1)) {
			$can_post = true;
		}
	}
	$db->sql_freeresult($result);

	$sql = 'SELECT count(DISTINCT author_id) as authorCount FROM rpg_roleplay_author_stats WHERE roleplay_id = '.(int) $roleplay;
	$myresult = $db->sql_query($sql);
	while ($myrow = $db->sql_fetchrow($myresult)) {
    $template->assign_vars(array(
      'TOTAL_AUTHORS' 			=> $myrow['authorCount'],
    ));
	}
	$db->sql_freeresult($myresult);
	
	
  $sql = 'SELECT total_words FROM rpg_roleplay_stats WHERE roleplay_id = '.(int) $roleplay['id']. '';
  $memberResult = $db->sql_query($sql);
  $thisRoleplay['words'] = $db->sql_fetchfield('total_words');
  $db->sql_freeresult($memberResult);
	
	
  $sql = 'SELECT * FROM rpg_roleplay_author_stats WHERE roleplay_id = '.(int) $roleplay .' ORDER BY words DESC LIMIT 25';
  $authorResult = $db->sql_query($sql);
  while ($author = $db->sql_fetchrow($authorResult)) {

    $sql = 'SELECT username FROM gateway_users WHERE user_id = '.(int) $author['author_id'];
    $userResult = $db->sql_query($sql);
    $thisRoleplay['authors'][$author['author_id']] = $db->sql_fetchrow($userResult);
    $db->sql_freeresult($userResult);
    
    $thisRoleplay['authors'][$author['author_id']]['words'] = $author['words'];
    $thisRoleplay['authors'][$author['author_id']]['percentage'] = ($author['words'] / (($thisRoleplay['words'] > 0) ? $thisRoleplay['words'] : 1 )) * 100;
    
    $template->assign_block_vars('authors', array(
      'ID'         => $author['author_id'],
      'USERNAME'   => $thisRoleplay['authors'][$author['author_id']]['username'],
      'PERCENTAGE' => round($thisRoleplay['authors'][$author['author_id']]['percentage'], 1),
    ));
    
  }
  $db->sql_freeresult($authorResult);
	

	if ($roleplay_data['require_approval'] == 1) {
		$sql = "SELECT id,name,synopsis,owner,username,user_id FROM rpg_characters c
					INNER JOIN gateway_users u ON c.owner = u.user_id
				WHERE c.owner = ".$user->data['user_id']." AND c.roleplay_id = ".$db->sql_escape($roleplay)." AND c.status = 'Approved' AND user_id<>0";
	} else {
		$sql = "SELECT id,name,synopsis,owner,username,user_id FROM rpg_characters c
					INNER JOIN gateway_users u ON c.owner = u.user_id
				WHERE c.owner = ".$user->data['user_id']." AND c.roleplay_id = ".$db->sql_escape($roleplay)." AND user_id<>0";	
	}
	$result = $db->sql_query_limit($sql,20);
	while ($row = $db->sql_fetchrow($result)) {
		$template->assign_block_vars('posting_characters', array(
			'NAME' 			=> $row['name'],
			'ID' 			=> $row['id'],
			'SYNOPSIS'		=> $row['synopsis'],
		));			
		
		if (($user->data['user_id'] == $row['owner']) && ($user->data['user_id'] > 1)) {
			$can_post = true;
		}		
	}
	$db->sql_freeresult($result);
	
	if (($roleplay_data['require_approval'] == 1)) {
	
	
		$sql = "SELECT id,name,url,synopsis,owner,username,user_id FROM rpg_characters c
					INNER JOIN gateway_users u ON c.owner = u.user_id
				WHERE c.roleplay_id = ".$db->sql_escape($roleplay)." AND c.isAdoptable = 0 AND status = 'Submitted' AND user_id<>0";
		$result = $db->sql_query_limit($sql,20);
		while ($row = $db->sql_fetchrow($result)) {
			$template->assign_block_vars('pending_characters', array(
				'ID' 				=> $row['id'],
				'NAME' 				=> $row['name'],
				'URL' 				=> $row['url'],
				'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
				'SYNOPSIS'			=> $row['synopsis'],
			));		
		}
		$db->sql_freeresult($result);
	}	


	$sql = "SELECT id,name,synopsis,owner,username,user_id,url FROM rpg_characters c
				INNER JOIN gateway_users u ON c.creator = u.user_id
			WHERE c.roleplay_id = ".$db->sql_escape($roleplay)." AND c.isAdoptable = 1 AND c.status = 'Approved'";
	$result = $db->sql_query_limit($sql,20);
	while ($row = $db->sql_fetchrow($result)) {

    $sql          = 'SELECT count(*) as count FROM rpg_content_tags WHERE character_id = '.(int) $row['id'] ;
    $countResult  = $db->sql_query($sql);
    $row['sightings'] = $db->sql_fetchfield('count');
    $db->sql_freeresult($countResult);

		$template->assign_block_vars('orphanage', array(
			'ID' 				=> $row['id'],
			'NAME' 				=> $row['name'],
      'SIGHTINGS'   => (int) $row['sightings'],
			'URL' 				=> $row['url'],
			'OWNER_USERNAME'	=> get_username_string('full', $row['creator'], $row['username']),
			'SYNOPSIS'			=> $row['synopsis'],
		));		
	}
	$db->sql_freeresult($result);	
		
	$sql = "SELECT id,name FROM rpg_characters WHERE roleplay_id = 1 AND owner = ".$user->data['user_id'];
	$result = $db->sql_query_limit($sql,20);
	while ($row = $db->sql_fetchrow($result)) {
	
		$template->assign_vars(array(
			'S_MULTIVERSE' 					=> ($roleplay == 1) ? false : true,
			'S_HAS_MULTIVERSE_CHARACTERS' 	=> ($roleplay == 1) ? false : true,
		));
	
		$template->assign_block_vars('multiverse_characters', array(
			'NAME' 			=> $row['name'],
			'ID' 			=> $row['id'],
		));
	}
	$db->sql_freeresult($result);

	$start 		= @request_var('start', 0);
	$limit   	= @request_var('limit', 25);
	
	$limit 		= ($limit > 100) ? 100 : $limit;
	
	if ($post_id > 0) {
	
		$contentID = $post_id;
	
		# HOLY MOTHER OF GOD
		$sql = '(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a
					LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
					LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
					 
				 WHERE a.id = '.(int) $contentID.' AND a.roleplay_id = '.  (int) $roleplay . ')
				 
				 
				 UNION DISTINCT
				 
				(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (PRIMARY) 
					LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
					LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
					 
				 WHERE a.id < '.(int) $contentID.' AND a.roleplay_id = '.  (int) $roleplay . ' ORDER BY a.written DESC LIMIT 10)
				 
				 UNION DISTINCT
				 
				(SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a USE INDEX (PRIMARY)
					LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
					LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
					 
				 WHERE a.id > '.(int) $contentID.' AND a.roleplay_id = '.  (int) $roleplay . ' ORDER BY a.written ASC LIMIT 10)	

					ORDER BY written ASC;
				 
				 ';		
		$result = @$db->sql_query($sql);
		

		
		while ($content_row = $db->sql_fetchrow($result)) {
		
			if ($character_id = $content_row['character_id']) {
		
				$sql = 'SELECT id,name,url FROM rpg_characters WHERE id = '. (int) $character_id ;
				$character_result = $db->sql_query($sql);
				$character = $db->sql_fetchrow($character_result);
				$db->sql_freeresult($character_result);
			}
		
      $content_row['content'] = generate_text_for_display($content_row['text'], $content_row['bbcode_uid'], $content_row['bbcode_bitfield'], 7);

      if ($content_row['type'] == 'Dialogue') {
        $content_row['tokens'] = explode(' ', $content_row['content']);
        if ($content_row['tokens'][0] == '/say') {
          $newContent = array_slice($content_row['tokens'], 1);
          $content_row['content'] = implode(' ', $newContent);
        }
      }

			$template->assign_block_vars('activity', array(
				'S_BBCODE_ALLOWED'		=> true,
				'S_SMILIES_ALLOWED'		=> true,
				'S_CAN_EDIT'			=> 	(($auth->acl_get('m_')) || ($content_row['author_id'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
				'S_IS_DIALOGUE'			=> ($content_row['type'] == 'Dialogue') ? true : false,
				'ID'	 				=> $content_row['id'],
				'AUTHOR'	 			=> get_username_string('full', @$content_row['user_id'], @$content_row['username']),
				'PLAYER_ID' 			=> @$content_row['user_id'],
				'LOCATION' 				=> @$content_row['place'],
				'LOCATION_ID' 			=> @$content_row['place_id'],
				'LOCATION_URL' 			=> urlify(@$content_row['place']),
				'CONTENT'				=> $content_row['content'],
				'TIME_AGO'				=> timeAgo(strtotime($content_row['written'])),
				'CHARACTER_NAME'		=> $character['name'],
				'CHARACTER_URL'			=> $character['url'],
				'TIME_ISO'				=> date('c',strtotime($content_row['written'])),
			));

				$sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $content_row['id'] . '';
				$tags_result = $db->sql_query($sql);
				while ($tags_row = $db->sql_fetchrow($tags_result)) {
					$template->assign_block_vars('activity.characters', array(
						'ID'		=> $tags_row['id'],
						'NAME'		=> $tags_row['name'],
						'URL'		=> $tags_row['url'],
						'SYNOPSIS'	=> $tags_row['synopsis'],
					));	
				}
				$db->sql_freeresult($tags_result); 


			$activity = ($location > 0) ? $row['place'] : "Activity";

			$location_description = ($location > 0) ? $row['description'] : "";
		}
		$db->sql_freeresult($result);	
	
	}  else {

		$sql = "SELECT posts FROM rpg_roleplay_stats WHERE roleplay_id =" . $roleplay;
		$posts_result = $db->sql_query($sql);
		$post_count = $db->sql_fetchrow($posts_result);
		$db->sql_freeresult($posts_result);

		// BEGIN HAX
		// TODO: collect this earlier in the code
		if ($post_count['posts'] >= 50000) {
			if ($location > 0) {
			
				$sql = 'SELECT count(*) as content FROM rpg_content WHERE roleplay_id = '.(int) $roleplay .' AND place_id = '.(int) $location . "" ;
				$result = $db->sql_query($sql);
				$total_activity = (int) $db->sql_fetchfield('content');	
				$db->sql_freeresult($result);
			
				$sql = 'SELECT url FROM rpg_places WHERE id = '.(int) $location;
				$result = $db->sql_query($sql);
				$place_url = (string) $db->sql_fetchfield('url');
				$db->sql_freeresult($result);
				
			} else {
			
				$sql = 'SELECT count(*) as content FROM rpg_content WHERE roleplay_id = '.(int) $roleplay . "" ;
				$result = $db->sql_query($sql);
				$total_activity = (int) $db->sql_fetchfield('content');
				$db->sql_freeresult($result);
			
			}
		} else {
			if ($location > 0) {
			
				$sql = 'SELECT count(*) as content FROM rpg_content WHERE roleplay_id = '.(int) $roleplay .' AND place_id = '.(int) $location ;
				$result = $db->sql_query($sql);
				$total_activity = (int) $db->sql_fetchfield('content');	
				$db->sql_freeresult($result);
			
				$sql = 'SELECT url FROM rpg_places WHERE id = '.(int) $location;
				$result = $db->sql_query($sql);
				$place_url = (string) $db->sql_fetchfield('url');
				$db->sql_freeresult($result);
				
			} else {
			
				$sql = 'SELECT count(*) as content FROM rpg_content WHERE roleplay_id = '.(int) $roleplay;
				$result = $db->sql_query($sql);
				$total_activity = (int) $db->sql_fetchfield('content');
				$db->sql_freeresult($result);
			
			}
		}
		// END HAX
		
    if ($location > 0) {
      $sql = "SELECT a.*, a.type FROM rpg_content a
               WHERE a.place_id = ".  (int) $location ."  
               ORDER BY a.written ASC";

    } else {              
      $sql = "SELECT a.*, a.type FROM rpg_content a
              WHERE a.roleplay_id = ".  (int) $roleplay . " 
              ORDER BY a.written ASC";
    }

	  /* SPECIAL PAGINATION HACK */
		if ($start == 0) {
			if ($total_activity % 25 == 0) {
				$start = (floor($total_activity / $limit) * $limit) - $limit; // - $limit fixes 25 post multiples
			} else {
				$start = (floor($total_activity / $limit) * $limit);
			}
		}
	  $result = @$db->sql_query_limit($sql,$limit,$start);
			
		
		
		while ($content_row = $db->sql_fetchrow($result)) {
		
			if ($character_id = $content_row['character_id']) {
		
				$sql = 'SELECT id,name,url FROM rpg_characters WHERE id = '. (int) $character_id ;
				$character_result = $db->sql_query($sql);
				$character = $db->sql_fetchrow($character_result);
				$db->sql_freeresult($character_result);
			}


				$sql = 'SELECT name as place FROM rpg_places WHERE id = '. (int) $content_row['place_id'] ;
				$thisResult = $db->sql_query($sql);
				$content_row = array_merge($content_row,  $db->sql_fetchrow($thisResult));
				$db->sql_freeresult($thisResult);
				
				$sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '. (int) $content_row['author_id'] ;
				$thisResult = $db->sql_query($sql);
				$content_row = @array_merge($content_row,  $db->sql_fetchrow($thisResult));
				$db->sql_freeresult($thisResult);

        $content_row['oldContent'] = $content_row['content'] = generate_text_for_display($content_row['text'], $content_row['bbcode_uid'], $content_row['bbcode_bitfield'], 7);

        if ($content_row['type'] == 'Dialogue') {
          $content_row['tokens'] = explode(' ', $content_row['content']);
          if ($content_row['tokens'][0] == '/say') {
            $newContent = array_slice($content_row['tokens'], 1);
            $content_row['content'] = implode(' ', $newContent);
          }
        }

  			$template->assign_block_vars('activity', array(
  				'S_BBCODE_ALLOWED'		=> true,
  				'S_SMILIES_ALLOWED'		=> true,
  				'S_CAN_EDIT'			=> 		(($auth->acl_get('m_')) || ($content_row['author_id'] == $user->data['user_id']) || (in_array($user->data['user_id'], $game_masters))) ? true : false,
  				'S_IS_DIALOGUE'			=> ($content_row['type'] == 'Dialogue') ? true : false,
  				'ID'	 				=> $content_row['id'],
  				'AUTHOR'	 			=> get_username_string('full', @$content_row['user_id'], @$content_row['username']),
  				'PLAYER_ID' 			=> @$content_row['user_id'],
  				'LOCATION' 				=> @$content_row['place'],
  				'LOCATION_ID' 			=> @$content_row['place_id'],
  				'LOCATION_URL' 			=> urlify(@$content_row['place']),
  				'CONTENT'				=> $content_row['content'],
  				'TIME_AGO'				=> timeAgo(strtotime($content_row['written'])),
  				'CHARACTER_NAME'		=> $character['name'],
  				'CHARACTER_URL'			=> $character['url'],
  			));

				$sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $content_row['id'] . '';
				$tags_result = $db->sql_query($sql);
				while ($tags_row = $db->sql_fetchrow($tags_result)) {
					$template->assign_block_vars('activity.characters', array(
						'ID'		=> $tags_row['id'],
						'NAME'		=> $tags_row['name'],
						'URL'		=> $tags_row['url'],
						'SYNOPSIS'	=> $tags_row['synopsis'],
					));	
				}
				$db->sql_freeresult($tags_result); 

			$activity = ($location > 0) ? $row['place'] : "Activity";

			$location_description = ($location > 0) ? $row['description'] : "";
		}
		$db->sql_freeresult($result);
	}
	


    /*
     *
     * Bookview...
	
		$sql = "SELECT a.*, a.type, p.name as place,u.username,u.user_id FROM rpg_content a
					LEFT OUTER JOIN gateway_users u ON a.author_id = u.user_id
					LEFT OUTER JOIN rpg_places p ON p.id = a.place_id
					 
				 WHERE a.roleplay_id = ".  (int) $roleplay . ' ORDER BY a.written ASC';

		$result = @$db->sql_query_limit($sql,$limit,$start);

		while ($content_row = $db->sql_fetchrow($result)) {
		
			if ($character_id = $content_row['character_id']) {
		
				$sql = 'SELECT id,name,url FROM rpg_characters WHERE id = '. (int) $character_id ;
				$character_result = $db->sql_query($sql);
				$character = $db->sql_fetchrow($character_result);
				$db->sql_freeresult($character_result);
			}
		
			$template->assign_block_vars('bookview', array(
				'S_BBCODE_ALLOWED'		=> true,
				'S_SMILIES_ALLOWED'		=> true,
				'S_CAN_EDIT'			=> ((($auth->acl_get('m_')) || ($content_row['author_id'] == $user->data['user_id']))) ? true : false,
				'S_IS_DIALOGUE'			=> ($content_row['type'] == 'Dialogue') ? true : false,
				'ID'	 				=> $content_row['id'],
				'AUTHOR'	 			=> get_username_string('full', @$content_row['user_id'], @$content_row['username']),
				'PLAYER_ID' 			=> @$content_row['user_id'],
				'LOCATION' 				=> @$content_row['place'],
				'LOCATION_ID' 			=> @$content_row['place_id'],
				'LOCATION_URL' 			=> urlify(@$content_row['place']),
				'CONTENT'				=> generate_text_for_display($content_row['text'], $content_row['bbcode_uid'], $content_row['bbcode_bitfield'], 7),
				'TIME_AGO'				=> timeAgo(strtotime($content_row['written'])),
				'CHARACTER_NAME'		=> $character['name'],
				'CHARACTER_URL'			=> $character['url'],
			));

				$sql = 'SELECT id,name,url,synopsis FROM rpg_content_tags t FORCE INDEX (PRIMARY) INNER JOIN rpg_characters c FORCE INDEX (PRIMARY) ON c.id = t.character_id WHERE content_id = '.(int) $content_row['id'] . '';
				$tags_result = $db->sql_query($sql);
				while ($tags_row = $db->sql_fetchrow($tags_result)) {
					$template->assign_block_vars('activity.characters', array(
						'ID'		=> $tags_row['id'],
						'NAME'		=> $tags_row['name'],
						'URL'		=> $tags_row['url'],
						'SYNOPSIS'	=> $tags_row['synopsis'],
					));	
				}
				$db->sql_freeresult($tags_result); 


			$activity = ($location > 0) ? $row['place'] : "Activity";

			$location_description = ($location > 0) ? $row['description'] : "";
		}
		$db->sql_freeresult($result);

    *
    *
    */

	$sql = 'SELECT url FROM rpg_roleplays WHERE id = '.(int) $roleplay;
	$result = $db->sql_query($sql);
	$roleplay_url = (string) $db->sql_fetchfield('url');
	$db->sql_freeresult($result);
		
	
	// Assign the pagination variables to the template.
	if ($start == -1) {
		$page_number = @on_page($total_activity, $limit, 0);
	} else {
		$page_number = @on_page($total_activity, $limit, $start);
	}

	$template->assign_vars(array(
		'PAGINATION'        => @generate_activity_pagination($total_activity, $limit, $start, $roleplay_url, $place_url, $selected_date),
		'PAGE_NUMBER'       => $page_number,
		'TOTAL_ACTIVITY'   => $total_activity . ' posts here',
	));	


	$sql = "SELECT thread_id FROM rpg_roleplay_threads WHERE roleplay_id = ".$db->sql_escape($roleplay). " AND type = 'Out Of Character'";
	$result = $db->sql_query_limit($sql,100);
	while ($row = $db->sql_fetchrow($result)) {
		$ooc_topics[] = $row['thread_id'];
	}
	$db->sql_freeresult($result);

	$search_limit = 5;

	if (count($ooc_topics) > 0) {
		
		$topic_id_where = create_where_clauses($ooc_topics, 'topic');
		$posts_ary = array(
				'SELECT'    => 'p.*, t.*, u.username, u.user_colour',
			
				'FROM'      => array(
					POSTS_TABLE     => 'p',
				),
			
				'LEFT_JOIN' => array(
					array(
						'FROM'  => array(USERS_TABLE => 'u'),
						'ON'    => 'u.user_id = p.poster_id'
					),
					array(
						'FROM'  => array(TOPICS_TABLE => 't'),
						'ON'    => 'p.topic_id = t.topic_id'
					),
				),
			
				'WHERE'     =>  str_replace( array('WHERE ', 'topic_id'), array('', 't.topic_id'), $topic_id_where) . '
								AND t.topic_status <> ' . ITEM_MOVED . '
								 AND t.topic_approved = 1',
			
				'ORDER_BY'  => 'p.post_id DESC',
			);

		$posts = $db->sql_build_query('SELECT', $posts_ary);

		$posts_result = $db->sql_query_limit($posts, 20);

		while( $posts_row = $db->sql_fetchrow($posts_result) )
		{
			$topic_title		= $posts_row['topic_title'];
			$post_author		= get_username_string('full', $posts_row['poster_id'], $posts_row['username'], $posts_row['user_colour']);
			$post_date			= $user->format_date($posts_row['post_time']);
			$post_link			= append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $posts_row['forum_id'] . '&amp;t=' . $posts_row['topic_id'] . '&amp;p=' . $posts_row['post_id']) . '#p' . $posts_row['post_id'];

			$post_text = nl2br($posts_row['post_text']);

			$bbcode = new bbcode(base64_encode(@$bbcode_bitfield));         
			$bbcode->bbcode_second_pass($post_text, $posts_row['bbcode_uid'], $posts_row['bbcode_bitfield']);

			$post_text = smiley_text($post_text);

			$template->assign_block_vars('ooc', array(
			'TOPIC_TITLE'       => censor_text($topic_title),
			'POST_AUTHOR'       => $post_author,
			'POST_SUBJECT'      => $posts_row['post_subject'],
			'POST_ID'       	=> $posts_row['post_id'],
			'POST_DATE'       	=> $post_date,
			'POST_LINK'       	=> $post_link,
			'POST_TEXT'         => censor_text($post_text),
			));
		}
		$db->sql_freeresult($posts_result);
		
		$sql = 'SELECT * FROM gateway_topics WHERE topic_id IN ('.implode(',',$ooc_topics).') ORDER BY topic_id DESC';
		$topics_result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($topics_result)) {
			
			$topic_id = $row['topic_id'];
			$forum_id = $row['forum_id'];

			// This will allow the style designer to output a different header
			// or even separate the list of announcements from sticky and normal topics
			$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

			// Replies
			$replies = ($auth->acl_get('m_approve', $forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];

			if ($row['topic_status'] == ITEM_MOVED)
			{
				$topic_id = $row['topic_moved_id'];
				$unread_topic = false;
			}
			else
			{
				$unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
			}

			// Get folder img, topic status/type related information
			$folder_img = $folder_alt = $topic_type = '';
			topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

			// Generate all the URIs ...
			$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . (($row['forum_id']) ? $row['forum_id'] : $forum_id) . '&amp;t=' . $topic_id);

			$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
			$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
			$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $user->session_id) : '';
			// www.phpBB-SEO.com SEO TOOLKIT BEGIN -> no dupe
			if ($phpbb_seo->seo_opt['no_dupe']['on']) {
				if (($replies + 1) > $phpbb_seo->seo_opt['topic_per_page']) {
					$phpbb_seo->seo_opt['topic_last_page'][$topic_id] = floor($replies / $phpbb_seo->seo_opt['topic_per_page']) * $phpbb_seo->seo_opt['topic_per_page'];
				}
			}
			// www.phpBB-SEO.com SEO TOOLKIT END -> no dupe
			// Send vars to template
			$template->assign_block_vars('topicrow', array(
				'FORUM_ID'					=> $forum_id,
				'TOPIC_ID'					=> $topic_id,
				'TOPIC_AUTHOR'				=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'TOPIC_AUTHOR_COLOUR'		=> get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'FIRST_POST_TIME'			=> $user->format_date($row['topic_time']),
				'LAST_POST_SUBJECT'			=> censor_text($row['topic_last_post_subject']),
				'LAST_POST_TIME'			=> $user->format_date($row['topic_last_post_time']),
				'LAST_VIEW_TIME'			=> $user->format_date($row['topic_last_view_time']),
				'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				
				'PAGINATION'		=> topic_generate_pagination($replies, $view_topic_url),
				'REPLIES'			=> $replies,
				'VIEWS'				=> $row['topic_views'],
				// www.phpBB-SEO.com SEO TOOLKIT BEGIN
				'TOPIC_TITLE'		=> (isset($phpbb_seo->seo_censored[$topic_id]) ) ? $phpbb_seo->seo_censored[$topic_id] : censor_text($row['topic_title']),
				// www.phpBB-SEO.com SEO TOOLKIT END
				'TOPIC_TYPE'		=> $topic_type,

				'TOPIC_FOLDER_IMG'		=> $user->img($folder_img, $folder_alt),
				'TOPIC_FOLDER_IMG_SRC'	=> $user->img($folder_img, $folder_alt, false, '', 'src'),
				'TOPIC_FOLDER_IMG_ALT'	=> $user->lang[$folder_alt],
				'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
				'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
				'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
				'ATTACH_ICON_IMG'		=> ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
				'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',

				'S_TOPIC_TYPE'			=> $row['topic_type'],
				'S_USER_POSTED'			=> (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
				'S_UNREAD_TOPIC'		=> $unread_topic,
				'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $auth->acl_get('m_report', $forum_id)) ? true : false,
				'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
				'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
				'S_HAS_POLL'			=> ($row['poll_start']) ? true : false,
				'S_POST_ANNOUNCE'		=> ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
				'S_POST_GLOBAL'			=> ($row['topic_type'] == POST_GLOBAL) ? true : false,
				'S_POST_STICKY'			=> ($row['topic_type'] == POST_STICKY) ? true : false,
				'S_TOPIC_LOCKED'		=> ($row['topic_status'] == ITEM_LOCKED) ? true : false,
				'S_TOPIC_MOVED'			=> ($row['topic_status'] == ITEM_MOVED) ? true : false,

				// www.phpBB-SEO.com SEO TOOLKIT BEGIN
				'U_NEWEST_POST'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $forum_id . '&amp;t=' . $topic_id . '&amp;view=unread') . '#unread',
				'U_LAST_POST'			=> $phpbb_seo->seo_opt['no_dupe']['on'] ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $forum_id . '&amp;t=' . $topic_id . '&amp;start=' . @intval($phpbb_seo->seo_opt['topic_last_page'][$topic_id])) . '#p' . $row['topic_last_post_id'] : append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $forum_id . '&amp;t=' . $topic_id . '&amp;p=' . $row['topic_last_post_id']) . '#p' . $row['topic_last_post_id'],
				// www.phpBB-SEO.com SEO TOOLKIT END
				'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
				'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
				'U_VIEW_TOPIC'			=> $view_topic_url,
				'U_MCP_REPORT'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $user->session_id),
				'U_MCP_QUEUE'			=> $u_mcp_queue,

				'S_TOPIC_TYPE_SWITCH'	=> (@$s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test)
			);


			$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

			if ($unread_topic)
			{
				$mark_forum_read = false;
			}

			// unset($rowset[$topic_id]);
		}
		
		$db->sql_freeresult($topics_result);
		
		
	}


	$sql = "SELECT * FROM rpg_roleplays WHERE id = ".$db->sql_escape($roleplay);
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$introduction = generate_text_for_display($row['introduction'], $row['introduction_uid'], $row['introduction_bitfield'], 7);
	$rules = generate_text_for_display($row['rules'], $row['rules_uid'], $row['rules_bitfield'], 7);

	$template->assign_vars(array(
		'ID' 					=> $row['id'],
		'TAGS'					=> display_roleplay_tags(get_roleplay_tags($row['id'])),
		'TITLE' 				=> $row['title'],
		//'GAME_MASTER' 			=> $row['owner'],
		'DESCRIPTION'			=> $row['description'],
		'INTRODUCTION'			=> $introduction,
		'RULES'					=> $rules,
		'ACTIVE_TAB'			=> $active_tab,
		'ACTIVITY'				=> @$activity,
		'LOCATION_DESCRIPTION' 	=> @$location_description,
		'S_CAN_POST'			=> $can_post
	));


	$places = get_parent_places($roleplay);
	@asort($places);	
	
	if (is_array($places)) {
		foreach (@$places as $place) {
			@$list .= @display_place_item($place,$row['url']);
		}
	}


  

	$sql = 'SELECT * FROM rpg_exits WHERE place_id IN (SELECT id FROM rpg_places WHERE roleplay_id = '.(int) $roleplay.') OR destination_id IN (SELECT id FROM rpg_places WHERE roleplay_id = '.(int) $roleplay.')';
	$exitResult = $db->sql_query($sql);
	while ($exit = $db->sql_fetchrow($exitResult)) {
    $exits[] = $exit;
	}
	$db->sql_freeresult($exitResult);

	$template->assign_vars(array(
		'PLACES_LIST'	=> 	$list,	
    'HAS_MAP'     => (count($exits) > 0 && $roleplay > 1) ? true : false,
	));

	$sql = 'SELECT id, name, synopsis, slug, s.characters, s.players, u.username, g.owner FROM rpg_groups g
          INNER JOIN rpg_group_stats s ON g.id = s.group_id
          INNER JOIN gateway_users u ON u.user_id = g.owner
          WHERE roleplay_id = '.(int) $roleplay .' /* AND synopsis IS NOT NULL */ ORDER BY s.players DESC, s.characters DESC LIMIT 20';
	$groupResult = $db->sql_query($sql);
	while ($group = $db->sql_fetchrow($groupResult)) {
		$template->assign_block_vars('groups', array(
			'NAME'						=> $group['name'],
			'SLUG'						=> $group['slug'],
			'SYNOPSIS'				=> $group['synopsis'],
			'OWNER_LINK'	    => get_username_string('full', $group['owner'], $group['username']),
			'OWNER_ID'        => $group['owner'],
			'OWNER_USERNAME'  => $group['username'],
			'SYNOPSIS'			  => $group['synopsis'],
	    'POSTS'           => $group['posts'],
	    'CHARACTERS'      => $group['characters'],
	    'UNIQUE_PLAYERS'  => $group['players'],
		));
	}
	$db->sql_freeresult($groupResult);



  if ($location > 1) {
		$seo_meta->collect('description', $place_row['name'].', a place in the '.$row['title'].' roleplay. '.$place_row['synopsis']);
		$seo_meta->collect('keywords', 'roleplay roleplaying forums chat collaborative fiction writing author ' . $place_row['synopsis'].' roleplay');

    page_header($place_row['name']. ' | '. $row['title'] . ' | RolePlayGateway');
  } else {
		$seo_meta->collect('description', $row['title'].', a roleplay on RolePlayGateway. '.$row['description']);
		$seo_meta->collect('keywords', 'roleplay roleplaying forums chat collaborative fiction writing author ' . $row['description'] .' roleplay');

	  page_header($row['title'] . ' | RolePlayGateway');
  }
  
	$template->set_filenames(array(
		'body' => 'roleplay_body.html')
	);

	page_footer();


} else {




  /**
   * ROLEPLAY LISTING
   * 
   *
   * 
   *
   * 
   *
   * 
   *
   * 
   *
   */

	$minimum_words 	= request_var('minimum_words',0);
	$sort_by		= request_var('sort','');
	$sort_direction	= request_var('sort','');
	
	switch ($sort_by) {
		case 'posts': 			      $sort_sql = 's.posts DESC'; break;
		case 'average-words': 		$sort_sql = 's.average_words DESC'; break;
		case 'words': 		        $sort_sql = 's.total_words DESC'; break;
		case 'newest': 			      $sort_sql = 'r.created DESC'; break;
		case 'activity': 		      $sort_sql = 'activity DESC'; break;
		case 'author':	 		      $sort_sql = 'u.username ASC'; break;
		case 'rating':	 		      $sort_sql = 's.roleplay_rating DESC'; break;
		default:
				$sort_sql = 'r.id DESC'; 
		break;
	}
	
	if ($user->data['is_bot'] || $user->data['is_registered'] == false) {
		$user->data['user_id'] == 1;
	}
	
	$sql = 'SELECT tag,type FROM rpg_user_filters
				WHERE user_id = '.$user->data['user_id'].'';

			
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {

		if ($row['type'] == 'Ignored') {
			$ignored_tags[] = '\''.$db->sql_escape($row['tag']).'\'';
			$ignored_tags_array[] = $row['tag'];
		}
		
		if ($row['type'] == 'Favorite') {
			$favorite_tags[] = '\''.$db->sql_escape($row['tag']).'\'';
			$favorite_tags_array[] = $row['tag'];
		}
	
		

	}
	$db->sql_freeresult($result);
	
	if (@count($ignored_tags) > 0) {
	   $ignored_tags_sql = " AND r.id NOT IN (SELECT roleplay_id FROM gateway_tags WHERE tag IN (".@implode(",",$ignored_tags) . '))';
	} else {
		$ignored_tags_sql = '';
	}

	$seo_meta->collect('description', 'Roleplay available on RolePlayGateway. Over 9000 roleplays.');
	$seo_meta->collect('keywords', 'roleplay roleplaying role playing games forums chat collaborative fiction writing author how to');

	page_header('Roleplay on RolePlayGateway');

	$start 		= @request_var('start',0);
	$limit   	= @request_var('limit', 25);
	
	$limit 		= ($limit > 100) ? 100 : $limit;
	
	
	// FEATURED ROLEPLAYS
	$sql = "SELECT id,title,type,description,owner,player_slots,username,require_approval,updated, s.total_words FROM rpg_roleplays r
				INNER JOIN gateway_users u
					ON r.owner = u.user_id
				LEFT JOIN rpg_roleplay_stats s
					ON s.roleplay_id = r.id
				WHERE r.featured = true
					AND r.status = 'Open'
				ORDER BY rand()
				LIMIT 1";
	
	
	$featured_result = @$db->sql_query($sql);

	while($row = $db->sql_fetchrow($featured_result)) {

		if ($row['require_approval'] == true) {
			$sql = "SELECT count(*) as players FROM rpg_characters WHERE roleplay_id = '".$row['id']."' AND (approved = 1 OR status = 'Approved')";
		} else {
			$sql = "SELECT count(*) as players FROM rpg_characters WHERE roleplay_id = '".$row['id']."'";
		}
		$player_result = $db->sql_query($sql);
		$row['characters'] = $db->sql_fetchfield('players');
		$db->sql_freeresult($player_result);
		
		$sql = 'SELECT count(*) as posts FROM rpg_content WHERE roleplay_id = '.$row['id'];
		$content_result = $db->sql_query($sql);
		$row['posts'] = $db->sql_fetchfield('posts');
		$db->sql_freeresult($content_result);
		
		$sql = 'SELECT average_words FROM rpg_roleplay_stats WHERE roleplay_id = '.$row['id'];
		$words_result = $db->sql_query($sql);
		$row['words_per_post'] = $db->sql_fetchfield('average_words');
		$db->sql_freeresult($words_result);
						
/* 		$sql = 'SELECT MAX(written) as written FROM rpg_content WHERE roleplay_id = '.$row['id'].'';
		$written_result = $db->sql_query($sql);
		$row['last_activity'] = ($row['posts'] > 0) ? strtotime($db->sql_fetchfield('written')) : strtotime($row['updated']);
		$db->sql_freeresult($written_result); */
		
		$row['tags'] = list_roleplay_tags($row['id']);

		$template->assign_block_vars('featured_roleplays', array(
			'S_CAN_EDIT'		=> (($auth->acl_get('a_')) || ($row['owner'] == $user->data['user_id'])) ? true : false,
			'ID'				=> $row['id'],
			'TITLE'				=> $row['title'],
			'URL'				=> urlify($row['title']),
			'DESCRIPTION'		=> $row['description'],
			'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
			'OPEN_SLOTS'		=> @$open_slots,
			'TOTAL_SLOTS'		=> number_format($row['player_slots']),
			'CHARACTERS'		=> number_format($row['characters']),
			'POSTS'				=> number_format($row['posts']),
			'WORDS' 	=> number_format($row['total_words']),
			'WORDS_PER_POST' 	=> number_format($row['words_per_post']),
			'TYPE'				=> $row['type'],
			'TYPE_DESCRIPTION'	=> @$type_description,
			'ACTIONS'			=> @$actions,
			'TAGS'				=> @display_roleplay_tags(get_roleplay_tags($row['id'])),
			'LAST_ACTIVITY'		=> $user->format_date(@$row['last_activity']),
		));
		
	}
	// free the result
	$db->sql_freeresult($result);
	$row = null;
	
	
	
	
	if ($user->data['user_id'] == 3) {
	
		$test = new Roleplay(1);
		// echo var_dump($test,true);
	}
	
	
	if (@$ignored_tags){ 
		$sql = "SELECT DISTINCT r.id,title,r.type,r.status,description,owner,player_slots,username,require_approval,r.updated, s.total_words, max(c.written) as activity, DATEDIFF(CURDATE(), max(c.written)) as age FROM rpg_roleplays r
					INNER JOIN gateway_users u
						ON r.owner = u.user_id
					LEFT JOIN rpg_roleplay_stats s
						ON s.roleplay_id = r.id
					LEFT JOIN rpg_content c
                                                ON c.roleplay_id = r.id
					WHERE r.id NOT IN (SELECT roleplay_id FROM gateway_tags WHERE roleplay_id IS NOT NULL AND tag IN  (".@implode(',',$ignored_tags)."))
						AND length(image) > 0 
						AND r.status='Open'
						AND s.total_words > 250
					GROUP BY r.id
					ORDER BY ".$sort_sql."";

					
			$ignored_result = $db->sql_query('SELECT count(r.id) as ignored_roleplays FROM rpg_roleplays r INNER JOIN gateway_tags t ON t.roleplay_id = r.id WHERE r.status = "Open" AND t.tag IN ('.@implode(',',$ignored_tags).');', 3600);
			$ignored_count = $db->sql_fetchfield('ignored_roleplays');
			$db->sql_freeresult($ignored_result);					
					
	} else {

		$sql = "SELECT r.id,title,r.type,r.status,description,owner,player_slots,username,require_approval,r.updated, s.total_words, max(c.written) as activity, DATEDIFF(CURDATE(), max(c.written)) as age FROM rpg_roleplays r
					INNER JOIN gateway_users u
						ON r.owner = u.user_id
					LEFT JOIN rpg_roleplay_stats s
						ON s.roleplay_id = r.id
          LEFT JOIN rpg_content c
                  ON c.roleplay_id = r.id
					WHERE r.status = 'Open'
						/* AND length(image) > 0
						AND r.status='Open'
						AND s.total_words > 250 */
          GROUP BY r.id
					ORDER BY ".$sort_sql."";
	}
		@$template->assign_vars(array(
				'IGNORED_TAGS' => (@count($ignored_tags) > 0) ? display_roleplay_filters($ignored_tags_array) : 'None!',
				'FAVORITE_TAGS' => (count($favorite_tags) > 0) ? display_roleplay_filters($favorite_tags_array) : 'None!',
				'IGNORED_COUNT' => number_format($ignored_count),
				'S_HAS_FILTERS' => true,
				'S_PAGE_ONLY' => true,
		));
					
		
				
				//trigger_error($sql);
				
	
					
	$result = @$db->sql_query_limit($sql,$limit,$start);

	while($row = $db->sql_fetchrow($result)) {

		
		// This code commented out because it is no longer in use...
		/*
		while($player_row = $db->sql_fetchrow($player_result)) {
			$open_slots = $row['player_slots'] - $player_row['players'];
		}
		
		$actions = "";

		if ($open_slots >= 1) {
			$actions .= '<li><a href="">Join this roleplay...</a></li>';
		}
		
		if ($user->data['user_id'] == $row['owner']) {
			$manage = '<li><a href="">[ Manage ]</a></li>';
		} else {
			$manage = "";
		}
		
		switch ($row['type']) {
			case "Casual":
				$type_description = "Beginner-level roleplay with no specific direction or storyline.";
			break;
			case "Intermediate":
				$type_description = "Intermediate-level roleplay that includes some storyline or complex plot elements.";
			break;
			case "Storyline":
				$type_description = "Epic-level roleplay that focuses on character development and a clearly defined storyline.";
			break;
		}
		*/ 

		if ($row['require_approval'] == true) {
			$sql = "SELECT count(*) as players FROM rpg_characters WHERE roleplay_id = '".$row['id']."' AND (approved = 1 OR status = 'Approved')";
		} else {
			$sql = "SELECT count(*) as players FROM rpg_characters WHERE roleplay_id = '".$row['id']."'";
		}
		$player_result = $db->sql_query($sql);
		$row['characters'] = $db->sql_fetchfield('players');
		$db->sql_freeresult($player_result);
		
		$sql = 'SELECT count(*) as posts, max(written) as written FROM rpg_content WHERE roleplay_id = '.$row['id'];
		$content_result = $db->sql_query($sql);
		$row['posts'] = $db->sql_fetchfield('posts');
		$db->sql_freeresult($content_result);

		$sql = 'SELECT average_words FROM rpg_roleplay_stats WHERE roleplay_id = '.$row['id'];
		$words_result = $db->sql_query($sql);
		$row['words_per_post'] = $db->sql_fetchfield('average_words');
		$db->sql_freeresult($words_result);
						
/* 		$sql = 'SELECT MAX(written) as written FROM rpg_content WHERE roleplay_id = '.$row['id'].'';
		$written_result = $db->sql_query($sql);
		$row['last_activity'] = ($row['posts'] > 0) ? strtotime($db->sql_fetchfield('written')) : strtotime($row['updated']);
		$db->sql_freeresult($written_result); */
		
				
		$row['tags'] = list_roleplay_tags($row['id']);
		
		

		$template->assign_block_vars('roleplays', array(
			'S_CAN_EDIT'		=> (($auth->acl_get('a_')) || ($row['owner'] == $user->data['user_id'])) ? true : false,
			'ID'				=> $row['id'],
			'TITLE'				=> $row['title'],
			'URL'				=> urlify($row['title']),
			'DESCRIPTION'		=> $row['description'],
			'OWNER_USERNAME'	=> get_username_string('full', $row['owner'], $row['username']),
			'OPEN_SLOTS'		=> @$open_slots,
			'TOTAL_SLOTS'		=> number_format($row['player_slots']),
			'CHARACTERS'		=> number_format($row['characters']),
			'POSTS'				=> number_format($row['posts']),
			'WORDS'				=> number_format($row['total_words']),
			'WORDS_PER_POST' 	=> number_format($row['words_per_post']),
			'TYPE'				=> $row['type'],
			'TYPE_DESCRIPTION'	=> @$type_description,
			'CLASS'				=> (count(@array_intersect($row['tags'],$favorite_tags_array)) > 0) ? 'favorite' : '',
			'ACTIONS'			=> @$actions,
			'TAGS'				=> @display_roleplay_tags(get_roleplay_tags($row['id'])),
			'LAST_ACTIVITY'		=> $user->format_date(@$row['last_activity']),
			'STATUS'      => generateStatusTag($row['status'])
		));
		
		// This code commented out because it is no longer in use...
		/*		
		$sql = "SELECT thread_id,topic_title FROM rpg_roleplay_threads
					INNER JOIN gateway_topics
						ON rpg_roleplay_threads.thread_id = gateway_topics.topic_id
					WHERE rpg_roleplay_threads.roleplay_id = '".$row['id']."'
						AND rpg_roleplay_threads.type = 'In Character'";
					
		$ic_thread_result = $db->sql_query_limit($sql, 10, null, 300);
		
		while($ic_thread_row = $db->sql_fetchrow($ic_thread_result)) {

			$template->assign_block_vars('roleplays.ic_threads', array(
				'THREAD_ID'	=> $ic_thread_row['thread_id'],
				'THREAD_TITLE'	=> character_limit($ic_thread_row['topic_title'],25),
			));
			
		}

		
		$sql = "SELECT thread_id,topic_title FROM rpg_roleplay_threads
					INNER JOIN gateway_topics
						ON rpg_roleplay_threads.thread_id = gateway_topics.topic_id
					WHERE rpg_roleplay_threads.roleplay_id = '".$row['id']."'
						AND rpg_roleplay_threads.type = 'Out Of Character'";
					
		$ooc_thread_result = $db->sql_query_limit($sql, 10, null, 300);
		
		while($ooc_thread_row = $db->sql_fetchrow($ooc_thread_result)) {

			$template->assign_block_vars('roleplays.ooc_threads', array(
				'THREAD_ID'	=> $ooc_thread_row['thread_id'],
				'THREAD_TITLE'	=> character_limit($ooc_thread_row['topic_title'],25),
			));
			
		}	
		
		$sql = "SELECT  gateway_users.user_id,
						gateway_users.username,
						rpg_characters.id,
						rpg_characters.name,
						rpg_characters.synopsis
					FROM rpg_roleplay_players, gateway_users, rpg_characters
					WHERE rpg_roleplay_players.roleplay_id = '".$row['id']."'
						AND	gateway_users.user_id = rpg_characters.owner
						AND	rpg_roleplay_players.character_id = rpg_characters.id";
					
		$player_result = $db->sql_query($sql);
		
		while($player_row = $db->sql_fetchrow($player_result)) {

			$template->assign_block_vars('roleplays.characters', array(
				'PLAYER_LINK'		=> get_username_string('full', $player_row['user_id'], $player_row['username']),
				'CHARACTER_NAME'	=> $player_row['name'],
				'CHARACTER_ID'		=> $player_row['id'],
				'SYNOPSIS'			=> $player_row['synopsis'],
			));
			
		}
	
		$sql = "SELECT name,id FROM rpg_characters WHERE owner = ".$user->data['user_id']." AND
					id NOT IN (SELECT character_id as id FROM rpg_roleplay_players WHERE roleplay_id = ".$row['id'].") ORDER BY id ASC";
					
		$character_result = $db->sql_query($sql);
		
		while($character_row = $db->sql_fetchrow($character_result)) {

			$template->assign_block_vars('roleplays.user_characters', array(

				'CHARACTER_NAME'	=> $character_row['name'],
				'CHARACTER_ID'		=> $character_row['id'],

			));
			
		}
		*/	
	}
	// free the result
	$db->sql_freeresult($result);
	

	// now we run the query again to get the total rows...
	// the query is identical except we count the rows instead
	$sql = "SELECT count(*) as total_roleplays FROM rpg_roleplays WHERE status = 'Open' AND length(image) > 0";
	$result = $db->sql_query($sql);

	// get the total users, this is a single row, single field.
	$total_roleplays = (int) $db->sql_fetchfield('total_roleplays');
	// free the result
	$db->sql_freeresult($result);
	
	
	
	if ($user->data['user_id'] == 4) {
		$sql = 'select distinct username from gateway_users where user_id IN (SELECT owner FROM rpg_roleplays) order by username';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			$roleplay_owners[] = $row['username'];
		}
		$db->sql_freeresult($result);
		
		$template->assign_vars(array(
			'ROLEPLAY_OWNERS' => implode("\n",$roleplay_owners)
		));
	
		
	}

	// Assign the pagination variables to the template.
	$template->assign_vars(array(
		'PAGINATION'        => @generate_roleplay_pagination($total_roleplays, $limit, $start, $sort_by),
		'PAGE_NUMBER'       => @on_page($total_roleplays, $limit, $start),
		'TOTAL_ROLEPLAYS'   => $total_roleplays . ' roleplays listed',
	));	


	$template->set_filenames(array(
		'body' => 'roleplays_body.html',)
	);

	page_footer();

}

function generate_roleplay_pagination($num_items, $per_page, $start_item, $sort = null)
{
	global $template, $user;
	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;

	$seperator = '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}
	
	if (!$sort) {
		$base_url = 'http://www.roleplaygateway.com/roleplay/';
	} else {
		$base_url = 'http://www.roleplaygateway.com/roleplay/?sort='.$sort;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';

	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}start=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}start=" . (($i - 1) * $per_page) . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}start=" . (($total_pages - 1) * $per_page) . '">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . "{$url_delim}start=" . (($on_page - 2) * $per_page) . '">' . $user->lang['PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "{$url_delim}start=" . ($on_page * $per_page) . '">' . $user->lang['NEXT'] . '</a>';
		}
	}
	
	


	return $page_string;
}


function generate_activity_pagination($num_items, $per_page, $start_item, $roleplay_url, $place_url = null, $sel_date="0000-00-00")
{
	global $template, $user;
	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;

	$seperator = '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}
	
	$base_url = 'http://www.roleplaygateway.com/roleplay/'.$roleplay_url.'/';
	
	if ($place_url) {
		$base_url .= 'places/'.$place_url.'/';
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';

	//$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '?date_set='.$sel_date.'#activity">1</a>';
  $page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . '?start=-1#activity">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}start=" . (($i - 1) * $per_page) . '#activity">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}start=" . (($i - 1) * $per_page) . '#activity">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}start=" . (($total_pages - 1) * $per_page) . '#activity">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . "{$url_delim}start=" . (($on_page - 2) * $per_page) . '#activity">' . $user->lang['PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "{$url_delim}start=" . ($on_page * $per_page) . '#activity">' . $user->lang['NEXT'] . '</a>';
		}
	}
	
	


	return $page_string;
}



/* create_where_clauses( int[] gen_id, String type )
* This function outputs an SQL WHERE statement for use when grabbing 
* posts and topics */

function create_where_clauses($gen_id, $type)
{
global $db, $auth;

    $size_gen_id = sizeof($gen_id);

        switch($type)
        {
            case 'forum':
                $type = 'forum_id';
                break;
            case 'topic':
                $type = 'topic_id';
                break;
            default:
                trigger_error('No type defined');
        }

    // Set $out_where to nothing, this will be used of the gen_id
    // size is empty, in other words "grab from anywhere" with
    // no restrictions
    $out_where = '';

    if( $size_gen_id > 0 )
    {
    // Get a list of all forums the user has permissions to read
    $auth_f_read = array_keys($auth->acl_getf('f_read', true));

        if( $type == 'topic_id' )
        {
            $sql     = 'SELECT topic_id FROM ' . TOPICS_TABLE . '
                        WHERE ' .  $db->sql_in_set('topic_id', $gen_id) . '
                        AND ' .  $db->sql_in_set('forum_id', $auth_f_read);

            $result     = $db->sql_query($sql);

                while( $row = $db->sql_fetchrow($result) )
                {
                        // Create an array with all acceptable topic ids
                        $topic_id_list[] = $row['topic_id'];
                }
			
			$db->sql_freeresult($result);

            unset($gen_id);

            $gen_id = $topic_id_list;
            $size_gen_id = sizeof($gen_id);
        }

    $j = 0;    

        for( $i = 0; $i < $size_gen_id; $i++ )
        {
        $id_check = (int) $gen_id[$i];

            // If the type is topic, all checks have been made and the query can start to be built
            if( $type == 'topic_id' )
            {
                $out_where .= ($j == 0) ? 'WHERE ' . $type . ' = ' . $id_check . ' ' : 'OR ' . $type . ' = ' . $id_check . ' ';
            }

            // If the type is forum, do the check to make sure the user has read permissions
            else if( $type == 'forum_id' && $auth->acl_get('f_read', $id_check) )
            {
                $out_where .= ($j == 0) ? 'WHERE ' . $type . ' = ' . $id_check . ' ' : 'OR ' . $type . ' = ' . $id_check . ' ';
            }    

        $j++;
        }
    }

    if( $out_where == '' && $size_gen_id > 0 )
    {
        trigger_error('A list of topics/forums has not been created');
    }

    return $out_where;
}

function get_roleplay_tags($id) {

	global $db, $auth;

	$sql = "SELECT tag FROM gateway_tags WHERE roleplay_id = ".$id;

	$result     = $db->sql_query($sql);

	while( $row = $db->sql_fetchrow($result) )
	{
		$tags[] = (string) '<a href="http://www.roleplaygateway.com/tag/'.$row['tag'].'">'.$row['tag'].'</a>';
	}
	
	$db->sql_freeresult($result);
	
	
	return $tags;

}

function list_roleplay_tags($id) {
	global $db, $auth;

	$sql = "SELECT tag FROM gateway_tags WHERE roleplay_id = ".$id;

	$result     = $db->sql_query($sql);

	while( $row = $db->sql_fetchrow($result) )
	{
		$tags[] = (string) $row['tag'];
	}
	
	$db->sql_freeresult($result);
	
	
	return $tags;
}

function display_roleplay_tags($tags) {

	$output = @implode(", ",$tags);

	return $output;
}

function display_roleplay_filters($tags) {

	foreach ($tags as $tag) {
		$controls[] = '<span class="tag"><a href="http://www.roleplaygateway.com/tag/'.$tag.'">'.$tag.'</a><a href="#" title="Delete this filter" onclick="deleteFilter(\''.$tag.'\'); return false;">X</a></span>';
	}

	return implode(', ',$controls);
}

function get_ignored_roleplays($user_id) {

	global $db, $auth;
	
	$sql = 'SELECT t.roleplay_id FROM rpg_user_filters f
				LEFT OUTER JOIN gateway_tags t
					ON t.tag = f.tag
			WHERE f.user_id = '.$user_id.' AND f.type = "Ignored"';
			
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		$ignored_roleplays[] = $row['roleplay_id'];
	}
	$db->sql_freeresult($result);

	return @implode(",",$ignored_roleplays);
}

function get_parent_places($id) {
	global $db;
	
	$sql 	= 'SELECT p.id,p.name,p.url,p.synopsis,p.owner,r.owner as roleplay_owner,s.posts,s.* FROM rpg_places p
					INNER JOIN rpg_roleplays r on p.roleplay_id = r.id
					LEFT OUTER JOIN rpg_places_stats s ON p.id = s.place_id
				WHERE p.roleplay_id = '.$id.' and (p.parent_id = "-1") AND visibility <> "Hidden"  AND p.id <> 0';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {

	  $sql = 'SELECT id, author_id, written FROM rpg_content WHERE place_id = '.(int) $row['id'] . ' ORDER BY written DESC LIMIT 1';
	  $contentResult = $db->sql_query($sql);
	  $lastPost = $db->sql_fetchrow($contentResult);
	  $db->sql_freeresult($contentResult);

	  if (!empty($lastPost)) {
		  $children[$row['id']]['last_post_time'] = timeAgo(strtotime($lastPost['written']));
		  $children[$row['id']]['last_post_id']		= $lastPost['id'];
		}

		$children[$row['id']]['id'] 					= $row['id'];
		$children[$row['id']]['name'] 					= $row['name'];
		$children[$row['id']]['url'] 					= $row['url'];
		$children[$row['id']]['owner'] 					= $row['owner'];
		$children[$row['id']]['roleplay_owner'] 		= $row['roleplay_owner'];
		$children[$row['id']]['synopsis'] 				= $row['synopsis'];
		$children[$row['id']]['posts'] 					= $row['posts'];
		$children[$row['id']]['parent'] 				= $id;
		$children[$row['id']]['children'] 				= get_place_children($row['id']);
	}
	
	return $children;
}

function get_place_children($id) {
	global $db;
	
	$sql 	= 'SELECT p.id,p.name,p.url,p.synopsis,p.owner,r.owner as roleplay_owner,s.posts FROM rpg_places p
					INNER JOIN rpg_roleplays r on p.roleplay_id = r.id
					LEFT OUTER JOIN rpg_places_stats s ON p.id = s.place_id
				WHERE p.parent_id = '.$id.' AND visibility <> "Hidden" AND p.id <> 0 AND p.url IS NOT NULL AND length(p.url) > 0
				ORDER BY p.id ASC';
	$result = $db->sql_query($sql);
	
	while ($row = $db->sql_fetchrow($result)) {

	  $sql = 'SELECT id, author_id, written FROM rpg_content WHERE place_id = '.(int) $row['id'] . ' ORDER BY written DESC LIMIT 1';
	  $contentResult = $db->sql_query($sql);
	  $lastPost = $db->sql_fetchrow($contentResult);
	  $db->sql_freeresult($contentResult);

	  if (!empty($lastPost)) {
		  $children[$row['id']]['last_post_time'] = timeAgo(strtotime($lastPost['written']));
		  $children[$row['id']]['last_post_id']		= $lastPost['id'];
		}

		$children[$row['id']]['id'] 						= $row['id'];
		$children[$row['id']]['name'] 					= $row['name'];
		$children[$row['id']]['url'] 						= $row['url'];
		$children[$row['id']]['owner'] 					= $row['owner'];
		$children[$row['id']]['roleplay_owner'] = $row['roleplay_owner'];
		$children[$row['id']]['synopsis'] 			= $row['synopsis'];
		$children[$row['id']]['posts'] 					= $row['posts'];
		$children[$row['id']]['parent'] 				= $id;
		$children[$row['id']]['children'] 			= get_place_children($row['id']);
	}
	$db->sql_freeresult($result);
	
	return @$children;
}

function display_place_item($item, $roleplay) {
	global $user, $auth;

	$output = '<div class="place">';
	
/* 	$output .= '<div class="controls" style="float:right;">';
	$output .= '<a href="javascript:toggleDiv(\'place_children_'.$item['id'].'\');">Toggle</a>';
	$output .= '</div>'; */
	
	$output .= '<div id="place_'.$item['id'].'">';
	$output .= '<img id="place_img_'.$item['id'].'" class="crisp-border" src="/roleplay/'.$roleplay.'/places/'.$item['url'].'/image" alt="'.$item['name'].': '.$item['synopsis'].'" />';
	$output .= '<div class="place-details">';
	$output .= '<h2>';
	$output .= '<span class="posts" style="float:right;">' . number_format($item['posts']) . ' posts</span>';
	$output .= '<a href="/roleplay/'.$roleplay.'/places/'.$item['url'].'/">'.$item['name'].'</a>';
	
	if (!empty($item['last_post_id']) && !empty($item['last_post_time'])) {
		$output .= ' <small>Last Activity: <a href="/roleplay/'.$roleplay.'/places/'.$item['url'].'/#roleplay'.$item['last_post_id'].'">'.$item['last_post_time'].' &raquo;</a></small>';
	}

	if (($item['roleplay_owner'] == $user->data['user_id']) || ($item['owner'] == $user->data['user_id']) || ($auth->acl_get('a_'))) {
		$output .= ' (<a href="/ucp.php?i=roleplays&mode=edit_place&place_id='.$item['id'].'">Edit &raquo;</a>)';
	}

	$output .= '</h2>';
	$output .= '<p id="place_synopsis_'.$item['id'].'">'.$item['synopsis'].'</p>';
	$output .= '</div>';
	
	if ($item['children']) {
		$output .= '<div id="place_children_'.$item['id'].'" class="place">';
		foreach ($item['children'] as $place) {
			$output .= display_place_item($place, $roleplay);
		}
		$output .= '</div>';
	}
	
	$output .= '</div>';
	$output .= '</div>';
	
	return $output;
}

function display_list_item($item) {
	echo '<li>';
	echo $item['name'];
	
	if ($item['children']) {
	
		echo '<ul>';
		
		foreach ($item['children'] as $place) {
			echo display_list_item($place);
		}
		
		echo '</ul>';
	}
	
	echo '</li>';
}

function scoreRubric($value) {
	switch ($value) {
		case 'Advanced': return 5;
		case 'Proficient': return 3;
		case 'In-Progress': return 1;
	}
}

function displayTag($tag) {
	return '<a href="/tag/'.$tag.'" class="tag">'.$tag.'</a>';
}


class Roleplay {

	public $id;
	public $activity;
	public $characters;
	public $places;
	public $players;
	public $events;
	public $groups;
	public $properties;
	
	function __construct($id, $debug = false) {
		global $db;
		
		$result = $db->sql_query('SELECT * FROM rpg_roleplays WHERE id = '.(int) $id);
		$this->properties = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	
	}
	
	function display_place_item($item,$roleplay) {
		global $user, $auth;

		$output = '<div class="place">';
		
		$output .= '<div class="controls" style="float:right;">';
		$output .= '<a href="javascript:toggleDiv(\'place_children_'.$item['id'].'\');">Toggle</a>';
		$output .= '</div>';
		
		$output .= '<div id="place_'.$item['id'].'">';
		$output .= '<div style="float:right;" class="stats"><span><label for="posts">'.$item['posts'].'</label> posts</span></div>';
		$output .= '<img id="place_img_'.$item['id'].'" class="crisp-border" src="/roleplay/'.$roleplay.'/places/'.$item['url'].'/image" alt="'.$item['name'].': '.$item['synopsis'].'" />';
		$output .= '<div style="margin-left:115px;">';
		$output .= '<h3>';
		$output .= '<a href="http://www.roleplaygateway.com/roleplay/'.$roleplay.'/places/'.$item['url'].'/">'.$item['name'].'</a>';
		if (($item['roleplay_owner'] == $user->data['user_id']) || ($item['owner'] == $user->data['user_id']) || ($auth->acl_get('a_'))) {
			$output .= ' (<a href="http://www.roleplaygateway.com/ucp.php?i=roleplays&mode=edit_place&place_id='.$item['id'].'">Edit &raquo;</a>)';
		}
		$output .= '</h3>';
		$output .= '<p id="place_synopsis_'.$item['id'].'">'.$item['synopsis'].'</p>';
		$output .= '</div>';
		
		if ($item['children']) {
			$output .= '<div id="place_children_'.$item['id'].'" class="place">';
			foreach ($item['children'] as $place) {
				$output .= display_place_item($place,$roleplay);
			}
			$output .= '</div>';
		}
		
		$output .= '</div>';
		$output .= '</div>';
		
		return $output;
	}	

}

function getStatusColor($status) {
	
  switch ($status) {
    case 'Open':
      return '#cec';
    break;
    case 'Closed':
      return '#ecc';
    break;
    case 'Completed':
      return '#FFD700';
    break;    
    default:
      return '#ccc';
    break;
  }
  
}

function generateStatusTag($status) {
  return  '<span style="background:'.getStatusColor($status).'; border-radius: 3px; text-transform: uppercase; font-size: 0.7em; padding-left: 5px; padding-right: 5px;">'.$status . '</span>';
}

?>
