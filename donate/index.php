<?php
/**
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package phpBB3
 * @version $Id: index.php 18 2008-10-16 06:25:09Z Highway of Life $
 * @copyright (c) 2008 Star Trek Guide Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
define('IN_PHPBB', true);
$this_dir = './';
$phpbb_root_path = $this_dir . '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($this_dir . 'functions_paypal.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/paypal_donation_mod');

if (!isset($config['paypal_founder_manage']))
{
	$u_admin = append_sid("{$phpbb_root_path}adm/index.$phpEx", array('i' => 'donation_mod', 'mode' => 'settings'), true, $user->session_id);
	trigger_error($auth->acl_get('a_board') ? sprintf($user->lang['UPDATE_MOD_REQUIRED'], '<a href="' . $u_admin . '">', '</a>') : 'MOD_DISABLED');
}

$donate = new paypal_class();

$donate->page = generate_board_url(true) . $user->page['script_path'] . $user->page['page_name'];

$submit = (isset($_REQUEST['submit'])) ? true : false;
$action = request_var('action', 'donate');

switch ($action)
{
	case 'success':
		trigger_error($user->lang['THANKS_DONATION']);
	break;

	case 'validate':
	case 'ipn':
		if (PAYPAL_DEBUG)
		{
			$donate->log_error('DEBUG:', false, E_USER_NOTICE, $_REQUEST);
		}

		$donate->validate_transaction();
	break;

	case 'cancel':
		trigger_error($user->lang['DONATION_CANCELED'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . append_sid($donate->page) . '">', '</a>'));
	break;

	case 'donate':
		if ($submit)
		{

		}
		else
		{
			$donate->hash_str = 'PGEgaHJlZj0iaHR0cDovL2h0dHA6Ly9zdGFydHJla2d1aWRlLmNvbS9jb21tdW5pdHkvdmlld3RvcGljLnBocD90PTI3ODEiPlBheVBhbCBE';
			$donate->hash_str .= 'b25hdGlvbiBNT0Q8L2E+ICZjb3B5IDIwMDggPGEgaHJlZj0iaHR0cDovL3N0YXJ0cmVrZ3VpZGUuY29tIj5TdGFyVHJla0d1aWRlPC9hPg==';

			$donate->add_fields(array(
				'cmd'			=> '_xclick',
				'business'		=> ($config['paypal_sandbox'] || (PAYPAL_DEBUG && $user->data['user_type'] == USER_FOUNDER)) ? $config['paypal_sandbox_address'] : $config['paypal_address'],
				'item_name'		=> $config['sitename'],
				'item_number'	=> 'uid_' . $user->data['user_id'] . '_' . time(),
				'no_shipping'	=> 1,
				'return'		=> append_sid($donate->page, 'action=success'),
				'notify_url'	=> $donate->page . '?action=ipn',
				'cancel_return'	=> append_sid($donate->page, 'action=cancel'),
				'tax'			=> 0,
				'bn'			=> 'PP-DonationsBF',
			));

			if ($config['paypal_style'])
			{
				$donate->add_fields(array('page_style'	=> $config['paypal_style']));
			}

			$donate->paypal_setup();

			$now = time();
			$sql = 'SELECT perk_title, perk_text, perk_desc_bitfield, perk_desc_options, perk_desc_uid
					FROM ' . DONATION_PERKS_TABLE . "
					WHERE perk_expire_date = 0 OR (perk_active_date < $now AND perk_expire_date > $now)
					ORDER BY perk_order";
			$result = $db->sql_query($sql, (60 * 15));

			while ($row = $db->sql_fetchrow($result))
			{
				$template->assign_block_vars('perk', array(
					'PERK_TITLE'		=> $row['perk_title'],
					'PERK_DESCRIPTION'	=> generate_text_for_display($row['perk_text'], $row['perk_desc_uid'], $row['perk_desc_bitfield'], $row['perk_desc_options']),
				));
			}

			$minimum_donation = '<select>' . $donate->minimum_currency_list() . '</select>';

			$template->assign_vars(array(
				'DONATE_TO_SITENAME'		=> sprintf($user->lang['DONATE_TO_SITENAME'], $config['sitename']),
				'DONATE_TO_SITENAME_EXPLAIN'=> sprintf($user->lang['DONATE_TO_SITENAME_EXPLAIN'], $config['sitename'], $minimum_donation, $config['paypal_supporters_group']),
				'MINIMUM_DONATION'			=> $minimum_donation,
			));

			@$donate->display($source);
		}
	break;
}

// Output page
page_header(sprintf($user->lang['DONATE_TO_SITENAME'], $config['sitename']), false);

$template->set_filenames(array(
	'body' => 'donate/index_body.html'
));

page_footer(false);

?>