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
 * AIML test application
 * 
 * Load up dev-testcases.aiml and run this file to see if it passes all the tests
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

// Read tests.txt into an array, each line a different element.
$tests=array(
"testatomic",
"testdisplayset",
"testhide",
"testget",
"testsetx",
"testalter",
"testsettopic",
"test6a",
"test6b",
"testsimplecondition",
"testsimpleconditiona",
"testsimpleconditionmatch",
"testconditionlist",
"testconditionlistmatch",
"testconditionlistdefault",
"testconditionlistname",
"teststar test passed",
"teststar Test passed one and Test passed two and Test passed three and Test passed four",
"testunderscore Test passed one and Test passed two and Test passed three and Test passed four",
"testrandom",
"testwordformat",
"testnestedwordformat",
"testsimplemultisentencethat",
"testarray4multisentencethat",
"testarray3multisentencethat",
"testarray2multisentencethat",
"testarray1multisentencethat",
"testthatarray",
"testbotproperties",
"testconditionsetvalue",
"testnestedcondition",
"testnestedcondition1",
"testnestedcondition2",
"testsetcondition",
"testversion",
"testsrai",
"testsr sraisucceeded",
"testnestedsrai",
"testthinksrai",
"teststarset test passed",
"testidsizedate",
"testgossip",
"testname",
"testinput",
"testinput1",
"testinput2",
"testinput3",
"testgender he",
"testthatstar",
"testthatstar1",
"testmultithatstar",
"testmultithatstar1",
"testtopicstar",
"testmultitopicstar",
"test35",
"testoldtopic",
"test36",
"testextremesrai",
"testperson i was",
"testperson2 with you");

ss_timing_start("alll");

// For each element in the array to a curl request to talk.php.
for ($x=0;$x<sizeof($tests);$x++){

	// Start the session or get the existing session.
	session_start();
	$myuniqueid=session_id();

	// Timer will let us know how long it took to get our response.
	ss_timing_start("single");

	// Here is where we get the reply.
	$botresponse=reply($tests[$x],$myuniqueid,1);

	// Stop the timer.
	ss_timing_stop("single");

	// Print the results.
	print "<B>RESPONSE: " . $botresponse->response . "<BR></b>";
	print "<BR><BR>execution time: " . ss_timing_current("single");
	print "<BR>";

}

ss_timing_stop("alll");
print "<BR><BR>all execution time: " . ss_timing_current("alll");
print "<BR>";

?>