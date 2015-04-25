<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'photo'    => array(
			'title'  => __('Albums'),
			'link'   => Route::url('modules', array(
				'controller' => 'photo',
				'query'      => 'pid={PAGE_ID}'
			)),
			'sub'    => array(),
		),
		'multi' => array(
			'title' => __('Multiupload'),
			'link'  => Route::url('modules', array(
				'controller' => 'photo',
				'action'     => 'multiupload',
				'query'      => 'pid={PAGE_ID}',
			)),
		),
	),
	'a2' => array(
		'resources' => array(
			'photo_controller' => 'module_controller',
			'photo_category'   => 'module',
			'photo'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'photo_controller',
					'privilege' => 'access',
				),

				'photo_category_add' => array(
					'role'      => 'main',
					'resource'  => 'photo_category',
					'privilege' => 'add',
				),
				
				'photo_category_edit_1' => array(
					'role'      => 'main',
					'resource'  => 'photo_category',
					'privilege' => 'edit',
				),

				'photo_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'photo',
					'privilege' => 'edit',
				),
				
				'photo_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'photo_category',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);