<?PHP
#########################################################################################
#          File: ipn.php                                                                #
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



/*********************************DO NOT EDIT BELOW THIS LINE.**************************************/

//Database login variables
include ("dbsettings.php");
error_reporting(E_ALL);
ini_set('display_errors', true);

//Connect to the database.
$loginParams = mysql_connect("$dbhost", "$dbusername", "$dbpass");
mysql_select_db("$dbname",$loginParams);

$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

// assign posted variables to local variables
$invoice = $_POST['invoice'];
$receiver_email = $_POST['receiver_email'];
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$quantity = $_POST['quantity'];
$payment_status = $_POST['payment_status'];
$pending_reason = $_POST['pending_reason'];
$payment_date = $_POST['payment_date'];
$payment_gross = $_POST['mc_gross'];
$payment_fee = $_POST['payment_fee'];
$trans_id = $_POST['txn_id'];
$txn_type = $_POST['txn_type'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$to = $_POST['payer_email'];
$payer_status = $_POST['payer_status'];
$payment_type = $_POST['payment_type'];
$notify_version = $_POST['notify_version'];
$verify_sign = $_POST['verify_sign'];
$payer_email = $_POST['payer_email'];
$ip =  $_SERVER['REMOTE_ADDR'];
$on1 = $_POST['option_name1'];
$os1 = $_POST['option_selection1'];
$on2 = $_POST['option_name2'];
$os2 = $_POST['option_selection2'];

if (!$fp) {
		// HTTP ERROR.
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp))
	{
		$res = fgets ($fp, 1024);
		//$log = fopen("ipn.log", "a");
		//Check to see if the txn_type is subscr_signup. If it is, start processing the subscription.
		if (strcmp ($payment_status, "Completed") == 0)
		{	
			//Check in the authusers table and see if this subscription has already been posted.
			//If it is, exit this portion of the script. Otherwise continue processing the subscription.
			$query = "Select trans_id from donations WHERE trans_id ='$trans_id'";
			$result = mysql_query($query);
			//Scripting needed here to check if already in database with subscription ID to prevent duplicate ipn calls filling database with duplicate records

			define('IN_PHPBB', true);
			$phpbb_root_path = '../';
			$phpEx = substr(strrchr(__FILE__, '.'), 1);
			include($phpbb_root_path . 'common.' . $phpEx);
			include_once($phpbb_root_path . 'functions_user.' . $phpEx);

			$sql = "select user_id from gateway_users where user_email = \"".$db->sql_escape($to)."\"";
			$result = $db->sql_query($sql);
			$user_id = (int) $db->sql_fetchfield('user_id');
			$user->session_begin();

			$auth->acl($user->data);
			$user->setup();

			if (function_exists('group_user_add')){
				group_user_add(2634, $user_id, false, false, true);
			} else {
				// mail("admin@roleplaygateway.com", "RPG: Donation Error", "Got error: $e", "From: donations@roleplaygateway.com");
			}

			//Create an email to send to the buyer. 
			$message = "Dear $first_name $last_name,\n
			Thank You For Your Donation To $CompanyName - $websiteURL. \n\n
			It's very much appreciated.";

			//Send the e-mail to the buyer.
			// mail($to, "Donation For " . $item_name . " Received", $message, "From: " . $CompanyEmail . "");

			$day = date("d");
			$month = date("m");
			$year = date("Y");
			$time = time();
			//Update users User Level.				
			$qry = "INSERT into donations 
			(ip, txn_id, name, donation, dnuid, day, month, year, time, user_id)
			VALUES
			(\"$ip\", \"$trans_id\", \"$os2\", \"$payment_gross\", \"$os1\", \"$day\", \"$month\", \"$year\", \"$time\", \"$user_id\")";
			$result = mysql_query($qry);
			exit;
		}
		
		//Check to see if the txn_type is subscr_failed. If it is, record faild subscription attempt then exit this portion of the script. Otherwise continue processing the payment.
		else if (strcmp ($payment_status, "Pending") == 0)
		{
			exit;
		}
		//Check to see if the txn_type is subscr_payment, if it is record payment details to database then exit this portion of the script. Otherwise continue processing the payment.
		else if (strcmp ($payment_status, "Failed") == 0)
		{
			exit;
		}
		//Check to see if the txn_type is subscr_eot, if it is remove user and exit this portion of the script. Otherwise continue processing the subscription.
		else if (strcmp ($payment_status, "Refunded") == 0) {
			//Check in the authusers table and see if this subscription has already been eot.
			//If it is, exit this portion of the script. Otherwise continue processing the eot.
			//$query = "Select paid from partybookings WHERE email ='" . $payer_email . "'";
			//$result = mysql_query($query);

			//Create an email to send to the buyer.
			//	$message = "Dear $first_name $last_name,\n
			//Your RSVP To Our Party, At $CompanyName Has Been Cancelled. \n\n
			//Your Payment Has Been Refunded.";

			//Send the e-mail to the buyer.
			//	mail($to, "RSVP For Our Party Refunded", $message, "From: " . $receiver_email . "");


			//$qry = "UPDATE partybookings SET paid = 'Refunded' WHERE email ='" . $payer_email . "'";
			//	$result = mysql_query($qry);
			exit;
		} else if (strcmp ($res, "INVALID") == 0) {
			//Send an e-mail to the webmaster explaining that someone has possibly tried to hack this script.
			echo "Sorry, you are not authorized to access this script! Your IP Address: $ip , is being E-Mailed to $CompanyName for investigation. \n\n If we find that you have been repeatidly attempting to access this script we will be forced to notify your ISP of this activity";
			$message .= "FYI - There has been an attempted hack from someone trying to access the PayPal IPN script directly. Their IP address is: $ip";
			mail($CompanyEmail, "APC PayPal IPN Hack Attempt", $message, "From: " . $CompanyEmail . "");
		}

		@fclose ($fp);
	}
}
?>
