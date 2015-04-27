<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu' => array(
		'promo' => array(
			'title'   => __('Banners list'),
			'link'    => Route::url('modules', array(
				'controller' => 'promo',
			)),
			'sub' => array(
				'add_promo' => array(
					'title' => __('Add banner'),
					'link'  => Route::url('modules', array(
						'controller' => 'promo',
						'action'     => 'edit',
					)),
				),
			),
		),
	),
	'a2'	=>	array(
		'resources' => array(
			'promo_controller' => 'module_controller',
			'promo'            => 'module',
		),
		'rules' => array(
			'allow' => array(
				'controller_access'	=>	array(
					'role'      => 'main',
					'resource'  => 'promo_controller',
					'privilege' => 'access',
				),

				'promo_edit_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'promo',
					'privilege' => 'edit',
				),
					
				'promo_fix_all_1'	=>	array(
					'role'      => 'main',
					'resource'  => 'promo',
					'privilege' => 'fix_all',
				),
			),
		)
	),
);