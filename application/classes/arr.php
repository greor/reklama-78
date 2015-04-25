<?php defined('SYSPATH') or die('No direct script access.');

class Arr extends Kohana_Arr {
	
	public static function pluck2($array, $key)
	{
		$values = array();
		foreach ($array as $k => $row) {
			if ( ! isset($row[$key]))
				return FALSE;
			
			if (isset($row[$key])) {
				$values[$k] = $row[$key];
			}
		}
		return $values;
	}
	
}