<?php
//require('begin_caching.php');
require('config.php');

$link = mysqli_connect($dbhost,$dbuser,$dbpasswd,$dbname);

if (!$link) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

function top_rated() {
	global $link;

	return $contents;
}

function longest_topics() {
	global $link;
	$query = "SELECT p.post_subject,p.topic_id,sum(word_count) as topic_length FROM gateway_posts p
				INNER JOIN gateway_post_stats s ON p.post_id=s.post_id
					GROUP BY p.topic_id
				ORDER BY topic_length DESC";
	
	if ($result = mysqli_query($link, $query)) {
		while ($row = mysqli_fetch_assoc($result)) {
				
				$topic_id = $row['topic_id'];
				
				$current_length = $topics[$topic_id]['topic_length'];
				$this_post_length = strlen($row['post_text']);
				
				//$contents .= "Current Length: ".$current_length."<br />This Length: ".$this_post_length."<br /><br />";
				
				$topics[$topic_id]['topic_length'] = $current_length + $this_post_length;
				$topics[$topic_id]['topic_id'] = $row['topic_id'];
				$topics[$topic_id]['post_title'] = $row['post_subject'];
				
				
		}
	} else {
		$contents .= 'query failed';
	}
	
	
	sort($topics);
	
	foreach ($topics as $topic) {
		$post_length = $topic['topic_length'];
		$contents .= "<a href=\"http://www.roleplaygateway.com/viewtopic.php?t=".$topic['topic_id']."\">".$topic['post_title']." (topic id #".$topic['topic_id'].")</a>, with ".$post_length." characters.<br />";
	}
	
	
	
	return $contents."<br />";
}


function frequent_posters() {

	global $link;


	$query = "SELECT user_id,user_posts,user_regdate,username FROM gateway_users";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			
			$user = $row['user_id'];
			$name = $row['username'];
			$posts = $row['user_posts'];
			$regdate = $row['user_regdate'];
			
			@$winner[$user]["score"] = ($posts / ceil((time() - $regdate) / 86400));
			$winner[$user]["user"] = $user;
			$winner[$user]["name"] = $name;
			$winner[$user]["regdate"] = $regdate;
			$winner[$user]["posts"] = $posts;
			
		}
		
		sort($winner);
		
		$winner = array_reverse($winner);
		$winner = array_slice($winner,0,20);
		
		//print_r($winner);
		
		
		foreach ($winner as $row) {
			$contents .= '<a href="member'.$row["user"].'.html">'.$row["name"].'</a> ('.round($row["score"],2).'/day after '.round(ceil((time() - $row["regdate"]) / 86400)).' days.)<br />';
		}
		
	    /* free result set*/
	    mysqli_free_result($result);
	} else {
		$contents .= 'query failed';
	}

	$contents .= '</div>';

	return $contents;
}

function most_replies() {
	global $link;
	
	$query = "SELECT * FROM gateway_topics ORDER BY topic_replies DESC LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="viewtopic.php?t='.$row["topic_id"].'">'.$row["topic_title"]."</a> with ".$row['topic_replies']." replies.<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;
}

function most_active_members_in_forum($forum_id) {
	global $link;
	
	$query = "SELECT *,count(*) as b FROM gateway_posts LEFT JOIN gateway_users ON gateway_posts.poster_id = gateway_users.user_id WHERE gateway_posts.forum_id = ".$forum_id." GROUP BY gateway_posts.poster_id ORDER BY b DESC LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

		/* fetch row */
		while ($row = mysqli_fetch_assoc($result)) {

			$contents .= "<a href=\"member-u".$row['user_id'].".html\">".$row['username']."</a><br />";

		}

		/* free result set*/
		mysqli_free_result($result);
	} else {
		$contents = "failed.".mysqli_error();
	}
	
	return $contents;
}

function common_icons() {
	global $link;
	
	$query = "SELECT * FROM gateway_topics GROUP BY topic_icon LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="viewtopic.php?t='.$row["topic_id"].'">'.$row["topic_title"]."</a> with ".$row['topic_replies']." replies.<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;
}

function most_views() {
	global $link;
	
	$query = "SELECT * FROM gateway_topics ORDER BY topic_views DESC LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="viewtopic.php?t='.$row["topic_id"].'">'.$row["topic_title"]."</a> with ".$row['topic_views']." views.<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;
}


function common_posts() {
	global $link;
	
	$query = "SELECT count(*) AS duplicate_count, post_id, topic_id, post_text, post_subject
			FROM gateway_posts
			GROUP BY post_text
			ORDER BY duplicate_count DESC
		LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= 'Posted '.$row["duplicate_count"].' times, as seen in <a href="viewtopic.php?p='.$row["post_id"].'#p'.$row["post_id"].'">'.$row["post_subject"].' (post id '.$row["post_id"].')</a>:<blockquote>'.$row["post_text"].'</blockquote>';
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;
}

function top_rated_users() {
	global $link;

	$query = "SELECT * FROM gateway_user_stats WHERE prs_o >= '0.5' ORDER BY prs_reputation DESC LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="member-u'.$row["user_id"].'.html">'.$row["user_id"]."</a> (rated ".$row["prs_reputation"].")<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;	

}
function bottom_rated_users() {
	global $link;

	$query = "SELECT * FROM gateway_user_stats WHERE prs_o >= '0.5' ORDER BY prs_reputation ASC LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="member-u'.$row["user_id"].'.html">'.$row["user_id"]."</a> (rated ".$row["prs_reputation"].")<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;	

}
function most_complex_posts() {
	global $link;

	$query = "SELECT * FROM gateway_post_stats ORDER BY flesch_kincaid_grade desc LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="post'.$row["post_id"].'.html#p'.$row['post_id'].'">'.$row["post_subject"]."</a> (graded ".$row["flesch_kincaid_grade"].")<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;	

}
function least_complex_posts() {
	global $link;

	$query = "SELECT * FROM gateway_post_stats ORDER BY flesch_kincaid_grade asc LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
		
			if (strlen($row['post_subject']) <= 0) {
				$row['post_subject'] = "no subject";
			}
		
			$contents .= '<a href="post'.$row["post_id"].'.html#p'.$row['post_id'].'">'.$row["post_subject"]."</a> (graded ".$row["flesch_kincaid_grade"].")<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;	

}
function verbose_users() {
	global $link;

	$query = "SELECT * FROM gateway_user_stats
				WHERE posts > 50
				ORDER BY total_words desc LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="member-u'.$row["user_id"].'.html">'.$row["user_id"]."</a> (".$row["total_words"]." words written)<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;	

}
function consistently_verbose_users() {
	global $link;

	$query = "SELECT * FROM gateway_user_stats
				WHERE posts > 50
				ORDER BY average_words desc LIMIT 25";

	if ($result = mysqli_query($link, $query)) {

	    /* fetch row */
	    while ($row = mysqli_fetch_assoc($result)) {
			$contents .= '<a href="member-u'.$row["user_id"].'.html">'.$row["user_id"]."</a> (".$row["average_words"]." words per post)<br />";
		}

	    /* free result set*/
	    mysqli_free_result($result);
	}
	
	return $contents;	

}

?>

<html>
<head>
	<title>Basic Forum Stats</title>
	<link href="style.php?id=7&lang=en" rel="stylesheet" type="text/css" media="screen, projection" />
	<style type="text/css">
		* {margin:0px; padding:0px;}
		body {background:#ccc;}
		#container {width:915px; margin-left:auto; margin-right:auto; background:#fff; padding:20px;}
		.block table * {font-size:90%;}
		blockquote {padding-top:20px;}
	</style>
</head>
<body>
	<div id="container">
		<div class="graph">
			<?php include('reports.php'); ?>
		</div>
		
		<div class="block">	
			<table width="100%">
				<tr>
					<td><h2>Top Rated Users</h2><?php echo top_rated_users(); ?></td>
					<td><h2>Bottom Rated Users</h2><?php echo bottom_rated_users(); ?></td>
					<td><h2>Highest Average</h2><?php echo consistently_verbose_users(); ?></td>
					<td><h2>Most Verbose Users</h2><?php echo verbose_users(); ?></td>
				</tr>
			</table>			
		</div>
		
		<div class="block">	
			<table width="100%">
				<tr>
					<td><h2>Most Complex Posts</h2><?php echo most_complex_posts(); ?></td>
					<td><h2>Least Complex Posts</h2><?php echo least_complex_posts(); ?></td>
				</tr>
			</table>			
		</div>
		
		<div class="block">
			<h2>Basic Forum Stats</h2>
			<table>
				<tr>
					<td><h3>Most Replied To</h3><?php echo most_replies(); ?></td>
					<td><h3>Most Viewed</h3><?php echo most_views(); ?></td>
					<td><h3>Biggest Spammers</h3><?php echo frequent_posters(); ?></td>
				</tr>
			</table>
		</div>
<?php
/*
		<div class="block">
			<h2>Most Common Posts</h2>
			<table>
				<tr>
					<td><?php echo common_posts(); ?></td>
				</tr>
			</table>
		</div>
*/
?>		
<?php
/*
		<div class="block">
			<h2>Top Users In Forums</h2>
			<table>
				<tr>
					<td><h3>Fantasy Forum</h3><?php echo most_active_members_in_forum('102'); ?></td>
					<td><h3>Realistic Forum</h3><?php echo most_active_members_in_forum('111'); ?></td>
					<td><h3>Futuristic Forum</h3><?php echo most_active_members_in_forum('112'); ?></td>
					<td><h3>Fiction-Based Forum</h3><?php echo most_active_members_in_forum('113'); ?></td>
					<td><h3>Everything Else Forum</h3><?php echo most_active_members_in_forum('67'); ?></td>
				</tr>
			</table>
		</div>
	*/
?>

	</div>
</body>
</html>

<?php

mysqli_close($link);

//require('end_caching.php');
?>