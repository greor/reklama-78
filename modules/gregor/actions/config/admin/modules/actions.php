<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'actions' => array(
			'title'   => __('Actions list'),
			'link'    => Route::url('modules', array(
				'controller' => 'actions',
			)),
			'sub' => array(
				'add_action' => array(
					'title' => __('Add action'),
					'link'  => Route::url('modules', array(
						'controller' => 'actions',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'actions_controller' => 'module_controller',
			'action'             => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'actions_controller',
					'privilege' => 'access',
				),

				'action_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'action',
					'privilege' => 'edit',
				),
			),
		)
	),
);