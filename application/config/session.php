<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	//	for admin side
	'admin'	=>	array(
		'name' => 'admin_session',
	//	'encrypted' => '',
		'lifetime' => Date::DAY,
	),

	// for public side
	'native'	=>	array(
		'name' => 'session',
	//	'encrypted' => '',
		'lifetime' => 60*60,
	),

);