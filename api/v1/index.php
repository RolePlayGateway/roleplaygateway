<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


$limit = (!empty($_REQUEST['limit'])) ? $_REQUEST['limit'] : 20; if ($limit > 100) { $limit = 100; }

if (!empty($_REQUEST['page'])) {
  $page = $_REQUEST['page'];
  $start = ($page - 1) * $limit;
} else {
  $start = (!empty($_REQUEST['start'])) ? $_REQUEST['start'] : 0;
  $page = floor($start / $limit) + 1;
}

$output = array();

switch ($_REQUEST['resource']) {

  case 'users':
    $resource = 'users';
    switch ($_REQUEST['aspect']) {
      default:
        $aspect = 'index';
        
        $sql = 'SELECT user_id as id, username FROM gateway_users WHERE user_id > 1 ORDER BY user_id DESC LIMIT '.(int) $start.','.(int) $limit;
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {  
          $status = 'success';
          $message = 'Successfully retrieved the list of users.';
          $description = 'This is a list of users on RolePlayGateway.';
          $meta = array();
          
          $row['url'] = 'http://www.roleplaygateway.com/member/'.urlencode($row['username']).'/';          
          $row['image'] = 'http://www.roleplaygateway.com/member/'.urlencode($row['username']).'/image';
          
          $row['stats'] = array(
            'timestamp' => 0,
            'posts' => 0,
            'words' => 0,
            'average_posts' => 0,
            'average_words' => 0,
            'average_grade_level' => 0,
            'average_time' => 0,
            'roleplays' => 0,
            'characters' => 0,
          );
          
          $sql = 'SELECT stats_updated as timestamp, posts, total_words as words, average_posts, average_words, average_grade_level, average_time FROM gateway_user_stats WHERE user_id = '.(int) $row['id'];
          $thisResult = $db->sql_query($sql);
          $thisRow = $db->sql_fetchrow($thisResult);
          $db->sql_freeresult($thisResult);
          
          if (!empty($thisRow)) {
            foreach (@$thisRow as $key => $value) {
               $row['stats'][$key] = (int) $value;
            }
          }
               
          $sql = 'SELECT count(*) as characterCount FROM rpg_characters WHERE owner = '.$row['id'] . ' OR creator = '.$row['id'];
          $thisResult = $db->sql_query($sql, 3600);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $row['stats']['characters'] += $thisRow['characterCount'];
          }
          $db->sql_freeresult($thisResult);
          
          if (!empty($roleplayIDs)) {
            $sql = 'SELECT id, title, description, owner, url FROM rpg_roleplays WHERE id IN ('.implode(',', $roleplayIDs).') OR owner = '.(int) $row['id'];
            $thisResult = $db->sql_query($sql, 3600);
            while ($roleplay = $db->sql_fetchrow($thisResult)) {
            
              $roleplayIDs[$roleplay['id']] = $roleplay['id'];

              $roleplay['url']   = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/';
              $roleplay['image'] = $roleplay['url'].'image';
              
              $roleplay['owner'] = (int) $roleplay['owner'];
              
              $roleplay['role'] = ($roleplay['owner'] == $row['id']) ? 'owner' : 'writer';

              //$row['roleplays'][] = $roleplay;
            }
            $db->sql_freeresult($thisResult);
            
            $row['stats']['roleplays'] = count($roleplayIDs);
          }
          
          
          $output[$row['id']] = $row;
        }
        $db->sql_freeresult($result);

        $sql = 'SELECT count(*) as totalUsers FROM gateway_users';
        $thisResult = $db->sql_query($sql);
        $thisRow = $db->sql_fetchrow($thisResult);
        $meta['total'] = (int) $thisRow['totalUsers'];
        $meta['perPage'] = (int) $limit;
        $meta['pages'] = ceil($meta['total'] / $limit);
        $meta['page'] = (int) $page;
        $db->sql_freeresult($thisResult);
        
      break;
    }
    
  break;
  
  case 'user':
    $resource = 'users';
    switch ($_REQUEST['aspect']) {
      default:
        $aspect = 'entity';
        
        $sql = 'SELECT user_id as id, username FROM gateway_users WHERE user_id = '.(int) $_REQUEST['id'];
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {  
          $status = 'success';
          $message = 'Successfully retrieved the list of users.';
          $description = 'This is a list of users on RolePlayGateway.';
          $meta = array();
          
          $row['url'] = 'http://www.roleplaygateway.com/member/'.urlencode($row['username']).'/';          
          $row['image'] = 'http://www.roleplaygateway.com/member/'.urlencode($row['username']).'/image';
          
          $row['stats'] = array(
            'timestamp' => 0,
            'posts' => 0,
            'words' => 0,
            'average_posts' => 0,
            'average_words' => 0,
            'average_grade_level' => 0,
            'average_time' => 0,
            'roleplays' => 0,
            'characters' => 0,
          );
          
          $sql = 'SELECT stats_updated as timestamp, posts, total_words as words, average_posts, average_words, average_grade_level, average_time FROM gateway_user_stats WHERE user_id = '.(int) $row['id'];
          $thisResult = $db->sql_query($sql);
          $thisRow = $db->sql_fetchrow($thisResult);
          $db->sql_freeresult($thisResult);
          
          if (!empty($thisRow)) {
            foreach (@$thisRow as $key => $value) {
               $row['stats'][$key] = (int) $value;
            }
          }
               
          $sql = 'SELECT count(*) as characterCount FROM rpg_characters WHERE owner = '.$row['id'] . ' OR creator = '.$row['id'];
          $thisResult = $db->sql_query($sql, 3600);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $row['stats']['characters'] += $thisRow['characterCount'];
          }
          $db->sql_freeresult($thisResult);
          
          $sql = 'SELECT id, name, synopsis, url, roleplay_id  FROM rpg_characters WHERE owner = '.$row['id'] . ' OR creator = '.$row['id'] ;
          $thisResult = $db->sql_query($sql, 3600);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
          
            $roleplayIDs[$thisRow['roleplay_id']] = $thisRow['roleplay_id'];
          
            $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $thisRow['roleplay_id']; unset($thisRow['roleplay_id']);
            $myResult = $db->sql_query($sql, 3600);
            $roleplay = $db->sql_fetchrow($myResult);
            $db->sql_freeresult($myResult);
            $roleplay['url']   = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/';
            $roleplay['image'] = $roleplay['url'].'image';
          
            $thisRow['url']   = $roleplay['url'] . 'characters/'.$thisRow['url'];
            $thisRow['image'] = $thisRow['url'].'/image';
            
            $row['characters'][] = $thisRow;
          }
          $db->sql_freeresult($thisResult);
          
          if (!empty($roleplayIDs)) {
            $sql = 'SELECT id, title, description, owner, url FROM rpg_roleplays WHERE id IN ('.implode(',', $roleplayIDs).') OR owner = '.(int) $row['id'];
            $thisResult = $db->sql_query($sql, 3600);
            while ($roleplay = $db->sql_fetchrow($thisResult)) {
            
              $roleplayIDs[$roleplay['id']] = $roleplay['id'];

              $roleplay['url']   = 'http://www.roleplaygateway.com/roleplay/'.$roleplay['url'].'/';
              $roleplay['image'] = $roleplay['url'].'image';
              
              $roleplay['owner'] = (int) $roleplay['owner'];
              
              $roleplay['role'] = ($roleplay['owner'] == $row['id']) ? 'owner' : 'writer';

              $row['roleplays'][] = $roleplay;
            }
            $db->sql_freeresult($thisResult);
            
            $row['stats']['roleplays'] = count($roleplayIDs);
          }
          
          
          $output[$row['id']] = $row;
        }
        $db->sql_freeresult($result);

        $sql = 'SELECT count(*) as totalUsers FROM gateway_users';
        $thisResult = $db->sql_query($sql);
        $thisRow = $db->sql_fetchrow($thisResult);
        $meta['total'] = (int) $thisRow['totalUsers'];
        $meta['perPage'] = (int) $limit;
        $meta['pages'] = ceil($meta['total'] / $limit);
        $meta['page'] = (int) $page;
        $db->sql_freeresult($thisResult);
      break;
    }
  break;

  case 'roleplays':
    $resource = 'roleplays';
    
    switch ($_REQUEST['aspect']) {
      default:
        $aspect = 'index';
        
        $sql = 'SELECT id, title, description, url FROM rpg_roleplays ORDER BY id DESC LIMIT '.(int) $start.','.(int) $limit;
        $result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
        
          $status = 'success';
          $message = 'Successfully retrieved the list of roleplays.';
          $description = 'This is a list of roleplays that exist on RolePlayGateway.';
          $meta = array();
              
          $row['url']   = 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/';
          $row['image'] = $row['url'].'image';          
               
          $sql = 'SELECT tag FROM gateway_tags WHERE roleplay_id = '.$row['id'];
          $thisResult = $db->sql_query($sql);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $row['tags'][] = $thisRow['tag'];
          }
          $db->sql_freeresult($thisResult);
               
          $sql = 'SELECT DISTINCT(author_id) as id, username, words FROM rpg_roleplay_author_stats a
                    INNER JOIN gateway_users u ON a.author_id = u.user_id
                    WHERE roleplay_id = '.$row['id'] . ' LIMIT '.(int) $limit;
          $thisResult = $db->sql_query($sql, 3600);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $row['writers'][$thisRow['username']] = $thisRow;
          }
          $db->sql_freeresult($thisResult);
          
          $sql = 'SELECT count(*) as totalRoleplays FROM rpg_roleplays';
          $thisResult = $db->sql_query($sql);
          $thisRow = $db->sql_fetchrow($thisResult);
          $meta['total'] = (int) $thisRow['totalRoleplays'];
          $meta['perPage'] = (int) $limit;
          $meta['pages'] = ceil($meta['total'] / $limit);
          $meta['page'] = (int) $page;
          $db->sql_freeresult($thisResult);

          $output[$row['id']] = $row;
        }
        $db->sql_freeresult($result);      
      break;
    }

  break;
  case 'roleplay':
    $resource = 'roleplays';
  
    if (!empty($_REQUEST['id'])) {
      $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $_REQUEST['id'];
      $result = $db->sql_query($sql);
      $row = $db->sql_fetchrow($result);
      $row['url']   = 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/';
      $row['image'] = $row['url'].'image';
    }
    
    switch ($_REQUEST['aspect']) {
      default:
        $aspect = 'entity';
        
        if (empty($row)) { $status = 'failure'; $message = 'No such roleplay.'; } else {
          $status = 'success';
          $message = 'Roleplay retrieved successfully.';
          $description = 'This is the detailed information available for “'.$row['title'].'”, a roleplay on RolePlayGateway.';
               
          $sql = 'SELECT tag FROM gateway_tags WHERE roleplay_id = '.$row['id'];
          $thisResult = $db->sql_query($sql);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $row['tags'][] = $thisRow['tag'];
          }
          $db->sql_freeresult($thisResult);
               
          $sql = 'SELECT stats_updated, posts, total_characters, total_words, average_characters, average_words, average_grade_level, roleplay_rating, total_reviews, unique_reviews FROM rpg_roleplay_stats WHERE roleplay_id = '.$row['id'];
          $thisResult = $db->sql_query($sql);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $myStats = $thisRow;
          }
          $db->sql_freeresult($thisResult);
                    
          foreach ($myStats as $key => $value) {
             $row['stats'][$key] = (int) $value;
          }
               
          $sql = 'SELECT id, name, synopsis, url, u.username as creator, u2.username as owner FROM rpg_characters c
                    INNER JOIN gateway_users u ON u.user_id = c.creator
                    INNER JOIN gateway_users u2 ON u2.user_id = c.owner
                  WHERE roleplay_id = '.$row['id'] . ' LIMIT '.(int) $start.','.(int) $limit;
          $thisResult = $db->sql_query($sql);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            
            $thisRow['image'] = $row['url'] . 'characters/'.$thisRow['url'].'/image';
            unset($thisRow['url']);
            
            $row['characters'][$thisRow['id']]          = $thisRow;
          }
          $db->sql_freeresult($thisResult);
               
          $sql = 'SELECT DISTINCT(author_id) as id, username, words FROM rpg_roleplay_author_stats a
                    INNER JOIN gateway_users u ON a.author_id = u.user_id
                    WHERE roleplay_id = '.$row['id'] . '
                      ORDER BY words DESC LIMIT '.(int) $limit;
          $thisResult = $db->sql_query($sql, 3600);
          while ($thisRow = $db->sql_fetchrow($thisResult)) {
            $row['writers'][$thisRow['username']] = $thisRow;
          }
          $db->sql_freeresult($thisResult);
          
          $output[$row['id']] = $row;

          $db->sql_freeresult($result);
        }
      break;
      
      case 'characters':
        $status = 'success';
        $aspect = 'characters';
        $message = 'Successfully retrieved a list of characters in the “'.$row['title'].'” universe.';
        $description = 'This is a list of characters that exist in the “'.$row['title'].'” universe.';
        $meta = array();
      
        $sql = 'SELECT id, name, synopsis, status, url, u.username as creator, u2.username as owner FROM rpg_characters c
                  INNER JOIN gateway_users u ON u.user_id = c.creator
                  INNER JOIN gateway_users u2 ON u2.user_id = c.owner
                WHERE roleplay_id = '.$row['id'] . ' LIMIT '.(int) $start.','.(int) $limit;
        $thisResult = $db->sql_query($sql);
        while ($thisRow = $db->sql_fetchrow($thisResult)) {
          
          $thisRow['image'] = $row['url'] . 'characters/'.$thisRow['url'].'/image';
          unset($thisRow['url']);
          
          $output[$thisRow['id']]          = $thisRow;
        }
        $db->sql_freeresult($thisResult);

        $sql = 'SELECT count(*) as totalCharacters FROM rpg_characters WHERE roleplay_id = '.$row['id'];
        $thisResult = $db->sql_query($sql);
        $thisRow = $db->sql_fetchrow($thisResult);
        $meta['total'] = (int) $thisRow['totalCharacters'];
        $meta['perPage'] = (int) $limit;
        $meta['pages'] = ceil($meta['total'] / $limit);
        $meta['page'] = (int) $page;
        $db->sql_freeresult($thisResult);
        
      break;
      
      case 'places':
        $status = 'success';
        $aspect = 'places';
        $message = 'Successfully retrieved a list of places in the “'.$row['title'].'” universe.';
        $description = 'This is a list of places that exist in the “'.$row['title'].'” universe.';
        $meta = array();
      
        $sql = 'SELECT id, name, synopsis, url FROM rpg_places
                WHERE roleplay_id = '.$row['id'] . ' LIMIT '.(int) $start.','.(int) $limit;
        $thisResult = $db->sql_query($sql);
        while ($thisRow = $db->sql_fetchrow($thisResult)) {
          
          $thisRow['image'] = $row['url'] . 'places/'.$thisRow['url'].'/image';
          unset($thisRow['url']);
          
          $output[$thisRow['id']]          = $thisRow;
        }
        $db->sql_freeresult($thisResult);

        $sql = 'SELECT count(*) as totalPlaces FROM rpg_places WHERE roleplay_id = '.$row['id'];
        $thisResult = $db->sql_query($sql);
        $thisRow = $db->sql_fetchrow($thisResult);
        $meta['total'] = (int) $thisRow['totalPlaces'];
        $meta['perPage'] = (int) $limit;
        $meta['pages'] = ceil($meta['total'] / $limit);
        $meta['page'] = (int) $page;
        $db->sql_freeresult($thisResult);
        
      break;
      
      case 'place':
        $status = 'success';
        $aspect = 'places';

      
        $sql = 'SELECT id, name, synopsis, url, roleplay_id FROM rpg_places
                WHERE id = '.(int) $_REQUEST['id'];
        $thisResult = $db->sql_query($sql);
        while ($thisRow = $db->sql_fetchrow($thisResult)) {     
        
          $message = 'Successfully retrieved “'.$thisRow['name'].'”.';
          $description = 'This is the detail page for “'.$thisRow['name'].'”.';
          $meta = array();
        
          $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $thisRow['roleplay_id']; unset($thisRow['roleplay_id']);
          $result = $db->sql_query($sql);
          $row = $db->sql_fetchrow($result);
          $row['url']   = 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/';
          $row['image'] = $row['url'].'image';         
          
          $thisRow['image'] = $row['url'] . 'places/'.$thisRow['url'].'/image';
          unset($thisRow['url']);
          
          $output[$thisRow['id']]          = $thisRow;
        }
        $db->sql_freeresult($thisResult);

      break;
      
      case 'groups':
        $status = 'success';
        $aspect = 'groups';
        $message = 'Successfully retrieved a list of groups in the “'.$row['title'].'” universe.';
        $description = 'This is a list of groups that exist in the “'.$row['title'].'” universe.';
        $meta = array();
      
        $sql = 'SELECT id, name, synopsis, slug, roleplay_id FROM rpg_groups
                WHERE roleplay_id = '.$row['id'] . ' LIMIT '.(int) $start.','.(int) $limit;
        $thisResult = $db->sql_query($sql);
        while ($thisRow = $db->sql_fetchrow($thisResult)) {
        
          $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $thisRow['roleplay_id']; unset($thisRow['roleplay_id']);
          $result = $db->sql_query($sql);
          $row = $db->sql_fetchrow($result);
          $row['url']   = 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/';
          $row['image'] = $row['url'].'image';
          
          $thisRow['image'] = $row['url'] . 'groups/'.$thisRow['slug'].'/image';
          unset($thisRow['slug']);
          
          $output[$thisRow['id']]          = $thisRow;

          $sql = 'SELECT characters, players, places FROM rpg_group_stats WHERE group_id = '.$thisRow['id'] . ' LIMIT '.(int) $start.','.(int) $limit;
          $myResult = $db->sql_query($sql);
          $myStats = $db->sql_fetchrow($myResult);
          $db->sql_freeresult($myResult);
          
          foreach ($myStats as $key => $value) {
             $output[$thisRow['id']]['stats'][$key] = (int) $value;
          }

        }
        $db->sql_freeresult($thisResult);

        $sql = 'SELECT count(*) as totalGroups FROM rpg_groups WHERE roleplay_id = '.$row['id'];
        $thisResult = $db->sql_query($sql);
        $thisRow = $db->sql_fetchrow($thisResult);
        $meta['total'] = (int) $thisRow['totalGroups'];
        $meta['perPage'] = (int) $limit;
        $meta['pages'] = ceil($meta['total'] / $limit);
        $meta['page'] = (int) $page;
        $db->sql_freeresult($thisResult);
        
      break;
      
      case 'group':
        $status = 'success';
        $aspect = 'groups';
      
        $sql = 'SELECT id, name, synopsis, slug, roleplay_id FROM rpg_groups
                WHERE id = '.(int) $_REQUEST['id'];
        $thisResult = $db->sql_query($sql);
        while ($thisRow = $db->sql_fetchrow($thisResult)) {
        
          $sql = 'SELECT id, title, description, url FROM rpg_roleplays WHERE id = '.(int) $thisRow['roleplay_id']; unset($thisRow['roleplay_id']);
          $result = $db->sql_query($sql);
          $row = $db->sql_fetchrow($result);
          $row['url']   = 'http://www.roleplaygateway.com/roleplay/'.$row['url'].'/';
          $row['image'] = $row['url'].'image';
          
          $message = 'Successfully retrieved group “'.$thisRow['name'].'”.';
          $description = 'This is the detail page for “'.$thisRow['name'].'”.';
          $meta = array();        
          
          $thisRow['image'] = $row['url'] . 'groups/'.$thisRow['slug'].'/image';
          unset($thisRow['slug']);
          
          $output[$thisRow['id']]          = $thisRow;
          
          $sql = 'SELECT characters, players, places FROM rpg_group_stats WHERE group_id = '.$thisRow['id'];
          $myResult = $db->sql_query($sql);
          $myStats = $db->sql_fetchrow($myResult);
          $db->sql_freeresult($myResult);
          
          foreach ($myStats as $key => $value) {
             $output[$thisRow['id']]['stats'][$key] = (int) $value;
          }          
            
          $sql = 'SELECT id, name, synopsis, url, u.username as creator, u2.username as owner FROM rpg_group_members m
                    INNER JOIN rpg_characters c ON c.id = m.character_id
                    INNER JOIN gateway_users u ON u.user_id = c.creator
                    INNER JOIN gateway_users u2 ON u2.user_id = c.owner
                  WHERE m.group_id = '.(int) $_REQUEST['id'] . ' LIMIT '.(int) $start.','.(int) $limit;
          $myResult = $db->sql_query($sql);
          while ($myRow = $db->sql_fetchrow($myResult)) {
            
            $myRow['image'] = $row['url'] . 'characters/'.$myRow['url'].'/image';
            unset($myRow['url']);
            
            $output[$thisRow['id']]['members'][] = $myRow;
          }
          $db->sql_freeresult($myResult);

        }
        $db->sql_freeresult($thisResult);

        $sql = 'SELECT count(*) as totalMembers FROM rpg_group_members WHERE group_id = '. (int) $_REQUEST['id'];
        $thisResult = $db->sql_query($sql);
        $thisRow = $db->sql_fetchrow($thisResult);
        $meta['total'] = (int) $thisRow['totalMembers'];
        $meta['perPage'] = (int) $limit;
        $meta['pages'] = ceil($meta['total'] / $limit);
        $meta['page'] = (int) $page;
        $db->sql_freeresult($thisResult);
        
      break;
    }

  break;

}

$response = array(
  'status' => $status,
  'message' => $message,
  'description' => $description,
  'resource' => $resource,
  'aspect'  => $aspect,
  'meta'    => $meta,
  'results' => $output
);

header('Content-Type: application/json');
echo json_encode($response);

?>
