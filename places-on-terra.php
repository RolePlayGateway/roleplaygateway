<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$places = array();

function getPlacesIn($placeID) {
  global $config, $db, $user;

  $sql = 'SELECT id FROM rpg_places WHERE parent_id = '.(int) $placeID.'';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $placeList[] = $place['id'];
  }
  $db->sql_freeresult($result);

  if (count($placeList) > 0) {

  }

  return (array) $placeList;

}


$sql = 'SELECT id FROM rpg_places WHERE id = 2';
$result = $db->sql_query($sql);
while ($place = $db->sql_fetchrow($result)) {

  $places[] = $place['id'];

  $topChildren = getPlacesIn($place['id']);
  $children = array();

  $sql = 'SELECT id FROM rpg_places WHERE parent_id IN ('.implode(',',$topChildren).')';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $places[] = $place['id'];
  }
  $db->sql_freeresult($result);

  $sql = 'SELECT id FROM rpg_places
    WHERE parent_id IN (2,17821,18509,18510,20313,104,112,125,16872,17950,24773,28281,28944,28945,28946,28947,28948,28949,28950,28951,28952,20977,20978,20979,20981,28387,30450,30452,28384,28385,28386,28388,24885,24886,24887,26682,30510,126,28275,28276,28375,28376,28377,28379,28380,28381,28382,28383,7,8,22,23,24,26,28,54,58,76,82,83,84,85,86,87,88,90,127,131,138,140,141,175,187,188,189,263,2614,16787,16791,16884,16885,17006,17811,17952,18034,18035,18036,18037,19672,19674,20299,20300,20310,20569,22954,23961,24523,27995,28279,29644,29645,30151,5,6,27,16895,38,59,29004,48,49,50,51,61,62,63,64,65,66,17820,16783,55,56,18026,29154,122,18512,102,103,17812,34,46,53,60,101,17124,17125,17126,17128,17931,17932,17933,28274,99,19624,128,129,130,132,133,134,29007,137,139,20494,142,36,37,115,116,117,118,119,41,78,79,80,105,106,107,18050,2622,16877,16879,16881,16882,16883,16887,16888,16889,29622,29623,29624,30345,16784,16785,16786,16788,16789,16790,16793,16795,16798,16799,16800,16802,81,16792,24524,29780,16794,16796,16797,9971,16874,30375,16893,16917,16935,16937,16941,16982,16986,16987,16891,27949,16894,16936,16977,16978,16896,16897,16939,16940,16981,16942,16943,16944,16945,16946,16947,16976,16992,16983,16984,16985,16988,16989,16990,16991,17011,17012,17013,17014,17015,17018,17016,17017,17675,17676,17677,17678,17679,17681,17682,17963,28943,29291,29292,29293,29740,30294,29179,17964,17965,17967,17991,17992,17993,17994,17995,17997,29862,18027,29186,19625,20309,20311,20314,20315,20316,20317,20318,20319,20320,20321,20323,20322,20324,20325,21155,23976,23977,21932,23871,24525,24690,27956,27951,17127,27952,27953,27954,27957,27959,27962,27960,28002,4,67,68,69,70,71,72,73,74,75,121,135,136,28424,3,32,33,35,39,40,43,45,47,52,89,92,93,94,95,96,97,98,100,108,109,110,111,186,190,354,1726,16871,16903,17673,17958,17959,17960,18124,18131,18511,19622,19623,20980,21597,21598,21601,21602,23956,23958,23959,24774,24775,24797,24798,24799,24800,24801,27988,28282,28909,28910,28911,28912,29229,29231,29412,29413,29553,29554,29555,29556,30297,29,30,31,42,27985,29240,29411,29414,29415,29481,29482,29484,29485,29486,29487,29488,29741,30007,30018,30019,30036,30037,30038,30040,30083,30103,30274,30282,30292,30293,29899,30266,30431,30727,30265,30269,30270,30271,30295,30296,30451,30455,30457,30458)';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $places[] = $place['id'];
  }
  $db->sql_freeresult($result);

  $places = array_unique($places);


  foreach ($places as $child) {
    $sql = 'SELECT id, name, url FROM rpg_places WHERE id = '.(int) $child.'';
    $placeResult = $db->sql_query($sql);
    $childrenMap[ $child ] = $db->sql_fetchrow($placeResult);
    $db->sql_freeresult($placeResult);
  }

  echo '<table>';

  $sql = 'SELECT place_id as id, count(*) as posts FROM rpg_content
            WHERE place_id IN ('.implode(',', $places).')
              AND written > DATE_SUB(now(), INTERVAL 1 WEEK)
            GROUP BY place_id
            ORDER BY count(*) DESC';
  $result = $db->sql_query($sql);
  while ($place = $db->sql_fetchrow($result)) {
    $childrenMap[ $place['id'] ]['posts'] = $place['posts'];

    echo '<tr><td>'.$childrenMap[ $place['id'] ]['name'] .'</td><td>' .$place['posts'].'</td></tr>';
  }
  $db->sql_freeresult($result);


  echo '</table>';
  //echo json_encode($childrenMap);
}
$db->sql_freeresult($result);




?>
