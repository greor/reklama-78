<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'elements' => array(
			'title'   => __('Element list'),
			'link'    => Route::url('modules', array(
				'controller' => 'elements',
				'query'      => 'pid={PAGE_ID}',
			)),
			'sub' => array(
				'add_element' => array(
					'title' => __('Add element'),
					'link'  => Route::url('modules', array(
						'controller' => 'elements',
						'action'     => 'edit',
						'query'      => 'pid={PAGE_ID}',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'elements_controller' => 'module_controller',
			'element'             => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'elements_controller',
					'privilege' => 'access',
				),

				'program_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'element',
					'privilege' => 'edit',
				),
				
				'program_fix_all_1'	=>	array(
					'role'      => 'element',
					'resource'  => 'element',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);