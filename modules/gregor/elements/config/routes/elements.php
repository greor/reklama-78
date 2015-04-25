<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'elements'	=>	array(
		'uri_callback'	=>	'(/<uri>)(?<query>)',
		'defaults'	=>	array(
			'directory'		=> 'modules',
			'controller'	=> 'elements',
			'action'		=> 'index',
		)
	),
);

