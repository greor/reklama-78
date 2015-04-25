<?php defined('SYSPATH') or die('No direct script access.');

return array (
	'video'	=>	array(
		'uri_callback' => '(/<category_uri>(/<element_id>))(?<query>)',
		'defaults'     => array(
			'directory'  => 'modules',
			'controller' => 'video',
			'action'     => 'index',
		)
	),
);

