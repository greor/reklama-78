<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'news'    => array(
			'title'  => __('Category list'),
			'link'   => Route::url('modules', array(
				'controller' => 'news',
				'query'      => 'pid={PAGE_ID}'
			)),
			'sub'    => array(),
		),
	),
	'a2' => array(
		'resources' => array(
			'news_controller' => 'module_controller',
			'news_category'   => 'module',
			'news'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'news_controller',
					'privilege' => 'access',
				),

				'news_category_add' => array(
					'role'      => 'main',
					'resource'  => 'news_category',
					'privilege' => 'add',
				),
				
				'news_category_edit_1' => array(
					'role'      => 'main',
					'resource'  => 'news_category',
					'privilege' => 'edit',
				),

				'news_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'news',
					'privilege' => 'edit',
				),
				
				'news_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'news_category',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);