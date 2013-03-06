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
 * Flash User Interface
 * 
 * Contains the basics of Flash communication
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Interpreter
 * @subpackage Responder
 */


/**
* Include the guts of the program.
*/
include "respond.php";

$numselects=0;

// Start the session or get the existing session.
session_start();
$myuniqueid=session_id();

// Here is where we get the reply. Make sure you fill in testbot with your bot's name
$botresponse=reply($HTTP_POST_VARS['input'],$myuniqueid,"TestBot");

// Print the results.
print "&bot_name=Test Agent\n";
print "&alice_out=" . $botresponse->response . "\n";
print "&textLoaded=1";


?>
