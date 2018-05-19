<?php
require('config.php');

// Increase to parse more data
$timescale = "4000000";

$link = mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);

if (!$link) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$query = "SELECT user_regdate FROM gateway_users ORDER BY user_regdate ASC";


if ($result = mysqli_query($link, $query)) {
	while ($row = mysqli_fetch_assoc($result)) {
		$regdate = $row['user_regdate'];
		
		$day = floor($regdate / $timescale);
		
		$days[$day]['day'] = $day;
		$days[$day]['registrations']++;
	}	

	$query = "SELECT post_time FROM gateway_posts ORDER BY post_time ASC";
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
	
	$query = "SELECT topic_time FROM gateway_topics ORDER BY topic_time ASC";
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
	
	$query = "SELECT unix_timestamp(created) as roleplay_date FROM rpg_roleplays ORDER BY created ASC";
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$roleplaydate = $row['roleplay_date'];
			
			$day = floor($roleplaydate / $timescale);
			
			//$days[$day]['day'] = $day;
			$days[$day]['roleplays']++;
		}
	} else {
		echo "roleplay query failed";
	}
	
	$query = "SELECT unix_timestamp(created) as character_date FROM rpg_characters ORDER BY created ASC";
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$characterdate = $row['character_date'];
			
			$day = floor($characterdate / $timescale);
			
			//$days[$day]['day'] = $day;
			$days[$day]['characters']++;
		}
	} else {
		echo "character query failed";
	}
	
	$query = "SELECT unix_timestamp(written) as content_date FROM rpg_content ORDER BY written ASC";
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {
			$contentdate = $row['content_date'];
			
			$day = floor($contentdate / $timescale);
			
			//$days[$day]['day'] = $day;
			$days[$day]['content']++;
		}
	} else {
		echo "content query failed";
	}
	
	$data = "0";
	$post_data = "|0";
	$topic_data = "|0";
	$roleplay_data = "|0";
	$character_data = "|0";
	$content_data = "|0";
	$all_data = "|0";
	foreach ($days as $row) {
		// The division factor will scale DOWN the results on the graph.
		// Increase the integer to fix off the chart results.
	
		$data .= ",".round(($row['registrations'] / 22));
		$post_data .= ",".round(($row['posts'] / 1000));
		$topic_data .= ",".round(($row['topics']) / 30);
		$roleplay_data .= ",".round(($row['roleplays']) / 8);
		$character_data .= ",".round(($row['characters']) / 75);
		$content_data .= ",".round(($row['content']) / 2000);
		$all_data .= ",".round((
				$row['content']
			+ $row['posts']) / 5000);
		
		//echo "<br />Registrations: ".($row['registrations'] / 3.5)." (Month ".$row['day'].")";
		//echo "<br />Posts: ".(($row['posts'] / 100))." (Month ".$row['day'].")";
	}
	
	
	echo "<img src=\"";
	echo "http://chart.apis.google.com/chart?cht=lc&chs=800x300&chd=t:".$data.$post_data.$topic_data.$roleplay_data.$character_data.$content_data.$all_data."&cht=lc&chxt=x,y&chxl=0:|Creation|Current|1:||Members&chco=ff0000,00ff00,0000ff,00ffff,ffff00,ff00ff,000000&chdl=Registrations|Forum Posts|Topics|Roleplays|Characters|Roleplay Content|Overall";
	echo "\" />";
} else {
	echo 'query failed';
}
?>