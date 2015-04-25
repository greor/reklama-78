<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'type'	=>	array(
		'static'	=>	__('Static'),
		'module'	=>	__('Module'),
		'page'		=>	__('Redirect to page'),
		'url'		=>	__('Link'),
	),
	'status'	=>	array(
		0	=>	__('Inactive page'),
		1	=>	__('Hidden page'),
		2	=>	__('Active page'),
	),
	'status_codes'	=>	array(
		'inactive'	=>	0,
		'hidden'	=>	1,
		'active'	=>	2,
	),
);