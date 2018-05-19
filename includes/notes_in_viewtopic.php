<?php
/**
*
* @package phpBB3
* @version $Id: notes_in_viewtopic.php,v 1.1.1.1 2008/11/20 20:31:25 rmcgirr83 Exp $
* @copyright (c) Rich McGirr
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

/**
* Include only once.
*/
if (!defined('INCLUDES_NOTES_IN_VIEWTOPIC_PHP'))
{
	define('INCLUDES_NOTES_IN_VIEWTOPIC_PHP', true);

	function notes_in_viewtopic()
	{
		global $db;
		
		$args = func_get_args();
		
		$mode		= array_shift($args);
		$user       = intval(array_shift($args));
		$count      = ($mode == 'some') ? intval(array_shift($args)) : '';

		switch ($mode)
		{
			case 'reset':
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_notes = 0
					WHERE user_id =' . $user;
				$db->sql_query($sql);
			break;

			case 'some':
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_notes = user_notes - ' . $count . '
					WHERE user_id =' . $user;
				$db->sql_query($sql);
			break;

			case 'add_feedback';
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET user_notes = user_notes + 1
					WHERE user_id =' . $user;
				$db->sql_query($sql);
			break;

			default:
				return false;
		}
	}

return;
}
?>
