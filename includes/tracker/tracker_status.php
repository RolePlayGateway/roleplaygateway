<?php
/**
*
* @package tracker
* @version $Id: tracker_status.php 114 2008-05-05 20:07:38Z evil3 $
* @copyright (c) 2008 http://www.jeffrusso.net
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/*
* Tracker status type defined below
* DO NOT REMOVE OR REPLACE ITEMS 0, 1, 2 and 3
* They are special types that the tracker
* expects to be there
*/

$status_type = array();

//Bug status types
$status_type['bugs'] = array(
	0 =>	array(
		'id' 		=> 0,
		'name' 		=> 'TRACKER_ALL',
		'filter' 	=> true,
		'open' 		=> true,
	),
	1 => array(
		'id' 		=> 1,
		'name' 		=> 'TRACKER_ALL_OPENED',
		'filter' 	=> true,
		'open' 		=> true,
	),
	2 => array(
		'id' 		=> 2,
		'name' 		=> 'TRACKER_ALL_CLOSED',
		'filter' 	=> true,
		'open' 		=> true,
	),
	3 => array(
		'id' 		=> 3,
		'name' 		=> 'TRACKER_NEW',
		'filter' 	=> false,
		'open' 		=> true,
	),
	4 => array(
		'id' 		=> 4,
		'name' 		=> 'TRACKER_NOT_A_BUG',
		'filter' 	=> false,
		'open'		=> false,
	),
	5 => array(
		'id' 		=> 5,
		'name' 		=> 'TRACKER_SUPPORT_REQUEST',
		'filter' 	=> false,
		'open' 		=> false,
	),
	6 => array(
		'id' 		=> 6,
		'name'		=> 'TRACKER_DUPLICATE',
		'filter' 	=> false,
		'open' 		=> false,
	),
	7 => array(
		'id' 		=> 7,
		'name' 		=> 'TRACKER_ALREADY_FIXED',
		'filter' 	=> false,
		'open' 		=> false,
	),
	8 => array(
		'id' 		=> 8,
		'name' 		=> 'TRACKER_REVIEWED',
		'filter' 	=> false,
		'open' 		=> true,
	),
	9 => array(
		'id' 		=> 9,
		'name' 		=> 'TRACKER_REVIEW_LATER',
		'filter' 	=> false,
		'open' 		=> true,
	),
	10 => array(
		'id' 		=> 10,
		'name' 		=> 'TRACKER_AWAITING_INFO',
		'filter' 	=> false,
		'open' 		=> true,
	),
	11 => array(
		'id' 		=> 11,
		'name' 		=> 'TRACKER_AWAITING_INPUT',
		'filter' 	=> false,
		'open' 		=> true,
	),
	12 => array(
		'id' 		=> 12,
		'name' 		=> 'TRACKER_PENDING',
		'filter' 	=> false,
		'open' 		=> true,
	),
	13 => array(
		'id' 		=> 13,
		'name' 		=> 'TRACKER_WILL_NOT_FIX',
		'filter' 	=> false,
		'open' 		=> false,
	),
	14 => array(
		'id' 		=> 14,
		'name' 		=> 'TRACKER_FIX_IN_PROGRESS',
		'filter' 	=> false,
		'open' 		=> true,
	),
	15 => array(
		'id' 		=> 15,
		'name' 		=> 'TRACKER_FIX_COMPLETED_SVN',
		'filter' 	=> false,
		'open' 		=> false,
	),
	16 => array(
		'id' 		=> 16,
		'name' 		=> 'TRACKER_FIX_COMPLETED',
		'filter' 	=> false,
		'open' 		=> false,
	),
	17 => array(
		'id' 		=> 17,
		'name' 		=> 'TRACKER_UNREPRODUCABLE',
		'filter' 	=> false,
		'open' 		=> false,
	),
);

//Feature status types
$status_type['feature'] = array(
	0 => array(
		'id' 		=> 0,
		'name' 		=> 'TRACKER_ALL',
		'filter' 	=> true,
		'open' 		=> true,
	),
	1 => array(
		'id' 		=> 1,
		'name' 		=> 'TRACKER_ALL_OPENED',
		'filter' 	=> true,
		'open' 		=> true,
	),
	2 => array(
		'id' 		=> 2,
		'name' 		=> 'TRACKER_ALL_CLOSED',
		'filter' 	=> true,
		'open' 		=> true,
	),
	3 => array(
		'id' 		=> 3,
		'name' 		=> 'TRACKER_NEW',
		'filter' 	=> false,
		'open' 		=> true,
	),
	4 => array(
		'id' 		=> 4,
		'name' 		=> 'TRACKER_SUPPORT_REQUEST',
		'filter' 	=> false,
		'open' 		=> false,
	),
	5 => array(
		'id' 		=> 5,
		'name' 		=> 'TRACKER_INVALID',
		'filter' 	=> false,
		'open' 		=> false,
	),
	6 => array(
		'id' 		=> 6,
		'name'		=> 'TRACKER_DUPLICATE',
		'filter' 	=> false,
		'open' 		=> false,
	),
	7 => array(
		'id' 		=> 7,
		'name'		=> 'TRACKER_IMPLEMENTING',
		'filter' 	=> false,
		'open' 		=> false,
	),
	8 => array(
		'id' 		=> 8,
		'name'		=> 'TRACKER_WILL_NOT_IMPLEMENT',
		'filter' 	=> false,
		'open' 		=> false,
	),
	9 => array(
		'id' 		=> 9,
		'name' 		=> 'TRACKER_IMPLEMENTED_SVN',
		'filter' 	=> false,
		'open' 		=> true,
	),
	10 => array(
		'id' 		=> 10,
		'name'		=> 'TRACKER_RESEARCHING',
		'filter' 	=> false,
		'open' 		=> false,
	),
	11 => array(
		'id' 		=> 11,
		'name' 		=> 'TRACKER_REVIEWED',
		'filter' 	=> false,
		'open' 		=> true,
	),
	12 => array(
		'id' 		=> 12,
		'name' 		=> 'TRACKER_REVIEW_LATER',
		'filter' 	=> false,
		'open' 		=> true,
	),
	13 => array(
		'id' 		=> 13,
		'name' 		=> 'TRACKER_AWAITING_INFO',
		'filter' 	=> false,
		'open' 		=> true,
	),
	14 => array(
		'id' 		=> 14,
		'name' 		=> 'TRACKER_AWAITING_INPUT',
		'filter' 	=> false,
		'open' 		=> true,
	),
	15 => array(
		'id' 		=> 15,
		'name' 		=> 'TRACKER_PENDING',
		'filter' 	=> false,
		'open' 		=> true,
	),
);

//Issue status types
$status_type['issue'] = array(
	0 => array(
		'id' 		=> 0,
		'name' 		=> 'TRACKER_ALL',
		'filter' 	=> true,
		'open' 		=> true,
	),
	1 => array(
		'id' 		=> 1,
		'name' 		=> 'TRACKER_ALL_OPENED',
		'filter' 	=> true,
		'open' 		=> true,
	),
	2 => array(
		'id' 		=> 2,
		'name' 		=> 'TRACKER_ALL_CLOSED',
		'filter' 	=> true,
		'open' 		=> true,
	),
	3 => array(
		'id' 		=> 3,
		'name' 		=> 'TRACKER_NEW',
		'filter' 	=> false,
		'open' 		=> true,
	),
	4 => array(
		'id' 		=> 4,
		'name' 		=> 'TRACKER_SUPPORT_REQUEST',
		'filter' 	=> false,
		'open' 		=> false,
	),
	5 => array(
		'id' 		=> 5,
		'name' 		=> 'TRACKER_INVALID',
		'filter' 	=> false,
		'open' 		=> false,
	),
	6 => array(
		'id' 		=> 6,
		'name'		=> 'TRACKER_RESEARCHING',
		'filter' 	=> false,
		'open' 		=> false,
	),
	7 => array(
		'id' 		=> 7,
		'name' 		=> 'TRACKER_REVIEWED',
		'filter' 	=> false,
		'open' 		=> true,
	),
	8 => array(
		'id' 		=> 8,
		'name' 		=> 'TRACKER_REVIEW_LATER',
		'filter' 	=> false,
		'open' 		=> true,
	),
	9 => array(
		'id' 		=> 9,
		'name' 		=> 'TRACKER_AWAITING_INFO',
		'filter' 	=> false,
		'open' 		=> true,
	),
	10 => array(
		'id' 		=> 10,
		'name' 		=> 'TRACKER_AWAITING_INPUT',
		'filter' 	=> false,
		'open' 		=> true,
	),
	11 => array(
		'id' 		=> 11,
		'name' 		=> 'TRACKER_PENDING',
		'filter' 	=> false,
		'open' 		=> true,
	),
	12 => array(
		'id' 		=> 12,
		'name' 		=> 'TRACKER_WILL_NOT_FIX',
		'filter' 	=> false,
		'open' 		=> false,
	),
	13 => array(
		'id' 		=> 13,
		'name' 		=> 'TRACKER_FIX_IN_PROGRESS',
		'filter' 	=> false,
		'open' 		=> true,
	),
	14 => array(
		'id' 		=> 14,
		'name' 		=> 'TRACKER_FIX_COMPLETED_SVN',
		'filter' 	=> false,
		'open' 		=> false,
	),
	15 => array(
		'id' 		=> 15,
		'name' 		=> 'TRACKER_FIX_COMPLETED',
		'filter' 	=> false,
		'open' 		=> false,
	),
);

return $status_type;

?>