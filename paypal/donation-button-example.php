<?PHP
#########################################################################################
#          File: donations-button-example.php                                           #
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

				  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <div align="center">
				  <table width="58%" border="1">
  <tr>
    <td width="40%"><div align="right">Donation Amount: </div></td>
    <td width="60%"><div align="left">
      <input type="hidden" name="cmd" value="_xclick"><!-- Do Not Modify Or Will Not Work Propperly-->
      <input type="hidden" name="business" value="PayPal@DonationBooster.info"><!-- Your payPal Email Address-->
      <input type="hidden" name="item_name" value="Sample Donation"><!-- Your donation name-->
      <input type="hidden" name="item_number" value="SD001"><!-- Donation item number-->
      <input type="hidden" name="no_shipping" value="1"><!-- st to 1 to not collect shipping address, set to 0 to collect it-->
	  <input type="hidden" name="no_note" value="1">
      <input type="hidden" name="cn" value="Comments"><!-- Do Not Modify Or Will Not Work Propperly-->
      <p><b>$</b><input name="amount" type="text" value="" size="6" maxlength="5"></p>
      <input type="hidden" name="currency_code" value="USD"><!-- modify to your paypal currency-->
      <input type="hidden" name="lc" value="US">
	  <input type="hidden" name="notify_url" value="http://www.DonationBooster.info/donations/ipn.php"> <!-- Location to the ipn.php file included with this script, change to your site or script will not function-->
      <input type="hidden" name="return" value="http://www.DonationBooster.info/demo-and-samples.php"><!-- Location to your thank you page change this to where the user is redirected after successful donation-->
      <input type="hidden" name="cancel_return" value="http://www.DonationBooster.info/demo-and-samples.php"><!-- Location to your cancelled page change this to where the user is redirected after cancelled donation-->
      <input type="hidden" name="tax" value="0"><!-- Do Not Modify Or Will Not Work Propperly-->
  	  <input type="hidden" name="pal" value="5NK4VSJNG9D22"><!-- Do Not Modify Or Will Not Work Propperly-->
      <input type="hidden" name="mbr" value="5NK4VSJNG9D22"><!-- Do Not Modify Or Will Not Work Propperly-->
      <input type="hidden" name="bn" value="PP-BuyNowBF"><!-- Do Not Modify Or Will Not Work Propperly-->
</div></td>
  </tr>
  <tr>
    <td width="40%"><div align="right">Donation Use: </div></td>
    <td width="60%"><div align="left"><p>
    <input type="hidden" name="on0" value="Donation Reason">
    <!-- The value for each option below is a number that is unique to that donation reason such as 1,2,3,4 or 5 etc for displaying unique donation amounts for different reasons etc -->
    <select name="os0">
    <option value"1">Donation Reason 1</option>
    <option value"2">Donation Reason 2</option>
    </select></p></div></td>
  </tr>
  <tr>
    <td width="40%"><div align="right">Display Name: </div></td>
    <td width="60%"><div align="left"><p><input type="hidden" name="on1" value="Display Name"><input type="text" name="os1" value=""></p></div></td>
  </tr>
  <tr>
    <td colspan="2"><div align="center">
      <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Make donations with PayPal - it's fast, free and secure!"><!-- modify the yourbutton.gif to the location of your submit button-->
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
      </div></td>
  </tr>
</table>
                    </div>
                  </form>
</body>
</html>