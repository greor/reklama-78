<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'list'  => array(
			'title' => __('Feedback list'),
			'link'  => Route::url('modules', array(
				'controller' => 'feedback',
				'query'      => 'pid={PAGE_ID}'
			)),
		),
		'config' => array(
			'title' => __('Config'),
			'link'  => Route::url('modules', array(
				'controller' => 'feedback',
				'action'     => 'config',
				'query'      => 'pid={PAGE_ID}'
			)),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'feedback_controller' => 'module_controller',
			'feedback_config'     => 'module',
			'feedback'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'feedback_controller',
					'privilege' => 'access',
				),
				'feedback_view_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'feedback',
					'privilege' => 'view',
				),
				'feedback_config_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'feedback_config',
					'privilege' => 'edit',
				),
			),
		)
	),

);