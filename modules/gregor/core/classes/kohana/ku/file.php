<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_File extends File {

	/**
	 * Generate safe name for file
	 *
	 * @param   string   filename
	 * @param   integer  max filename length
	 * @return  string   safe filename
	 * @return  boolean  FALSE, if filename can not be safed
	 */
	public static function safe_name($filename, $remove_spaces = TRUE, $max_length = NULL)
	{
		if ($filename)
		{
			$info = pathinfo($filename);

			$fname = trim($info['filename']);

			if ($remove_spaces === TRUE)
			{
				// Remove spaces and other separators from the filename
				$fname = preg_replace('/[\pZ]+/uD', '_', $fname);
			}

			// Remove any punctuation from filename
			$fname = preg_replace('/[\p{Po}]+/uD', '', $fname);
			$fname = trim($fname);

			// Get the extension from the filename
			$extension = Arr::get($info, 'extension', '');
			$ext_length = strlen($extension);
			$ext_length and ++$ext_length; // Extension length with dot

			if ($max_length !== NULL AND $ext_length > $max_length)
			{
				return FALSE;
			}

			if ($ext_length AND preg_match('/[^a-zA-Z0-9]/', $extension))
			{
				// Extension is invalid
				return FALSE;
			}

			// Transliterate filename
			$fname = UTF8::transliterate_to_ascii($fname);

			// Remove any special characters from filename
			$fname = preg_replace('/[^-a-zA-Z0-9_]+/', '', $fname);

			if ($fname == '')
			{
				// Use sha1 hash as safe name
				$fname = sha1($info['basename']);
			}

			if ($max_length !== NULL AND (strlen($fname) + $ext_length) > $max_length)
			{
				$fname = substr($fname, 0, $max_length - $ext_length);
				if ( ! strlen($fname))
				{
					// Can not truncate filename
					return FALSE;
				}
			}

			$filename = ($info['dirname'] !== '.') ? $info['dirname'].DIRECTORY_SEPARATOR : '';
			$filename .= $fname. ($ext_length ? '.'.$extension : '');
		}

		return $filename;
	}


	/**
	 * Tests if assigned file data is not empty.
	 *
	 *     $validate->callback('file', 'Ku_File::not_empty')
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @return  bool
	 */
	public static function not_empty($file)
	{
		if (is_array($file))
		{
			return Ku_Upload::not_empty($file);
		}

		if (empty($file))
			return FALSE;

		if (basename($file) == $file)
			return TRUE;

		return (bool) is_file($file);
	}

	/**
	 * Tests if assigned file data is valid, even if no file was uploaded.
	 *
	 *     $validate->rule('file', 'Ku_File::valid')
	 *
	 * @param   mixed    $_FILES item or filepath
	 * @return  bool
	 */
	public static function valid($file)
	{
		if (is_array($file))
		{
			return Upload::valid($file);
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		return (bool) is_file($file);
	}

	/**
	 * Test if an file is an allowed file type, by extension.
	 *
	 *     $validate->rule('file', 'Ku_File::type', array('jpg, png, gif'));
	 *     $validate->rule('file', 'Ku_File::type', array('.JPG, .PNG, .GIF'));
	 *
	 * @param   mixed    $_FILES item or filepath
	 * @param   string    allowed file extensions
	 * @return  bool
	 */
	public static function type($file, $allowed)
	{
		if (is_string($allowed))
		{
			$allowed = explode(',', str_replace('.', '', $allowed));
			$allowed = array_map('trim', $allowed);
		}

		$allowed = array_map('strtolower', $allowed);

		if (is_array($file))
		{
			return Ku_Upload::type($file, $allowed);
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

		return in_array($ext, $allowed);
	}

	/**
	 * Validation rule to test if an file is allowed by file size.
	 * File sizes are defined as: SB, where S is the size (1, 15, 300, etc) and
	 * B is the byte modifier: (B)ytes, (K)ilobytes, (M)egabytes, (G)igabytes.
	 *
	 *     // Assigned file must be 1MB or less
	 *     $validate->rule('file', 'Ku_File::size', array('1M'))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   string   maximum file size
	 * @return  bool
	 */
	public static function size($file, $size)
	{
		if (is_array($file))
		{
			return Ku_Upload::size($file, $size);
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		// Only one size is allowed
		$size = strtoupper($size);

		if ( ! preg_match('/[0-9]++[BKMG]/', $size))
			return FALSE;

		// Make the size into a power of 1024
		switch (substr($size, -1))
		{
			case 'G': $size = intval($size) * pow(1024, 3); break;
			case 'M': $size = intval($size) * pow(1024, 2); break;
			case 'K': $size = intval($size) * pow(1024, 1); break;
			default:  $size = intval($size);                break;
		}

		if ( ! is_file($file))
			return FALSE;

		// Test that the file is under or equal to the max size
		return (filesize($file) <= $size);
	}


	/**
	 * Return mime-type group (image, audio, video...)
	 *
	 * @param   mixed    $_FILES item or filepath
	 * @return  string	group
	 */
	public static function mime_group($file)
	{
		$mime = is_array($file)
			? Arr::get($file,'type')
			: self::mime($file);

		$mime = explode('/', $mime, 2);

		return $mime[0];
	}

} // End Kohana_Ku_File