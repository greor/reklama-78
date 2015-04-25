<?php

class Acl_Assert_Page implements Acl_Assert_Interface {

	protected $_site_id;
	protected $_main_site_id;

	public function __construct($arguments)
	{
		$this->_site_id = $arguments['site_id'];
		$this->_main_site_id = $arguments['main_site_id'];
	}

	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		$hasnt_module = Helper_Page::instance()->not_equal( $resource, 'type', 'module' );

		if ( ! $resource->can_hiding OR $resource->site_id != $this->_main_site_id OR $this->_site_id == $this->_main_site_id OR ! $hasnt_module )
		{
			return FALSE;
		}

		return TRUE;
	}
}