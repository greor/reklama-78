<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_I18n extends I18n {

	/**
	 * Get a i18n message from a file. Messages are arbitary strings that are stored
	 * in the messages/i18/en-en, messages/i18/ru-ru directories and reference by a key.
	 *
	 *     // Get "username" from messages/i18n/en-en/text.php
	 *     $username = Ku_I18n::message('text', 'username');
	 *
	 * @uses  Kohana::message
	 *
	 * @param   string|array  file name
	 * @param   string  key path to get
	 * @param   string  default message
	 * @param   array   array values to substititution
	 * @return  string  message string for the given path
	 * @return  array   complete message list, when no path is specified
	 */
	public static function message($file, $path = NULL, $default = NULL, array $values = NULL)
	{
		$file = (array) $file;

		foreach ($file as $filename)
		{
			$message = Kohana::message('i18n/'.I18n::$lang.'/'.$filename, $path);

			if ($message === NULL)
			{
				// Try common message
				$message = Kohana::message($filename, $path);
				if ($message)
				{
					$message = I18n::get($message);
					break;
				}
			}
			else
			{
				break;
			}
		}

		if ($message === NULL)
		{
			// Try common message
			$message = is_string($default) ? I18n::get($default) : $default;
		}

		if ($message AND $values)
		{
			$message = strtr($message, $values);
		}

		return $message;
	}


	/**
	 * Perform key transformation
	 *
	 *     $array = array('foo' => 'bla-bla', 'bar' => 'bla-bla-bla');
	 *     $array = Ku_I18n::transform_keys($array, '%key%', 'strtoupper');
	 *     // Result: array('%FOO%' => 'bla-bla', '%BAR%' => 'bla-bla-bla')
	 *
	 * @param   array    assoc array
	 * @param   string   template for key transformation: key position marked as 'key'
	 * @param   string   name of key transform function such as 'ucfirst', 'strtoupper', 'strtolower'
	 * @return  array
	 */
	public static function transform_keys(array $array, $key_template, $transform_function = NULL)
	{
		$result = array();

		if ($transform_function)
		{
			foreach ($array as $key => $value)
			{
				$result[str_replace('key', $transform_function($key), $key_template)] = $value;
			}
		}
		else
		{
			foreach ($array as $key => $value)
			{
				$result[str_replace('key', $key, $key_template)] = $value;
			}
		}

		return $result;
	}


	/**
	 * Returns short lang
	 *
	 * @param   string   full lang key
	 * @return  string
	 */
	public static function short_lang($lang = NULL)
	{
		return substr($lang === NULL ? Ku_I18n::$lang : $lang, 0, 2);
	}

	/**
	 * Returns the error messages. If no file is specified, the error message
	 * will be the name of the rule that failed. When a file is specified, the
	 * message will be loaded from "field/rule", or if no rule-specific message
	 * exists, "field/default" will be used. If neither is set, the returned
	 * message will be "file/field/rule".
	 *
	 * By default all messages are translated using the default language.
	 * A string can be used as the second parameter to specified the language
	 * that the message was written in.
	 *
	 *     // Get errors from messages/forms/login.php
	 *     $errors = $validate->errors('forms/login');
	 *
	 * @uses    Kohana::message
	 * @param   Validation  validation object
	 * @param   string  file to load error messages from
	 * @param   mixed   translate the message
	 * @return  array
	 */
	public static function errors(Validation $array, $file = NULL, $translate = TRUE)
	{
		if ($file === NULL)
		{
			// Return the error list
			return $array->errors();
		}

		// Create a new message list
		$messages = array();

		foreach ($array->errors() as $field => $set)
		{
			list($error, $params) = $set;

			// Translate the label
			$label = __(Inflector::humanize($field));
			$label = Ku_I18n::message($file, "{$field}.label", $label);

			// Start the translation values list
			$values = array(':field' => $label);

			// Start the translation values list
			$values = array(
					':field' => $label,
					':value' => Arr::get($array, $field),
			);

			if (is_array($values[':value']))
			{
				// All values must be strings
				$values[':value'] = implode(', ', Arr::flatten($values[':value']));
			}

			if ($params)
			{
				foreach ($params as $key => $value)
				{
					if (is_array($value))
					{
						// All values must be strings
						$value = implode(', ', Arr::flatten($value));
					}
					elseif (is_object($value))
					{
						// Objects cannot be used in message files
						continue;
					}

					/*
					// Check if a label for this parameter exists
					if (isset($this->_labels[$value]))
					{
						// Use the label as the value, eg: related field name for "matches"
						$value = $this->_labels[$value];

						if ($translate)
						{
							if (is_string($translate))
							{
								// Translate the value using the specified language
								$value = __($value, NULL, $translate);
							}
							else
							{
								// Translate the value
								$value = __($value);
							}
						}
					}
					*/

					// Add each parameter as a numbered value, starting from 1
					$values[':param'.($key + 1)] = $value;
				}
			}

			if ($message = Ku_I18n::message($file, "{$field}.{$error}", NULL, $values))
			{
				// Found a message for this field and error
			}
			elseif ($message = Ku_I18n::message($file, "{$field}.default", NULL, $values))
			{
				// Found a default message for this field
			}
			elseif ($message = Ku_I18n::message($file, $error, NULL, $values))
			{
				// Found a default message for this error
			}
			elseif ($message = Ku_I18n::message('validation', $error, NULL, $values))
			{
				// Found a default message for this error
			}
			else
			{
				// No message exists, display the path expected
				$message = "{$file}.{$field}.{$error}";
			}

			// Set the message for this field
			$messages[$field] = $message;
		}

		return $messages;
	}

	/**
	 * Translate month and weekday names in the string
	 *
	 *
	 * @uses    I18n::load
	 * @uses    I18n::$lang
	 * @param   string  string to translate
	 * @return  string
	 */
	public static function date($string)
	{
		static $translate = array();

		if (empty($string))
		{
			return $string;
		}

		$string = (string) $string;

		// Create a new message list
		$messages = array();

		if ( ! isset($translate[I18n::$lang]))
		{
			$translate[I18n::$lang] = array_intersect_key(I18n::load(I18n::$lang), Kohana::message('date'));
		}

		return strtr($string, $translate[I18n::$lang]);
	}


} // End Kohana_Ku_I18n