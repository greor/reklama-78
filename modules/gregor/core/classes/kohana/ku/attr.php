<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Ku_Attr {

	/**
	 * Convert attrubute to array.
	 *
	 * @param mixed string or array
	 * @param string Delimiter of attributes in the string
	 * @param boolean Normalize attribute (trim and remove duplicate elements)
	 * @return  array
	 */
	public static function to_array($attr, $delimiter = ' ', $normalize = FALSE)
	{
		$result = array();
		if ( ! empty($attr))
		{
			$result = is_array($attr) ? $attr : explode($delimiter, $attr);

			if ($normalize)
			{
				$tmp = array();
				foreach ($result as $item)
				{
					$item = trim(strtr($str, $delimiter, ' '));
					$item and $tmp[] = $item;
				}
				$result = array_unique($tmp);
			}
		}
		return $result;
	}

	/**
	 * Convert attribute to string.
	 *
	 * @param mixed string or array
	 * @param string Delimiter of attributes in the string
	 * @param boolean Normalize attribute (trim and remove duplicate elements)
	 * @return  string
	 */
	public static function to_string($attr, $delimiter = ' ', $normalize = FALSE)
	{
		$result = '';
		if ($normalize)
		{
			$attr = self::to_array($attr, TRUE);
			$result = implode($delimiter, $attr);
		}
		elseif (is_array($attr))
		{
			$result = implode($delimiter, $attr);
		}
		else
		{
			$result = $attr;
		}

		return (string) $result;
	}

	/**
	 * Summary 2 attrubute values.
	 *
	 * @param mixed string or array
	 * @param mixed string or array
	 * @param string Delimiter of attributes in the string
	 * @param boolean Normalize attribute (trim and remove duplicate elements)
	 * @return  array
	 */
	public static function sum($attr1, $attr2, $delimiter = ' ', $normalize = FALSE)
	{
		is_array($attr1) or $attr1 = self::to_array($attr1, $delimiter, $normalize);
		is_array($attr2) or $attr2 = self::to_array($attr2, $delimiter, $normalize);

		return array_merge($attr1, $attr2);
	}

	/**
	 * Different 2 attrubute values.
	 *
	 * @param mixed string or array
	 * @param mixed string or array
	 * @param string Delimiter of attributes in the string
	 * @param boolean Normalize attribute (trim and remove duplicate elements)
	 * @return  array
	 */
	public static function diff($attr1, $attr2, $delimiter = ' ', $normalize = FALSE)
	{
		is_array($attr1) or $attr1 = self::to_array($attr1, $delimiter, $normalize);
		is_array($attr2) or $attr2 = self::to_array($attr2, $delimiter, $normalize);

		return array_diff($attr1, $attr2);
	}

	/**
	 * Convert class attrubute to array.
	 *
	 * @param mixed string or array
	 * @param boolean Normalize class (trim and remove duplicate classes)
	 * @return  array
	 */
	public static function class_to_array($class, $normalize = FALSE)
	{
		$result = array();
		if ( ! empty($class))
		{
			$result = is_array($class) ? $class : explode(' ', $class);

			if ($normalize)
			{
				$result = array_map('trim', $result);
				$result = array_filter($result, 'strlen');
				$result = array_unique($result);
			}
		}
		return $result;
	}

	/**
	 * Convert class attrubute to string.
	 *
	 * @param mixed string or array
	 * @param boolean Normalize class (trim and remove duplicate classes)
	 * @return  string
	 */
	public static function class_to_string($class, $normalize = FALSE)
	{
		$result = '';
		if ($normalize)
		{
			$class = self::class_to_array($class, TRUE);
			$result = implode(' ', $class);
		}
		elseif (is_array($class))
		{
			$result = implode(' ', $class);
		}
		else
		{
			$result = $class;
		}

		return (string) $result;
	}

	/**
	 * Summary class attrubutes.
	 *
	 * @param mixed string or array
	 * @param mixed string or array
	 * @param boolean Normalize class (trim and remove duplicate classes)
	 * @return  array
	 */
	public static function class_sum($class1, $class2, $normalize = FALSE)
	{
		is_array($class1) or $class1 = self::class_to_array($class1);
		is_array($class2) or $class2 = self::class_to_array($class2);

		$result = array_merge($class1, $class2);

		if ($normalize)
		{
			$result = array_map('trim', $result);
			$result = array_filter($result, 'strlen');
			$result = array_unique($result);
		}
		return $result;
	}

	/**
	 * Different class attrubutes.
	 *
	 * @param mixed string or array
	 * @param mixed string or array
	 * @param boolean Normalize class (trim and remove duplicate classes)
	 * @return  array
	 */
	public static function class_diff($class1, $class2, $normalize = FALSE)
	{
		is_array($class1) or $class1 = self::class_to_array($class1);
		is_array($class2) or $class2 = self::class_to_array($class2);

		$result = array_diff($class1, $class2);

		if ($normalize)
		{
			$result = array_map('trim', $result);
			$result = array_unique($result);
		}
		return $result;
	}


	/**
	 * Convert style attrubute to array.
	 *
	 * @param mixed string or array
	 * @param boolean Normalize style (trim and lowercase)
	 * @return  array
	 */
	public static function style_to_array($style, $normalize = FALSE)
	{
		$result = array();
		if ( ! empty($style))
		{
			if ( ! is_array($style))
			{
				$style = explode(';', $style);
				foreach ($style as $pair)
				{
					$pair = trim($pair);
					if ($pair)
					{
						list($name, $value) = (explode(':', $pair) + array('',''));
						if ($normalize)
						{
							$name = strtolower(trim($name));
							$value = strtolower(trim($value));
						}
						$name and $result[$name] = $value;
					}
				}
			}
			elseif($normalize)
			{
				foreach ($style as $name => $value)
				{
					$name = strtolower(trim(strtr($name, ':', ' ')));
					var_dump($name);
					$name and $result[$name] = strtolower(trim(strtr($value, ';', ' ')));
				}
			}
			else
			{
				$result = $style;
			}
		}
		return $result;
	}

	/**
	 * Convert style attrubute to string.
	 *
	 * @param mixed string or array
	 * @param boolean Normalize style (trim and lowercase)
	 * @return  string
	 */
	public static function style_to_string($style, $normalize = FALSE)
	{
		$result = '';
		if ( ! empty($style))
		{
			if (is_array($style))
			{
				foreach ($style as $key => $value)
				{
					if ($normalize)
					{
						$key = strtolower(trim(strtr($key, ':', ' ')));
						$key and $result .= $key.': '.strtolower(trim(strtr($value, ';', ' '))).'; ';
					}
					else
					{
						$result .= $key.': '.$value.'; ';
					}
				}
				$result = rtrim($result);
			}
			elseif ($normalize)
			{
				$style = self::style_to_array($style, TRUE);
				foreach ($style as $key => $value)
				{
					$result .= $key.': '.$value.'; ';
				}
				$result = rtrim($result);
			}
			else
			{
				$result = $style;
			}
		}

		return $result;
	}

	/**
	 * Summary style attrubutes.
	 *
	 * @param mixed string or array
	 * @param mixed string or array
	 * @param boolean Normalize style (trim and lowercase)
	 * @return  array
	 */
	public static function style_sum($style1, $style2, $normalize = FALSE)
	{
		is_array($style1) or $style1 = self::style_to_array($style1, $normalize);
		is_array($style2) or $style2 = self::style_to_array($style2, $normalize);

		return array_merge($style1, $style2);
	}

	/**
	 * Different style attrubutes.
	 *
	 * @param mixed string or array
	 * @param mixed string or array
	 * @param boolean Normalize style (trim and lowercase)
	 * @return  string
	 */
	public static function style_diff($style1, $style2, $normalize = FALSE)
	{
		is_array($style1) or $style1 = self::style_to_array($style1, $normalize);
		is_array($style2) or $style2 = self::style_to_array($style2, $normalize);

		return array_diff_key($style1, $style2);
	}

	/**
	 * Creates an ArrayObject from the passed
	 * array of attrubutes.
	 *
	 * @param   array   array of attrubutes
	 * @return  void
	 */
	public function __construct(array $array)
	{
		parent::__construct($array, ArrayObject::ARRAY_AS_PROPS | ArrayObject::STD_PROP_LIST);
	}

	/**
	 * Magic __call method.
	 * Sets or returns attribute.
	 *
	 * @chainable
	 * @param   string   property name
	 * @param   string   property value
	 * @return  mixed
	 */
	public function __call($method, $args)
	{
		if (count($args))
		{
			$this[$method] = $args[0];
		}
		elseif (isset($this[$method]))
		{
			return $this[$method];
		}
		// Return this object
		return $this;
	}

	/**
	 * Set attribute value.
	 *
	 * @chainable
	 * @param string $name The attrubute name
	 * @param string $value The attribute value
	 * @return object Ku_Attr $this object
	 */
	public function set($name, $value)
	{
		if ($name[0] === '+')
		{
			$this->add(substr($name, 1), $value);
		}
		elseif ($name[0] === '-')
		{
			$this->remove(substr($name, 1), $value);
		}
		else
		{
			$this[$name] = $value;
		}
		// Return this object
		return $this;
	}

	/**
	 * Add attribute value to the current value.
	 *
	 * @chainable
	 * @param string $name The attrubute name
	 * @param string $value The attribute value
	 * @return object Ku_Attr $this object
	 */
	public function add($name, $value)
	{
		if (func_num_args() > 2)
		{
			$args = func_get_args();
			$value = array_slice($args, 2);
		}
		if (isset($this[$name]))
		{
			if ($name === 'class')
			{
				$this['class'] = Ku_Attr::class_sum($this['class'], $value, TRUE);
			}
			elseif($name === 'style')
			{
				$this['style'] = Ku_Attr::style_sum($this['style'], $value, TRUE);
			}
			else
			{
				$this[$name] = Ku_Attr::sum($this[$name], $value, ' ', TRUE);
			}
		}
		else
		{
			$this[$name] = $value;
		}
		// Return this object
		return $this;
	}

	/**
	 * Remove attribute value from the current value.
	 *
	 * @chainable
	 * @param string $name The attrubute name
	 * @param string $value The attribute value
	 * @return object Ku_Attr $this object
	 */
	public function remove($name, $value = NULL)
	{
		if (func_num_args() > 2)
		{
			$args = func_get_args();
			$value = array_slice($args, 2);
		}
		if (isset($this[$name]) AND $value !== NULL)
		{
			if ($name === 'class')
			{
				$this['class'] = Ku_Attr::class_diff($this['class'], $value, TRUE);
			}
			elseif($name === 'style')
			{
				$this['style'] = Ku_Attr::style_diff($this['style'], $value, TRUE);
			}
			else
			{
				$this[$name] = Ku_Attr::diff($this[$name], $value, ' ', TRUE);
			}
		}
		else
		{
			unset($this[$name]);
		}
		// Return this object
		return $this;
	}

	/**
	 * Set a class name to the class attribute.
	 * This method automatically handles multiple class names.
	 *
	 * @chainable
	 * @param string $value The class name to add to the class attribute
	 * @return object Ku_Attr $this object
	 */
	public function add_class($value)
	{
		(func_num_args() > 1) and $value = func_get_args();

		$this->add('class', $value);
		// Return this object
		return $this;
	}

	/**
	 * Remove a class name from the class attribute.
	 * This method automatically handles multiple class names.
	 *
	 * @chainable
	 * @param string $value The class name to remove from the class attribute
	 * @return object Ku_Attr $this object
	 */
	public function remove_class($value = NULL)
	{
		(func_num_args() > 1) and $value = func_get_args();

		$this->remove('class', $value);
		// Return this object
		return $this;
	}

	/**
	 * Add style to the style attribute.
	 *
	 * @chainable
	 * @param mixed $value The string or array style for adding
	 * @return object Ku_Attr $this object
	 */
	public function add_style($value)
	{
		(func_num_args() > 1) and $value = func_get_args();

		$this->add('style', $value);
		// Return this object
		return $this;
	}

	/**
	 * Remove a style from the style attribute.
	 *
	 * @chainable
	 * @param mixed $value The string or array style for remove
	 * @return object Ku_Attr $this object
	 */
	public function remove_style($value = NULL)
	{
		(func_num_args() > 1) and $value = func_get_args();

		$this->remove('style', $value);
		// Return this object
		return $this;
	}

	/**
	 * Returns the HTML attribute array.
	 *
	 * @param  array New attributes array
	 * @return object Ku_Attr $this object
	 */
	public function load(array $array)
	{
		$this->exchangeArray((array) $array);
		// Return this object
		return $this;
	}

	/**
	 * Returns the attributes array.
	 *
	 * @param  boolean Prepare flag. If TRUE
	 *   empty attributes will be removed,
	 *   array attributes will be converted to string.
	 * @return  array
	 */
	public function as_array($prepare = TRUE)
	{
		return $prepare ? $this->_make() : $this->getArrayCopy();
	}

	/**
	 * Returns the HTML attribute string.
	 * Empty attributes will be removed.
	 * Array attributes will be converted to string.
	 *
	 * @return  string
	 */
	public function render()
	{
		$attr = $this->_make();
		return html::attributes($attr);
	}

	public function __toString()
	{
		// Default render header
		return $this->render();
	}

	protected function _make()
	{
		$data = $this->getArrayCopy();

		// Generate class attribute
		isset($data['class']) and $data['class'] = Ku_Attr::class_to_string($data['class'], TRUE);
		// Generate style attribute
		isset($data['style']) and $data['style'] = Ku_Attr::style_to_string($data['style'], TRUE);

		$result = array();
		foreach ($data as $key => $value)
		{
			// Convert array values to string
			is_array($value) and $value = Ku_Attr::to_string($value, ' ', TRUE);
			// Skip empty attributes
			($value !== NULL AND $value !== '') and $result[$key] = $value;
		}

		return $result;
	}

} // End Kohana_Ku_Attr class