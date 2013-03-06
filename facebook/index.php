<?php

include_once 'facebook.php';
include_once 'lib.php';
include_once 'config.php';

define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$facebook = new Facebook($api_key, $secret);
$facebook->require_frame();
$user = $facebook->require_login();

?>
<img src="http://www.roleplaygateway.com/styles/prosilver/imageset/rpgateway.png" style="float:left;" />
<h1>Facebook Roleplay with RolePlayGateway</h1>
<div style="padding: 10px;">
	<fb:profile-pic uid="loggedinuser" size="q" />
	<h2>Welcome <fb:name firstnameonly="true" uid="<?=$user?>" useyou="false"/>!</h2>
	<br style="clear:both;" />
	<table>
		<tr>
			<th><h3>Roleplays</h3></th>
			<th><h3>Threads</h3></th>
			<th><h3>Posts</h3></th>
		</tr>
		<tr>
			<td valign="top">
				<ul>
					<?php 

					$roleplays = get_roleplays();
					$topics = get_topics();

					foreach ($roleplays as $roleplay) {

						echo "<li><a href=\"http://www.roleplaygateway.com/\">".$roleplay."</a></li>";

					}

					?>
				</ul>
			</td>
			<td valign="top">
				<ul>
					<?php
					foreach ($topics as $topic) {

						echo "<li><a href=\"http://www.roleplaygateway.com/\">".$topic."</a></li>";

					}

					?>
				</ul>
			</td>
			<td valign="top">

			</td>
		</tr>
	</table>
	<p>This has been a test of the RolePlayGateway application!</p>

	<hr/>

	<div style="clear: both;"/>
</div>

<?php

//echo "<pre>Debug:" . print_r($facebook,true) . "</pre>";

?>