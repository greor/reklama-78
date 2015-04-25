<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Ku_Upload extends Upload {

	/**
	 * @var  string  default temp upload directory
	 */
	public static $default_temp_directory = 'upload/temp';

	/**
	 * @var  integer max allowed length of filename
	 */
	public static $max_filename_length;

	/**
	 * @var  integer default file chmod
	 */
	public static $default_file_chmod = 0666;

	/**
	 * @var  integer default directiory chmod
	 */
	public static $default_dir_chmod = 0777;

	/**
	 * @var  string  default temp upload directory for persitent files
	 */
	public static $persistent_temp_directory = 'upload/persistent';

	/**
	 * @var  integer uploaded persistent file lifetime
	 */
	public static $persistent_lifetime = 3600;

	/**
	 * @var  integer uploaded persistent files garbage collector probability
	 */
	public static $persistent_gc_probability = 5;

	/**
	 * Save an uploaded file to a new location.
	 *
	 * @param   array    uploaded file data
	 * @param   string   new filename
	 * @param   string   new directory
	 * @param   integer  chmod mask
	 * @param   boolean  use "copy" unstead "rename" command for persistent files
	 * @return  string   on success, full path to new file
	 * @return  FALSE    on failure
	 */
	public static function save(array $file, $filename = NULL, $directory = NULL, $chmod = NULL, $copy = FALSE)
	{
		if ($filename === NULL)
		{
			// Use the default filename, with a unique ID pre-pended
			$filename = uniqid().'_'.$file['name'];
		}

		($chmod === NULL) and $chmod = Ku_Upload::$default_file_chmod;

		$filename = Ku_File::safe_name($filename, Ku_Upload::$remove_spaces, Ku_Upload::$max_filename_length);

		if ($filename === FALSE)
			return FALSE;

		if (isset($file['persistent']))
		{
			if ($directory === NULL)
			{
				// Use the pre-configured upload directory
				$directory = Ku_Upload::$persistent_temp_directory;
			}

			if ( ! is_dir($directory) OR ! is_writable(realpath($directory)))
			{
				throw new Kohana_Exception('Directory :dir must be writable',
					array(':dir' => Debug::path($directory)));
			}

			// Make the filename into a complete path
			$filename = realpath($directory).DIRECTORY_SEPARATOR.$filename;

			if (file_exists($file['persistent']) AND is_file($file['persistent']))
			{
				if ($copy === TRUE)
				{
					$result = copy($file['persistent'], $filename);
				}
				else
				{
					$result = rename($file['persistent'], $filename);
					Ku_Upload::persistent_delete($file);
				}

				if ($result)
				{
					if ($chmod !== FALSE)
					{
						// Set permissions on filename
						chmod($filename, $chmod);
					}
					// Return new file path
					return $filename;
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				Ku_Upload::persistent_delete($file);
				return FALSE;
			}
		}
		else
		{
			return parent::save($file, $filename, $directory, $chmod);
		}
	}


	/* Validation Rules */

	/**
	 * Tests if input data has valid upload data.
	 *
	 * @param   array    $_FILES item
	 * @return  bool
	 */
	public static function not_empty(array $file)
	{
		if (isset($file['persistent']))
		{
			return (isset($file['tmp_name'])
				AND isset($file['error'])
				AND is_file($file['persistent'])
				AND file_exists($file['persistent'])
				AND (int) $file['error'] === UPLOAD_ERR_OK);
		}
		else
		{
			return parent::not_empty($file);
		}
	}

	/* Persistent helpers */

	/**
	 * Save an uploaded file to a temporary persistent directory.
	 *
	 * @param   array    uploaded file data
	 * @param   string   temp directory for persistent uploaded files
	 * @param   integer  chmod mask
	 * @param   string   session key for persistent uploaded files
	 * @param   string   path to saved file (useful if move_uploaded_file() already was called)
	 * @return  boolean  TRUE if file temporary saved, FALSE if file not saved
	 */
	public static function persistent_save(array & $file, $directory = NULL, $chmod = NULL, $sess_key = NULL, $saved_file = NULL)
	{
		static $cache;

		$input_name = Arr::get($file, 'input_name');

		if ($input_name === NULL)
		{
			return FALSE;
		}

		if ($cache === NULL)
		{
			$cache = array();
		}
		elseif (isset($cache[$input_name]))
		{
			// This file already saved
			return FALSE;
		}

		$cache[$input_name] = TRUE;

		$valid_file = FALSE;

		if ($saved_file === NULL)
		{
			$valid_file = parent::not_empty($file);
		}
		else
		{
			$valid_file = (isset($file['error'])
				AND isset($file['tmp_name'])
				AND $file['error'] === UPLOAD_ERR_OK
				AND is_string($saved_file)
				AND is_file($saved_file)
				AND filesize($saved_file) == $file['size']
			);
		}

		if ($valid_file)
		{
			// Use the default temp filename, with a prefix and a timestamp and uniquid prepended
			$filename = 'persistent~'.time().'~'.uniqid().'_'.basename($file['tmp_name']);

			if ($chmod === NULL)
			{
				// Use the pre-configured chmod value
				$chmod = Ku_Upload::$default_file_chmod;
			}

			if ($directory === NULL)
			{
				// Use the pre-configured persistent temp upload directory
				$directory = Ku_Upload::$persistent_temp_directory;
			}

			if ( ! is_dir($directory) OR ! is_writable(realpath($directory)))
			{
				throw new Kohana_Exception('Directory :dir must be writable',
					array(':dir' => Debug::path($directory)));
			}

			// Try save file to persistent directory
			$file_path = NULL;
			if ($saved_file === NULL)
			{
				$file_path = parent::save($file, $filename, $directory, $chmod);
			}
			else if (rename($saved_file, $directory.'/'.$filename))
			{
				$file_path = $directory.'/'.$filename;
				chmod($file_path, $chmod);

			}

			if ($file_path)
			{
				($sess_key === NULL) and $sess_key = Ku_Upload::persistent_key($input_name);
				$sess_file = $file;
				$sess_file['persistent'] = $file_path; // Remember full file path
				$sess_file['persistent_key'] = $sess_key; // Remember session key

				// Get all of the session data as an array
				$_SESSION =& Session::instance()->as_array();

				// Save information about temporary saved file in the session
				$_SESSION['persistents'][$sess_key] = $sess_file;

				// Update file
				$file = $sess_file;
				// Update $_FILES
				$_FILES[$input_name] = $sess_file;

				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Check if file has been saved to a temporary persistent directory.
	 *
	 * @param   array    uploaded file data
	 * @param   string   session key for persistent uploaded files
	 * @return  boolean  TRUE if file temporary saved, FALSE if file not saved
	 */
	public static function persistent_check(array & $file, $sess_key = NULL)
	{
		static $cache = array();

		$input_name = Arr::get($file, 'input_name');

		if (isset($cache[$input_name]))
		{
			return FALSE;
		}

		$cache[$input_name] = TRUE;

		($sess_key === NULL) and $sess_key = Ku_Upload::persistent_key($input_name);
		$sess_file = NULL;

		// Get all of the session data as an array
		$_SESSION =& Session::instance()->as_array();

		if ( ! empty($_SESSION['persistents']) AND
		     is_array($_SESSION['persistents']) AND
		     isset($_SESSION['persistents'][$sess_key]))
		{
			$sess_file = $_SESSION['persistents'][$sess_key];
		}

		if ($sess_file)
		{
			if (parent::valid($file) AND ! parent::not_empty($file))
			{
				if (isset($sess_file['persistent'])
				    AND isset($sess_file['persistent_key'])
				    AND $sess_file['persistent_key'] === $sess_key)
				{
					$filename = $sess_file['persistent'];

					// Make sure the directory ends with a slash
					$directory = dirname($filename).DIRECTORY_SEPARATOR;

					// Delete old persistent files from this directory
					Ku_Upload::persistent_gc($directory);

					// Refresh timestamp in the file name
					$new_filename = preg_replace('/persistent~\d+~/', 'persistent~'.time().'~', $filename);

					if (file_exists($filename) AND is_file($filename) AND rename($filename, $new_filename))
					{
						$sess_file['persistent'] = $new_filename;
						// Save information about temporary saved file in the session
						$_SESSION['persistents'][$sess_key] = $sess_file;
						// Update file
						$file = $sess_file;
						// Update $_FILES
						$_FILES[$input_name] = $sess_file;

						return TRUE;
					}
				}
			}

			Ku_Upload::persistent_delete($sess_file);
		}
		return FALSE;
	}

	/**
	 * Delete persistent file.
	 *
	 * @param   array    uploaded file data
	 * @return  void
	 */
	public static function persistent_delete(array & $file)
	{
		if (isset($file['persistent']))
		{
			if (file_exists($file['persistent']) AND is_file($file['persistent']))
			{
				unlink($file['persistent']);
			}
			unset($file['persistent']);
		}

		if (isset($file['persistent_key']))
		{
			// Get all of the session data as an array
			$_SESSION =& Session::instance()->as_array();

			unset($_SESSION['persistents'][$file['persistent_key']]);
			unset($file['persistent_key']);
		}

		if (isset($file['input_name']))
		{
			if (isset($_FILES[$file['input_name']]))
			{
				$f = & $_FILES[$file['input_name']];
				unset($f['persistent'], $f['persistent_key']);
			}
		}
	}

	/**
	 * Delete old files from temporary persistent directory.
	 *
	 * @param   string  directory name
	 * @return  void
	 */
	public static function persistent_gc($dir)
	{
		static $cache = array();

		// Make sure the directory ends with a slash
		$dir = realpath($dir).DIRECTORY_SEPARATOR;

		if ( ! is_dir($dir)) return;

		if (isset($cache[$dir])) return;

		$cache[$dir] = TRUE;

		if (mt_rand(0, 100) > Ku_Upload::$persistent_gc_probability) return;

		$min_ts = time() - Ku_Upload::$persistent_lifetime;

		$list = glob($dir.'persistent~??????????~*', GLOB_NOSORT);
		if ($list)
		{
			foreach ($list as $file)
			{
				$ts = (int) substr(basename($file), 11, 10);
				if ($ts < $min_ts)
				{
					unlink($file);
				}
			}
		}

		// Get all of the session data as an array
		$_SESSION =& Session::instance()->as_array();

		// Clear session from broken files
		if (isset($_SESSION['persistents']) AND is_array($_SESSION['persistents']))
		{
			$remove = array();
			foreach ($_SESSION['persistents'] as $key => $value)
			{
				if ( ! file_exists($value['persistent']))
				{
					$remove[$key] = TRUE;
				}
			}

			$_SESSION['persistents'] = array_diff_key($_SESSION['persistents'], $remove);
		}
	}

	/**
	 * Get key for persistent save info about uploaded file in the session.
	 *
	 * @param   string   name of $_FILE input
	 * @return  string   key for persistent save info about uploaded file in the session
	 */
	public static function persistent_key($file)
	{
		return 'persist~'.sha1(Request::instance()->uri.$file);
	}

	/**
	 * Initialize $_FILES array to use persistent files.
	 *
	 * @return  void
	 */
	public static function persistent_init()
	{
		static $init;

		if ($init !== TRUE)
		{
			foreach ($_FILES as $key => $value)
			{
				if (is_string($value['name']))
				{
					// File name is not array like my_file[]...
					$_FILES[$key]['input_name'] = $key;
				}
			}
			$init = TRUE;
		}
	}

} // End Kohana_Ku_Upload class

