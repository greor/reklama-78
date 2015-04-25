<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Date {

	public static function str_to_sec($str)
	{
		$sign = ( strpos($str, '-') === 0 ) ? -1 : 1;
		$array = explode(':', str_replace('-', '', $str));

		return  $sign * ( (int) Arr::get($array, 0, 0) * Date::HOUR
						+ (int) Arr::get($array, 1, 0) * Date::MINUTE
						+ (int) Arr::get($array, 2, 0) );
	}

	public static function sec_to_str($val, $pattern = "H:i:s")
	{
		return date($pattern, mktime(0, 0, $val));
	}

}

