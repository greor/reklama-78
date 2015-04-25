<?php defined('SYSPATH') or die('No direct access allowed.');

$left_menu = array(
	'left_menu'	=>	array(
		'admins'	=>	array(
			'title'		=>	__('Admin list'),
			'link'		=>	Route::url('admin', array(
								'controller' => 'admins',
							)),
		),
	),
);

$acl = A2::instance('admin/a2');
$user = $acl->get_user();

if ( $acl->is_allowed( $user, 'admin', 'add' ) )
{
	$left_menu['left_menu']['admins']['sub'] = array(
		'add'	=>	array(
			'title'		=>	__('Add admin'),
			'link'		=>	Route::url('admin', array(
								'controller' => 'admins',
								'action' => 'edit'
							)),
		),
	);
}

return $left_menu;