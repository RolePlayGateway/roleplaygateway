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
$start      = (int) $_REQUEST['start'];

$sql = 'SELECT character_id, count(*) as tagCount FROM rpg_content_tags GROUP BY character_id ORDER BY tagCount DESC LIMIT 256';
$tagResult = $db->sql_query($sql);
while ($tag = $db->sql_fetchrow($tagResult)) {

  $sql = 'SELECT name, url, synopsis, owner, roleplay_id FROM rpg_characters WHERE id = '.(int) $tag['character_id'];
  $characterResult = $db->sql_query($sql);
  $character = $db->sql_fetchrow($characterResult);
  $db->sql_freeresult($characterResult);

  $sql = 'SELECT username, user_lastvisit FROM gateway_users WHERE user_id = '.(int) $character['owner'];
  $playerResult = $db->sql_query($sql);
  $player = $db->sql_fetchrow($playerResult);
  $db->sql_freeresult($playerResult);

  $sql = 'SELECT UNIX_TIMESTAMP(max(written)) as lastPost FROM rpg_content WHERE author_id = '.(int) $character['owner'];
  $contentResult = $db->sql_query($sql);
  $content = $db->sql_fetchrow($contentResult);
  $db->sql_freeresult($contentResult);

  $characters[ $tag['character_id'] ] = array(
      'id'          => $tag['character_id']
    , 'sightings'   => $tag['tagCount']
    , 'name'        => $character['name']
    , 'slug'        => $character['url']
    , 'roleplayID'  => $character['roleplay_id']
    , 'synopsis'    => $character['synopsis']
    , 'player'      => array(
          'name' => $player['username']
        , 'lastvisit' => timeAgo($content['lastPost'])
      )
  );

}

echo '
<style type="text/css">
ol {
  list-style-position: inside;
}
</style>
<textarea>';


foreach ($characters as $character) {
  if ($character['roleplayID'] == 1) {
    echo '[url=http://www.roleplaygateway.com/roleplay/the-multiverse/characters/'.$character['slug'].']'.$character['name'].'[/url], ';
  }
}
echo '</textarea>';

echo '<ol>';



foreach ($characters as $character) {
  if ($character['roleplayID'] == 1) {
    echo '<li style="clear:both;">
  <img src="/roleplay/the-multiverse/characters/'.$character['slug'].'/image" style="float:left; margin-right:1em;" />
  <small style="float:right;"><a href="/member/'.$character['player']['name'].'">'.$character['player']['name'].'</a>, 
    last visited '. $character['player']['lastvisit'] .'</small>
  <a href="/roleplay/the-multiverse/characters/'.$character['slug'].'">'.$character['name']. '</a> appears in ' . $character['sightings'] . ' posts.
  <p>'.$character['synopsis'].'</p>

</li>';
  }
}

echo '</ol>';



//header('Content-Type: application/json');
//die(json_encode($characters));

?>
