<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Logout extends Controller_Template {

	public function before()
	{
		$this->auto_render = FALSE;
		Session::$default = 'admin';
	}

	public function action_index()
	{
		A2::instance('admin/a2')->auth()->logout(TRUE);
		Request::current()->redirect( Route::url('admin') );
	}
} 
