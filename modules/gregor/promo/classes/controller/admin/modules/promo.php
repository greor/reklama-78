<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Promo extends Controller_Admin_Front {

	public $template = 'modules/promo/list';
	public $inner_layout = 'layout/inner';

	protected $top_menu_item = 'modules';
	protected $title = 'Promo';

	protected function get_aside_view()
	{
		$resource_name = ORM::factory('promo')
			->object_name();
		if ($this->acl->is_allowed($this->user, $resource_name, 'fix_all')){
			$this->left_menu_fix();
		}
		
		$menu_items = array_merge_recursive(
			$this->module_config->get('left_menu'),
			$this->_ex_menu_items
		);

		return parent::get_aside_view()
			->set('menu_items', $menu_items);
	}

	public function action_index()
	{
		$orm = ORM::factory('promo');
		
		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);

		$elements = $orm
			->paginator( $paginator )
			->find_all();

		$this->sub_title = __('Banners list');
		$this->template
			->set_filename('modules/promo/list')
			->set('elements', $elements)
			->set('paginator', $paginator);
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
			'controller' => 'promo',
			'query'      => $query,
		));
		if ($this->is_cancel) {
			Request::current()->redirect( $list_url );
		}

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('promo');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('id', '=', $id)
				->find();

			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit banner');
		} else {
			$this->sub_title = __('Add banner');
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
				
				
				$values = Request::current()->post();
				$values['settings'] = serialize($values['settings']);
				
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
				->set_filename('modules/promo/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper);
		} else {
			Request::current()->redirect( $list_url );
		}
	}

	public function action_delete()
	{
		$id = (int) Request::current()->param('id');

		$wrapper = ORM_Helper::factory('promo');
		$wrapper->orm()
			->where('id', '=', $id)
			->find();

		if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
			throw new HTTP_Exception_404();
		}

		if ($this->delete_element($wrapper)) {
// 			Controller_Admin_Structure::clear_structure_cache();

			Request::current()->redirect(Route::url('modules', array(
				'controller' => 'promo',
			)));
		}
	}
	
	public function action_position()
	{
		$id = (int) Request::current()->param('id');
		$mode = Request::current()->query('mode');
	
		$errors = array();
		$wrapper = ORM_Helper::factory('promo');
	
		try {
			if ( $mode !== 'fix' ) {
				$wrapper->orm()
					->and_where('id', '=', $id)
					->find();
				if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
					throw new HTTP_Exception_404();
				}
					
				switch ($mode) {
					case 'up':
						$wrapper->position_move('position', ORM_Position::MOVE_PREV);
						break;
					case 'down':
						$wrapper->position_move('position', ORM_Position::MOVE_NEXT);
						break;
				}
			} elseif ( $mode == 'fix' ) {
				if ($this->acl->is_allowed($this->user, $wrapper->orm(), 'fix_all')) {
					$wrapper->position_fix('position');
				}
			}
	
// 			Controller_Admin_Structure::clear_structure_cache();
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( TRUE );
			$this->template
				->set_filename('layout/error')
				->set('errors', $errors)
				->set('title', __('Error'));
		}
	
		if (empty($errors)) {
			$query_array = array();
			if (Request::current()->query(Paginator::QUERY_PARAM)) {
				$query_array[Paginator::QUERY_PARAM] = Request::current()->query(Paginator::QUERY_PARAM);
			}
			Request::current()->redirect( Route::url('modules', array(
				'controller' => 'promo',
				'query'      => Helper_Page::make_query_string($query_array),
			)));
		}
	}
	
	private function left_menu_fix()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'fix' => array(
				'title' => __('Fix positions'),
				'link'  => Route::url('modules', array(
					'controller' => 'promo',
					'action'     => 'position',
					'query'      => 'mode=fix',
				)),
			),
		));
	}
} 
