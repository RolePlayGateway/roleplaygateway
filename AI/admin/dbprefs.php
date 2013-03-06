<?php

/*
    Program E
	Copyright 2002, Paul Rydell

	This file is part of Program E.

	Program E is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Program E is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Program E; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Preferences file
 * 
 * Contains the preferences settings for the database and the application itself. Constants
 * and the main MySQL connection.
 * @author Paul Rydell
 * @author Anne Kootstra
 * @copyright 2002 & 2004
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Settings
 */

// Turn this off in case people have it on.
set_magic_quotes_runtime(0);

// Can't turn off magic quotes gpc so just redo what it did if it is on.
if (get_magic_quotes_gpc()) {
	foreach($HTTP_GET_VARS as $k=>$v)
		$HTTP_GET_VARS[$k] = stripslashes($v);
	foreach($HTTP_POST_VARS as $k=>$v)
		$HTTP_POST_VARS[$k] = stripslashes($v);
	foreach($HTTP_COOKIE_VARS as $k=>$v)
		$HTTP_COOKIE_VARS[$k] = stripslashes($v);
}

/** 
* The errormessage when a loop is detected. 
*/
define("LOOPINGERRORMSG", "Oops. I wasn't paying attention. Tell me again what is going on?");

/**
* The number of times a loop may occur before the error is thrown. -1 equals to no limit.
*/
define("LOOPINGLIMIT",150); // -1 for no limit

/**
* Has something to do with the random generator
*/
define("RANDOMCHANCECLEAN",100); // -1 to never check

/**
* the amount of minutes certain data needs to be kept.
*/
define("MINUTESTOKEEPDATA",120); // -1 to keep forever

/**
* Minutes to keep the chatlot, standard set to -1, meaning forever.
*/
define("MINUTESTOKEEPCHATLOG",7200); // -1 to keep forever

/**
* standard value when a user predicate hasn't been set. 
*/ 
define("DEFAULTPREDICATEVALUE", "that");


/**
* wether or not to use the resonse caching mechanism. 1=yes, 0=no
*/
define('CACHE_CONTROL', 1);

/**
* version number of the application.
*/
define("PROGRAMEVERSION","v0.09");

// This is where all the AIML and startup.xml resides
$rootdir="/var/www/html/AI/admin/";

$DB_HOST="10.179.39.224";
$DB_UNAME="hal";
$DB_PWORD="sky22midnight";
$DB_DB="ai";

$errors="";

mysql_connect($DB_HOST,$DB_UNAME,$DB_PWORD) or $errors = $errors . "Could not connect to database.\n";
mysql_select_db($DB_DB) or $errors = $errors . "Unable to select database\n";

?>