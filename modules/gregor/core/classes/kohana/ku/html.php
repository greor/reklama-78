<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML extends HTML {

	/**
	 * Creates and returns a new html object.
	 *
	 * @chainable
	 * @param   string  object name
	 * @param   mixed   constructor arguments
	 * @return  Ku_HTML_Element
	 */
	public static function factory($type, $args = NULL)
	{
		// Set class name
		$class = 'Ku_HTML_'.ucfirst($type);

		if (func_num_args() > 2)
		{
			$reflection = new ReflectionClass($class);
			return $reflection->newInstanceArgs(array_slice(func_get_args(), 1));
		}
		else
		{
			return new $class($args);
		}
	}

	/**
	 * Generate a conditional comments statement
	 *
	 * @param   string   condition
	 * @param   string   content of comments
	 * @param   string   new line symbol
	 * @return  string   safe filename
	 */
	public static function conditional_comments($condition, $content, $new_line = "\n")
	{
		return '<!--[if '.$condition.']>'.$new_line.$content.$new_line.'<![endif]-->';
	}

	/**
	 * Creates a style tag.
	 *
	 * @param   string  style inner HTML
	 * @param   array   default attributes
	 * @param   string   new line symbol
	 * @return  string
	 */
	public static function style_tag($content, array $attributes = NULL, $new_line = "\n")
	{
		// Set the stylesheet type
		$attributes['type'] = 'text/css';

		return '<style'.HTML::attributes($attributes).' />'.$new_line.$content.$new_line.'</style>';
	}

	/**
	 * Creates a script tag.
	 *
	 * @param   string  script inner HTML
	 * @param   array   default attributes
	 * @param   string   new line symbol
	 * @return  string
	 */
	public static function script_tag($content, array $attributes = NULL, $new_line = "\n")
	{
		if ( ! isset($attributes['type']))
		{
			// Set the script type
			$attributes['type'] = 'text/javascript';
		}

		return '<script'.HTML::attributes($attributes).' />'.$new_line.$content.$new_line.'</script>';
	}

	/**
	 * Creates a base tag.
	 *
	 * @param   string  href of base
	 * @param   array   default attributes
	 * @param   string   new line symbol
	 * @return  string
	 */
	public static function base($href, array $attributes = NULL)
	{
		// Set the href
		$attributes['href'] = $href;

		return '<base'.HTML::attributes($attributes).' />';
	}

	/**
	 * Creates a meta tag.
	 *
	 * @param   string  content attrubute
	 * @param   array   default attributes
	 * @param   string   new line symbol
	 * @return  string
	 */
	public static function meta($content, array $attributes = NULL)
	{
		// Set the href
		$attributes['content'] = $content;

		return '<meta'.HTML::attributes($attributes).' />';
	}

} // End Kohana_Ku_HTML