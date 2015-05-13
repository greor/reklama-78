<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Page extends Controller_Front {

	public function before()
	{
		if ($this->request->action() == 'static') {
			$this->template = $this->request->action();
		} else {
			$this->auto_render = FALSE;
		}

		parent::before();
	}

	public function action_static()
	{
		$this->breadcrumbs[] = array(
			'title' => $this->request->page['title']
		);
		$this->breadcrumbs_title = $this->request->page['title'];
		
		$orm = ORM::factory('page', $this->page_id);
		if ( ! $orm->loaded())
			throw new HTTP_Exception_404;
		
		$meta = $this->extract_meta($orm->as_array());
		$this->set_page_meta($meta);
		
		$this->title = $orm->title;
		$this->template
			->set('page', $orm);
	}

	public function action_page()
	{
		Request::current()->redirect( 
			URL::base().Page_Route::dynamic_base_uri($this->request->page['data']), 
			301
		);
	}

	public function action_url()
	{
		Request::current()->redirect( $this->request->page['data'], 301 );
	}

}