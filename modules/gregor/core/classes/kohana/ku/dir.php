<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_Dir {

	/**
	 * @var  integer default directiory chmod
	 */
	public static $default_dir_chmod = 0777;

	/**
	 * This function will recursively delete all files in the given path, without
	 * following symlinks.
	 *
	 * @param   string  path
	 * @return  void
	 * @throw   Kohana_Exception
	 */
	public static function remove($path)
	{
		try
		{
			// Constructs a new directory iterator from a path
			$dir = new DirectoryIterator($path);

			foreach ($dir as $fileinfo)
			{
				// Determine if current DirectoryIterator item is a regular file or symbolic link
				if ($fileinfo->isFile() OR $fileinfo->isLink())
				{
					// Deletes a file
					unlink($fileinfo->getPathName());
				}

				// Determine if current DirectoryIterator item is not '.' or '..' and is a directory
				elseif ( ! $fileinfo->isDot() AND $fileinfo->isDir())
				{
					// Recursion
					Ku_Dir::remove($fileinfo->getPathName());
				}
			}

			// Removes directory
			rmdir($path);
		}

		catch (Exception $e)
		{
			throw new Kohana_Exception('Could not remove :path directory',
				array(':path' => Debug::path($path)));
		}
	}

	/**
	 * This function will recursively create directories in the given path.
	 *
	 * @param   string  path
	 * @return  void
	 * @throw   Kohana_Exception
	 */
	public static function make($path, $chmod = NULL)
	{
		try
		{
			$dir = new SplFileInfo($path);

			if ($dir->isFile())
			{
				throw new Kohana_Exception('Could not make :path directory because it is regular file',
					array(':path' => Debug::path($path)));
			}
			elseif ($dir->isLink())
			{
				throw new Kohana_Exception('Could not make :path directory because it is link',
					array(':path' => Debug::path($path)));
			}
			elseif ( ! $dir->isDir())
			{
				mkdir($path, ($chmod === NULL ? Ku_Dir::$default_dir_chmod : $chmod), TRUE);

				// Check result
				if ( ! $dir->isDir())
				{
					throw new Exception('Make dir failed', 0);
				}
			}
		}

		catch (Kohana_Exception $e)
		{
			// Rethrow exception
			throw $e;
		}

		catch (Exception $e)
		{
			throw new Kohana_Exception('Could not make :path directory',
				array(':path' => Debug::path($path)));
		}
	}

	/**
	 * This function will make directory writable.
	 *
	 * @param   string  path
	 * @return  void
	 * @throw   Kohana_Exception
	 */
	public static function make_writable($path, $chmod = NULL)
	{
		try
		{
			$dir = new SplFileInfo($path);

			if ($dir->isFile())
			{
				throw new Kohana_Exception('Could not make :path writable directory because it is regular file',
					array(':path' => Debug::path($path)));
			}
			elseif ($dir->isLink())
			{
				throw new Kohana_Exception('Could not make :path writable directory because it is link',
					array(':path' => Debug::path($path)));
			}
			elseif ( ! $dir->isDir())
			{
				// Try create directory
				Ku_Dir::make($path, $chmod);
			}

			if ( ! $dir->isWritable())
			{
				// Try make directory writable
				chmod($dir->getRealPath(), ($chmod === NULL ? Ku_Dir::$default_dir_chmod : $chmod));

				// Check result
				if ( ! $dir->isWritable())
				{
					throw new Exception('Make dir writable failed', 0);
				}
			}
		}

		catch (Kohana_Exception $e)
		{
			// Rethrow exception
			throw $e;
		}

		catch (Exception $e)
		{
			throw new Kohana_Exception('Could not make :path directory writable',
				array(':path' => Debug::path($path)));
		}
	}

	final private function __construct()
	{
		// This is a static class
	}

} // End Kohana_Ku_Dir