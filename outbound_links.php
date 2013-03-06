<?php

require('config.php');

$mysqli = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if ($result = $mysqli->query("SELECT post_id,post_text FROM gateway_posts WHERE post_text LIKE '%http%'")) {
   
   
   while($obj = $result->fetch_object()){
            $line.=$obj->post_id;
            $line.=$obj->post_text;
			$line.="\n";
        }

    /* free result set */
    $result->close();
}

$line = html_entity_decode($line);

preg_match_all('/(http|https):\/\/(.*)\]/',$line,$out);

print_r($out);


/* close connection */
$mysqli->close();

?>