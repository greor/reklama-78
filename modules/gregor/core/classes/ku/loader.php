<?php defined('SYSPATH') OR die('No direct access allowed.');

class Ku_Loader {

	public static $cache_dir;
	public static $default_filename = 'php_class_cache';
	public static $chmod = 0666;

	public static $skip_classes = array();

	protected static $_cache = array();

	protected static $_files;

	protected static $_init;


	/**
	 * Init new cache file
	 *
	 * @param   string   $filename name of cache file
	 * @param   array    $skip_classes list of skipped from cache classes
	 * @return  void
	 */
	public static function init($filename = NULL,array $skip_classes = NULL)
	{
		if (self::$_init)
		{
			self::save();
		}

		if ($filename === NULL)
		{
			$filename = self::$default_filename;
		}

		if ( ! self::$cache_dir)
		{
			self::$cache_dir = Kohana::$cache_dir
				? Kohana::$cache_dir
				: APPPATH.'cache';

		}

		$filename = self::$cache_dir.DIRECTORY_SEPARATOR.$filename.EXT;

		$is_file = is_file($filename);
		if ($is_file)
		{
//			timer('requere start');
			require $filename;
//			timer('requere stop');
		}

		if ($skip_classes !== NULL)
		{
			self::$skip_classes += array_change_key_case(array_combine($skip_classes, $skip_classes), CASE_LOWER);
		}
		else
		{
			self::$skip_classes = array();
		}

		self::$_cache[] = array(
			'filename' => $filename,
			'is_file'  => $is_file,
			'mtime'    => $is_file ? @filemtime($filename) : FALSE,
			'size'     => $is_file ? @filesize($filename) : FALSE,
			'files'    => array(),
		);

		self::$_files = & self::$_cache[count(self::$_cache)-1]['files'];

		if (self::$_init === NULL)
		{
			spl_autoload_unregister(array('Kohana', 'auto_load'));
			spl_autoload_register(array('Ku_Loader', 'auto_load'));
			register_shutdown_function(array('Ku_Loader', 'shutdown_handler'));
			self::$_init = TRUE;
		}
//		die ('Init');
	}


	/**
	 * Assign Ku_Loader::save() as last shutdown function
	 *
	 * @return  void
	 */
	public static function shutdown_handler()
	{
		self::$_init and register_shutdown_function(array('Ku_Loader', 'save'));
	}

	/**
	 * Deinit loader
	 *
	 * @return  void
	 */
	public static function deinit()
	{
		if (! self::$_init)
		{
			return;
		}

		self::$cache_dir =
		self::$_filename =
		self::$_files = NULL;
		self::$_cache = array();

		spl_autoload_unregister(array('Ku_Loader', 'auto_load'));
		spl_autoload_register(array('Kohana', 'auto_load'));

		self::$_init = NULL;
	}


	/**
	 * Provides auto-loading support of Kohana classes, as well as transparent
	 * extension of classes that have a _Core suffix.
	 *
	 * Class names are converted to file names by making the class name
	 * lowercase and converting underscores to slashes:
	 *
	 *     // Loads classes/my/class/name.php
	 *     Kohana::auto_load('My_Class_Name');
	 *
	 * @param   string   class name
	 * @return  boolean
	 */
	public static function auto_load($class)
	{
		try
		{
			$class = strtolower($class);
			// Transform the class name into a path
			$file = str_replace('_', '/', $class);

			if ($path = Kohana::find_file('classes', $file))
			{
				// Load the class file
				require $path;

				if (self::$_files !== NULL AND ! isset(self::$skip_classes[$class]))
				{
					self::$_files[] = $path;
				}

				// Class has been found
				return TRUE;
			}

			// Class is not in the filesystem
			return FALSE;
		}
		catch (Exception $e)
		{
			Kohana_Exception::handler($e);
			die;
		}
	}

	/**
	 * Save new loaded files to cache
	 *
	 *
	 * @return  bool
	 */
	public static function save()
	{
//		return FALSE;
		$success_files = 0;

		if ( ! self::$_init OR empty(self::$_cache))
		{
			return FALSE;
		}

		try
		{
			foreach (self::$_cache as $item)
			{
				if (empty($item['files']))
					continue;

				$filename = $item['filename'];
				$lockfile = $filename.'.lock';
				$tmpfilename = $filename.'.'.md5(mt_rand()).'.tmp';

				// Create lock file if not exist
				$lock = @fopen($lockfile, 'a+b');
				@chmod($lockfile, self::$chmod);

				if ( ! $lock)
					continue;


				if ( ! $item['is_file'])
				{
					if (is_file($filename))
					{
						@fclose($lock);
						continue;
					}
				}
				else
				{
					if ($item['mtime'] != @filemtime($filename) OR
					    $item['size'] != @filesize($tmpfilename))
					{
						@fclose($lock);
						continue;
					}
				}

				// Try lock
				if ( ! @flock($lock, LOCK_EX))
				{
					@fclose($lock);
					continue;
				};

				if ($item['is_file'])
				{
					if ( ! @copy($filename, $tmpfilename) OR
					     $item['mtime'] != @filemtime($filename) OR
					     $item['size'] != @filesize($filename))
					{
						@flock($lock, LOCK_UN);
						@fclose($lock);
						continue;
					}
					chmod($tmpfilename, self::$chmod);
				}
				elseif (is_file($filename))
				{
					@flock($lock, LOCK_UN);
					@fclose($lock);
					continue;
				}

				$str = '';

				foreach ($item['files'] as $path)
				{
					$str .= file_get_contents($path)."\n".'?'.'>';
				}

				@file_put_contents($tmpfilename, $str, FILE_APPEND);

				$str = implode("\n", $item['files'])."\n";

				if (@rename($tmpfilename, $filename))
				{
					$success_files++;
					@file_put_contents($filename.'.list', $str, $item['is_file'] ? FILE_APPEND : NULL);
				}
				else
				{
					@unlink($tmpfilename);
				}

				$str = '';

				@flock($lock, LOCK_UN);
				@fclose($lock);

			}
		}
		catch (Exception $e)
		{
			// Do nothing
		}

		self::$_files = NULL;
		self::$_cache = array();

		return $success_files;
	}

} // End Ku_Loader
