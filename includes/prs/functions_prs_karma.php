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

$karma_cache = array();

function prs_karma($user_id,$from_cron = false,$debug = false)
{
	global $db, $auth, $user, $config, $phpEx, $template, $phpbb_root_path;
	global $karma_cache;

	if (isset($karma_cache[$user_id]))
	{
		return $karma_cache[$user_id];
	}

	$high = time();// - $config['prs_votes_period'];
		
	// Ignore votes from past... 10 minutes or so?
	$high -= $high % 900;
	
	$low = $high - $config['prs_karma_period'];
	
	$karma = $mod = 0;
	$o = -1;
	$stat = array();
	

	// Start with 1, for 100%
	$weight = 1;
	//$config['prs_karma_n'];

	if ($user_id >= 0)
	{
		for ($i = 0; $i < $config['prs_karma_n']; $i++)
		{
	
			$sql_array = array(
				'SELECT'	=> 'v.score',
				'FROM'		=> POSTS_TABLE . ' AS p',
				'WHERE'		=> ' v.time >= ' . $low . '
					AND v.time < ' . $high . '
					AND p.poster_id = ' . $user_id . '
					AND v.post_id = p.post_id',
			);
			$post_list =& prs_get_post_list_time($low, $high);
			$votes_data =& prs_get_votes_dataset($post_list, NULL, $sql_array, NULL, 3600, $from_cron);
			$n = $votes_data['n'];
			
			$score = $votes_data['score'];
			if ($n > 0)
			{
				$karma += $score;
			} else {
				//echo "\nError, \$n = $n";
			}
			if ($o < 0.001 && $n > 0)
			{
				$o = $votes_data['o'];
			}
			
			$post_list_string = implode(",",$post_list);
			
			if ($debug == true) {
				echo "\n".$i.": (".$n." posts: (redacted...) ".$karma." / ".PRS_MULTIPLIER_SCORE." (". $karma / PRS_MULTIPLIER_SCORE .")";
			}

			//$weight = $weight / $i;
			$low = $high;
			$high -= $config['prs_karma_period'];
		}
	}
	if ($karma && $n > 0)
	{
		$karma = round($karma / PRS_MULTIPLIER_SCORE, PRS_KARMA_PRECISION);
		$o = round($o / PRS_MULTIPLIER_SCORE, PRS_KARMA_PRECISION);
	}
	else
	{
		$karma = round($config['prs_default_rating'] / PRS_MULTIPLIER_SCORE, PRS_KARMA_PRECISION);
		$o = 0;
	}

	$karma_cache[$user_id] = array('karma' => $karma, 'o' => $o);	
	return $karma_cache[$user_id];
}
?>
