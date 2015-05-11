<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Actions extends Controller_Front {
	
	public $template = 'modules/actions/';
	
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
		
		$link_tpl = URL::base().Page_Route::uri($this->page_id, 'actions', array(
			'date' => '{date}',
			'uri' => '{uri}',
		));
		
		$_date = date('Y-m-d H:i:s');
		$orm = ORM::factory('action')
			->where('public_date', '<=', $_date)
			->and_where_open()
				->where('hidden_date', '>', $_date)
				->or_where('hidden_date', '=', '0000-00-00 00:00:00')
			->and_where_close()
			->where('public_date', '!=', '0000-00-00 00:00:00')
			->where('uri', '!=', '');
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('layout/more');
		$paginator
			->per_page(10)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);
		
		$_actions = $orm
			->paginator( $paginator )
			->find_all();
		
		$actions = array();
		$helper = ORM_Helper::factory('action');
		foreach ($_actions as $_row) {
			$_item = $_row->as_array();
			
			$_item['link'] = str_replace(array(
				'{date}', '{uri}'
			), array(
				date('Ymd', strtotime($_row->public_date)), $_row->uri
			), $link_tpl);

			if ( ! empty($_item['image'])) {
				$_item['image'] = URL::base().Thumb::uri('isotope_360', $helper->file_uri('image', $_item['image']));
			}
			
			$actions[] = $_item;
		}
		
		if ( ! empty($actions)) {
			$this->switch_on_plugin('isotope');
		}
		
		$this->template
			->set('page', $page->as_array())
			->set('actions', $actions)
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
		$date = $this->_parse_date($this->request->param('date'));
		
		$_action = ORM::factory('action')
			->where('uri', '=', $uri)
			->where('public_date', '>=', $date['start'])
			->where('public_date', '<', $date['end'])
			->find();
		
		if ( ! $_action->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$action = $_action->as_array();
		if ( ! empty($action['image'])) {
			$helper = ORM_Helper::factory('action');
			$action['image'] = URL::base().Thumb::uri('detail_555', $helper->file_uri('image', $action['image']));
		}
		
		$this->breadcrumbs[] = array(
			'title' => $action['title']
		);
		$this->breadcrumbs_title = $action['title'];
		
		$widget_service = Request::factory(Route::url('widgets', array(
				'controller' => 'service'
			)))
			->execute()
			->body();
		
		$widget_recent = Request::factory(Route::url('widgets', array(
				'controller' => 'recent',
				'query' => 'actions='.$action['id']
			)))
			->execute()
			->body();
		
		$this->template
			->set('widget_recent', $widget_recent)
			->set('widget_service', $widget_service)
			->set('action', $action);
	}
	
	private function _parse_date($date) {
		$y = substr($date, 0, 4);
		$m = substr($date, 4, 2);
		$d = substr($date, 6, 2);
		
		$ts = strtotime("{$y}-{$m}-{$d}");
		
		return array(
			'start' => date('Y-m-d 00:00:00', $ts),
			'end' => date('Y-m-d 00:00:00', strtotime('+1 day', $ts))
		);
	}
	
}