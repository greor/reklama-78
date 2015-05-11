<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Service extends Controller_Front {
	
	public $template = 'modules/service/';
	
	public function before()
	{
		$uri = $this->request->param('uri');
		if ( ! empty($uri)) {
			$this->request->action('detail');
		}
		$this->template .= $this->request->action();
		
		parent::before();
	}
	
	public function action_index()
	{
		$this->breadcrumbs[] = array(
			'title' => $this->request->page['title']
		);
		$this->breadcrumbs_title = $this->request->page['title'];
		
		$page = ORM::factory('page', $this->page_id);
		
		$link_tpl = URL::base().Page_Route::uri($this->page_id, 'service', array(
			'uri' => '{uri}'
		));
		$_service = ORM::factory('service')
			->find_all();
		
		$service = array();
		$helper = ORM_Helper::factory('service');
		foreach ($_service as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('service_850', $helper->file_uri('image', $_item['image']));
			}
			$service[] = $_item;
		}
		
		$this->template
			->set('page', $page->as_array())
			->set('service', $service)
		;
		
	}
	
	
	public function action_detail()
	{
		$this->breadcrumbs[] = array(
			'title' => $this->request->page['title'],
			'link' => URL::base().$this->request->page['uri_full']
		);
		
		$uri = $this->request->param('uri');
		$_service = ORM::factory('service')
			->where('uri', '=', $uri)
			->find();
		
		if ( ! $_service->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$service = $_service->as_array();
		if ( ! empty($service['image'])) {
			$helper = ORM_Helper::factory('service');
			$service['image'] = URL::base().Thumb::uri('service_555x300', $helper->file_uri('image', $service['image']));
		}
		
		$this->breadcrumbs[] = array(
			'title' => $service['title']
		);
		$this->breadcrumbs_title = $service['title'];
		
		$widget_service = Request::factory(Route::url('widgets', array(
				'controller' => 'service'
			)))
			->execute()
			->body();
		
		$widget_recent = Request::factory(Route::url('widgets', array(
				'controller' => 'recent',
			)))
			->execute()
			->body();
		
		$this->template
			->set('widget_service', $widget_service)
			->set('widget_recent', $widget_recent)
			->set('service', $service);
	}
	
}