<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Ku_HTML_Element extends ArrayObject {

	// HTML tag
	protected $_tag;

	protected $_empty = TRUE;
	protected $_short = TRUE;

	protected $_html;
	protected $_cdata;

	// Condition string for conditional comments
	protected $_condition;

	// List of required properties
	protected $_required;

	// List of read only properties
	protected $_readonly;

	// List of ignored properties
	protected $_ignored;

	/**
	 * Creates and returns a new html object.
	 *
	 * @chainable
	 * @param   string  object name
	 * @param   array   additional attributes
	 * @return  Ku_HTML_Element
	 */
	public static function factory($type, array $attributes = NULL)
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
			return new $class($attributes);
		}
	}

	/**
	 * Prepares the model database connection and loads the object.
	 *
	 * @param   array   attributes of html element
	 * @return  void
	 */
	public function __construct(array $attributes = NULL)
	{
		if ($this->_required)
		{
			foreach ($this->_required as $attribute)
			{
				if ( ! isset($attributes[$attribute]))
				{
					throw new Kohana_Exception('Attribute ":attribute" must be specified',
						array(':attribute' => $attribute));
				}
			}
			$this->_required = array_combine($this->_required, $this->_required);
		}

		if ($this->_readonly)
		{
			$this->_readonly = array_combine($this->_readonly, $this->_readonly);
		}

		if ($this->_ignored)
		{
			$this->_ignored = array_combine($this->_ignored, $this->_ignored);
		}

		parent::__construct(
			($attributes === NULL) ? array() : $attributes,
			ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS
		);
	}

	public function offsetExists($index)
	{
		if (is_int($index)) return FALSE;

		if ( ! isset($this->_ignored[$index]))
		{
			return parent::offsetExists($index);
		}
	}

	public function offsetGet($index)
	{
		if (is_int($index)) return NULL;

		if ( ! isset($this->_ignored[$index]))
		{
			return parent::offsetGet($index);
		}
	}

	public function offsetSet($index, $value)
	{
		if ($index === NULL OR is_int($index)) return;

		if ($value === NULL AND isset($this->_required[$index]))
		{
			// Protect required attribute
			$value = '';
		}

		if (isset($this->_readonly[$index]))
		{
			// Protect required attribute
			throw new Kohana_Exception('Attribute ":attribute" is a readonly and can not be modified',
				array(':attribute' => $index));
		}

		if ( ! isset($this->_ignored[$index]))
		{
			parent::offsetSet($index, $value);
		}
	}

	public function offsetUnset($index)
	{
		if (isset($this->_required[$index]))
		{
			// Protect required attribute
			throw new Kohana_Exception('Attribute ":attribute" is a required and can not be removed',
				array(':attribute' => $index));
		}
		if (isset($this->_readonly[$index]))
		{
			// Protect required attribute
			throw new Kohana_Exception('Attribute ":attribute" is a readonly and can not be removed',
				array(':attribute' => $index));
		}
		return parent::offsetUnset($index);
	}

	public function __get($name)
	{
		if ($this->_empty === FALSE AND $name === 'html')
		{
			return $this->_html;
		}
		elseif ($this->_empty === FALSE AND $name === 'cdata')
		{
			return $this->_cdata;
		}
		elseif ($name === 'condition')
		{
			$this->_condition;
		}
		return NULL;
	}

	public function __set($name, $value)
	{
		if ($this->_empty === FALSE AND $name === 'html')
		{
			$this->_html = $value;
		}
		elseif ($this->_empty === FALSE AND $name === 'cdata')
		{
			$this->_cdata = $value;
		}
		elseif ($name === 'condition')
		{
			$this->_condition = $value;
		}
	}

	public function html($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_html;
		}
		elseif ($this->_empty === FALSE)
		{
			$this->_html = $value;
		}
		return $this;
	}

	public function cdata($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_cdata;
		}
		elseif ($this->_empty === FALSE)
		{
			$this->_cdata = $value;
		}
		return $this;
	}

	public function condition($value = NULL)
	{
		if ($value === NULL)
		{
			return $this->_condition;
		}
		else
		{
			$this->_condition = $value;
		}
		return $this;
	}

	/**
	 * Returns the HTML code of tag.
	 *
	 * @return  string
	 */
	public function render()
	{
		$attributes = $this->getArrayCopy();
		if ($this->_ignored)
		{
			$attributes = array_diff_key($attributes, $this->_ignored);
		}

		if ($this->_empty === FALSE AND $this->_html)
		{
			$value = '<'.$this->_tag.HTML::attributes($attributes).'>'.(string) $this->_html.'</'.$this->_tag.'>';
		}
		elseif ($this->_empty === FALSE AND $this->_cdata)
		{
			if (strpos($this->_cdata, "\n") !== FALSE)
			{
				$value = '<'.$this->_tag.HTML::attributes($attributes).">\n"
					."// <![CDATA[\n"
					.(string) $this->_cdata."\n"
					."// ]]>\n"
					.'</'.$this->_tag.'>';
			}
			else
			{
				$value = '<'.$this->_tag.HTML::attributes($attributes).'>'
					.'/* <![CDATA[ */ '
					.(string) $this->_cdata
					.' /* ]]> */'
					.'</'.$this->_tag.'>';
			}
		}
		elseif ($this->_short === TRUE)
		{
			// Short form of tag
			$value = '<'.$this->_tag.HTML::attributes($attributes).' />';
		}
		else
		{
			// Full form of tag
			$value = '<'.$this->_tag.HTML::attributes($attributes).'></'.$this->_tag.'>';
		}

		return $this->_condition
			? Ku_HTML::conditional_comments($this->_condition, $value)
			: $value;
	}

	public function __toString()
	{
		try
		{
			return (string) $this->render();
		}
		catch (Exception $e)
		{
			// Display the exception message
			Kohana_Exception::handler($e);

			return '';
		}
	}

} // End Ku_HTML_Element