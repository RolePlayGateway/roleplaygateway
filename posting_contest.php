<?php
require('config.php');

$timescale = "500000";

$link = mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);

if (!$link) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$query = "SELECT user_regdate FROM gateway_users WHERE user_regdate > 1180670400  AND user_regdate <  1199163600 ORDER BY user_regdate ASC";


if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_assoc($result)) {
		$regdate = $row['user_regdate'];
		
		$day = floor($regdate / $timescale);
		
		$days[$day]['day'] = $day;
		$days[$day]['registrations']++;
	}	

	$query = "SELECT post_time FROM gateway_posts WHERE post_time > 1180670400  AND post_time <  1199163600 ORDER BY post_time ASC";
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$postdate = $row['post_time'];
			
			$day = floor($postdate / $timescale);
			
			//$days[$day]['day'] = $day;
			$days[$day]['posts']++;
		}
	} else {
		echo "post query failed";
	}
	
	$query = "SELECT topic_time FROM gateway_topics WHERE topic_time > 1180670400  AND topic_time <  1199163600 ORDER BY topic_time ASC";
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$topicdate = $row['topic_time'];
			
			$day = floor($topicdate / $timescale);
			
			//$days[$day]['day'] = $day;
			$days[$day]['topics']++;
		}
	} else {
		echo "topic query failed";
	}
	
	$data = "0";
	$post_data = "|0";
	$topic_data = "|0";
	foreach ($days as $row) {
		$data .= ",".round(($row['registrations'] / 1));
		$post_data .= ",".round(($row['posts'] / 20));
		$topic_data .= ",".round(($row['topics']) / 1);
		
		//echo "<br />Registrations: ".($row['registrations'] / 3.5)." (Month ".$row['day'].")";
		//echo "<br />Posts: ".(($row['posts'] / 100))." (Month ".$row['day'].")";
	}
	
	
	echo "<img src=\"";
	echo "http://chart.apis.google.com/chart?cht=lc&chs=800x300&chd=t:".$data.$post_data.$topic_data."&cht=lc&chxt=x,y&chxl=0:|Creation|Current|1:||Members&chco=ff0000,00ff00,0000ff&chdl=Registrations|Posts|Topics";
	echo "\" />";
} else {
	echo 'query failed';
}
?>