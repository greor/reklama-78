<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Widgets_Service extends Controller {
	
	public function action_index()
	{
		$page = Page_Route::page_by_name('service');
		
		if (empty($page['id'])) {
			return;
		}
		
		$link_tpl = URL::base().Page_Route::uri($page['id'], 'service', array(
			'uri' => '{uri}'
		));
		
		$_service = ORM::factory('service')
			->find_all();
		
		$service = array();
		foreach ($_service as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			$service[] = $_item;
		}
		
		$template = View_Theme::factory('widgets/service', array(
			'service' => $service
		));
		
		$this->response->body($template->render());
	}
	
}