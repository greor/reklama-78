<?php defined('SYSPATH') or die('No direct script access.');

Route::set('thumb', 'thumb/<group>/<file>', array('file' => '.*?'))
	->defaults(array(
		'controller' => 'thumb',
		'action'     => 'index',
	));
