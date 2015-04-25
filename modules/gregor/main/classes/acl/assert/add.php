<?php

class Acl_Assert_Add implements Acl_Assert_Interface {

	protected $_site_id;
	protected $_resource_site_id_key;
	protected $_resource_for_all_key;

	public function __construct($arguments)
	{
		$this->_site_id = $arguments['site_id'];
		$this->_resource_site_id_key = $arguments['resource_site_id_key'];
		$this->_resource_for_all_key = $arguments['resource_for_all_key'];
	}

	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		if ( $resource->{$this->_resource_site_id_key} == $this->_site_id OR (bool) $resource->{$this->_resource_for_all_key} )
		{
			return TRUE;
		}

		return FALSE;
	}
}