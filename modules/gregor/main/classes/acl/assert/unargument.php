<?php

class Acl_Assert_Unargument implements Acl_Assert_Interface {

	protected $_arguments;

	public function __construct($arguments)
	{
		$this->_arguments = $arguments;
	}

	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		foreach($this->_arguments as $role_key => $resource_key)
		{
			if($role->$role_key === $resource->$resource_key)
			{
				return FALSE;
			}
		}

		return TRUE;
	}
}