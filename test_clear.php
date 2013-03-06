<?php 
if(!defined('GID_EXTENSION_LOADED_APC'))
	define('GID_EXTENSION_LOADED_APC',extension_loaded('apc')); 
apc_clear_cache(); 
apc_cache_info();
?>
