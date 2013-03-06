<?php
#########################################################################################
#          File: display.php                                                            #
#   Script Name: Donation Meter v2.0.0                                                  #
# Re-Written By: Michael Milson                                                         #
#   Web Address: http://www.DonationBooster.info                                     #
# Email Address: Michael@DonationBooster.info                                        #
#                                                                                       #
# Description:                                                                          #
# Donation Meter is a script allowing you to take donations online via PayPal and store #
# a record of these donations in a mysql database, using the stored information the     #
# script generates a donation meter from images supplied depending on the level of the  #
# total amount of donations received so far.                                            #
#                                                                                       #
# Customization of the script and/or images can be done by us for a small fee of $25.   #  
#                                                                                       #
#                                                                                       #
# Michael Milson                                                                        #
# Michael@DonationBooster.info                                                       #
# http://www.DonationBooster.info                                                    #
#########################################################################################

// Do Not Modify This Code In Any Way (Unless you know what your doing).

include ("../dbsettings.php");

/// date_default_timezone_set('America/New_York');

$loginParams = mysql_connect("$dbhost", "$dbusername", "$dbpass");
mysql_select_db("$dbname",$loginParams);

@$dnuid=$_GET['dnuid'];
$month = date("m");
$year = date("Y");

if(($dnuid=="") or ($dnuid=="0")) {
$result = mysql_query("SELECT donation FROM `donations` WHERE month = '$month' AND year = '$year'");
} else {
	$sql = "SELECT donation FROM `donations` WHERE dnuid = $dnuid AND month = '$month' AND year = '$year'";

	$result = mysql_query($sql);
}

echo $sql;
	
$row = mysql_fetch_array($result);
$donation = 0;
while ($row) {
	$donation = $row['donation']+$donation;
		
	$row = mysql_fetch_array($result);
}
mysql_free_result($result);

// print_r($row);
// die();
//$donation = 200;

$count1 = @$donation / $goal;	
$count2 = $count1 * 100;
$current = number_format($count2, 0);



$start = isset($_GET['s']) ? $_GET['s'] : 0;
$end = isset($_GET['e']) ? $_GET['e'] : 100;
$p = isset($_GET['p']) ? $_GET['p'] : 1;
 
$pos = floor(2 * $current/($end - $start) * 100);
 
$im = imagecreate(250, 30); // width , height px
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$green = imagecolorallocate($im, 0, 204, 51);
 
imagesetthickness($im, 2);
imagealphablending($im, true); // setting alpha blending on
imagesavealpha($im, true);
//imagerectangle($im, 0, 0, 250, 15, $white);
imagefilledrectangle($im, 0, 0, $pos, 16, $green);
 
if ($p) {
	$text = ($pos / 2) . '%';
	$pos = $pos / 2;
	$text .= " of $".$goal;
	if ($pos <= 5) { $cel = " D:"; }
	if ($pos >= 10) { $cel = " :{"; }
	if ($pos >= 20) { $cel = " :|"; }
	if ($pos >= 30) { $cel = " :)"; }
	if ($pos >= 40) { $cel = " :D"; }
	if ($pos >= 50) { $cel = " :D~"; }
	if ($pos >= 60) { $cel = ' }:D'; }
	if ($pos >= 70) { $cel = ' !'; }
	if ($pos >= 80) { $cel = ' !!'; }
	if ($pos >= 90) { $cel = ' !!!'; }
	if ($pos >= 100) { $cel = ' epic win!'; }
	if ($pos >= 105) { $cel = ' holy %$&#!'; }
	if ($pos >= 110) { $cel = ' double kill!'; }
	if ($pos >= 120) { $cel = ' triple kill!'; }
	if ($pos >= 130) { $cel = ' ultra kill!'; }
	if ($pos >= 150) { $cel = ' mmmonster kill!'; }


	$text .= $cel;
	$font = '../../images/visitor-tt2-brk.ttf';
	$black = imagecolorallocate($im, 0, 0, 0);
	imagecolortransparent($im, $white);
	imagettftext($im, 12, 0, 45, 12, -1, $font, $text);
	imagettftext($im, 12, 0, 5, 30, -1, $font, "RPG's Monthly Costs: Donate today!");
}
 
header("Content-type: image/png");
imagepng($im);
?>