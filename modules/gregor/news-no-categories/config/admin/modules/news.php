<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'news' => array(
			'title'   => __('News list'),
			'link'    => Route::url('modules', array(
				'controller' => 'news',
			)),
			'sub' => array(
				'add_news' => array(
					'title' => __('Add news'),
					'link'  => Route::url('modules', array(
						'controller' => 'news',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'news_controller' => 'module_controller',
			'news'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'news_controller',
					'privilege' => 'access',
				),

				'news_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'news',
					'privilege' => 'edit',
				),
			),
		)
	),
);