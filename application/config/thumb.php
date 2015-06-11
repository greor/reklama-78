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
	'service_555x300' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 555,
			'height' => 300,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 555,
			'height' => 300,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'post_850' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 850,
			'master' => Image::WIDTH,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'isotope_360' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 360,
			'master' => Image::WIDTH,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'detail_555' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 555,
			'master' => Image::WIDTH,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'isotope_360x240' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 360,
			'height' => 240,
			'master' => Image::INVERSE,
		),
		'crop' => array(
			'width' => 360,
			'height' => 240,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'isotope_800x600' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 800,
			'height' => 600,
			'master' => Image::AUTO,
		),
		'crop' => array(
			'width' => 800,
			'height' => 600,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	'logo' => array(
		'path' => 'upload/images/',
		'resize' => array(
			'width' => 120,
			'master' => Image::WIDTH,
		),
		'path' => 'upload/images/',
		'quality' => 100
	),
	
	
);