<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$roleplayURL = $_REQUEST['roleplayURL'];
$placeURL = $_REQUEST['placeURL'];
$start      = (int) $_REQUEST['start'];
$groupCount = 12;

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $user->data['user_id'];
$creditResult = $db->sql_query($sql);
$credits = $db->sql_fetchrow($creditResult);
$db->sql_freeresult($creditResult);

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $user->data['user_id'];
$debitResult = $db->sql_query($sql);
$debits = $db->sql_fetchrow($debitResult);
$db->sql_freeresult($debitResult);

$userBalance = $credits['total'] - $debits['total'];

if (empty($roleplayURL)) {
  $sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id, count(f.user_id) as total FROM rpg_characters c
        INNER JOIN rpg_characters_followed f
          ON c.id = f.character_id
        GROUP BY f.character_id
        ORDER BY total DESC, rand()
        LIMIT 5";
  $result = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($result)) {
    $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
    $ownerResult = $db->sql_query($sql);
    $owner = $db->sql_fetchrow($ownerResult);

    $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
    $roleplayResult = $db->sql_query($sql);
    $roleplay = $db->sql_fetchrow($roleplayResult);

    $template->assign_block_vars('featured', array(
      'ID'              => $character['id'],
      'NAME'            => $character['name'],
      'URL'             => $character['url'],
      'LINK'            => '/roleplay/'.$roleplay['url'] .'/characters/' . $character['url'],
      'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
      'SYNOPSIS'        => $character['synopsis'],
      'ROLEPLAY_URL'    => $roleplay['url'],
      'TOTAL'           => $character['views'],
    ));
  }

  $sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id, count(f.content_id) as total FROM rpg_characters c
        INNER JOIN rpg_content_tags f
          ON c.id = f.character_id
        GROUP BY f.character_id
        ORDER BY total DESC
        LIMIT ". (int) $groupCount;
  $result = $db->sql_query($sql, 3600);
  while ($character = $db->sql_fetchrow($result)) {
    $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
    $ownerResult = $db->sql_query($sql);
    $owner = $db->sql_fetchrow($ownerResult);

    $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
    $roleplayResult = $db->sql_query($sql);
    $roleplay = $db->sql_fetchrow($roleplayResult);

    $template->assign_block_vars('prolific', array(
      'ID'              => $character['id'],
      'NAME'            => $character['name'],
      'URL'             => $character['url'],
      'LINK'            => '/roleplay/'.$roleplay['url'] .'/characters/' . $character['url'],
      'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
      'SYNOPSIS'        => $character['synopsis'],
      'ROLEPLAY_URL'    => $roleplay['url'],
      'TOTAL'           => $character['views'],
    ));
  }

  $sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id, count(f.user_id) as total FROM rpg_characters c
        INNER JOIN rpg_characters_followed f
          ON c.id = f.character_id
        GROUP BY f.character_id
        ORDER BY total DESC
        LIMIT ". (int) $groupCount;
  $result = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($result)) {
    $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
    $ownerResult = $db->sql_query($sql);
    $owner = $db->sql_fetchrow($ownerResult);

    $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
    $roleplayResult = $db->sql_query($sql);
    $roleplay = $db->sql_fetchrow($roleplayResult);

    $template->assign_block_vars('followed', array(
      'ID'              => $character['id'],
      'NAME'            => $character['name'],
      'URL'             => $character['url'],
      'LINK'            => '/roleplay/'.$roleplay['url'] .'/characters/' . $character['url'],
      'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
      'SYNOPSIS'        => $character['synopsis'],
      'ROLEPLAY_URL'    => $roleplay['url'],
      'TOTAL'           => $character['total'],
    ));
  }

  $sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id, views as total FROM rpg_characters c
        ORDER BY total DESC
        LIMIT ". (int) ($groupCount + 2);
  $result = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($result)) {
    $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
    $ownerResult = $db->sql_query($sql);
    $owner = $db->sql_fetchrow($ownerResult);

    $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
    $roleplayResult = $db->sql_query($sql);
    $roleplay = $db->sql_fetchrow($roleplayResult);

    $template->assign_block_vars('views', array(
      'ID'              => $character['id'],
      'NAME'            => $character['name'],
      'URL'             => $character['url'],
      'LINK'            => '/roleplay/'.$roleplay['url'] .'/characters/' . $character['url'],
      'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
      'SYNOPSIS'        => $character['synopsis'],
      'ROLEPLAY_URL'    => $roleplay['url'],
      'TOTAL'           => $character['views'],
    ));
  }

  $characters = array();

  $sql = "SELECT c.id, c.name, c.owner, c.synopsis, c.url, c.views, c.roleplay_id FROM rpg_characters c
        WHERE length(c.image) > 0
        ORDER BY id DESC
        LIMIT ". (int) $groupCount;
  $result = $db->sql_query($sql);
  while ($character = $db->sql_fetchrow($result)) {
    $characters[] = $character;

    $sql = 'SELECT username, user_id FROM gateway_users WHERE user_id = '.(int) $character['owner'];
    $ownerResult = $db->sql_query($sql);
    $owner = $db->sql_fetchrow($ownerResult);

    $sql = 'SELECT id, url FROM rpg_roleplays WHERE id = '.(int) $character['roleplay_id'];
    $roleplayResult = $db->sql_query($sql);
    $roleplay = $db->sql_fetchrow($roleplayResult);

    $template->assign_block_vars('newest', array(
      'ID'              => $character['id'],
      'NAME'            => $character['name'],
      'URL'             => $character['url'],
      'LINK'            => '/roleplay/'.$roleplay['url'] .'/characters/' . $character['url'],
      'OWNER_USERNAME'	=> get_username_string('full', $character['owner'], $owner['username']),
      'SYNOPSIS'        => $character['synopsis'],
      'ROLEPLAY_URL'    => $roleplay['url'],
      'TOTAL'           => $character['views'],
    ));
  }

  if (!empty($placeURL)) {
    page_header('Characters located in '.$place['name']. '| '.$config['sitename']);
  } else {
    page_header('Characters | '.$config['sitename']);
  }

  $template->set_filenames(array(
    'body' => 'rpg_characters.html'
  ));

  $template->assign_vars(array(
    'TOTAL_CHARACTERS' => count($characters),
    'S_PAGE_ONLY'     => true,
  ));

  page_footer();
  
} else {
  $extra_sql = '';

  $sql = 'SELECT id, title, description, url, require_approval FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
  $result = $db->sql_query($sql);
  $roleplay = $db->sql_fetchrow($result);
  $db->sql_freeresult($result);

  if (!empty($placeURL)) {
    $sql = 'SELECT id, name, synopsis FROM rpg_places WHERE roleplay_id = '.(int) $roleplay['id'].' AND url = "'.$db->sql_escape($placeURL).'"';
    $result = $db->sql_query($sql);
    $place = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $extra_sql = ' AND `location` = ' .(int) $place['id'];
  }

  $roleplayID = $roleplay['id'];
  $limit = 54;

  if ($roleplay['require_approval'] == 1) {
    $sql = "SELECT id,name,username,user_id,owner,synopsis,url,anonymous FROM rpg_characters c
        INNER JOIN gateway_users u ON c.owner = u.user_id
      WHERE c.roleplay_id = ".$db->sql_escape($roleplayID). " AND c.owner <> 0 AND c.status = 'Approved' AND c.isAdoptable = 0 ".$extra_sql." ORDER BY id DESC";
  } else {
    $sql = "SELECT id,name,username,user_id,owner,synopsis,url,anonymous FROM rpg_characters c
          INNER JOIN gateway_users u ON c.owner = u.user_id
        WHERE c.roleplay_id = ".$db->sql_escape($roleplayID) . " AND c.owner <> 0 AND isAdoptable = 0 ".$extra_sql." ORDER BY id DESC";
  }

  $result = $db->sql_query_limit($sql, $limit, $start);
  while ($row = $db->sql_fetchrow($result)) {

    $sql = 'SELECT count(*) as sightings FROM rpg_content_tags WHERE character_id = '.(int) $row['id'];
    $contentResult = $db->sql_query($sql);
    $row['sightings'] = $db->sql_fetchfield('sightings');
    $db->sql_freeresult($contentResult);

    $template->assign_block_vars('characters', array(
      'ID'         => $row['id'],
      'NAME'         => $row['name'],
      'URL'         => $row['url'],
      'OWNER_USERNAME'  => get_username_string('full', $row['owner'], $row['username']),
      'SYNOPSIS'      => $row['synopsis'],
      'SIGHTINGS'     => $row['sightings'],
      'S_IS_ANONYMOUS'     => ($row['anonymous'] == 1) ? true : false,
    ));
  }
  $db->sql_freeresult($result);

  if ($roleplay['require_approval'] == 1) {
    $sql = "SELECT count(*) as count FROM rpg_characters c
        INNER JOIN gateway_users u ON c.owner = u.user_id
      WHERE c.roleplay_id = ".$db->sql_escape($roleplayID). " AND c.owner <> 0 AND c.status = 'Approved' AND c.isAdoptable = 0 ".$extra_sql." ORDER BY id ASC";
  } else {
    $sql = "SELECT count(*) as count FROM rpg_characters c
          INNER JOIN gateway_users u ON c.owner = u.user_id
        WHERE c.roleplay_id = ".$db->sql_escape($roleplayID) . " AND c.owner <> 0 AND isAdoptable = 0 ".$extra_sql." ORDER BY id ASC";
  }
  $result = $db->sql_query($sql);
  $roleplay['characters'] = $db->sql_fetchfield('count');
  $db->sql_freeresult($result);

	$buyableCharacters = array();
  $sql = 'SELECT * FROM rpg_orders WHERE asset LIKE "/characters/%" AND status = "Open" ORDER BY rand()';
  $ordersResult = $db->sql_query($sql);
  while ($order = $db->sql_fetchrow($ordersResult)) {
		$parts = explode('/', $order['asset']);

		switch ($parts[1]) {
			case 'places':
				$sql = 'SELECT id, name, synopsis, owner, url, roleplay_id FROM rpg_places WHERE id = '.(int) $parts[2];
				break;
			case 'characters':
				$sql = 'SELECT id, name, synopsis, owner, url, roleplay_id FROM rpg_characters WHERE id = '.(int) $parts[2];
				break;
			case 'instances':
				$sql = 'SELECT a.id, a.name, a.description as synopsis, a.creator, a.slug as url, a.roleplay_id FROM rpg_items a
					INNER JOIN rpg_item_instances i
						ON a.id = i.item_id
					WHERE i.id = '.(int) $parts[2];
				break;
		}

		$assetResult = $db->sql_query($sql);
		$asset = $db->sql_fetchrow($assetResult);
		$db->sql_freeresult($assetResult);

		if ($asset['roleplay_id'] !== $roleplay['id']) continue;

		switch ($parts[1]) {
			case 'places':
				$asset['type'] = 'Location';
				$asset['link'] = '/universes/'.$roleplay['url'].'/'.$parts[1].'/'.$asset['url'];
				$asset['image'] = '/universes/'.$roleplay['url'].'/places/'.$asset['url'].'/image';
				break;
			case 'characters':
				$asset['type'] = 'Character';
				$asset['link'] = '/universes/'.$roleplay['url'].'/'.$parts[1].'/'.$asset['url'];
				$asset['image'] = '/universes/'.$roleplay['url'].'/characters/'.$asset['url'].'/image';
				break;
			case 'instances':
				$asset['type'] = 'Item';
				$asset['link'] = '/items/'.$asset['id']; // https://www.gravatar.com/avatar/94d093eda664addd6e450d7e9881bcad?s=100&d=identicon&r=PG
				$asset['image'] = '/universes/'.$roleplay['url'].'/items/'.$asset['id'].'/image';
				break;
		}

    $buyableCharacters[] = array(
			'ID' => $order['id'],
			'CREATOR' => $order['creator'],
			'CREATED' => $order['created'],
			'ASSET' => $order['asset'],
			'PRICE' => money_format('%i', $order['price']),
			'PRICE_RAW' => $order['price'],
			'STATUS' => $order['status'],
			'S_CAN_EDIT'      => ($order['creator'] == $user->data['user_id']) ? true : false,
			'S_CAN_AFFORD'    => (($userBalance >= $order['price']) && ($user->data['is_registered'])) ? true : false,
			'SALE_PRICE'      => (($order['price'] == 0.0) || $order['price'] < 1 && $order['price'] > 0) ? money_format('%.8n', $order['price']) : money_format('%i', $order['price']),
			'ORDER_ID'        => $order['id'],
			'ASSET_NAME'      => $asset['name'],
			'ASSET_TYPE'      => $asset['type'],
			'ASSET_DESCRIPTION' => $asset['synopsis'],
			'ASSET_CONTEXT'   => $roleplay['title'],
			'ASSET_CONTEXT_LINK'   => '/universes/'.$roleplay['url'],
			'ASSET_LINK'      => $asset['link'],
			'ASSET_IMG_LINK'  => ($asset['image']) ? $asset['image'] : $asset['link'] . '/image',
		);
  }
  $db->sql_freeresult($ordersResult);

	foreach ($buyableCharacters as $character) {
		$template->assign_block_vars('buyable_characters', $character);
	}

  $template->assign_vars(array(
    'S_MORE_CHARACTERS'    => (@$roleplay['characters'] > $limit) ? true : false,
    'MORE_CHARACTERS_COUNT'  => (@$roleplay['characters'] > $limit) ? @$roleplay['characters'] - $limit : null,
    )
  );

  $pagination_url = '/universes/'.$roleplay['url'].'/characters';

  page_header('Characters | '.$roleplay['title'].' | '.$config['sitename']);

  $template->set_filenames(array(
    'body' => 'roleplay_characters.html'
    )
  );

  $template->assign_vars(array(
      'PAGINATION'        => generate_pagination($pagination_url, $roleplay['characters'], $limit, $start),
      'PAGE_NUMBER'       => on_page($roleplay['characters'], $limit, $start),
      'TOTAL_CHARACTERS'       => (int) $roleplay['characters'],
      'ROLEPLAY_NAME'     => $roleplay['title'],
      'ROLEPLAY_URL'    => $roleplay['url'],
      'ROLEPLAY_DESCRIPTION'    => $roleplay['description'],
      'S_PAGE_ONLY'     => true,
  ));

  page_footer();
}
