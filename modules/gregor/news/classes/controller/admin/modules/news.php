<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_News extends Controller_Admin_Front {

	public $template = 'modules/news/list';
	public $inner_layout = 'layout/inner';

	protected $top_menu_item = 'modules';
	protected $title = 'News';
	protected $category_id;

	private $not_deleted_categories = array();

	public function before()
	{
		parent::before();

		$this->category_id = (int) Request::current()->query('cid');
	}

	protected function get_aside_view()
	{
		$category_resource_name = ORM::factory('news_Category')->object_name();
		if ( $this->acl->is_allowed($this->user, $category_resource_name, 'fix_all') ) {
			$this->left_menu_fix();
		}
		
		$menu_items = array_merge_recursive(
			$this->module_config->get('left_menu'),
			$this->_ex_menu_items
		);

		return parent::get_aside_view()
			->set('menu_items', $menu_items)
			->set('replace', array(
				'{CATEGORY_ID}' =>	$this->category_id,
				'{PAGE_ID}' =>	$this->module_page_id,
			));
	}

	public function action_index()
	{
		$categories = ORM::factory('news_Category')
			->where('page_id', '=', $this->module_page_id)
			->order_by('position', 'asc')
			->find_all();
		
		if ($this->acl->is_allowed($this->user, ORM::factory('news_Category'), 'add')) {
			$this->left_menu_add_category();
		}
		$this->sub_title = __('Category list');
		$this->template
			->set_filename('modules/news/list_category')
			->set('categories', $categories)
			->set('not_deleted_categories', $this->not_deleted_categories);
	}

	public function action_category()
	{
		$this->category_id = $id = (int) Request::current()->param('id');
		$category_orm = ORM::factory('news_Category')
			->where('page_id', '=', $this->module_page_id)
			->and_where('id', '=', $this->category_id)
			->find();
		if ( ! $category_orm->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$element_orm = ORM::factory('news')
			->where('category_id', '=', $this->category_id)
			->order_by('public_date', 'desc');
		$this->apply_filter($element_orm);
		
		$paginator_orm = clone $element_orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);

		$elements = $element_orm
			->paginator( $paginator )
			->find_all();

		if ( $category_orm->loaded() ) {
			if ($this->acl->is_allowed($this->user, ORM::factory('news_Category'), 'edit')) {
				$this->left_menu_add_category();
			}
			$this->left_menu_add_news();
		}
		$this->sub_title = __('News list');
		$this->template
			->set_filename('modules/news/list')
			->set('elements', $elements)
			->set('category_id', $this->category_id)
			->set('breadcrumbs', $this->get_breadcrumbs($this->category_id))
			->set('paginator', $paginator);
	}

	private function apply_filter($orm)
	{
		$filter_query = $this->request->query('filter');
		if ( ! empty($filter_query)) {
			$title = $filter_query['title'];
			if ( ! empty($title)) {
				$orm->where('title', 'like', '%'.$title.'%');
			}
		}
	}

	public function action_edit()
	{
		$query_array = array(
			'pid' => $this->module_page_id,
			'cid' => $this->category_id,
		);
		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$list_url = Route::url( 'modules', array(
			'controller' => 'news',
			'action'     => 'category',
			'id'         => $this->category_id,
			'query'      => Helper_Page::make_query_string($query_array),
		));

		if ($this->is_cancel) {
			Request::current()->redirect( $list_url );
		}

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('news');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('id', '=', $id)
				->find();

			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit news');

		} else {
			$this->sub_title = __('Add news');
		}

		$errors = array();
		$submit = Request::$current->post('submit');
		if ($submit) {
			try {
				$orm = $wrapper->orm();

				if ( (bool) $id) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
				} else {
					$orm->creator_id = $this->user->id;
				}

				/* public_date */
				$multiple_date = Request::current()->post('multiple_date');
				$multiple_time = Request::current()->post('multiple_time');
				Request::current()->post( 'public_date', $multiple_date.' '.$multiple_time );

				$values = $orm->check_meta_fields(
					Request::current()->post(),
					'meta_tags'
				);
				
				$wrapper->save($values + $_FILES);
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
				if ( ! empty($errors['_files'])) {
					$errors = array_merge($errors, $errors['_files']);
					unset($errors['_files']);
				}
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$categories = ORM::factory('news_Category')
				->where('page_id', '=', $this->module_page_id)
				->order_by('position', 'asc')
				->find_all()
				->as_array('id', 'title');
			if ( ! (bool) $id) {
				$wrapper->orm()->category_id = $this->category_id;
			}

			$this->template
				->set_filename('modules/news/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper)
				->set('categories', $categories);
		} else {
			Request::current()->redirect($list_url);
		}
	}

	public function action_delete()
	{
		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('news');
		$wrapper->orm()
			->where('id', '=', $id)
			->find();

		if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit') ) {
			throw new HTTP_Exception_404();
		}

		if ($this->delete_element($wrapper)) {
			Request::current()->redirect( Route::url('modules', array(
				'controller' => 'news',
				'action'     => 'category',
				'id'         => $this->category_id,
				'query'      => Helper_Page::make_query_string(array(
					'pid' => $this->module_page_id,
					'cid' => $this->category_id,
				)),
			)));
		}
	}

	public function action_edit_category()
	{
		$query_array = array(
			'pid' => $this->module_page_id,
		);
		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$list_url = Route::url( 'modules', array(
			'controller' => 'news',
			'query'      => Helper_Page::make_query_string($query_array),
		));

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('news_Category');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('page_id', '=', $this->module_page_id)
				->and_where('id', '=', $id)
				->find();
			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit category');
		} else {
			$this->sub_title = __('Add category');
		}
		if ($this->is_cancel) {
			Request::$current->redirect($list_url);
		}

		$errors = array();
		$submit = Request::$current->post('submit');
		if ($submit) {
			try {
				$orm = $wrapper->orm();
				if ( (bool) $id) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
				} else {
					$orm->page_id = $this->module_page_id;
					$orm->creator_id = $this->user->id;
				}

				$values = $orm->check_meta_fields(
					Request::$current->post(),
					'meta_tags'
				);
				if (empty($values['uri']) OR $this->row_exist($orm, 'uri', $values['uri'])) {
					$values['uri'] = $this->unique_transliterate($values['title'], $orm, 'uri');
				}
				
				$wrapper->save($values);
				Controller_Admin_Structure::clear_structure_cache();
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors('');
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$this->template
				->set_filename('modules/news/edit_category')
				->set('errors', $errors)
				->set('wrapper', $wrapper)
				->set('not_deleted_categories', $this->not_deleted_categories);
		} else {
			Request::current()->redirect( $list_url );
		}
	}

	public function action_delete_category()
	{
		$this->category_id = $id = (int) Request::current()->param('id');

		$wrapper = ORM_Helper::factory('news_Category');
		$wrapper->orm()
			->where('page_id', '=', $this->module_page_id)
			->and_where('id', '=', $id)
			->find();

		if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
			throw new HTTP_Exception_404();
		}
		if (in_array($wrapper->orm()->code, $this->not_deleted_categories)) {
			throw new HTTP_Exception_404();
		}

		if ($this->delete_element($wrapper)) {
			Controller_Admin_Structure::clear_structure_cache();
			
			Request::current()->redirect(Route::url('modules', array(
				'controller' => 'news',
				'query'      => Helper_Page::make_query_string(array(
					'pid' => $this->module_page_id,
				)),
			)));
		}
	}

	public function action_position()
	{
		$id = (int) Request::current()->param('id');
		$mode = Request::current()->query('mode');
		$errors = array();
		$wrapper = ORM_Helper::factory('news_Category');

		try {
			if ( $mode !== 'fix' ) {
				$wrapper->orm()
					->where('page_id', '=', $this->module_page_id)
					->and_where('id', '=', $id)
					->find();
				if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit'))
					throw new HTTP_Exception_404();
				
				switch ($mode) {
					case 'up':
						$wrapper->position_move( 'position', ORM_Position::MOVE_PREV );
						break;
					case 'down':
						$wrapper->position_move( 'position', ORM_Position::MOVE_NEXT );
						break;
				}
			} elseif ( $mode == 'fix' ) {
				if ($this->acl->is_allowed($this->user, $wrapper->orm(), 'fix_all')) {
					$wrapper->position_fix('position');
				}
			}

			Controller_Admin_Structure::clear_structure_cache();
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( '' );
			$this->template
				->set_filename('layout/error')
				->set('errors', $errors)
				->set('title', __('Error'));
		}

		if (empty($errors)) {
			Request::current()->redirect( Route::url('modules', array(
				'controller' => 'news',
				'query'      => Helper_Page::make_query_string(array(
					'pid' => $this->module_page_id,
				)),
			)));
		}
	}

	private function get_breadcrumbs($category_id)
	{
		return ORM::factory('news_Category', $category_id);
	}
	
	private function left_menu_add_category()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'news' => array(
				'sub' => array(
					'add_category' => array(
						'title'    => __('Add category'),
						'link'     => Route::url('modules', array(
							'controller' => 'news',
							'action'     => 'edit_category',
							'query'      => 'pid={PAGE_ID}',
						)),
					),
				),
			),
		));
	}
	
	private function left_menu_add_news()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'news' => array(
				'sub' => array(
					'add_news' => array(
						'title'   => __('Add news'),
						'link'    => Route::url('modules', array(
							'controller' => 'news',
							'action'     => 'edit',
							'query'      => 'cid={CATEGORY_ID}&pid={PAGE_ID}',
						)),
					),
				),
			),
		));
	}
	
	private function left_menu_fix()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'fix' => array(
				'title' => __('Fix positions'),
				'link'  => Route::url('modules', array(
					'controller' => 'news',
					'action'     => 'position',
					'query'      => 'cid={CATEGORY_ID}&pid={PAGE_ID}&mode=fix',
				)),
			),
		));
	}
} 
