<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'projects' => array(
			'title'   => __('Projects list'),
			'link'    => Route::url('modules', array(
				'controller' => 'projects',
			)),
			'sub' => array(
				'add_project' => array(
					'title' => __('Add project'),
					'link'  => Route::url('modules', array(
						'controller' => 'projects',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'projects_controller' => 'module_controller',
			'project'             => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'projects_controller',
					'privilege' => 'access',
				),

				'project_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'project',
					'privilege' => 'edit',
				),
				
				'project_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'project',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);