<?php

class creature
{
	function show($ignore = "false") {
	
		if ($ignore == "true" ) {
			$ignore = "&ignore=true";
		} else {
			$ignore = "";
		}
		
		require('config.php');
		
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

		if (mysqli_connect_errno()) {
		    echo "Connect failed: ". mysqli_connect_error();
		} else {

			$sql = "SELECT * FROM creatures WHERE id=".$this->creature_id;
			if ($result = $mysqli->query($sql)) {
				if (mysqli_num_rows($result) == 0 ) {
					echo "No such creature!";
				} else {
					while ($row = mysqli_fetch_assoc($result)) {
						
						$this->name = $row['name'];
						$this->views = $row['views'];
						$this->met = $row['met'];
						$this->born = $row['born'];
						$this->owner = $row['owner'];
						$this->evolution = $row['evolution'];
						
					}
				}
			}
			
			$sql = "SELECT * FROM evolutions WHERE id=".$this->evolution;
			if ($result = $mysqli->query($sql)) {
				while ($row = mysqli_fetch_assoc($result)) {
					$this->evolution_name = $row['name'];
				}
			}
			
			mysqli_close($mysqli);
		}
		
		$output = '<div class="creature">'."\n\t".'<dd><img src="http://gwing.net/yogurt/view.php?id='.$this->creature_id.$ignore.'" /></dd>';
		
		if ($this->owner != 0) {
			$output .= "\n\t".'<dd class="evolution">'.$this->evolution_name.'</dd>';

		
			$output .= "\n\t".'<dd><strong>Name:</strong> <a href="http://gwing.net/yogurt/show.php?id='.$this->creature_id.'">'.$this->name.'</a></dd>';
			$output .= "\n\t".'<dd><strong>Views:</strong> '.$this->views.'</dd>';
			$output .= "\n\t".'<dd><strong>Met:</strong> '.$this->met.' people</dd>';
			$output .= "\n\t".'<dd><strong>Born:</strong> '.$this->born.'</dd>';
		

			$output .= '<hr />';
			$output .= "\n\t".'<dt><strong>BBcode, for forums:</strong></dt><dd><textarea onclick="select();" scrolling="no" style="width:100%; height:auto;">[url=http://gwing.net/yogurt/show.php?id='.$this->creature_id.'][img]http://gwing.net/yogurt/view.php?id='.$this->creature_id.'[/img][/url]</textarea></dd>';
			$output .= "\n\t".'<dt><strong>HTML, for websites and MySpaces:</strong></dt><dd><textarea onclick="select();" scrolling="no" style="width:100%; height:auto;"><a href="http://gwing.net/yogurt/show.php?id='.$this->creature_id.'"><img src="http://gwing.net/yogurt/view.php?id='.$this->creature_id.'" border="0" /></a></textarea></dd>';
		} else {
			$output .= '<a href="adopt.php?id='.$this->creature_id.'">Steal this orb...</a>';
		}
		$output .= "</div>";
		
		return $output;
	}
	
	function abandon() {
		require('config.php');
		
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

		if (mysqli_connect_errno()) {
		    echo "Connect failed: ". mysqli_connect_error();
		} else {

			$sql = "UPDATE creatures SET owner = 0 WHERE id=".$this->creature_id;
			if ($result = $mysqli->query($sql)) {
				if (mysqli_num_rows($result) == 0 ) {
					$output = "You fail to abandon this creature.";
				} else {
					$output = "You abandoned ".$this->name."! :(";
				}
			}
			
			mysqli_close($mysqli);
		}

		return $output;
	}
	
	function name($name) {
	
		require('config.php');
		
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

		if (mysqli_connect_errno()) {
		    echo "Connect failed: ". mysqli_connect_error();
		} else {

			$sql = "UPDATE creatures SET name = ".$name." WHERE id=".$this->creature_id;
			if ($result = $mysqli->query($sql)) {
				if (mysqli_num_rows($result) == 0 ) {
					$output = "You have named this creature!";
				} else {
					$output = "For some reason, you didn't name this creature!";
				}
			}
			
			mysqli_close($mysqli);
		}

		return $output;	
	
	}
	
	function change_owner($user_id) {
		
	}

}

class user
{
	function login() {
	}
}

?>