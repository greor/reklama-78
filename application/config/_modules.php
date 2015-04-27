<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
/*
	'elements'	=>	array(
		'alias' =>  'gregor-elements',
		'name'  =>  'Elements module',
		'type'  =>  Helper_Module::MODULE_SINGLE,
	),
*/
	'news'	=>	array(
		'alias' =>  'gregor-news-no-categories',
		'name'  =>  'News module',
		'type'  =>  Helper_Module::MODULE_SINGLE,
	),
	'actions'	=>	array(
		'alias' =>  'gregor-actions',
		'name'  =>  'Actions module',
		'type'  =>  Helper_Module::MODULE_SINGLE,
	),
	'blog'	=>	array(
		'alias' =>  'gregor-blog',
		'name'  =>  'Blog module',
		'type'  =>  Helper_Module::MODULE_SINGLE,
	),
	'promo'	=>	array(
		'alias' =>  'gregor-promo',
		'name'  =>  'Promo module',
		'type'  =>  Helper_Module::MODULE_HIDDEN,
	),
	'photo'	=>	array(
		'alias' =>  'gregor-photo',
		'name'  =>  'Photo module',
		'type'  =>  Helper_Module::MODULE_SINGLE,
	),
);