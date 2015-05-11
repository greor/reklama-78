<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Widgets_Recent extends Controller {
	
	private $limit = 10;
	
	public function before()
	{
		$limit = $this->request->query('limit');
		if ( ! empty($limit)) {
			$this->limit = (int) $limit;
		}
	}
	
	public function action_index()
	{
		$date = date('Y-m-d H:i:s', strtotime('-1 month'));
		
		
		$news = $this->_get_news($date);
		$actions = $this->_get_actions();
		$posts = $this->_get_posts($date);
		
		$elements = array_merge_recursive($news, $actions, $posts);
		
		if ( ! empty($elements)) {
			krsort($elements);
		}
		
		$elements = $this->_flatten_array($elements);
		
		$template = View_Theme::factory('widgets/recent', array(
			'elements' => array_slice($elements, 0, $this->limit)
		));
		
		$this->response->body($template->render());
	}
	
	private function _get_news($date) {
		$_page = Page_Route::page_by_name('news');
		if (empty($_page['id'])) {
			return array();
		}
	
		$_date = date('Y-m-d H:i:s');
		$_news = ORM::factory('news')
			->where('public_date', '<=', $_date)
			->where('public_date', '>=', $date)
			->where('uri', '!=', '')
			->limit($this->limit)
			->find_all();
	
		$link_tpl = URL::base().Page_Route::uri($_page['id'], 'news', array(
			'uri' => '{uri}'
		));
	
		$news = array();
		foreach ($_news as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			$news[$_row->public_date][] = $_item;
		}
	
		return $news;
	}
	
	private function _get_actions() {
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
			->limit($this->limit)
			->find_all();
	
		$link_tpl = URL::base().Page_Route::uri($_page['id'], 'actions', array(
			'date' => '{date}',
			'uri' => '{uri}',
		));
	
		$actions = array();
		foreach ($_actions as $_row) {
			$_item = $_row->as_array();
			
			$_public_date = explode(' ', $_row->public_date);
			$_item['link'] = str_replace(array(
				'{date}', '{uri}'
			), array(
				$_public_date[0], $_row->uri
			), $link_tpl);
				
			$actions[$_row->public_date][] = $_item;
		}
	
		return $actions;
	}
	
	private function _get_posts($date) {
		$_page = Page_Route::page_by_name('blog');
		if (empty($_page['id'])) {
			return array();
		}
	
		$_date = date('Y-m-d H:i:s');
		$_posts = ORM::factory('post')
			->where('public_date', '<=', $_date)
			->where('public_date', '>=', $date)
			->limit($this->limit)
			->find_all();
	
		$link_tpl = URL::base().Page_Route::uri($_page['id'], 'blog', array(
			'uri' => '{uri}'
		));
	
		$posts = array();
		foreach ($_posts as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
				
			$posts[$_row->public_date][] = $_item;
		}
	
		return $posts;
	}
	
	private function _flatten_array($array)
	{
		$return = array();
		foreach ($array as $_item) {
			$return = array_merge($return, $_item);
		}
		return $return; 
	}
	
}