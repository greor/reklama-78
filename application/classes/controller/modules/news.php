<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_News extends Controller_Front {
	
	public $template = 'modules/news/';
	
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
		
		$link_tpl = URL::base().Page_Route::uri($this->page_id, 'news', array(
			'uri' => '{uri}'
		));
		
		$_date = date('Y-m-d H:i:s');
		$orm = ORM::factory('news')
			->where('public_date', '<=', $_date)
			->where('uri', '!=', '');
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('layout/more');
		$paginator
			->per_page(10)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);
		
		$_news = $orm
			->paginator( $paginator )
			->find_all();
		
		$news = array();
		$helper = ORM_Helper::factory('news');
		foreach ($_news as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('isotope_360', $helper->file_uri('image', $_item['image']));
			}
			
			$news[] = $_item;
		}
		
		if ( ! empty($news)) {
			$this->switch_on_plugin('isotope');
		}
		
		$this->template
			->set('page', $page->as_array())
			->set('news', $news)
			->set('paginator', $paginator)
		;
		
	}
	
	
	public function action_detail()
	{
		$this->breadcrumbs[] = array(
			'title' => $this->request->page['title'],
			'link' => URL::base().$this->request->page['uri_full']
		);
		
		$uri = $this->request->param('uri');
		$_news = ORM::factory('news')
			->where('uri', '=', $uri)
			->find();
		
		if ( ! $_news->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$news = $_news->as_array();
		if ( ! empty($news['image'])) {
			$helper = ORM_Helper::factory('news');
			$news['image'] = URL::base().Thumb::uri('detail_555', $helper->file_uri('image', $news['image']));
		}
		
		$this->breadcrumbs[] = array(
			'title' => $news['title']
		);
		$this->breadcrumbs_title = $news['title'];
		
		$widget_service = Request::factory(Route::url('widgets', array(
				'controller' => 'service'
			)))
			->execute()
			->body();
		
		$widget_recent = Request::factory(Route::url('widgets', array(
				'controller' => 'recent',
				'query' => 'news='.$news['id']
			)))
			->execute()
			->body();
		
		$this->template
			->set('widget_recent', $widget_recent)
			->set('widget_service', $widget_service)
			->set('news', $news);
	}
	
}