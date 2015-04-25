<?php

class Acl_Assert_Module implements Acl_Assert_Interface {


	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		return Helper_Page::instance()
			->not_equal($resource, 'type', 'module');
	}
}