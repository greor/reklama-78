<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'news'	=>	array(
		'uri_callback' => '(/<category_uri>(/<element_id>))(?<query>)',
		'defaults'     => array(
			'directory'  => 'modules',
			'controller' => 'news',
			'action'     => 'index',
		)
	),
);

