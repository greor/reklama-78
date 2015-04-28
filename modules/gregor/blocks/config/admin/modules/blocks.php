<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'blocks' => array(
			'title'   => __('Block list'),
			'link'    => Route::url('modules', array(
				'controller' => 'blocks',
			)),
// 			'sub' => array(
// 				'add_service' => array(
// 					'title' => __('Add block'),
// 					'link'  => Route::url('modules', array(
// 						'controller' => 'blocks',
// 						'action'     => 'edit',
// 					)),
// 				),
// 			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'blocks_controller' => 'module_controller',
			'block'             => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'blocks_controller',
					'privilege' => 'access',
				),

				'block_add_1'	=>	array(
					'role'      => 'super',
					'resource'  => 'block',
					'privilege' => 'add',
				),
					
				'block_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'block',
					'privilege' => 'edit',
				),
				
			),
		)
	),
);