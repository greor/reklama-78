<?php

return array(
	'lib' => array(
		'class'  => 'A1', // (or AUTH)
		'params' => array(
			'name' => 'admin/a1'
		)
	),
	'exception' => FALSE,
	'exception_type' => 'a2_exception',
	'roles' => array
	(
		'guest'	=>	NULL,
		'user'	=>	'guest',
		'main'	=>	'user',
		'super'	=>	'main',
	),
	'guest_role' => 'guest',
	'resources' => array
	(
		'settings'	=>	NULL,

		'structure'	=>	NULL,
		'page'	=>	NULL,

		'sites'	=>	NULL,
		'site'	=>	NULL,

		'admins'	=>	NULL,
		'admin'	=>	NULL,

		'modules'	=>	NULL,
		'module'	=>	NULL,
		'module_controller'	=>	NULL,
	),
	'rules' => array
	(
		'allow' => array
		(
			'no_category_option'	=>	array(
				'role'      => 'super',
				'resource'  => NULL,
				'privilege' => 'show_no_category',
			),

			'admins'	=>	array(
				'role'      => 'super',
				'resource'  => 'admins',
				'privilege' => 'read',
			),
			'admin_read_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'admin',
				'privilege' => 'read',
			),
			'admin_edit_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'admin',
				'privilege' => 'edit',
			),
			'admin_add_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'admin',
				'privilege' => 'add',
			),

			'settings'	=>	array(
				'role'      => 'main',
				'resource'  => 'settings',
				'privilege' => 'read',
			),

			'sites'	=>	array(
				'role'      => 'main',
				'resource'  => 'sites',
				'privilege' => 'read',
			),
			'sites_read_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'site',
				'privilege' => 'read',
			),
			'sites_add_1'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'add',
			),
			'sites_del_1'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'delete',
			),
			'sites_edit_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'site',
				'privilege' => 'edit',
			),
			'sites_edit_name'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_name',
			),
			'sites_edit_url'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_url',
			),
			'sites_edit_vk_api_id'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_vk_api_id',
			),
			'sites_vk_group_id'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_vk_group_id',
			),
			'sites_edit_fb_app_id'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_fb_app_id',
			),
			'sites_fb_group_link'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_fb_group_link',
			),
			'sites_tw_widget'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'edit_tw_widget',
			),
			'sites_active_change'	=>	array(
				'role'      => 'super',
				'resource'  => 'site',
				'privilege' => 'active_change',
			),

			'structure'	=>	array(
				'role'      => 'main',
				'resource'  => 'structure',
				'privilege' => 'read',
			),
			'structure_edit_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'page',
				'privilege' => 'edit',
			),
			
			'structure_fix_master'	=>	array(
				'role'      => 'main',
				'resource'  => 'page',
				'privilege' => 'fix_master',
			),
			
			
			'structure_link_module'	=>	array(
				'role'      => 'main',
				'resource'  => 'page',
				'privilege' => 'link_module',
			),

			'structure_for_all_change_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'page',
				'privilege' => 'for_all_change',
				'assertion'	=> array( 'Acl_Assert_Module' ),
			),
			'structure_for_all_change_2'	=>	array(
				'role'      => 'super',
				'resource'  => 'page',
				'privilege' => 'for_all_change',
			),

			'structure_status_change_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'page',
				'privilege' => 'status_change',
				'assertion'	=> array( 'Acl_Assert_Module' ),
			),
			'structure_status_change_2'	=>	array(
				'role'      => 'super',
				'resource'  => 'page',
				'privilege' => 'status_change',
			),
			'structure_page_type_change_1'	=>	array(
				'role'      => 'main',
				'resource'  => 'page',
				'privilege' => 'page_type_change',
				'assertion'	=> array( 'Acl_Assert_Module' ),
			),
			'structure_page_type_change_2'	=>	array(
				'role'      => 'super',
				'resource'  => 'page',
				'privilege' => 'page_type_change',
			),
			'modules_list'	=>	array(
				'role'      => 'main',
				'resource'  => 'modules',
				'privilege' => 'read',
			),

		),
		'deny' => array
		(
			// ADD YOUR OWN DENY RULES HERE
		)
	)
);