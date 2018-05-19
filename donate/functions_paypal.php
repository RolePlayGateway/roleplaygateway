<?php
/**
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package donate
 * @version $Id: functions_paypal.php 18 2008-10-16 06:25:09Z Highway of Life $
 * @copyright (c) 2008 Star Trek Guide Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * Special Thanks to the following individuals for their inspiration:
 * 	Exreaction (Nathan Guse) http://lithiumstudios.com
 * 	Micah Carrick (email@micahcarrick.com) http://www.micahcarrick.com
 * 	Gary White
 *
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB') || !isset($table_prefix))
{
	// Avoid hacking attempts for IN_PHPBB,
	// if IN_PHPBB, $table_prefix would always be set, so that should never be not set if IN_PHPBB
	// but you never know...
	exit;
}

define('DONATION_PERKS_TABLE',	$table_prefix . 'donation_perks');
define('DONATION_DATA_TABLE',	$table_prefix . 'donation_data');
define('ASCII_RANGE', '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('PAYPAL_DEBUG', (isset($config['paypal_debug']) && $config['paypal_debug']) ? true : false);

class currency_exchange
{
	private $rates = array();

	private $exchange_url = 'http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml';

	/**
	 * Log error messages
	 *
	 * @param string $message
	 */
	public function log_error($message, $exit = false, $error_type = E_USER_NOTICE, $args = array())
	{
		global $phpbb_root_path, $config;

		$error_timestamp = date('d-M-Y H:i:s Z');

		$backtrace = '';
		if (PAYPAL_DEBUG)
		{
			$backtrace = get_backtrace();
			$backtrace = html_entity_decode(strip_tags(str_replace(array('<br />', "\n\n"), "\n", $backtrace)));
		}

		$message = str_replace('<br />', '; ', $message);

		if (sizeof($args))
		{
			$message .= '[args] ';
			foreach ($args as $key => $value)
			{
				$value = urlencode($value);
				$message .= "{$key} = $value; ";
			}
		}

		if ($config['paypal_logging'] || PAYPAL_DEBUG)
		{
			error_log("[$error_timestamp] $message $backtrace", 3, $phpbb_root_path . 'store/transaction.log');
		}

		if ($exit)
		{
			trigger_error($message, $error_type);
		}
	}

	/**
	 * Obtain a cached list of currency exchange rates
	 */
	public function obtain_exchange_data()
	{
		global $cache, $user;

		if (($exchange_data = $cache->get('_exchange_rates')) === false)
		{
			if (!function_exists('curl_init'))
			{
				$errno = 0;
				$errstr = '';

				if (!function_exists('get_remote_file'))
				{
					global $phpbb_root_path, $phpEx;

					include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
				}

				$parse_url = parse_url($this->exchange_url);
				$pathinfo = pathinfo($parse_url['path']);

				$port = 80;
				if ($parse_url['scheme'] === 'https')
				{
					$port = 443;
				}

				$exchange_data = get_remote_file($parse_url['host'], $pathinfo['dirname'], $pathinfo['basename'], $errstr, $errno, $port, 30);
			}
			else
			{
				$exchange_data = $this->get_remote_file($this->exchange_url);
			}

			$cache->put('_exchange_rates', $exchange_data, 86400);
		}

		if (!$exchange_data)
		{
			// hopefully this never happens... but I need to think about a solution if it does.
			$this->log_error($user->lang['ERROR_NO_EXCHANGE_DATA'], true, E_USER_ERROR);
		}

		if (!function_exists('simplexml_load_string'))
		{
			$this->log_error($user->lang['PHP5_OR_ABOVE_REQUIRED'], true, E_USER_ERROR);
		}

		$this->xml = simplexml_load_string($exchange_data);
		$this->parse();
	}

	/**
	 * If cURL is enabled, we pull the remote data from there.
	 *
	 * @param unknown_type $url
	 * @return unknown
	 */
	private function get_remote_file($url)
	{
		$curl_handle = curl_init();

		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);

		$result = curl_exec($curl_handle);

		curl_close($curl_handle);

		return $result;
	}

	/**
	 * Convert one currency to another currency.
	 *
	 * @param string $from_currency
	 * @param string $to_currency
	 * @param float $amount
	 * @return converted value
	 */
	public function convert_currency($from_currency, $to_currency, $amount = 1)
	{
		global $config;

		if (array_key_exists($from_currency, $this->rates) && array_key_exists($to_currency, $this->rates))
		{
			$rate = ($amount * ($this->rates[$to_currency] / $this->rates[$from_currency]));
			return ((1 + $config['paypal_convert_percentage']) * $rate);
		}
		else
		{
			global $user;

			$error = $debug = array();

			if (!array_key_exists($from_currency, $this->rates))
			{
				$error[] = sprintf($user->lang['CURRENCY_NOT_RECOGNISED'], $from_currency);
			}

			if (!array_key_exists($to_currency, $this->rates))
			{
				$error[] = sprintf($user->lang['CURRENCY_NOT_RECOGNISED'], $to_currency);
			}

			if (PAYPAL_DEBUG)
			{
				$debug = $this->rates;
			}

			$this->log_error(implode('<br />', $error), true, E_USER_NOTICE, $debug);
		}
	}

	/**
	 * Parse the XML into an array of data
	 */
	private function parse()
	{
		if ($this->xml)
		{
			$this->rates['EUR'] = 1.00;

			foreach ($this->xml->Cube->Cube->Cube as $row)
			{
				$attributes = $row->attributes();
				$this->rates[(string) $attributes['currency']] = (float) $attributes['rate'];
			}
		}
	}
}

/**
 * paypal class, this function must work with both PHP4 and PHP5, so var method is used.
 * Configuration is done through the ACP Paypal Donation MOD Module, nothing to change within this file.
 */
class paypal_class extends currency_exchange
{
	// Hold the data for field information to send to paypal
	private $fields = array();

	// Data from transaction
	private $data = array();

	// Sender details
	private $sender_data = array();

	// PayPal URL
	public $u_paypal = '';

	// Transaction verified (bool)
	public $verified = false;

	public $page;

	public $hash_str;

	public $currency = array();

	/**
	 * __construct, set some initial values
	 */
	public function __construct()
	{
		global $config, $db;

		// set the PayPal URL depending on if the board is using the PayPal sandbox (used for debugging and testing)
		$this->u_paypal = ($config['paypal_sandbox'] || PAYPAL_DEBUG) ? 'http://www.sandbox.paypal.com/cgi-bin/webscr' : 'http://www.paypal.com/cgi-bin/webscr';

		$this->obtain_exchange_data();
		$this->currency = $this->currency_data();
	}

	/**
	 * Setup the PayPal fields
	 */
	public function paypal_setup()
	{
		global $template;

		// Assigne the variables to the template (MVC)
		$template->assign_vars(array(
			'S_DONATE_ACTION'		=> $this->u_paypal,
			'S_HIDDEN_FIELDS'		=> build_hidden_fields($this->fields),
		));
	}

	/**
	 * List the currency minimum in a list
	 *
	 * @return option values
	 */
	public function minimum_currency_list()
	{
		global $user, $config;

		$s_currency_list = '';

		foreach($user->lang['currency_code'] as $key => $value)
		{
			$selected = ($key == $config['paypal_default_currency']) ? ' selected="selected"' : '';

			$amount = ($key == $config['paypal_default_currency']) ? $config['paypal_donate_minimum'] : $this->convert_currency($config['paypal_default_currency'], $key, $config['paypal_donate_minimum']);
			$amount = number_format($amount, 2, $this->currency[$key]['decimal'], ' ');

			$currency_placement = ($this->currency[$key]['prefix']) ? $this->currency[$key]['symbol'] . $amount . ' ' . $key : $amount . $this->currency[$key]['symbol'] . ' ' . $key;
			$s_currency_list .= '<option' . $selected . ">$currency_placement</option>\n";
		}

		return $s_currency_list;
	}

	/**
	 * Send the received data back to PayPal to validate the authenticity of the transaction.
	 * set $this->data['confirmed'] = true; if PayPal has verified the transaction.
	 */
	public function validate_data($data = array())
	{
		global $user;

		$values = array();
		$errstr = $msg = '';
		$errno = 0;

		if (!sizeof($data))
		{
			// Grab the post data from and set in an array to be used in the URI to PayPal
			foreach ($_POST as $key => $value)
			{
				$encoded = urlencode(stripslashes($value));
				$values[] = $key . '=' . $encoded;

				// Assign the values to the $user->data array
				$this->data[$key] = $value;
			}
		}
		else
		{
			foreach ($data as $key => $value)
			{
				$encoded = urlencode(stripslashes($value));
				$values[] = $key . '=' . $encoded;

				$this->data[$key] = $value;
			}
		}

		// Add the cmd=_notify-validate for PayPal
		$values[] = 'cmd=_notify-validate';

		// implode the array into a string URI
		$params = implode('&', $values);

		// post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($params) . "\r\n\r\n";

		$parse_url = parse_url($this->u_paypal);

		$port = 80;
		if ($parse_url['scheme'] === 'https')
		{
			$port = 443;
		}

		$fp = fsockopen($parse_url['host'], $port, $errno, $errstr, 30);

		if (!$fp)
		{
			$msg = $errno . ' (' . $errstr . ')';
			$this->log_error($user->lang['HTTP_ERROR'] . " $errno ($errstr)");
			$this->send_message(true, "$errno ($errstr)<br />\n", $user->lang['HTTP_ERROR']);
		}
		else
		{
			// Send the data to PayPal
			fputs($fp, $header . $params);

			// Loop through the response
			while (!feof($fp))
			{
				// if the result is not verified...
				if (!$this->verified)
				{
					$line = fgets($fp, 1024);
					$msg .= $line . '<br />';

					// if the line is verified, set verified to true and break out of the loop
					if (strcmp($line, 'VERIFIED') == 0)
					{
						$this->verified = true;
						break;
					}
					else if (strcmp($line, 'INVALID') == 0)
					{
						// if the line is invalid, set verified to false and break out of the loop
						$this->verified = false;
						break;
					}
				}
			}
			fclose($fp);

			return $msg;
		}
	}

	/**
	 * Add a key=>value pair to the fields array, this will be sent to PayPal
	 *
	 * Usage:
	 * <code>
	 * $paypal->add_fields(array('field_name' => 'value'));
	 * </code>
	 * Unlimited array to add fields, single or multiple.
	 *
	 * @param array $fields
	 */
	public function add_fields($fields)
	{
		if (is_array($fields) && sizeof($fields))
		{
			foreach ($fields as $field => $value)
			{
				$this->fields[$field] = $value;
			}
		}
	}

	/**
	 * Post Data back to PayPal to validate
	 */
	public function validate_transaction()
	{
		global $user, $auth, $db, $config, $action;

		$data = array();
		$this->data_list();
		$validate = ($action == 'validate') ? true : false;

		// we ensure that the txn_id (transaction ID) contains only ASCII chars...
		$pos = strspn($this->data['txn_id'], ASCII_RANGE);
		$len = strlen($this->data['txn_id']);

		if ($pos != $len)
		{
			return;
		}

		$decode_ary = array('payer_email', 'payment_date', 'business');
		foreach ($decode_ary as $key)
		{
			$this->data[$key] = urldecode($this->data[$key]);
		}

		if ($validate)
		{
			// If we are trying to confirm a previous transaction -- the first attempt to confirm
			// the transaction failed and the administrator is trying to auto confirm it again
			// I do not believe there is any way this could be abused by non-founder administrators.
			// ensure the user is logged in
			if (!$user->data['is_registered'])
			{
				if ($user->data['is_bot'])
				{
					global $phpbb_root_path, $phpEx;

					// if the user is a bot, we do not proceed, send the bot back to home page
					redirect(append_sid($phpbb_root_path . 'index.' . $phpEx));
				}

				// force the user to login before we continue
				// common lang file
				login_box('', 'LOGIN');
			}

			// if the user is not an administrator, we cannot continue
			if (!$auth->acl_get('a_'))
			{
				// common lang file
				trigger_error('NOT_AUTHORISED');
			}

			// If there is no transaction ID, we cannot continue
			if (!$this->data['txn_id'])
			{
				trigger_error('NO_TRANSACTION_ID');
			}

			// select all the records in the DB for this transaction
			$sql = 'SELECT *
					FROM ' . DONATION_DATA_TABLE . "
					WHERE txn_id = '" . $this->data['txn_id'] . "'";
			$result = $db->sql_query($sql);
			$data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			// there is no transaction ID or no data was returned
			if (!sizeof($data) || !$data['txn_id'])
			{
				$this->log_error($user->lang['INVALID_TRANSACTION_RECORD'], true);
			}

			// if data['confirmed'] is set to true, this record has already been verified and confirmed.
			if ($data['confirmed'] == true)
			{
				$this->log_error($user->lang['TRANSACTION_ALREADY_CONFIRMED'], true, E_USER_NOTICE, array($data['txn_id']));
			}

			$msg = $this->validate_data($data);
			$this->data['confirmed'] = ($this->verified) ? true : false;
			$this->log_to_db(true);

			if ($this->verified)
			{
				trigger_error('TRANSACTION_VERIFIED');
			}
			else
			{
				trigger_error($user->lang['TRANSACTION_VERIFICATION_FAILED'] . "\n<hr /><br />\n" . $msg, E_USER_ERROR);
			}
		}
		else if (!$this->data['txn_id'])
		{
			$this->log_error($user->lang['INVALID_TRANSACTION_RECORD'], true, E_USER_NOTICE, $this->data);
		}

		$this->validate_data();

		// set confirmed to true/false depending on if the transaction was verified.
		$this->data['confirmed'] = ($this->verified) ? true : false;

		// the item number contains the user_id and the payment time in timestamp format
		list($uid, $this->data['user_id'], $this->data['payment_time']) = explode('_', $this->data['item_number']);

		$anonymous_user = false;

		// if the user_id is not anonymous, get the user information (user id, username)
		if ($this->data['user_id'] != ANONYMOUS)
		{
			$sql = 'SELECT user_id, username
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . (int) $this->data['user_id'];
			$result = $db->sql_query($sql);
			$this->sender_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!sizeof($this->sender_data))
			{
				// no results, therefore the user is anonymous...
				$anonymous_user = true;
			}
		}
		else
		{
			// the user is anonymous by default
			$anonymous_user = true;
		}

		if ($anonymous_user)
		{
			// if the user is anonymous, check thier paypal email address with all known email hashes
			// to determine if the user exists in the database with that email
			$sql = 'SELECT user_id, username
					FROM ' . USERS_TABLE . '
					WHERE user_email_hash = ' . crc32(strtolower($this->data['payer_email'])) . strlen($this->data['payer_email']);
			$result = $db->sql_query($sql);
			$this->sender_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!sizeof($this->sender_data))
			{
				// no results, therefore the user is really a guest
				$this->sender_data = false;
			}
		}

		// If the user is registered, we check to ensure they have donated the minimum amount
		// before being added to the supporters group
		if ($this->sender_data)
		{
			// set the minimum amount to the config minimum amount value
			$minimum_amount = $config['paypal_donate_minimum'];
			if ($this->data['mc_currency'] != $config['paypal_default_currency'])
			{
				// if the payer currency is not the default currency, convert the default currency
				// to the payer currency to determine if they paid the minimum in that currency.
				$minimum_amount = $this->convert_currency($config['paypal_default_currency'], $this->data['mc_currency'], $config['paypal_donate_minimum']);
			}

			if ($this->data['mc_gross'] >= $minimum_amount)
			{
				if (!function_exists('group_user_add'))
				{
					global $phpbb_root_path, $phpEx;

					include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
				}

				// if they meet or exceed the minimum amount, add the user to the supporters group and set as default.
				group_user_add($config['paypal_supporters_group_id'], array($this->sender_data['user_id']), array($this->sender_data['username']), $config['paypal_supporters_group'], true);
			}
		}

		$this->send_message();
		$this->log_to_db();
	}

	/**
	 * Send a message to the Founders informing them of the Donation received.
	 * Echo information based on if the donation is verified or unverified.
	 *
	 * @param bool $send_pm
	 * @param string $message
	 * @param string $subject
	 */
	private function send_message($send_pm = false, $message = '', $subject = '')
	{
		global $user, $config, $db, $phpbb_root_path, $phpEx;

		if (!$subject)
		{
			$l_title = ($this->verified) ? 'DONATION_RECEIVED_VERIFIED' : 'DONATION_RECEIVED_UNVERIFIED';

			$subject = sprintf($user->lang[$l_title], ($this->sender_data) ? $this->sender_data['username'] : $user->lang['GUEST']);
		}

		if (!$message)
		{
			$currency = $this->currency[$this->data['mc_currency']];
			$currency_format = ($currency['prefix']) ? $currency['symbol'] . $this->data['mc_gross'] : $this->data['mc_gross'] . $currency['symbol'];
			$amount = $currency_format . ' ' . $this->data['mc_currency'];

			$message = ($this->verified) ? 'DONATION_RECEIVED_MSG_VERIFIED' : 'DONATION_RECEIVED_MSG_UNVERIFIED';
			$message = sprintf($user->lang[$message], $this->data['payer_email'], ($this->sender_data) ? $this->sender_data['username'] : $user->lang['GUEST'], $amount);

			// if there is a memo, add the memo to the message
			if (!empty($this->data['memo']))
			{
				$message .= "\n\n" . $user->lang['DONATION_MESSAGE'] . ":\n\n" . $db->sql_escape($this->data['memo']);
			}

			// if the transaction is not verified, all the admin to manually verify the transaction.
			if (!$this->verified)
			{
				$message .= "\n\n" . sprintf($user->lang['TRANSACTION_NOT_VERIFIED'], $this->page, 'action=validate&amp;txn_id=' . $this->data['txn_id']);
			}
		}

		// grab user data from all founders.
		$sql = 'SELECT user_id, username, user_email, user_lang, user_notify_type
				FROM ' . USERS_TABLE . '
				WHERE user_type = ' . USER_FOUNDER;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			// alternatively we could use rowset.
			$founder_ary[] = array(
				'user_id'			=> $row['user_id'],
				'username'			=> $row['username'],
				'user_email'		=> $row['user_email'],
				'user_lang'			=> $row['user_lang'],
				'user_notify_type'	=> $row['user_notify_type'],
			);
		}
		$db->sql_freeresult($result);

		// Determine if we are sending a PM or e-mailing the founders instead.
		if ($config['paypal_send_pm'] || $send_pm)
		{
			include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
			include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

			// setup the PM message parser.
			$message_parser = new parse_message();
			$message_parser->message = $message;
			$message_parser->parse(true, true, true, true, true, true, true);

			foreach ($founder_ary as $id)
			{
				$address_list[$id['user_id']] = 'to';
			}

			// setup the PM data...
			$pm_data = array(
				'from_user_id'		=> ($this->sender_data) ? $this->sender_data['user_id'] : $user->data['user_id'],
				'from_username'		=> 'Paypal',
				'address_list'		=> array('u' => $address_list),
				'icon_id'			=> 10,
				'from_user_ip'		=> $user->ip,
				'enable_bbcode'		=> true,
				'enable_smilies'	=> true,
				'enable_urls'		=> true,
				'enable_sig'		=> false,
				'message'			=> $message_parser->message,
				'bbcode_bitfield'	=> $message_parser->bbcode_bitfield,
				'bbcode_uid'		=> $message_parser->bbcode_uid,
			);

			// send the PM to the founders.
			submit_pm('post', $subject, $pm_data, false);
		}
		else
		{
			// setup the e-mail for the founders
			if (!class_exists('messenger'))
			{
				include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
			}

			$messenger = new messenger(false);
			// we may be using one e-mail template, not decided yet...
			$email_tpl = ($this->verified) ? 'paypal_donation' : 'paypal_unverified';

			foreach ($founder_ary as $row)
			{
				// use the specified email language template according tho this users' language settings.
				$messenger->template($email_tpl, $row['user_lang']);

				// set the "reply to" header.
				$messenger->replyto($this->data['payer_email']);

				// set the "to" header.
				$messenger->to($row['user_email'], $row['username']);

				// E-mail subject
				$messenger->subject(htmlspecialchars_decode($subject));

				$user_id = ($this->sender_data) ? $this->sender_data['user_id'] : $user->data['user_id'];
				$username = ($this->sender_data) ? $this->sender_data['username'] : $user->data['username'];

				// set some X-AntiAbuse headers, may not be needed but...
				$messenger->headers('X-AntiAbuse: Board servername - ' . $config['server_name']);
				$messenger->headers('X-AntiAbuse: User_id - ' . $user_id);
				$messenger->headers('X-AntiAbuse: Username - ' . $username);
				$messenger->headers('X-AntiAbuse: User IP - ' . $user->ip);

				// Assign variables for the MVC to be used in the e-mail template
				$messenger->assign_vars(array(
					'TO_USERNAME'	=> $row['username'],
					'MESSAGE'		=> $message,
					'SUBJECT'		=> $subject,
					'AMOUNT'		=> $this->data['mc_gross'],
					'PAYER_EMAIL'	=> $this->data['payer_email'],
					'PAYER_USERNAME'=> ($this->sender_data) ? $this->sender_data['username'] : $this->data['first_name'],
				));

				// now send the e-mail message
				$messenger->send($row['user_notify_type']);
			}
		}
	}

	/**
	 * Send final variables to the template for display.
	 */
	public function display()
	{
		global $user, $template;

		if (!$this->hash_str)
		{
			$this->hash_str = 'Q29weXJpZ2h0IFJlbW92ZWQsIE1PRCBkaXNhYmxlZA==';
			trigger_error($hash_str);
		}

		$string = ((!empty($user->lang['TRANSLATION_INFO'])) ? $user->lang['TRANSLATION_INFO'] . '<br />' : '') . base64_decode($this->hash_str);

		$user->lang['TRANSLATION_INFO'] = $string;

		$template->assign_vars(array(
			'S_CURRENCY_OPTIONS'		=> currency_options(),
			'S_COUNTRY_OPTIONS'			=> country_options(),
			'S_DONATE_ACTION'			=> $this->u_paypal,
		));
	}

	/**
	 * Log the transaction to the database
	 *
	 * @param bool $update -- update an existing transaction or insert a new transaction
	 */
	public function log_to_db($update = false)
	{
		global $db;

		list($uid, $this->data['user_id'], $this->data['payment_time']) = explode('_', $this->data['item_number']);

		// list the data to be thrown into the database
		$sql_ary = array(
			'confirmed'			=> $this->data['confirmed'],
			'user_id'			=> $this->data['user_id'],
			'txn_id'			=> $this->data['txn_id'],
			'txn_type'			=> $this->data['txn_type'],

			'item_name'			=> $this->data['item_name'],
			'item_number'		=> $this->data['item_number'],
			'business'			=> $this->data['business'],

			'payment_status'	=> $this->data['payment_status'],
			'payment_gross'		=> $this->data['mc_gross'],
			'payment_fee'		=> $this->data['payment_fee'],
			'payment_type'		=> $this->data['payment_type'],
			'payment_time'		=> $this->data['payment_time'],
			'mc_currency'		=> $this->data['mc_currency'],
			'payment_date'		=> $this->data['payment_date'],

			'payer_id'			=> $this->data['payer_id'],
			'payer_email'		=> $this->data['payer_email'],
			'payer_status'		=> $this->data['payer_status'],
			'first_name'		=> $this->data['first_name'],
			'last_name'			=> $this->data['last_name'],

			'memo'				=> $this->data['memo'],
		);

		if ($update)
		{
			$sql = 'UPDATE ' . DONATION_DATA_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary) . " WHERE txn_id = '" . $this->data['txn_id'] . "'";
			$db->sql_query($sql);
		}
		else
		{
			// insert the data
			$sql = 'INSERT INTO ' . DONATION_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
		}
	}

	/**
	 * Setup the data list with default values.
	 */
	private function data_list()
	{
		$data_ary = array(
			'txn_id'			=> '',		// Transaction ID
			'txn_type'			=> '',		// Transaction type - Should be: 'send_money'

			'item_name'			=> '',		// $config['sitename']
			'item_number'		=> '',		// 'uid_' . $user->data['user_id'] . '_' . time()
			'business'			=> '',		// $config['board_contact']

			'payment_status'	=> '',		// 'Completed'
			'payment_gross'		=> '',		// Amt recieved (before fees)
			'payment_fee'		=> 0,		// Amt of fees
			'payment_type'		=> '',		// Payment type
			'mc_currency'		=> '',		// Currency
			'payment_date'		=> '',		// Payment Date/Time EX: '19:08:04 Oct 03, 2007 PDT'

			'payer_id'			=> '',		// Paypal sender ID
			'payer_email'		=> '',		// Paypal sender email address
			'payer_status'		=> '',		// Paypal sender status (verified, unverified?)
			'first_name'		=> '',		// First name of sender
			'last_name'			=> '',		// Last name of sender
			'memo'				=> '',		// Memo sent by the donor
		);

		$this->data['confirmed'] = false;	// used to check if the payment is confirmed

		foreach ($data_ary as $key => $default)
		{
			$this->data[$key] = request_var($key, $default);
		}
	}

	/**
	 * List each currency symbol, prefix or suffix and decimal in an array of options
	 * Used to merge with $user->lang['currency_code'] array.
	 *
	 * @return array $currency_code
	 */
	private function currency_data()
	{
		$currency_code = array(
			'USD'	=> array('symbol' => '$', 'prefix' => true, 'decimal' => '.'),
			'AUD'	=> array('symbol' => '$', 'prefix' => true, 'decimal' => '.'),
			'CAD'	=> array('symbol' => '$', 'prefix' => true, 'decimal' => ','),
			'CZK'	=> array('symbol' => 'Kč', 'prefix' => false, 'decimal' => ','),
			'DKK'	=> array('symbol' => 'Kr', 'prefix' => false, 'decimal' => ','),
			'EUR'	=> array('symbol' => '€', 'prefix' => true, 'decimal' => ','),
			'HKD'	=> array('symbol' => 'HK$', 'prefix' => false, 'decimal' => NULL),
			'HUF'	=> array('symbol' => 'Ft', 'prefix' => false, 'decimal' => ','),
			'NZD'	=> array('symbol' => '$', 'prefix' => true, 'decimal' => '.'),
			'NOK'	=> array('symbol' => 'kr', 'prefix' => false, 'decimal' => ','),
			'PLN'	=> array('symbol' => 'zł', 'prefix' => false, 'decimal' => ','),
			'GBP'	=> array('symbol' => '£', 'prefix' => false, 'decimal' => '.'),
			'SGD'	=> array('symbol' => '$', 'prefix' => true, 'decimal' => '.'),
			'SEK'	=> array('symbol' => 'kr', 'prefix' => false, 'decimal' => ','),
			'CHF'	=> array('symbol' => 'CHF', 'prefix' => false, 'decimal' => ','),
			'JPY'	=> array('symbol' => '¥', 'prefix' => false, 'decimal' => NULL),
		);

		return $currency_code;
	}
}


/**
 * List the currency options defined in the language file
 *
 * @return option list $s_currency_options
 */
function currency_options($default = 0)
{
	global $user, $config;

	$default = ($default) ? $default : $config['paypal_default_currency'];

	// setup a list of currencies
	$s_currency_options = '';

	// if currencies need to be removed, they may be done so from the language file
	foreach ($user->lang['currency_code'] as $key => $value)
	{
		$selected = ($key == $default) ? ' selected="selected"' : '';
		$s_currency_options .= '<option value="' . $key . '"' . $selected . '>' . $value . ' (' . $key . ")</option>\n";
	}

	return $s_currency_options;
}

/**
 * List the country options defined in the language file
 *
 * @return option list $s_country_options
 */
function country_options($default = '')
{
	global $user, $config;

	$default = ($default) ? $default : $config['paypal_default_country'];

	// Setup a list of Countries.
	$s_country_options = '';

	// If Countries need to be removed, they may be done so from the language file.
	foreach ($user->lang['country_options'] as $key => $value)
	{
		$selected = ($key == $default) ? ' selected="selected"' : '';
		$s_country_options .= '<option value="' . $key . '"' . $selected . '>' . $value . "</option>\n";
	}

	return $s_country_options;
}

?>