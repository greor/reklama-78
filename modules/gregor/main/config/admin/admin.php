<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'title'	=>	'Administrative Interface',
	'logo'		=>	array(
		'src'	=>	'img/logo.png',
		'title'	=>	'Empty',
	),
	'admin_part_route_names'	=>	array(
		'admin',
		'modules',
	),
	'default_ui_theme' => 'ui-lightness',
	'theme' => NULL,
	'media' => '/media/admin/',

	'help_link'	=>	'/guide0000.pdf',
	'help_title'	=>	__('Help'),
);