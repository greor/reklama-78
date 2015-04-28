<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'service'	=>	array(
		'uri_callback'	=>	'(/<uri>)(?<query>)',
		'defaults'	=>	array(
			'directory'		=> 'modules',
			'controller'	=> 'service',
			'action'		=> 'index',
		)
	),
);

