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
if ($fp = @fopen($phpbb_root_path . $filename, 'wb'))
{
	$output = "<?php\n" . "\$prs_md5_copied = array();\n";
	foreach ($prs_check_files as $key => $value)
	{
		$output .= "\$prs_md5_copied['$key'] = array();\n";
		$arr_md5 = prs_md5_file($value);  
		foreach ($arr_md5 as $file => $md5)   
		{
			$output .= "\$prs_md5_copied['$key']['$file'] = '$md5';\n";
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
