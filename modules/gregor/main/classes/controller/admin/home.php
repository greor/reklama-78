<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Home extends Controller_Admin_Front {

	public $template = 'home';

	public function action_index()
	{
		$this->template
			->set('logo', $this->admin_config['logo']);
	}
} 
