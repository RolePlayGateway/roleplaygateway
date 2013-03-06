<?php
/*
*
* @name donate.php
* @package phpBB3 Portal  a.k.a canverPortal
* @version $Id: donate.php,v 1.5 2007/04/14 02:05:16 angelside Exp $
* @copyright (c) Canver Software - www.canversoft.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
*/

if ($CFG['pay_acc'] != '')
{
	if ($CFG['pay_c_block'] == true)
	{
		$template->assign_vars(array(
			'S_DISPLAY_PAY_C' => true
			)
		);
	}

	if ($CFG['pay_s_block'] == true)
	{
		$template->assign_vars(array(
			'S_DISPLAY_PAY_S' => true
			)
		);
	}

	// Assign specific vars
	$template->assign_vars(array(
		'PAY_ACC' => $CFG['pay_acc'],
		)
	);
}

?>