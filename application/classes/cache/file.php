<?php defined('SYSPATH') or die('No direct script access.');

class Cache_File extends Kohana_Cache_File
{
	private $_delete_flag_path = 'upload/.clear_cache.flg';

	/**
	 * Delete all cache entries.
	 *
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 *
	 *     // Delete all cache entries in the file group
	 *     Cache::instance('file')->delete_all();
	 *
	 * @return  boolean
	 */
	public function delete_all($set_flag = FALSE)
	{
		if ($set_flag)
		{
			$this->_set_delete_flag();
		}

		return parent::delete_all();
	}

	private function _set_delete_flag()
	{
		$this->_delete_flag_path = str_replace( '/' , DIRECTORY_SEPARATOR, DOCROOT.$this->_delete_flag_path);

		$file_array = array();
		if (file_exists($this->_delete_flag_path))
		{
			$file_array = file($this->_delete_flag_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		}

		$directory = $this->_cache_dir->getFilename();
		if ( ! in_array($directory, $file_array))
		{
			$file_array[] = $directory;
		}

		$fs = fopen($this->_delete_flag_path, 'w');
		fwrite( $fs, implode("\r\n", $file_array) );
		fclose($fs);
	}
}