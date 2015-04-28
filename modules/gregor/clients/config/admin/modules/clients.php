<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'clients' => array(
			'title'   => __('Clients list'),
			'link'    => Route::url('modules', array(
				'controller' => 'clients',
			)),
			'sub' => array(
				'add_client' => array(
					'title' => __('Add client'),
					'link'  => Route::url('modules', array(
						'controller' => 'clients',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'clients_controller' => 'module_controller',
			'client'             => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'clients_controller',
					'privilege' => 'access',
				),

				'client_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'client',
					'privilege' => 'edit',
				),
				
				'client_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'client',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);