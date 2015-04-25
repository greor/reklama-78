<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'menu'	=>	array(
		array(
			'url'	=>	Route::url('admin', array( 'controller' => 'sites' )),
			'title'	=>	__('Site manager'),
			'name'	=>	'sites',
		),
//		array(
//			'url'	=>	Route::url('admin', array( 'controller' => 'settings', 'action' => 'common' )),
//			'title'	=>	__('Common settings'),
//			'name'	=>	'common_settings',
//		),
	),

	'settings_name'	=>	array(
		'confirm_email' => array(
			'title'	=>	__('Confirm user registration by email'),
			'type'	=>	'checkbox',
		),
		'reconstuct' => array(
			'title'	=>	__('Site under reconstruction'),
			'type'	=>	'checkbox',
		),
	),
);