<?

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
 * HTML interface for AIML loading
 * 
 * This script is loading your AIML files one at a time to prevent the 
 * script from timing out. Only use this script if your PHP is running in safe mode. 
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Loader
 */
 
/**
* The general preferences and database details.
*/ 
require_once "dbprefs.php";


/**
* Contains the actual functions used in this file to load the AIML files into MySQL.
*/
require_once "botloaderfuncs.php";

ss_timing_start("all");

print "<font size='3' color='black'>This script is loading your AIML files one at a time to prevent the script from timing out. Only use this script if your PHP is running in safe mode. If you are still experiencing timeouts then you need to edit your AIML files into smaller files.</font><br><BR>\n";


$fp="";

$templatesinserted=0;

$depth = array();
$whaton="";

$pattern="";
$topic="";
$that="";
$template="";

$startupwhich="";
$splitterarray=array();
$inputarray=array();
$genderarray=array();
$personarray=array();
$person2array=array();

if (!isset($HTTP_GET_VARS['fileid'])){
	#deletebot();
	$fileid=1;
}
else {
	$fileid=$HTTP_GET_VARS['fileid'];
}

#deletejustbot();
$doneloading=loadstartupinc($fileid);
$fileid++;
makesubscode();


print "<font size='3' color='BLUE'>Inserted $templatesinserted categories into database</font><br><BR>\n";

if ($doneloading==0){
	print "<p><font size='3' color='BLACK'><a href='botloaderinc.php?fileid=$fileid'>Click here to load the next file.</a></p></font>\n";
}
else {
	print "<font size='3' color='RED'><b>DONE LOADING</B><BR></font>\n";
	print "<font size='3' color='RED'><b>WARNING!</b> You should password protect the admin directory or remove the botloader.php script or people may be able to abuse your server.</b></font>\n";
	print "<p><font size='3' color='BLACK'><a href='../talk.php'>Click here to talk to the bot</a></p></font>\n";
}

print "<BR>";

ss_timing_stop("all");
print "<BR><BR><font size='3' color='BLACK'>execution time: " . ss_timing_current("all");
$avgts=$templatesinserted/ss_timing_current("all");
$avgtm=$templatesinserted/((ss_timing_current("all"))/60);
print "<BR><font size='3' color='BLACK'>Templates per second=$avgts<BR>";
print "<font size='3' color='BLACK'>Templates per minute=$avgtm<BR>";


?>