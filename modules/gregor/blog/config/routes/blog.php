<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'blog'	=>	array(
		'uri_callback'	=>	'(/<uri>)(?<query>)',
		'defaults'	=>	array(
			'directory'		=> 'modules',
			'controller'	=> 'blog',
			'action'		=> 'index',
		)
	),
);

