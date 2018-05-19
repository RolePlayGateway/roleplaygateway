<?php 
// m_lock controlls if moderator can lock / unlock / edit ratings
/**
*
* @package prs
* @version 1.0.0 2007/12/23 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
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

// Values
define('PRS_EXTRA_TOPICS_RATINIGS_FIRST_POST', 0);
define('PRS_EXTRA_TOPICS_RATINIGS_AVERAGE_POSTS', 1);

// Table and DB related
define('PRS_MULTIPLIER_PERCENT', 100.0);
define('PRS_MULTIPLIER_PERMILL', 1000.0);
define('PRS_MULTIPLIER_PERMILL_DOUBLE', 2 * PRS_MULTIPLIER_PERMILL);

// DB related
define('PRS_MULTIPLIER_CHI', 100);
define('PRS_MULTIPLIER_DIFF', 2);
define('PRS_MULTIPLIER_SCORE', 10);
define('PRS_MAX_MODPOINTS_PER_VOTE', 255);
define('PRS_MAX_CHI', 255);
define('PRS_MAX_O', 65535);

// Control values - you can safely change this
define('PRS_START_SCORE', 0);
define('PRS_DEFAULT_SCORE_NULL', 1);
//define('PRS_DEFAULT_SCORE', 3 * PRS_MULTIPLIER_SCORE);
define('PRS_KARMA_PRECISION', 2);
define('PRS_STATS_PRECISION', 2);
define('ENABLE_CACHE', 1);

// But this one doesn't effect the number of stars you see
define('PRS_MAX_NUMBER_STARS', 5);

// Warning: PRS_DEFAULT_SCORE may not be 0,
// use PRS_DEFAULT_SCORE_NULL instead
?>
