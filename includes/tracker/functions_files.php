<?php
/**
*
* @package tracker
* @version $Id: functions_files.php 206 2008-12-31 15:37:03Z JRSweets $
* @copyright (c) 2007, 2008 jrsweets
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/*
* Some of these functions are borrowed from evil<3
* at http://www.phpbbmodders.net from the
* quickinstall mod for phpbb3
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * Useful class for directory and file actions
 */
class file_functions
{
	public static function copy_file($src_file, $dst_file)
	{
		return copy($src_file, $dst_file);
	}

	public static function delete_file($file)
	{
		return unlink($file);
	}

	public static function move_file($src_file, $dst_file)
	{
		self::copy_file($src_file, $dst_file);
		self::delete_file($src_file);
	}

	public static function copy_dir($src_dir, $dst_dir)
	{
		self::append_slash($src_dir);
		self::append_slash($dst_dir);

		if (!is_dir($dst_dir))
		{
			mkdir($dst_dir);
		}

		foreach (scandir($src_dir) as $file)
		{
			if (in_array($file, array('.', '..', '.svn'), true))
			{
				continue;
			}

			$src_file = $src_dir . $file;
			$dst_file = $dst_dir . $file;

			if (is_file($src_file))
			{
				if (is_file($dst_file))
				{
					$ow = filemtime($src_file) - filemtime($dst_file);
				}
				else
				{
					$ow = 1;
				}

				if ($ow > 0)
				{
					if (copy($src_file, $dst_file))
					{
						touch($dst_file, filemtime($src_file));
					}
				}
			}
			else if (is_dir($src_file))
			{
				self::copy_dir($src_file, $dst_file);
			}
		}
	}

	public static function delete_dir($dir, $empty = false)
	{
		self::append_slash($dir);

		if (!file_exists($dir) || !is_dir($dir) || !is_readable($dir))
		{
			return false;
		}

		foreach (scandir($dir) as $file)
		{
			if (in_array($file, array('.', '..', '.svn'), true))
			{
				continue;
			}

			if (is_dir($dir . $file))
			{
				self::delete_dir($dir . $file);
			}
			else
			{
				self::delete_file($dir . $file);
			}
		}

		if (!$empty)
		{
			@rmdir($dir);
		}
	}

	public static function move_dir($src_dir, $dst_dir)
	{
		self::copy_dir($src_dir, $dst_dir);
		self::delete_dir($src_dir);
	}

	public static function delete_files($dir, $files_ary, $recursive = true)
	{
		self::append_slash($dir);

		foreach (scandir($dir) as $file)
		{
			if (in_array($file, array('.', '..'), true))
			{
				continue;
			}

			if (is_dir($dir . $file))
			{
				if ($recursive)
				{
					self::delete_files($dir . $file, $files_ary, true);
				}
			}

			if (in_array($file, $files_ary, true))
			{
				if (is_dir($dir . $file))
				{
					self::delete_dir($dir . $file);
				}
				else
				{
					self::delete_file($dir . $file);
				}
			}
		}
	}

	public static function append_slash(&$dir)
	{
		if ($dir != '' && $dir[strlen($dir) - 1] != '/')
		{
			$dir .= '/';
		}
	}

	public static function remove_extension($file)
	{
		$ext = strrchr($file, '.');

		if($ext !== false)
		{
		    $file = substr($file, 0, -strlen($ext));
		}
		return $file;
	}

	public static function filesize($files)
	{
		// Seperate files from directories and calculate the size
		$filesize = 0;
		$files = (!is_array($files)) ? array($files) : $files;

		$dir_list = $file_list = array();
		if (is_array($files))
		{
			foreach ($files as $file)
			{
				if (is_dir($file))
				{
					$dir_list[] = $file;
				}
				else if(file_exists($file))
				{
					$filesize += filesize($file);
				}
			}
		}
		unset($files);

		// If there are directories listed we need to list the files and get the file size
		if (!empty($dir_list))
		{
			foreach ($dir_list as $dir)
			{
				$file_list =  array_merge($file_list, self::filelist('', $dir));
			}
			unset($dir_list);

			foreach ($file_list as $file)
			{
				if (file_exists($file))
				{
					$filesize += filesize($file);
				}
			}
			unset ($file_list);
		}

		return $filesize;
	}

	public static function filelist($path, $dir = '', $ignore = '', $ignore_index = true)
	{
		$list = array();
		self::append_slash($dir);

		if ($files = scandir($path . $dir))
		{
			foreach ($files as $file)
			{
				if ($file == '.' || $file == '..' || $file == '.svn' || preg_match('#\.' . $ignore . '$#i', $file))
				{
					continue;
				}

				if ($ignore_index && ($file == 'index.html' || $file == 'index.htm'))
				{
					continue;
				}

				if (is_dir($path . $dir . $file))
				{
					$list = array_merge($list, self::filelist($path, $dir . $file, $ignore, $ignore_index));
				}
				else
				{
					$list[] = $dir . $file;
				}
			}
		}

		return $list;
	}
}

?>