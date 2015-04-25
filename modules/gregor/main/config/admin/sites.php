<?php defined('SYSPATH') or die('No direct access allowed.');

$left_menu = array(
	'left_menu'	=>	array(
		'sites'	=>	array(
			'title'		=>	__('Site manager'),
			'link'		=>	Route::url('admin', array(
								'controller' => 'sites'
							)),
		),
	),
);

$acl = A2::instance('admin/a2');
$user = $acl->get_user();

if ( $acl->is_allowed( $user, 'site', 'add' ) )
{
	$left_menu['left_menu']['sites']['sub'] = array(
		'add'	=>	array(
			'title'		=>	__('Add site'),
			'link'		=>	Route::url('admin', array(
								'controller' => 'sites',
								'action' => 'edit'
							)),
		)
	);
}

return $left_menu;