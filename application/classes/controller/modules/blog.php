<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Blog extends Controller_Front {
	
	public $template = 'modules/blog/';
	
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
		$orm = ORM::factory('post')
			->where('public_date', '<=', $_date)
			->where('uri', '!=', '');
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('layout/paginator');
		$paginator
			->per_page(5)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);
		
		$_posts = $orm
			->paginator( $paginator )
			->find_all();
		
		$posts = array();
		$helper = ORM_Helper::factory('post');
		foreach ($_posts as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			
			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('post_850', $helper->file_uri('image', $_item['image']));
			}
			
			$posts[] = $_item;
		}
		
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
			->set('page', $page->as_array())
			->set('posts', $posts)
			->set('paginator', $paginator)
			->set('widget_service', $widget_service)
			->set('widget_recent', $widget_recent)
		;
		
	}
	
	
	public function action_detail()
	{
		$this->breadcrumbs[] = array(
			'title' => $this->request->page['title'],
			'link' => URL::base().$this->request->page['uri_full']
		);
		
		$uri = $this->request->param('uri');
		$_post = ORM::factory('post')
			->where('uri', '=', $uri)
			->find();
		
		if ( ! $_post->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$post = $_post->as_array();
		if ( ! empty($post['image'])) {
			$helper = ORM_Helper::factory('post');
			$post['image'] = URL::base().Thumb::uri('detail_555', $helper->file_uri('image', $post['image']));
		}
		
		$this->breadcrumbs[] = array(
			'title' => $post['title']
		);
		$this->breadcrumbs_title = $post['title'];
		
		$widget_service = Request::factory(Route::url('widgets', array(
				'controller' => 'service'
			)))
			->execute()
			->body();
		
		$widget_recent = Request::factory(Route::url('widgets', array(
				'controller' => 'recent',
				'query' => 'posts='.$post['id']
			)))
			->execute()
			->body();
		
		$this->template
			->set('widget_recent', $widget_recent)
			->set('widget_service', $widget_service)
			->set('post', $post);
	}
	
}