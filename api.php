<?php
require('config.php');
$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);

$user_id = intval($_REQUEST['user_id']);

if (mysqli_connect_errno()) {
    echo "Connect failed: ". mysqli_connect_error();
} else {

	$sql = "SELECT * FROM gateway_post_stats WHERE poster_id = ".$user_id;

	if ($result = $mysqli->query($sql)) {
	
		$a = 1;
		while ($row = $result->fetch_assoc()) {
		
			$output[$a]['poster_id'] = $row['poster_id'];
			$output[$a]['post_id'] = $row['post_id'];
			$output[$a]['character_count'] = $row['character_count'];
			$output[$a]['word_count'] = $row['word_count'];
			$output[$a]['flesch_kincaid'] = $row['flesch_kincaid'];
			$output[$a]['flesch_kincaid_grade'] = $row['flesch_kincaid_grade'];
			$output[$a]['gunning_fog'] = $row['gunning_fog'];
			
			$a++;
			
		}

		foreach ($output as $post) {
			$user['character_total'] = $user['character_total'] + $post['character_count'];
			$user['word_total'] = $user['word_total'] + $post['word_count'];
			$user['posts'] = count($output);
			$user['user_id'] = $post['poster_id'];
		}

		$user['average_characters'] = $user['character_total'] / $user['posts'];
		$user['average_words'] = $user['word_total'] / $user['posts'];
		
		$buffer = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$buffer .= "<user>\n";
		$buffer .= "\t<userID>".$user['user_id']."</userID>\n";
		$buffer .= "\t<totalCharacters>".$user['character_total']."</totalCharacters>\n";
		$buffer .= "\t<totalWords>".$user['word_total']."</totalWords>\n";
		$buffer .= "\t<posts>".$user['posts']."</posts>\n";
		$buffer .= "\t<averageCharacters>".round($user['average_characters'],2)."</averageCharacters>\n";
		$buffer .= "\t<averageWords>".round($user['average_words'],2)."</averageWords>\n";
		$buffer .= "</user>";
	

		$result->close();
	} else {
		 echo "failed: ".mysqli_error($mysqli);
	}

	$mysqli->close();
}

echo $buffer;
?>
