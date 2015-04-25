<?php defined('SYSPATH') or die('No direct script access.');

class Extended_Validation extends Validation {

	/**
	 * Creates a new Extended_Validation instance.
	 *
	 * @param   array   $array  array to use for validation
	 * @return  Extended_Validation
	 */
	public static function factory(array $array)
	{
		return new Extended_Validation($array);
	}
	
	public function add_empty_rule($name)
	{
		array_push($this->_empty_rules, $name);
	}

} // End Validation
