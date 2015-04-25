<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_Text extends Text {

	/**
	 * Converts a slug to value valid for a URL.
	 *
	 * We could validate it by setting a rule, but for the most part, who cares?
	 *
	 * @param   mixed  $value
	 * @return  mixed
	 */
	public static function slug($value)
	{
		$value = UTF8::transliterate_to_ascii($value);

		// Only allow slashes, dashes, and lowercase letters
		$value = preg_replace('/[^a-z0-9-\/]/', '-', strtolower($value));

		// Strip multiple dashes
		$value = preg_replace('/-{2,}/', '-', $value);

		// Trim an ending or starting dashes
		$value = trim($value, '-');

		return $value;
	}

} // End Kohana_Ku_Text