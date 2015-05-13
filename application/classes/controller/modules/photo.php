<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Photo extends Controller_Front {
	
	public $template = 'modules/photo/';
	
	public function before()
	{
		$uri = $this->request->param('category_uri');
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
		
		$link_tpl = URL::base().Page_Route::uri($this->page_id, 'photo', array(
			'category_uri' => '{uri}'
		));
		
		$_date = date('Y-m-d H:i:s');
		$orm = ORM::factory('photo_Category')
			->where('public_date', '<=', $_date)
			->where('uri', '!=', '')
			->where('image', '!=', '');
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('layout/more');
		$paginator
			->per_page(9)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);
		
		$_albums = $orm
			->paginator( $paginator )
			->find_all();
		
		$albums = array();
		$helper = ORM_Helper::factory('photo_Category');
		foreach ($_albums as $_row) {
			$_item = $_row->as_array();
			$_item['link'] = str_replace('{uri}', $_row->uri, $link_tpl);
			$_item['image'] = URL::base().Thumb::uri('isotope_360x240', $helper->file_uri('image', $_item['image']));
			
			$albums[] = $_item;
		}
		
		if ( ! empty($albums)) {
			$this->switch_on_plugin('isotope');
		}
		
		$this->template
			->set('page', $page->as_array())
			->set('albums', $albums)
			->set('paginator', $paginator)
		;
		
	}
	
	
	public function action_detail()
	{
		$this->breadcrumbs[] = array(
			'title' => $this->request->page['title'],
			'link' => URL::base().$this->request->page['uri_full']
		);
		
		$uri = $this->request->param('category_uri');
		$_album = ORM::factory('photo_Category')
			->where('uri', '=', $uri)
			->find();
		
		if ( ! $_album->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$orm = $_album->photo;
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('layout/more');
		$paginator
			->per_page(6)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);
		
		$_photos = $orm
			->paginator( $paginator )
			->find_all();
		
		$photos = array();
		$helper = ORM_Helper::factory('photo');
		foreach ($_photos as $_row) {
			$_item = $_row->as_array();
			$_item['image'] = array(
				'thumb' => URL::base().Thumb::uri('isotope_360x240', $helper->file_uri('image', $_item['image'])),
				'full' => URL::base().Thumb::uri('isotope_800x600', $helper->file_uri('image', $_item['image'])),
			);
				
			$photos[] = $_item;
		}
		
		if ( ! empty($photos)) {
			$this->switch_on_plugin('isotope');
			$this->switch_on_plugin('fancybox');
		}
		
		$this->breadcrumbs[] = array(
			'title' => $_album->title
		);
		$this->breadcrumbs_title = $_album->title;
		
		$this->template
			->set('album', $_album->as_array())
			->set('photos', $photos)
			->set('paginator', $paginator);
	}
	
}