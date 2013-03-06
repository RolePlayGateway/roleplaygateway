<?php
/**
*
* @package prs
* @version 1.0.0 2008/05/30 07:00:00 GMT
* @copyright (c) 2008 Alfatrion
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

$filename = 'prs_check.' . $phpEx;
if ($fp = @fopen($filename, 'wb'))
{
	$output = "<?php\n" . "\$check_arr = array();\n";
	foreach ($files as $key => $value)
	{
		$output .= "\$check_arr['$key'] = array();\n";
		$arr_md5 = prs_md5_file($value);  
		foreach ($arr_md5 as $file => $md5)   
		{
			$output .= "\$check_arr['$key']['$file'] = '$md5';\n";
		}
	}
		$output .= "?>\n";

	@flock($fp, LOCK_EX);
	fwrite($fp, $output);
	@flock($fp, LOCK_UN);
	fclose($fp);
}
else
{
	$error = '';
	if (!@is_writable($phpbb_root_path))
	{
		$error .= $phpbb_root_path . ' is NOT writable.<br>';

	}
	if (!@is_writable($phpbb_root_path . $filename))
	{
		$error .= 'Not able to open ' . $phpbb_root_path . $filename . '<br>';
	}
	if (strlen($error))
	{
		trigger_error($error, E_USER_ERROR);
	}
}
?>
