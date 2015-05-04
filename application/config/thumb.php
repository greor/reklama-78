<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'admin_image_300' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 300,
			'height' => 300,
			'master' => Image::AUTO,
		),
		'quality' => 100
	),
	'admin_image_100' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 100,
			'height' => 100,
			'master' => Image::AUTO,
		),
		'quality' => 90
	),
		
		
	'promo_1920x635' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 1920,
			'height' => 635,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 1920,
			'height' => 635,
		),
		'quality' => 100
	),
	'promo_786x449' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 786,
			'height' => 449,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 786,
			'height' => 449,
		),
		'quality' => 100
	),
);