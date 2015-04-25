<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'100x100' => array(
		'path' => 'upload',
		'resize' => array(
			'width' => 100,
			'height' => 100,
			'master' => Image::INVERSE
		),
		'crop' => array(
			'width' => 100,
			'height' => 100,
			'offset_y' => 0
		),
		'quality' => 80
	),
	
	'50x50' => array(
		'path' => 'upload',
		'resize' => array(
			'width' => 50,
			'height' => 50,
			'master' => Image::INVERSE
		),
		'crop' => array(
			'width' => 50,
			'height' => 50,
			'offset_y' => 0
		),
		'quality' => 80
	),
);