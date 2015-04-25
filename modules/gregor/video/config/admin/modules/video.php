<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'video'    => array(
			'title'  => __('Albums'),
			'link'   => Route::url('modules', array(
				'controller' => 'video',
				'query'      => 'pid={PAGE_ID}'
			)),
			'sub'    => array(),
		),
	),
	'a2' => array(
		'resources' => array(
			'video_controller' => 'module_controller',
			'video_category'   => 'module',
			'video'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'video_controller',
					'privilege' => 'access',
				),

				'video_category_add' => array(
					'role'      => 'main',
					'resource'  => 'video_category',
					'privilege' => 'add',
				),
				
				'video_category_edit_1' => array(
					'role'      => 'main',
					'resource'  => 'video_category',
					'privilege' => 'edit',
				),

				'video_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'video',
					'privilege' => 'edit',
				),
				
				'video_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'video_category',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);