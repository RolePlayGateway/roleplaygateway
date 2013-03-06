<?php

// Get these from http://developers.facebook.com
$api_key = '863b190cde7b8efe6e049b40c837b1c2';
$secret  = '07311331e369443582b1d47487977c46';
/* While you're there, you'll also want to set up your callback url to the url
 * of the directory that contains Footprints' index.php, and you can set the
 * framed page URL to whatever you want.  You should also swap the references
 * in the code from http://apps.facebook.com/footprints/ to your framed page URL. */

// The IP address of your database
$db_ip = '127.0.0.1';           

$db_user = 'root';
$db_pass = 'sky22midnight';

// the name of the database that you create for footprints.
$db_name = 'db_gateway';

/* create this table on the database:
CREATE TABLE `footprints` (
  `from` int(11) NOT NULL default '0',
  `to` int(11) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  KEY `from` (`from`),
  KEY `to` (`to`)
)
*/
