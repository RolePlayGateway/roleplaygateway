<?PHP
#########################################################################################
#          File: dbsettings.php                                                         #
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

//Modify all the below settings to match your mysql database settings.

$dbhost = "127.0.0.1"; //usually localhost or mysql check with your hosting documentation.
$dbusername = "admin"; //Your database login username.
$dbpass = "sky22midnight"; //Your database password.
$dbname = "db_gateway"; //The name of your database.

require('/var/www/html/config.php');
$dbusername = $dbuser;
$dbpass = $dbpasswd;


$cursym = "$"; // Modify to the symbol of the currency your using with paypal.
$goal = "500"; //Modify this value with numbers only to be the goal of your meter such as 1000 or 20000 or 500 etc
$dateformat = "D jS M Y h:i:s a"; // Modify to the formatting of the date to be displayed next to the donations when displaying the last x number of donations see php date command for instructions on what to use here or leave it as it is.
$zone=3600*-5; //Time zone offset from GM Time, 3600*-5 would be Eastern Time 3500*0 would be GM Time etc etc just change the number after the * to be the time offset from GM Time +1 +2 +3 -1 -2 -3 etc
$lastxdonations = "15"; //Set to how many donations to display maximum when displaying the last x donations received.
//Create script variables Modify These.
$CompanyName = "RolePlayGateway";
$websiteURL = "http://www.roleplaygateway.com";
$CompanyEmail = "admin@roleplaygateway.com";      // Make sure this is the same as you paypal email address.
?>
