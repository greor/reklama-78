<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Actions extends Controller_Admin_Front {

	public $template = 'modules/actions/list';
	public $inner_layout = 'layout/inner';

	protected $top_menu_item = 'modules';
	protected $title = 'Actions';

	protected function get_aside_view()
	{
		$menu_items = array_merge_recursive(
			$this->module_config->get('left_menu'),
			$this->_ex_menu_items
		);

		return parent::get_aside_view()
			->set('menu_items', $menu_items);
	}

	public function action_index()
	{
		$orm = ORM::factory('action');
		$this->apply_filter($orm);
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);

		$elements = $orm
			->paginator( $paginator )
			->find_all();

		$this->sub_title = __('Actions list');
		$this->template
			->set_filename('modules/actions/list')
			->set('elements', $elements)
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
		$query_array = array();
		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$query = Helper_Page::make_query_string($query_array);

		$list_url = Route::url( 'modules', array(
			'controller' => 'actions',
			'query'      => $query,
		));
		if ($this->is_cancel) {
			Request::current()->redirect( $list_url );
		}

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('action');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('id', '=', $id)
				->find();

			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit action');
		} else {
			$this->sub_title = __('Add action');
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
				$multiple_date = Request::current()->post('multiple_date_p');
				$multiple_time = Request::current()->post('multiple_time_p');
				Request::current()->post( 'public_date', $multiple_date.' '.$multiple_time );
				
				/* hidden_date */
				$multiple_date = Request::current()->post('multiple_date_h');
				$multiple_time = Request::current()->post('multiple_time_h');
				Request::current()->post( 'hidden_date', $multiple_date.' '.$multiple_time );
				
				$values = $orm->check_meta_fields(
					Request::current()->post(),
					'meta_tags'
				);
				if (empty($values['uri']) OR $this->row_exist($orm, 'uri', $values['uri'])) {
					$values['uri'] = $this->unique_transliterate($values['title'], $orm, 'uri' );
				}
				$wrapper->save($values + $_FILES);

// 				Controller_Admin_Structure::clear_structure_cache();
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
				if ( ! empty($errors['_files'])) {
					$errors = array_merge($errors, $errors['_files']);
					unset($errors['_files']);
				}
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$this->template
				->set_filename('modules/actions/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper);
		} else {
			Request::current()->redirect( $list_url );
		}
	}

	public function action_delete()
	{
		$id = (int) Request::current()->param('id');

		$wrapper = ORM_Helper::factory('action');
		$wrapper->orm()
			->where('id', '=', $id)
			->find();

		if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
			throw new HTTP_Exception_404();
		}

		if ($this->delete_element($wrapper)) {
// 			Controller_Admin_Structure::clear_structure_cache();

			Request::current()->redirect(Route::url('modules', array(
				'controller' => 'actions',
			)));
		}
	}
} 
