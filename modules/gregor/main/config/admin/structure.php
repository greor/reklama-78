<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'left_menu'	=>	array(
		'structure'	=>	array(
			'title'		=>	__('Site structure'),
			'link'		=>	Route::url('admin', array(
								'controller' => 'structure'
							)),
			'sub'		=>	array(
				'add'	=>	array(
					'title'		=>	__('Add page'),
					'link'		=>	Route::url('admin', array(
										'controller' => 'structure',
										'action' => 'edit'
							)),
				),
				'fix'	=>	array(
					'title'		=>	__('Fix positions'),
					'link'		=>	Route::url('admin', array(
										'controller' => 'structure',
										'action' => 'position',
										'query'	=> 'mode=fix',
									)),
				),
			),
		),
		'clear_cache'	=>	array(
			'title'		=>	__('Clear structure cache'),
			'link'		=>	Route::url('admin', array(
								'controller' => 'structure',
								'action' => 'clear_cache',
							)),
		),
	),
);