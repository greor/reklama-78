<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'photo'	=>	array(
		'uri_callback' => '(/<category_uri>(/<element_id>))(?<query>)',
		'defaults'     => array(
			'directory'  => 'modules',
			'controller' => 'photo',
			'action'     => 'index',
		)
	),
);

