<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'actions'	=>	array(
		'uri_callback'	=>	'(/<date>-<uri>)(?<query>)',
		'regex' => array('date' => '\d{8}'),
		'defaults'	=>	array(
			'directory'		=> 'modules',
			'controller'	=> 'actions',
			'action'		=> 'index',
		)
	),
);

