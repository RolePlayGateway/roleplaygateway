<?php
/**
*
* @package acm
* @version $Id: acm_memcache.php 10307 2009-12-08 22:55:33Z toonarmy $
* @copyright (c) 2005, 2009 phpBB Group
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

// Include the abstract base
if (!class_exists('acm_memory'))
{
	require("{$phpbb_root_path}includes/acm/acm_memory.$phpEx");
}

if (!defined('PHPBB_ACM_MEMCACHE_PORT'))
{
	define('PHPBB_ACM_MEMCACHE_PORT', 11211);
}

if (!defined('PHPBB_ACM_MEMCACHE_COMPRESS'))
{
	define('PHPBB_ACM_MEMCACHE_COMPRESS', false);
}

if (!defined('PHPBB_ACM_MEMCACHE_HOST'))
{
	define('PHPBB_ACM_MEMCACHE_HOST', 'localhost');
}

/**
* ACM for Memcached
* @package acm
*/
class acm extends acm_memory
{
	var $extension = 'memcache';

	var $memcache;
	var $flags = 0;

	function acm()
	{
		// Call the parent constructor
		parent::acm_memory();

		$this->memcache = new Memcache;
		$this->memcache->connect(PHPBB_ACM_MEMCACHE_HOST, PHPBB_ACM_MEMCACHE_PORT);
		$this->flags = (PHPBB_ACM_MEMCACHE_COMPRESS) ? MEMCACHE_COMPRESSED : 0;
	}

	/**
	* Unload the cache resources
	*
	* @return void
	*/
	function unload()
	{
		parent::unload();

		$this->memcache->close();
	}

	/**
	* Purge cache data
	*
	* @return void
	*/
	function purge()
	{
		$this->memcache->flush();

		parent::purge();
	}

	/**
	* Fetch an item from the cache
	*
	* @access protected
	* @param string $var Cache key
	* @return mixed Cached data
	*/
	function _read($var)
	{
		return $this->memcache->get($this->key_prefix . $var);
	}

	/**
	* Store data in the cache
	*
	* @access protected
	* @param string $var Cache key
	* @param mixed $data Data to store
	* @param int $ttl Time-to-live of cached data
	* @return bool True if the operation succeeded
	*/
	function _write($var, $data, $ttl = 2592000)
	{
		if (!$this->memcache->replace($this->key_prefix . $var, $data, $this->flags, $ttl))
		{
			return $this->memcache->set($this->key_prefix . $var, $data, $this->flags, $ttl);
		}
		return true;
	}

	/**
	* Remove an item from the cache
	*
	* @access protected
	* @param string $var Cache key
	* @return bool True if the operation succeeded
	*/
	function _delete($var)
	{
		return $this->memcache->delete($this->key_prefix . $var);
	}
}

?>