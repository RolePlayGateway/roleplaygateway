<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	
	<title>Project Yogurt</title>

	<style type="text/css">
		* {
			padding: 0px;
			margin:0px;
		}
		body {
			background:#ccc;
		}
		#container {
			background:#fff;
			margin:auto;
			padding-left:20px;
			padding-right:20px;
			width:450px;
		}
		#header {
			padding:10px;
		}
		#menu {
			padding:10px;
		}
		#menu a {
			padding:5px;
			margin-left:10px;
			border:1px solid black;
		}
		#content {
			padding:10px;
			border:1px solid black;
			margin-top:10px;
		}
		#footer {
			padding:10px;
			font-size:10px;
			text-align:right;
		}
		.evolution {
			font-size:0.8em;
			font-weight:bold;
		}
		.actions {
			float:right;
		}
		div.creature {
			padding:10px;
			margin:10px;
		}
	</style>
	
</head>
<body>

	<div id="container">
	
		<div id="header">
			
			<h1><a href="http://gwing.net/yogurt">Project Yogurt</a></h1>
			<span>Coming soon™.</span>
			
		</div>
		
		<div id="menu">
			<?php
			
				require('config.php');
				require('includes/library.php');
			
				session_start();
				
				if(isset($_SESSION['user_id'])) {
					echo '<a href="backpack.php">Backpack</a> <a href="logout.php">Log Out</a>';
				} else {
					echo '<a href="login.php">Log In</a> <a href="register.php">Register</a>';
				}
			?>
		</div>
		
		<div id="content">