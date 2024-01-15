<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_money.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$itemID = request_var('itemID', 0);
$roleplayURL = request_var('roleplayURL', '');
$mode = request_var('mode', '');

if ($user->data['user_id'] < 4) {
  trigger_error('Forge is only enabled for registered users.  Sign in or register!');
}

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `to` = '.(int) $user->data['user_id'];
$creditResult = $db->sql_query($sql);
$credits = $db->sql_fetchrow($creditResult);
$db->sql_freeresult($creditResult);

$sql = 'SELECT SUM(amount) as total FROM rpg_ledger WHERE `from` = '.(int) $user->data['user_id'];
$debitResult = $db->sql_query($sql);
$debits = $db->sql_fetchrow($debitResult);
$db->sql_freeresult($debitResult);

$userBalance = $credits['total'] - $debits['total'];

$sql = 'SELECT id, title, description, url, owner FROM rpg_roleplays WHERE url = "'.$db->sql_escape($roleplayURL).'"';
$roleplayResult = $db->sql_query($sql);
$roleplay = $db->sql_fetchrow($roleplayResult);
$db->sql_freeresult($roleplayResult);

if (isset($_POST['asset'])) {
  $asset = $_POST['asset'];

  if (isset($_POST['itemID'])) {
    $sql = 'SELECT * FROM rpg_items WHERE id = '.(int) $_POST['itemID'];
    $itemResult = $db->sql_query($sql);
    $item = $db->sql_fetchrow($itemResult);
    $db->sql_freeresult($itemResult);

    if (empty($item) || empty($item['id'])) {
      trigger_error('No such item.');
    }

    if ($user->data['user_id'] != 4) {
      trigger_error('You are not allowed to edit this item.');
    }

    if (isset($_FILES['item_image'])) {
      try {
        if (is_uploaded_file($_FILES['item_image']['tmp_name'])) {
          // check the file is less than the maximum file size
          if($_FILES['item_image']['size'] < 1500000) {

            // prepare the image for insertion
            $imgData = addslashes(file_get_contents($_FILES['item_image']['tmp_name']));

            // get the image info..
            $imgSize = getimagesize($_FILES['item_image']['tmp_name']);

            if (($imgSize[0] > 100) || ($imgSize[1] > 100)) {
              trigger_error('This image is larger than 100x100.  Resize it.');
            }

          }
        }
      } catch(Exception $e) {
        trigger_error('Sorry, could not upload file: '.$e->getMessage());
      }
    }

    if (!empty($imgData)) {
      $sql = 'UPDATE rpg_items SET
        image = "'.$imgData.'",
        image_type = "'.$imgSize['mime'].'",
        description = "'.$db->sql_escape($asset['description']).'"
        WHERE id = '.(int) $item['id'];
    } else {
      $sql = 'UPDATE rpg_items SET
        description = "'.$db->sql_escape($asset['description']).'"
        WHERE id = '.(int) $item['id'];
    }

    $db->sql_query($sql);
    $asset['link'] = '/items/'.(int) $item['id'];

    meta_refresh(3, $asset['link']);
    trigger_error('Item edited successfully!');
  } else {
    if (empty($asset['deposit'])) {
      trigger_error('You must provide a non-zero deposit for the item you are creating.');
    }

    if ($userBalance < $asset['deposit']) {
      trigger_error('You do not have sufficient funds to create this object.');
    }

    // check if a file was submitted
    if (isset($_FILES['item_image'])) {
      try {
        if (is_uploaded_file($_FILES['item_image']['tmp_name'])) {
          // check the file is less than the maximum file size
          if($_FILES['item_image']['size'] < 1500000) {

            // prepare the image for insertion
            $imgData = addslashes(file_get_contents($_FILES['item_image']['tmp_name']));

            // get the image info..
            $imgSize = getimagesize($_FILES['item_image']['tmp_name']);

            if (($imgSize[0] > 100) || ($imgSize[1] > 100)) {
              trigger_error('This image is larger than 100x100.  Resize it.');
            }

          }
        }
      } catch(Exception $e) {
        trigger_error('Sorry, could not upload file: '.$e->getMessage());
      }
    }

    $db->sql_transaction('begin');

    if (!empty($imgData)) {
      $sql = 'INSERT INTO rpg_items (
        roleplay_id, creator, name, slug, quantity, deposit, description, image, image_type
      ) VALUES (
        '.(int) $roleplay['id'].',
        '.(int) $user->data['user_id'].',
        "'.$db->sql_escape($asset['name']).'",
        "'.$db->sql_escape(urlify($asset['name'])).'",
        '.(int) $asset['quantity'].',
        "'.(float) $asset['deposit'].'",
        "'.$db->sql_escape($asset['description']).'",
        "'.$imgData.'",
        "'.$imgSize['mime'].'"
      )';
    } else {
      $sql = 'INSERT INTO rpg_items (
        roleplay_id, creator, name, slug, quantity, deposit, description
      ) VALUES (
        '.(int) $roleplay['id'].',
        '.(int) $user->data['user_id'].',
        "'.$db->sql_escape($asset['name']).'",
        "'.$db->sql_escape(urlify($asset['name'])).'",
        '.(int) $asset['quantity'].',
        "'.(float) $asset['deposit'].'",
        "'.$db->sql_escape($asset['description']).'"
      )';
    }

    if ($db->sql_query($sql)) {
      $itemID = $db->sql_nextid();

      $asset['link'] = '/items/'.(int) $itemID;

      // if (spend($roleplay['owner'], $asset['deposit'], $asset['link'])) {
      // TODO: bond into the Universe instead...
      // i.e., pay to 0, link should be `/universes/:id` or some other tracking method
      if (spend($roleplay['owner'], $asset['deposit'], $asset['link'])) {
        $db->sql_transaction('commit');

        meta_refresh(3, $asset['link']);
        trigger_error('Item created successfully!');
      } else {
        $db->sql_transaction('rollback');
        meta_refresh(-1);
        trigger_error('Something went wrong!');
      }
    } else {
      $db->sql_transaction('rollback');
      meta_refresh(-1);
      trigger_error('Could not create item.  Try again.');
    }
  }
  /*exit(json_encode(array(
    'status' => 'error',
    'message' => 'not yet implemented',
    'extra' => $sql
  )));*/
}

$sql = 'SELECT * FROM rpg_items WHERE roleplay_id = '.(int) $roleplay['id'];
$result = $db->sql_query($sql);
while ($item = $db->sql_fetchrow($result)) {
  $template->assign_block_vars('items', array(
    'ID' => $item['id'],
    'NAME' => $item['name'],
    'DESCRIPTION' => $item['description'],
    'QUANTITY' => $item['quantity'],
    'DEPOSIT' => $item['deposit'],
    'VALUE' => $item['deposit'] / $item['quantity'],
    'ROLEPLAY_ID'         => $roleplay['id'],
    'ROLEPLAY_NAME'       => $roleplay['title'],
    'ROLEPLAY_URL'        => $roleplay['url'],
    'S_HAS_IMAGE' => (strlen($item['image']) > 0) ? true : false
  ));
}
$db->sql_freeresult($result);

$itemClasses = array();
$sql = 'SELECT id, name FROM rpg_item_classes';
$result = $db->sql_query($sql);
while ($classItem = $db->sql_fetchrow($result)) {
  $itemClasses[] = $classItem;
  $template->assign_block_vars('item_classes', array(
    'ID' => $classItem['id'],
    'NAME' => $classItem['name'],
  ));
}
$db->sql_freeresult($result);

// Output page
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - META
$seo_meta->collect('description', $config['sitename'] . ' : ' .  $config['site_desc']);
$seo_meta->collect('keywords', $config['sitename'] . ' ' . $seo_meta->meta['description']);
// www.phpBB-SEO.com SEO TOOLKIT END - META
// www.phpBB-SEO.com SEO TOOLKIT BEGIN - TITLE
page_header('Forge &middot; RPG');
// www.phpBB-SEO.com SEO TOOLKIT END - TITLE

$template->assign_vars(array(
  'S_PAGE_ONLY' => true,
  'ROLEPLAY_ID' => $roleplay['id'],
  'ROLEPLAY_NAME' => $roleplay['title'],
  'ROLEPLAY_SYNOPSIS' => $roleplay['description'],
  'ROLEPLAY_URL' => $roleplay['url'],
));

if ($mode == 'edit') {
  $sql = 'SELECT * FROM rpg_items WHERE id = '.(int) $itemID;
  $itemResult = $db->sql_query($sql);
  $item = $db->sql_fetchrow($itemResult);
  $db->sql_freeresult($itemResult);

  $template->assign_vars(array(
    'ITEM_ID' => $item['id'],
    'ITEM_NAME' => $item['name'],
    'ITEM_DESCRIPTION' => $item['description'],
  ));

  $template->set_filenames(array(
  	'body' => 'rpg_forge_edit.html')
  );
} else {
  $template->set_filenames(array(
  	'body' => 'rpg_forge.html')
  );
}

page_footer();

?>
