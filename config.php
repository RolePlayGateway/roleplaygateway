<?php
// phpBB 3.0.x auto-generated configuration file
// Do not change anything in this file!
ini_set('display_errors', false);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

$dbms = 'mysqli';
$dbhost = '127.0.0.1';
$dbport = '';
$dbname = 'db_gateway';
$dbuser = 'admin';
$dbpasswd = 'sky22midnight';

$table_prefix = 'gateway_';
$acm_type = 'apc';
$load_extensions = '';

@define('PHPBB_INSTALLED', false);

date_default_timezone_set('UTC');
ini_set('display_errors', true);
if (@$_REQUEST['explain']) {
	@define('DEBUG', true);
	@define('DEBUG_EXTRA', true);
	
	
	
	//ini_set('display_errors',true);

	//$dbms = 'mysql_highload';
}
/* 
if(
	@$_SERVER['HTTP_X_FORWARDED_FOR'] ||
	@$_SERVER['HTTP_FORWARDED_FOR'] ||
	@$_SERVER['HTTP_X_FORWARDED'] ||
	@$_SERVER['HTTP_FORWARDED'] ||
	@$_SERVER['HTTP_FORWARDED_FOR'] ||
	@$_SERVER['HTTP_FORWARDED_FOR_IP'] ||
	@$_SERVER['HTTP_PROXY_CONNECTION'] ||
	@$_SERVER['HTTP_CLIENT_IP'] ||
	@$_SERVER['CLIENT_IP'] ||
	@$_SERVER['FORWARDED_FOR_IP'] ||
	@$_SERVER['HTTP_VIA'] ||
	@$_SERVER['VIA']
	) {
		die('No proxies allowed');
} */
?>
