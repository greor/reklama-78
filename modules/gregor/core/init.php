<?php defined('SYSPATH') or die('No direct script access.');

function _ku($args)
{
	$args = func_get_args();
	echo call_user_func_array('Debug::vars', $args);
}

function _log($var, $title = '')
{
	Kohana::$log->add(Kohana::ERROR, $title. ' = :x',array(':x' => Debug::dump($var)));
}

function _timer($title = NULL)
{
	static $list = array();
	if ($title === NULL)
	{
		$prev = KOHANA_START_TIME;
		foreach ($list as $k => $v)
		{
			$tm = number_format(($v - KOHANA_START_TIME) * 1000, 3);
			$diff = number_format(($v - $prev) * 1000, 3);

			$list[$k] = str_pad($k, 20, ' ', STR_PAD_RIGHT).' '
				.str_pad($tm, 10, ' ', STR_PAD_LEFT).' ms'
				.str_pad($diff, 10, ' ', STR_PAD_LEFT).' ms';

			$prev = $v;
		}
		return implode("\n", $list);
	}
	else
	{
		$list[$title] = microtime(TRUE);
	}
}
