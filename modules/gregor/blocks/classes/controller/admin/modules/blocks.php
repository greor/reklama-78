<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Blocks extends Controller_Admin_Front {

	public $template = 'modules/blocks/list';
	public $inner_layout = 'layout/inner';

	protected $top_menu_item = 'modules';
	protected $title = 'Blocks';

	protected function get_aside_view()
	{
		$resource_name = ORM::factory('block')
			->object_name();
		if ($this->acl->is_allowed($this->user, $resource_name, 'add')){
			$this->left_menu_add();
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
		$elements = ORM::factory('block')
			->find_all();

		$this->sub_title = __('Block list');
		$this->template
			->set_filename('modules/blocks/list')
			->set('elements', $elements);
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
			'controller' => 'blocks',
			'query'      => $query,
		));
		if ($this->is_cancel) {
			Request::current()->redirect( $list_url );
		}

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('block');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('id', '=', $id)
				->find();

			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit block');
		} else {
			if ( ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'add')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Add block');
		}

		$errors = array();
		$submit = Request::$current->post('submit');
		if ($submit) {
			try {
				$orm = $wrapper->orm();
				$values = Request::current()->post();
				
				if ( ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'add')) {
					unset($values['name']);
					unset($values['code']);
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
				->set_filename('modules/blocks/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper);
		} else {
			Request::current()->redirect( $list_url );
		}
	}
	
	public function action_delete()
	{
		$id = (int) Request::current()->param('id');
	
		$wrapper = ORM_Helper::factory('block');
		$wrapper->orm()
			->where('id', '=', $id)
			->find();
	
		if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'add')) {
			throw new HTTP_Exception_404();
		}
	
		if ($this->delete_element($wrapper)) {
			Request::current()->redirect(Route::url('modules', array(
				'controller' => 'blocks',
			)));
		}
	}

	private function left_menu_add()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'blocks' => array(
				'sub' => array(
					'add_service' => array(
						'title' => __('Add block'),
						'link'  => Route::url('modules', array(
							'controller' => 'blocks',
							'action'     => 'edit',
						)),
					),
				),
			),
		));
	}
} 
