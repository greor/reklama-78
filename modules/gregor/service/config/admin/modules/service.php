<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'services' => array(
			'title'   => __('Service list'),
			'link'    => Route::url('modules', array(
				'controller' => 'service',
			)),
			'sub' => array(
				'add_service' => array(
					'title' => __('Add service'),
					'link'  => Route::url('modules', array(
						'controller' => 'service',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'service_controller' => 'module_controller',
			'service'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'service_controller',
					'privilege' => 'access',
				),

				'service_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'service',
					'privilege' => 'edit',
				),
				
				'service_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'service',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);