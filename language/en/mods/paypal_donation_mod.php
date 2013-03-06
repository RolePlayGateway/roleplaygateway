<?php
/**
 *
 * paypal_donation_mod [English]
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package language
 * @version $Id: paypal_donation_mod.php 8 2008-04-08 19:30:42Z Highway of Life $
 * @copyright (c) 2008 Star Trek Guide Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	// Avoid hacking attempts
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_DONATION_MOD_SETTINGS'		=> 'Donation MOD Settings',
	'ACP_DONATION_MOD_SETTINGS_EXPLAIN'	=> 'Manage Donation MOD Settings',

	'CONVERT_PERCENTAGE'			=> 'Currency Conversion Percentage',
	'CONVERT_PERCENTAGE_EXPLAIN'	=> 'Enter the Currency Conversion Percentage for converting currencies',
	'COUNTRY'						=> 'Country',
	'COUNTRY_EXPLAIN'				=> '',
	'CURRENCY_NOT_RECOGNISED'		=> 'The Currency code: %s is not recognised',

	'DEFAULT_COUNTRY'				=> 'Default Country',
	'DEFAULT_CURRENCY'				=> 'Default Currency',
	'DONATE_AMOUNT'					=> 'Amount to donate',
	'DONATE_AMOUNT_EXPLAIN' 		=> '',
	'DONATION_CANCELED'				=> 'Donation Transaction Canceled',
	'DONATE_EXPLAIN'				=> 'Make a one-time donation using PayPal',
	'DONATE_MINIMUM'				=> 'Minimum Donation Amount',
	'DONATE_MINIMUM_EXPLAIN'		=> 'Enter Minimum Donation Amount in the default currency',
	'DONATE_TO_SITENAME'			=> 'Donate to %s',
	'DONATE_TO_SITENAME_EXPLAIN'	=> 'Support %1$s by making a donation using PayPal.<br />
	Your donations ensure that we can pay our hosting bills and are able to keep this site online for the benefit of our users.<br />
	When you support %1$s with a minimum of %2$s, you will automatically be added to %3$s group and will receive all current and future rewards. Ensure you are logged in when you donate to receive this benefit.<br /><br />If you are able to donate more than %2$s it will keep us motiviated to produce Rewards for our Supporters and helps us that much more. :)',
	'DONATION_MESSAGE'				=> 'Donation Message (Memo)',
	'DONATION_RECEIVED_VERIFIED'	=> 'Donation Received (Verified) from %s',
	'DONATION_RECEIVED_UNVERIFIED'	=> 'Donation Received (Not Verified) from %s',
	'DONATION_RECEIVED_MSG_VERIFIED'	=> 'Hello,
	You’ve received a donation from %2$s ( %1$s ) in the amount of %3$s.
	This transaction has already been verified and no action is required.',
	'DONATION_RECEIVED_MSG_UNVERIFIED'	=> 'Hello,
	You’ve received a donation from %2$s ( %1$s ) in the amount of %3$s',

	'ERROR_LOGGING'					=> 'Log Errors',
	'ERROR_LOGGING_EXPLAIN'			=> 'Log Errors and Data into /store/transaction.log',
	'ERROR_NO_EXCHANGE_DATA'		=> 'Error: No Exchange Data',

	'FOUNDER_MANAGE'				=> 'Founder Manage Only',
	'FOUNDER_MANAGE_EXPLAIN'		=> 'If this option is enabled, only founders can adjust the settings of this MOD',

	'HTTP_ERROR'					=> 'HTTP Error attempting to connect',

	'INVALID_TRANSACTION_RECORD'	=> 'Invalid Transaction Record: No Transaction ID found',

	'MOD_DISABLED'					=> 'This MOD is currently disabled, please contact an Administrator',
	'MOD_INSTALLED_SUCCESSFULLY'	=> 'MOD Successfully installed version: %s',
	'MOD_UPDATED_SUCCESSFULLY'		=> 'MOD Successfully updated to %s',

	'NO_SUBJECT'					=> 'No Subject specified',
	'NO_TRANSACTION_ID'				=> 'No Transaction ID specified',

	'PAYPAL_ADDRESS'				=> 'PayPal Address',
	'PAYPAL_DEBUG'					=> 'Turn on Debugging',
	'PAYPAL_DEBUG_EXPLAIN'			=> 'Founders always use Sandbox and every detail of transaction is logged, turn off for normal usage',
	'PAYPAL_STYLE'					=> 'PayPal Style',
	'PAYPAL_STYLE_EXPLAIN'			=> 'Enter the name of the PayPal Style if any',
	'PHP5_OR_ABOVE_REQUIRED'		=> 'PHP 5.0 or above is required to use this MOD. PHP4 is end-of-life as of January 1, 2008',

	'SANDBOX_ADDRESS'				=> 'PayPal Sandbox Address',
	'SANDBOX_TESTING'				=> 'Sandbox Testing',
	'SEND_CONFIRM_PM'				=> 'Send Confirmation PM',
	'SEND_CONFIRM_PM_EXPLAIN'		=> 'Select No to have Confirmation E-mails sent instead',
	'SUPPORTER_REWARDS'				=> 'Supporter Rewards',
	'SUPPORTERS_GROUP'				=> 'Supporters Group',
	'SUPPORTERS_GROUP_EXPLAIN'		=> 'Select the group the user will automatically be added to',

	'THANKS_DONATION'				=> 'Thanks for your donation',
	'TRANSACTION_ALREADY_CONFIRMED'	=> 'This Transaction has already been confirmed',
	'TRANSACTION_NOT_VALID'			=> 'This Transaction is not valid or did not originate from this site',
	'TRANSACTION_NOT_VERIFIED'		=> 'Transaction not verified. Click the following link to verify the transaction: %s',
	'TRANSACTION_VERIFIED'			=> 'Transaction Verified',
	'TRANSACTION_VERIFICATION_FAILED'	=> 'Transaction Verification Failed',

	'UPDATE_MOD_REQUIRED'			=> 'You must update this MOD to proceed. %sClick here to Update the MOD%s',

	'currency_code'			=> array(
		'USD'	=> 'U.S. Dollars',
		'AUD'	=> 'Australian Dollars',
		'CAD'	=> 'Canadian Dollars',
		'CZK'	=> 'Czech Koruna',
		'DKK'	=> 'Danish Kroner',
		'EUR'	=> 'Euros',
		'HKD'	=> 'Hong Kong Dollars',
		'HUF'	=> 'Hungarian Forint',
		'NZD'	=> 'New Zealand Dollars',
		'NOK'	=> 'Norwegian Kroner',
		'PLN'	=> 'Polish Zlotych',
		'GBP'	=> 'Pounds Sterling',
		'SGD'	=> 'Singapore Dollars',
		'SEK'	=> 'Swedish Kronor',
		'CHF'	=> 'Swiss Francs',
		'JPY'	=> 'Yen',
	),

	'country_options'		=> array(
		'US'	=> 'United States',
		'AL'	=> 'Albania',
		'DZ'	=> 'Algeria',
		'AD'	=> 'Andorra',
		'AO'	=> 'Angola',
		'AI'	=> 'Anguilla',
		'AG'	=> 'Antigua and Barbuda',
		'AR'	=> 'Argentina',
		'AM'	=> 'Armenia',
		'AW'	=> 'Aruba',
		'AU'	=> 'Australia',
		'AT'	=> 'Austria',
		'AZ'	=> 'Azerbaijan Republic',
		'BS'	=> 'Bahamas',
		'BH'	=> 'Bahrain',
		'BB'	=> 'Barbados',
		'BE'	=> 'Belgium',
		'BZ'	=> 'Belize',
		'BJ'	=> 'Benin',
		'BM'	=> 'Bermuda',
		'BT'	=> 'Bhutan',
		'BO'	=> 'Bolivia',
		'BA'	=> 'Bosnia and Herzegovina',
		'BW'	=> 'Botswana',
		'BR'	=> 'Brazil',
		'VG'	=> 'British Virgin Islands',
		'BN'	=> 'Brunei',
		'BG'	=> 'Bulgaria',
		'BF'	=> 'Burkina Faso',
		'BI'	=> 'Burundi',
		'KH'	=> 'Cambodia',
		'CA'	=> 'Canada',
		'CV'	=> 'Cape Verde',
		'KY'	=> 'Cayman Islands',
		'TD'	=> 'Chad',
		'CL'	=> 'Chile',
		'C2'	=> 'China',
		'CO'	=> 'Colombia',
		'KM'	=> 'Comoros',
		'CK'	=> 'Cook Islands',
		'CR'	=> 'Costa Rica',
		'HR'	=> 'Croatia',
		'CY'	=> 'Cyprus',
		'CZ'	=> 'Czech Republic',
		'CD'	=> 'Democratic Republic of the Congo',
		'DK'	=> 'Denmark',
		'DJ'	=> 'Djibouti',
		'DM'	=> 'Dominica',
		'DO'	=> 'Dominican Republic',
		'EC'	=> 'Ecuador',
		'SV'	=> 'El Salvador',
		'ER'	=> 'Eritrea',
		'EE'	=> 'Estonia',
		'ET'	=> 'Ethiopia',
		'FK'	=> 'Falkland Islands',
		'FO'	=> 'Faroe Islands',
		'FM'	=> 'Federated States of Micronesia',
		'FJ'	=> 'Fiji',
		'FI'	=> 'Finland',
		'FR'	=> 'France',
		'GF'	=> 'French Guiana',
		'PF'	=> 'French Polynesia',
		'GA'	=> 'Gabon Republic',
		'GM'	=> 'Gambia',
		'DE'	=> 'Germany',
		'GI'	=> 'Gibraltar',
		'GR'	=> 'Greece',
		'GL'	=> 'Greenland',
		'GD'	=> 'Grenada',
		'GP'	=> 'Guadeloupe',
		'GT'	=> 'Guatemala',
		'GN'	=> 'Guinea',
		'GW'	=> 'Guinea Bissau',
		'GY'	=> 'Guyana',
		'HN'	=> 'Honduras',
		'HK'	=> 'Hong Kong',
		'HU'	=> 'Hungary',
		'IS'	=> 'Iceland',
		'IN'	=> 'India',
		'ID'	=> 'Indonesia',
		'IE'	=> 'Ireland',
		'IL'	=> 'Israel',
		'IT'	=> 'Italy',
		'JM'	=> 'Jamaica',
		'JP'	=> 'Japan',
		'JO'	=> 'Jordan',
		'KZ'	=> 'Kazakhstan',
		'KE'	=> 'Kenya',
		'KI'	=> 'Kiribati',
		'KW'	=> 'Kuwait',
		'KG'	=> 'Kyrgyzstan',
		'LA'	=> 'Laos',
		'LV'	=> 'Latvia',
		'LS'	=> 'Lesotho',
		'LI'	=> 'Liechtenstein',
		'LT'	=> 'Lithuania',
		'LU'	=> 'Luxembourg',
		'MG'	=> 'Madagascar',
		'MW'	=> 'Malawi',
		'MY'	=> 'Malaysia',
		'MV'	=> 'Maldives',
		'ML'	=> 'Mali',
		'MT'	=> 'Malta',
		'MH'	=> 'Marshall Islands',
		'MQ'	=> 'Martinique',
		'MR'	=> 'Mauritania',
		'MU'	=> 'Mauritius',
		'YT'	=> 'Mayotte',
		'MX'	=> 'Mexico',
		'MN'	=> 'Mongolia',
		'MS'	=> 'Montserrat',
		'MA'	=> 'Morocco',
		'MZ'	=> 'Mozambique',
		'NA'	=> 'Namibia',
		'NR'	=> 'Nauru',
		'NP'	=> 'Nepal',
		'NL'	=> 'Netherlands',
		'AN'	=> 'Netherlands Antilles',
		'NC'	=> 'New Caledonia',
		'NZ'	=> 'New Zealand',
		'NI'	=> 'Nicaragua',
		'NE'	=> 'Niger',
		'NU'	=> 'Niue',
		'NF'	=> 'Norfolk Island',
		'NO'	=> 'Norway',
		'OM'	=> 'Oman',
		'PW'	=> 'Palau',
		'PA'	=> 'Panama',
		'PG'	=> 'Papua New Guinea',
		'PE'	=> 'Peru',
		'PH'	=> 'Philippines',
		'PN'	=> 'Pitcairn Islands',
		'PL'	=> 'Poland',
		'PT'	=> 'Portugal',
		'QA'	=> 'Qatar',
		'CG'	=> 'Republic of the Congo',
		'RE'	=> 'Reunion',
		'RO'	=> 'Romania',
		'RU'	=> 'Russia',
		'RW'	=> 'Rwanda',
		'VC'	=> 'Saint Vincent and the Grenadines',
		'WS'	=> 'Samoa',
		'SM'	=> 'San Marino',
		'ST'	=> 'S„o TomÈ and PrÌncipe',
		'SA'	=> 'Saudi Arabia',
		'SN'	=> 'Senegal',
		'SC'	=> 'Seychelles',
		'SL'	=> 'Sierra Leone',
		'SG'	=> 'Singapore',
		'SK'	=> 'Slovakia',
		'SI'	=> 'Slovenia',
		'SB'	=> 'Solomon Islands',
		'SO'	=> 'Somalia',
		'ZA'	=> 'South Africa',
		'KR'	=> 'South Korea',
		'ES'	=> 'Spain',
		'LK'	=> 'Sri Lanka',
		'SH'	=> 'St. Helena',
		'KN'	=> 'St. Kitts and Nevis',
		'LC'	=> 'St. Lucia',
		'PM'	=> 'St. Pierre and Miquelon',
		'SR'	=> 'Suriname',
		'SJ'	=> 'Svalbard and Jan Mayen Islands',
		'SZ'	=> 'Swaziland',
		'SE'	=> 'Sweden',
		'CH'	=> 'Switzerland',
		'TW'	=> 'Taiwan',
		'TJ'	=> 'Tajikistan',
		'TZ'	=> 'Tanzania',
		'TH'	=> 'Thailand',
		'TG'	=> 'Togo',
		'TO'	=> 'Tonga',
		'TT'	=> 'Trinidad and Tobago',
		'TN'	=> 'Tunisia',
		'TR'	=> 'Turkey',
		'TM'	=> 'Turkmenistan',
		'TC'	=> 'Turks and Caicos Islands',
		'TV'	=> 'Tuvalu',
		'UG'	=> 'Uganda',
		'UA'	=> 'Ukraine',
		'AE'	=> 'United Arab Emirates',
		'GB'	=> 'United Kingdom',
		'UY'	=> 'Uruguay',
		'VU'	=> 'Vanuatu',
		'VA'	=> 'Vatican City State',
		'VE'	=> 'Venezuela',
		'VN'	=> 'Vietnam',
		'WF'	=> 'Wallis and Futuna Islands',
		'YE'	=> 'Yemen',
		'ZM'	=> 'Zambia',
	),
));

?>