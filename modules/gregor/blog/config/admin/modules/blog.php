<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'blog' => array(
			'title'   => __('Posts list'),
			'link'    => Route::url('modules', array(
				'controller' => 'blog',
			)),
			'sub' => array(
				'add_post' => array(
					'title' => __('Add post'),
					'link'  => Route::url('modules', array(
						'controller' => 'blog',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'blog_controller' => 'module_controller',
			'post'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'blog_controller',
					'privilege' => 'access',
				),

				'post_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'post',
					'privilege' => 'edit',
				),
			),
		)
	),
);