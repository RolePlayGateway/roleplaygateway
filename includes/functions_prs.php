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

/*
 * In general functions may not call function that are below them.
 *
 * STATISTICS SUPPORT:	general functions that provide statistical support
 * GENERAL SUPPORT:	general functions that provide general support
 * BASIS:		minimal functionality that one needs
 * KARMA:		calulation of karma
 * SHADOW:		
 * PENALTY:		
 * MODPOINTS:		
 * HOOKS:		general function that provide hook and binds these to one ore more module function
*/

include("prs/functions_prs_compat.php");
include("prs/functions_prs_constants.php");
include("prs/functions_prs_stats.php");
include("prs/functions_prs_support.php");
include("prs/functions_prs_basis.php");
include("prs/functions_prs_karma.php");
include("prs/functions_prs_shadow.php");
include("prs/functions_prs_penalty.php");
include("prs/functions_prs_modpoints.php");
include("prs/functions_prs_moderator.php");
include("prs/functions_prs_hooks.php");

?>
