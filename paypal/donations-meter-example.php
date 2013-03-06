<?PHP
#########################################################################################
#          File: donations-meter-example.php                                            #
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

// Include the following code completely un changed anywhere on your php page.
?>

<?
//Database login routine needs to be placed in your page code somewhere above one of the image codes below, if you are displaying more than one donation meter on the same page you only need to place the database login routine on the page once, somewhere near the top of the page, before any of the image codes appear on that page.
include ("donationmeter/dbsettings.php");

$loginParams = mysql_connect("$dbhost", "$dbusername", "$dbpass");
mysql_select_db("$dbname",$loginParams);
?>

<!-- Start Display a donation meter image for all donations -->
<?
//Here we select all donations for all donation ids/reasons
$result = mysql_query("SELECT donation FROM `donations`");
	
$row = mysql_fetch_array($result);
	while ($row) {
		$donation = $row['donation']+$donation;
			
		$row = mysql_fetch_array($result);
	}	

$new_value = number_format ($donation, 2);
?>
<p align="center"><img src="/donationmeter/images/display.php?dnuid=0" alt="We Have Raised <?PHP echo $cursym.$new_value; ?> So Far" border="0">&nbsp;</p>
<!-- End Display a donation meter image for all donations -->

<!-- Start Display a donation meter image for donations with id/reason 1 -->
<?
//Here we select all donations that were for donation id 1
$result1 = mysql_query("SELECT donation FROM `donations` WHERE dnuid=1");
	
$row1 = mysql_fetch_array($result1);
	while ($row1) {
		$donation1 = $row1['donation']+$donation1;
			
		$row2 = mysql_fetch_array($result1);
	}	

$new_value1 = number_format ($donation1, 2);
?>
<p align="center"><img src="/donationmeter/images/display.php?dnuid=1" alt="We Have Raised <?PHP echo $cursym.$new_value1; ?> So Far" border="0">&nbsp;</p>
<!-- End Display a donation meter image for donations with id/reason 1 -->

<!-- Start Display a donation meter image for donations with id/reason 2 -->
<?
//Here we select all donations that were for donation id 2
$result2 = mysql_query("SELECT donation FROM `donations` WHERE dnuid=2");
	
$row2 = mysql_fetch_array($result2);
	while ($row2) {
		$donation2 = $row2['donation']+$donation2;
			
		$row2 = mysql_fetch_array($result2);
	}	

$new_value2 = number_format ($donation2, 2);
?>
<p align="center"><img src="/donationmeter/images/display.php?dnuid=2" alt="We Have Raised <?PHP echo $cursym.$new_value2; ?> So Far" border="0">&nbsp;</p>
<!-- End Display a donation meter image for donations with id/reason 2 -->


<!-- Using the codes above you can display one or more donation meters on your page for different reason ids, so ay you wanted to rais donations for site maintenance and upgrades etc and you wanted to have one raising donations for some event you were planning you could have both on the same page displaying the different total donations, also you would only need one donation button with the option of which reason they were donating, if you dont want multiple donation reasons or you want to display total donations overall thats possible aswell using the first code example above. -->