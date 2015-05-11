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
	'promo_560x325' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 560,
			'height' => 325,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 560,
			'height' => 325,
		),
		'quality' => 100
	),
	'service_icon_70' => array(
		'resize' => array(
			'width' => 80,
			'master' => Image::WIDTH,
		),
		'quality' => 100
	),
	'clients_195x195' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 195,
			'height' => 195,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 195,
			'height' => 195,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'projects_195x195' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 195,
			'height' => 195,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 195,
			'height' => 195,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
);