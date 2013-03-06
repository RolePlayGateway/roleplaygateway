<?php
error_reporting(E_ALL);
echo "Rating everything in the thread.";

$topic_rating = 10;
$topic_id = 26723;


// your connection
$mysqli = new mysqli("db.roleplaygateway.com","admin","sky22midnight","db_gateway");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// convert code
if ($result = $mysqli->query("SELECT post_id FROM gateway_posts WHERE topic_id = ".$topic_id.""))) {
    printf("Select returned %d rows.\n", $result->num_rows);

	while ($row = mysqli_fetch_assoc($result)) {

		if (!$mysqli->query("INSERT INTO gateway_prs_votes (post_id,user_id,score,time) VALUES ($row['post_id'],4,".$topic_rating.",NOW()))")) {
			echo "\nFailed: ";
		} else {
			echo "\nhandled post id #".$row['post_id'];
		}

	}
	
} else {
	printf("Query failed.\n");
}



?> 