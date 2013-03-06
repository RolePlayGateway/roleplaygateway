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
$loginParams = mysql_connect("$dbhost", "$dbusername", "$dbpass");
mysql_select_db("$dbname",$loginParams);

$dnuid=$_GET['dnuid'];

if(($dnuid=="") or ($dnuid=="0")) {
$result = mysql_query("SELECT * FROM `donations`");
} else {
$result = mysql_query("SELECT * FROM `donations` WHERE dnuid = $dnuid");
}
	
$row = mysql_fetch_array($result);
	while ($row) {
		$donation = $row['donation']+$donation;
		$row = mysql_fetch_array($result);
}

$goal = number_format(str_replace(",","",$goal),2);
$donation = number_format(str_replace(",","",$donation),2);

$goal = str_replace(",","",$goal);
$new_value2 = number_format($donation,2);
$new_value = $new_value2;
$total = $new_value-$fees;
$per = $total / $goal;
$res = $per * 100;
$res = number_format($res, 0);

$current = $donation;


if ($res == 0){
$imgsrc = '0';
}elseif ($res == 1){
$imgsrc = '1';
}elseif ($res == 2){
$imgsrc = '2';
}elseif ($res == 3){
$imgsrc = '3';
}elseif ($res == 4){
$imgsrc = '4';
}elseif ($res == 5){
$imgsrc = '5';
}elseif ($res == 6){
$imgsrc = '6';
}elseif ($res == 7){
$imgsrc = '7';
}elseif ($res == 8){
$imgsrc = '8';
}elseif ($res == 9){
$imgsrc = '9';
}elseif ($res == 10){
$imgsrc = '10';
}elseif ($res == 11){
$imgsrc = '11';
}elseif ($res == 12){
$imgsrc = '12';
}elseif ($res == 13){
$imgsrc = '13';
}elseif ($res == 14){
$imgsrc = '14';
}elseif ($res == 15){
$imgsrc = '15';
}elseif ($res == 16){
$imgsrc = '16';
}elseif ($res == 17){
$imgsrc = '17';
}elseif ($res == 18){
$imgsrc = '18';
}elseif ($res == 19){
$imgsrc = '19';
}elseif ($res == 20){
$imgsrc = '20';
}elseif ($res == 21){
$imgsrc = '21';
}elseif ($res == 22){
$imgsrc = '22';
}elseif ($res == 23){
$imgsrc = '23';
}elseif ($res == 24){
$imgsrc = '24';
}elseif ($res == 25){
$imgsrc = '25';
}elseif ($res == 26){
$imgsrc = '26';
}elseif ($res == 27){
$imgsrc = '27';
}elseif ($res == 28){
$imgsrc = '28';
}elseif ($res == 29){
$imgsrc = '29';
}elseif ($res == 30){
$imgsrc = '30';
}elseif ($res == 31){
$imgsrc = '31';
}elseif ($res == 32){
$imgsrc = '32';
}elseif ($res == 33){
$imgsrc = '33';
}elseif ($res == 34){
$imgsrc = '34';
}elseif ($res == 35){
$imgsrc = '35';
}elseif ($res == 36){
$imgsrc = '36';
}elseif ($res == 37){
$imgsrc = '37';
}elseif ($res == 38){
$imgsrc = '38';
}elseif ($res == 39){
$imgsrc = '39';
}elseif ($res == 40){
$imgsrc = '40';
}elseif ($res == 41){
$imgsrc = '41';
}elseif ($res == 42){
$imgsrc = '42';
}elseif ($res == 43){
$imgsrc = '43';
}elseif ($res == 44){
$imgsrc = '44';
}elseif ($res == 45){
$imgsrc = '45';
}elseif ($res == 46){
$imgsrc = '46';
}elseif ($res == 47){
$imgsrc = '47';
}elseif ($res == 48){
$imgsrc = '48';
}elseif ($res == 49){
$imgsrc = '49';
}elseif ($res == 50){
$imgsrc = '50';
}elseif ($res == 51){
$imgsrc = '51';
}elseif ($res == 52){
$imgsrc = '52';
}elseif ($res == 53){
$imgsrc = '53';
}elseif ($res == 54){
$imgsrc = '54';
}elseif ($res == 55){
$imgsrc = '55';
}elseif ($res == 56){
$imgsrc = '56';
}elseif ($res == 57){
$imgsrc = '57';
}elseif ($res == 58){
$imgsrc = '58';
}elseif ($res == 59){
$imgsrc = '59';
}elseif ($res == 60){
$imgsrc = '60';
}elseif ($res == 61){
$imgsrc = '61';
}elseif ($res == 62){
$imgsrc = '62';
}elseif ($res == 63){
$imgsrc = '63';
}elseif ($res == 64){
$imgsrc = '64';
}elseif ($res == 65){
$imgsrc = '65';
}elseif ($res == 66){
$imgsrc = '66';
}elseif ($res == 67){
$imgsrc = '67';
}elseif ($res == 68){
$imgsrc = '68';
}elseif ($res == 69){
$imgsrc = '69';
}elseif ($res == 70){
$imgsrc = '70';
}elseif ($res == 71){
$imgsrc = '71';
}elseif ($res == 72){
$imgsrc = '72';
}elseif ($res == 73){
$imgsrc = '73';
}elseif ($res == 74){
$imgsrc = '74';
}elseif ($res == 75){
$imgsrc = '75';
}elseif ($res == 76){
$imgsrc = '76';
}elseif ($res == 77){
$imgsrc = '77';
}elseif ($res == 78){
$imgsrc = '78';
}elseif ($res == 79){
$imgsrc = '79';
}elseif ($res == 80){
$imgsrc = '80';
}elseif ($res == 81){
$imgsrc = '81';
}elseif ($res == 82){
$imgsrc = '82';
}elseif ($res == 83){
$imgsrc = '83';
}elseif ($res == 84){
$imgsrc = '84';
}elseif ($res == 85){
$imgsrc = '85';
}elseif ($res == 86){
$imgsrc = '86';
}elseif ($res == 87){
$imgsrc = '87';
}elseif ($res == 88){
$imgsrc = '88';
}elseif ($res == 89){
$imgsrc = '89';
}elseif ($res == 90){
$imgsrc = '90';
}elseif ($res == 91){
$imgsrc = '91';
}elseif ($res == 92){
$imgsrc = '92';
}elseif ($res == 93){
$imgsrc = '93';
}elseif ($res == 94){
$imgsrc = '94';
}elseif ($res == 95){
$imgsrc = '95';
}elseif ($res == 96){
$imgsrc = '96';
}elseif ($res == 97){
$imgsrc = '97';
}elseif ($res == 98){
$imgsrc = '98';
}elseif ($res == 99){
$imgsrc = '99';
}elseif ($res == 100){
$imgsrc = '100';
}else{
$imgsrc = '100';
}

$pic = "donation-meter".$imgsrc.".png";

//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");
//header('Content-Length: '.filesize($pic));
//header("Content-type: image/gif");
$im = imagecreatefrompng($pic); /* Attempt to open */
//$string = $cursym.$new_value2." In Donations";
//$string = " ";
//$textcolor = imagecolorallocate($im, 0, 0, 0);
$white = imagecolorallocate($im, 255, 255, 255);
$green = imagecolorallocate($im, 0, 255, 0);
$black = imagecolorallocate($im, 0, 0, 0);

$percent0 = "-- ".$cursym."0";
$percent25  = "-- ".$cursym.(($goal/100)*25);
$percent50  = "-- ".$cursym.(($goal/100)*50);
$percent75  = "-- ".$cursym.(($goal/100)*75);
$percent100  = "-- ".$cursym.(($goal/100)*100);
$totalcurrent = "-- ".$cursym.$total;
$curperres = ($res*2)-200;
function change_pol($integer){ 
    return (0 - $integer); 
} 
$curperres = change_pol($curperres);
$totalpercent = $res."%";



//$px    = (imagesx($im) - 7 * strlen($string)) / 2;
$px = 60;
$px2 = 42;
$px3 = 26;
imagestring($im, 3, $px, 200, $percent0, $white);
imagestring($im, 3, $px, 150, $percent25, $white);
imagestring($im, 3, $px, 100, $percent50, $white);
imagestring($im, 3, $px, 50, $percent75, $white);
imagestring($im, 3, $px, 0, $percent100, $white);
imagestring($im, 3, $px2, $curperres, $totalcurrent, $green);
imagestring($im, 3, $px3, 220, $totalpercent, $white);
imagepng($im);
imagedestroy($im);
?>