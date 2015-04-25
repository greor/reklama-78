<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Ku_Image extends Image {


	/**
	 * Validation rule to test if an image has specified width.
	 *
	 *     // Assigned file must have width === 300px
	 *     $validate->rule('file', 'Ku_Image::width', array(300))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   integer  Image width
	 * @return  bool
	 */
	public static function width($file, $width)
	{
		if (is_array($file))
		{
			$file = isset($file['persistent']) ? $file['persistent'] : $file['tmp_name'];
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		$image = Image::factory($file);

		// Test that the file is under or equal to the max size
		return ($image->width == $width);
	}

	/**
	 * Validation rule to test if an image has specified width.
	 *
	 *     // Assigned file must have width <= 600px
	 *     $validate->rule('file', 'Ku_Image::max_width', array(600))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   integer  Maximum image width
	 * @return  bool
	 */
	public static function max_width($file, $width)
	{
		if (is_array($file))
		{
			$file = isset($file['persistent']) ? $file['persistent'] : $file['tmp_name'];
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		$image = Image::factory($file);

		// Test that the file is under or equal to the max size
		return ($image->width <= $width);
	}

	/**
	 * Validation rule to test if an image has specified width.
	 *
	 *     // Assigned file must have width >= 100px
	 *     $validate->rule('file', 'Ku_Image::min_width', array(100))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   integer   Minimum image width
	 * @return  bool
	 */
	public static function min_width($file, $width)
	{
		if (empty($file) OR basename($file) == $file)
			return TRUE;

		if (is_array($file))
		{
			$file = isset($file['persistent']) ? $file['persistent'] : $file['tmp_name'];
		}

		$image = Image::factory($file);

		// Test that the file is under or equal to the max size
		return ($image->width >= $width);
	}

	/**
	 * Validation rule to test if an image has specified height.
	 *
	 *     // Assigned file must have height === 300px
	 *     $validate->rule('file', 'Ku_Image::height', array(300))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   integer   Image height
	 * @return  bool
	 */
	public static function height($file, $height)
	{
		if (is_array($file))
		{
			$file = isset($file['persistent']) ? $file['persistent'] : $file['tmp_name'];
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		$image = Image::factory($file);

		return ($image->height == $height);
	}

	/**
	 * Validation rule to test if an image has specified max height.
	 *
	 *     // Assigned file must have height <= 600px
	 *     $validate->rule('file', 'Ku_Image::max_height', array(600))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   integer   Maximum image height
	 * @return  bool
	 */
	public static function max_height($file, $height)
	{
		if (is_array($file))
		{
			$file = isset($file['persistent']) ? $file['persistent'] : $file['tmp_name'];
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		$image = Image::factory($file);

		return ($image->height <= $height);
	}

	/**
	 * Validation rule to test if an image has specified min height.
	 *
	 *     // Assigned file must have height >= 100px
	 *     $validate->rule('file', 'Ku_Image::min_height', array(100))
	 *
	 * @param   mixed    $_FILES item or filepath or filename
	 * @param   integer   Minimum image height
	 * @return  bool
	 */
	public static function min_height($file, $height)
	{
		if (is_array($file))
		{
			$file = isset($file['persistent']) ? $file['persistent'] : $file['tmp_name'];
		}

		if (empty($file) OR basename($file) == $file)
			return TRUE;

		$image = Image::factory($file);

		return ($image->height >= $height);
	}

} // End Kohana_Ku_Image