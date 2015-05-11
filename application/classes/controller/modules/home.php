<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Home extends Controller_Front {

	public $template = 'modules/home/content';

	public function action_index() {
		
		$this->switch_on_plugin('counter');
		
		$this->template
			->set('promo', $this->_get_promo())
			->set('services', $this->_get_services())
			->set('clients', $this->_get_clients())
			->set('projects', $this->_get_projects())
			->set('elemenets', $this->_get_elements())
		;
		
	}
	
	private function _get_promo()
	{
		$_date = date('Y-m-d H:i:s');
		$_elements = ORM::factory('promo')
			->where('public_date', '<=', $_date)
			->and_where_open()
				->where('hidden_date', '>', $_date)
				->or_where('hidden_date', '=', '0000-00-00 00:00:00')
			->and_where_close()
			->find_all();
		
		
		$elements = array();
		$helper = ORM_Helper::factory('promo');
		$url_base = URL::base();
		foreach ($_elements as $_row) {
			$_item = $_row->as_array();
// 			if ( ! empty($_item['background'])) {
// 				$_item['background'] = $url_base.Thumb::uri('promo_1920x635', $helper->file_uri('background', $_item['background']));
// 			}
			if ( ! empty($_item['image'])) {
				$_item['image'] = $url_base.Thumb::uri('promo_560x325', $helper->file_uri('image', $_item['image']));
			} 
// 			$_item['settings'] = @unserialize($_item['settings']);
// 			$_item['settings'] = empty($_item['settings']) ? array() : $_item['settings'];
			
			$elements[] = $_item;
		}
		
		if ( ! empty($elements)) {
			$this->switch_on_plugin('bxslider');
		}
		
		return $elements;
	}
	
	private function _get_services()
	{
		$_services = ORM::factory('service')
			->find_all();
		
		$services = array();
		$helper = ORM_Helper::factory('service');
		$url_base = URL::base();
		$page = Page_Route::page_by_name('service');
		foreach ($_services as $_row) {
			$_item = $_row->as_array();
			
			if ( ! empty($_item['icon'])) {
				$_item['icon'] = $url_base.Thumb::uri('service_icon_70', $helper->file_uri('icon', $_item['icon']));
			}
			
			if ( ! empty($page['id'])) {
				$_item['link'] = $url_base.Page_Route::uri($page['id'], 'service', array(
					'uri' => $_item['uri']
				));
			}
			
			$services[] = $_item;
		}
		
		return $services;
	}

	private function _get_clients()
	{
		$_clients = ORM::factory('client')
			->find_all();
		
		$clients = array();
		$helper = ORM_Helper::factory('client');
		foreach ($_clients as $_row) {
			$_item = $_row->as_array();
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = Thumb::uri('clients_195x195', $helper->file_uri('image', $_item['image']));
			}
			$clients[] = $_item;
		}
		
		if ( ! empty($clients)) {
			$this->switch_on_plugin('bxslider');
		}
		
		return $clients;
	}

	private function _get_projects()
	{
		$_projects = ORM::factory('project')
			->find_all();
		
		$projects = array();
		$helper = ORM_Helper::factory('project');
		foreach ($_projects as $_row) {
			$_item = $_row->as_array();
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = Thumb::uri('projects_195x195', $helper->file_uri('image', $_item['image']));
			}
			$projects[] = $_item;
		}
		
		if ( ! empty($projects)) {
			$this->switch_on_plugin('bxslider');
		}
		
		return $projects;
	}
	
	private function _get_elements()
	{
		
		$news = $this->_get_news(5);
		$actions = $this->_get_actions(5);
		$posts = $this->_get_posts(5);
		
		$elements = array_merge_recursive($news, $actions, $posts);
		
		if ( ! empty($elements)) {
			krsort($elements);
			$this->switch_on_plugin('isotope');
		}
		
		return $elements;
	}
	
	private function _get_news($limit = 3) {
		$_page = Page_Route::page_by_name('news');
		if (empty($_page['id'])) {
			return array();
		}
		
		$_date = date('Y-m-d H:i:s');
		$_news = ORM::factory('news')
			->where('public_date', '<=', $_date)
			->where('uri', '!=', '')
			->find_all();
		
		$list_link = URL::base().Page_Route::uri($_page['id'], 'news');
		$link_tpl = URL::base().Page_Route::uri($_page['id'], 'news', array(
			'uri' => '{uri}'
		));
		
		$news = array();
		$helper = ORM_Helper::factory('news');
		foreach ($_news as $_row) {
			$_item = $_row->as_array();
			$_item['element_type'] = 'news';
			$_item['list_link'] = $list_link;
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('isotope_360', $helper->file_uri('image', $_item['image']));
			}
			
			$news[$_row->public_date][] = $_item;
		}
		
		return $news;
	}
	
	private function _get_actions($limit = 3) {
		$_page = Page_Route::page_by_name('actions');
		if (empty($_page['id'])) {
			return array();
		}
		
		$_date = date('Y-m-d H:i:s');
		$_actions = ORM::factory('action')
			->where('public_date', '<=', $_date)
			->and_where_open()
				->where('hidden_date', '>', $_date)
				->or_where('hidden_date', '=', '0000-00-00 00:00:00')
			->and_where_close()
			->where('uri', '!=', '')
			->where('public_date', '!=', '0000-00-00 00:00:00')
			->find_all();
		
		$list_link = URL::base().Page_Route::uri($_page['id'], 'actions');
		$link_tpl = URL::base().Page_Route::uri($_page['id'], 'actions', array(
			'date' => '{date}',
			'uri' => '{uri}',
		));
		
		$actions = array();
		$helper = ORM_Helper::factory('action');
		foreach ($_actions as $_row) {
			$_item = $_row->as_array();
			$_item['element_type'] = 'actions';
			
			$_item['list_link'] = $list_link;
			$_item['link'] = str_replace(array(
				'{date}', '{uri}'
			), array(
				date('Ymd', strtotime($_row->public_date)), $_row->uri
			), $link_tpl);
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('isotope_360', $helper->file_uri('image', $_item['image']));
			}
			
			$actions[$_row->public_date][] = $_item;
		}
		
		return $actions;
	}
	
	private function _get_posts($limit = 3) {
		$_page = Page_Route::page_by_name('blog');
		if (empty($_page['id'])) {
			return array();
		}
		
		$_date = date('Y-m-d H:i:s');
		$_posts = ORM::factory('post')
			->where('public_date', '<=', $_date)
			->find_all();
		
		$list_link = URL::base().Page_Route::uri($_page['id'], 'blog');
		$link_tpl = URL::base().Page_Route::uri($_page['id'], 'blog', array(
			'uri' => '{uri}'
		));
		
		$posts = array();
		$helper = ORM_Helper::factory('post');
		foreach ($_posts as $_row) {
			$_item = $_row->as_array();
			$_item['element_type'] = 'blog';
			$_item['list_link'] = $list_link;
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('isotope_360', $helper->file_uri('image', $_item['image']));
			}
			
			$posts[$_row->public_date][] = $_item;
		}
		
		return $posts;
	}
	
}