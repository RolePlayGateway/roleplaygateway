<?PHP
#########################################################################################
#          File: display-last-15-donations.php                                          #
#   Script Name: Donation Meter v4.0.0                                                  #
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

// Include the following code anywhere on your php page modify the bits marked to do so.
?>
<?PHP
//This code calls the database login routine so it can access info in the database, this needs to be placed only once somewhere near the top of the page containing the donation meter image or last x donations script.
include ("donationmeter/dbsettings.php");

$loginParams = mysql_connect("$dbhost", "$dbusername", "$dbpass");
mysql_select_db("$dbname",$loginParams);
?>

<?
//Display last x donations for any donation reason
echo '<table border="1">';
$result = mysql_query("SELECT * FROM `donations` ORDER BY id DESC LIMIT $lastxdonations");
$row = mysql_fetch_array($result);
	while ($row) {
	$dated=gmdate($dateformat, $row['time'] + $zone);
	
		echo '<tr><td>'.$row['name'].' Donated '.$cursym.$row['donation'].' On '.$dated.'</td></tr>';
		$row = mysql_fetch_array($result);
	}
echo '</table>';
?>

<?
//Here we display last x donations for reason 1
echo '<table border="1">';
$result1 = mysql_query("SELECT * FROM `donations` WHERE dnuid=1 ORDER BY id DESC LIMIT $lastxdonations");
$row1 = mysql_fetch_array($result1);
	while ($row1) {
	$dated1=gmdate($dateformat, $row1['time'] + $zone);
	
		echo '<tr><td>'.$row1['name'].' Donated '.$cursym.$row1['donation'].' On '.$dated1.'</td></tr>';
		$row1 = mysql_fetch_array($result1);
	}
echo '</table>';
?>

<?
//Here we display last x donations for reason 2
echo '<table border="1">';
$result2 = mysql_query("SELECT * FROM `donations` WHERE dnuid=2 ORDER BY id DESC LIMIT $lastxdonations");
$row2 = mysql_fetch_array($result2);
	while ($row2) {
	$dated2=gmdate($dateformat, $row2['time'] + $zone);
	
		echo '<tr><td>'.$row2['name'].' Donated '.$cursym.$row2['donation'].' On '.$dated2.'</td></tr>';
		$row2 = mysql_fetch_array($result2);
	}
echo '</table>';
?>

<!-- Using one of the above codes including the database login code at the top you can display the name of the donor, the amount donated and date donation was made, of the last 15 donations. -->