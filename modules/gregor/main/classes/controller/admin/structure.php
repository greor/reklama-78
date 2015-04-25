<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Structure extends Controller_Admin_Front {

	public $template = 'structure/list';
	protected $top_menu_item = 'structure';

	protected $title = 'Site structure';

	public function get_aside_view()
	{
		$menu_items = Kohana::$config
			->load('admin/structure.left_menu');
		
		return parent::get_aside_view()
				->set('menu_items', $menu_items);
	}

	public function action_index()
	{
		$pages = ORM::factory('page')
			->order_by('level', 'asc')
			->order_by('position', 'asc')
			->find_all()
			->as_array('id');

		$this->template
			->set('pages', $this->parse_to_tree($pages))
			->set('base_uri_list', Helper_Page::parse_to_base_uri($pages) )
			->set('modules', Helper_Module::modules());
	}

	public function action_edit()
	{
		$list_url = Route::url( 'admin', array(
			'controller' => 'structure',
		));
		if ($this->is_cancel) {
			Request::current()
				->redirect($list_url);
		}

		$id = (int) Request::current()->param('id');
		$pages = ORM::factory('page')
			->select('id', 'parent_id', 'uri', 'title', 'level')
			->order_by('level', 'asc')
			->order_by('parent_id', 'asc')
			->find_all();

		$relations = $pages->as_array('id', 'parent_id');
		$pages = $pages->as_array('id');

		if ( (bool) $id) {
			$page_wrapper = ORM_Helper::factory('page', $id);
			$this->sub_title = __('Edit page');

			if ( ! $this->acl->is_allowed($this->user, $page_wrapper->orm(), 'edit') ) {
				Request::current()->redirect( $list_url );
			}
		} else {
			$page_wrapper = ORM_Helper::factory('page');
			$this->sub_title = __('Add page');
		}

		$errors = array();

		$submit = Request::$current->post('submit');
		if ($submit) {
			try {
				$orm = $page_wrapper->orm();

				if ( (bool) $id) {
					$orm->updater_id = $this->user->id;
					$orm->updated = date('Y-m-d H:i:s');
				} else {
					$orm->creator_id = $this->user->id;
				}
				$parent_id = (int) Request::current()->post('parent_id');

				if ( $parent_id > 0 AND isset( $pages[ $parent_id ] ) ) {
					$orm->level = $pages[ $parent_id ]->level + 1;
				} else {
					$orm->level = Model_Page::LEVEL_START;
				}

				$values = $orm->check_meta_fields(
					Request::current()->post(),
					'meta_tags'
				);

				if (empty($values['uri']) OR $this->row_exist($orm, 'uri', $values['uri'])) {
					$values['uri'] = $this->unique_transliterate( $values['title'], $orm, 'uri' );
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'status_change')) {
					unset($values['status']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'page_type_change')) {
					unset($values['type']);
					unset($values['data']);
				}
				$page_wrapper->save($values + $_FILES);

				self::clear_structure_cache();
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$modules = array();
			$linked_modules = Helper_Module::linked_modules();
			$leave_module_types = array(
				Helper_Module::MODULE_SINGLE, 
				Helper_Module::MODULE_STANDALONE
			);
			$this_page = $page_wrapper->orm()
				->as_array();
			
			foreach (Helper_Module::modules() as $key => $value) {
				$_own_module = ($this_page['type'] == 'module' AND $this_page['data'] == $key);
				if (
					in_array($key, $linked_modules) 
					AND in_array($value['type'], $leave_module_types)
					AND ! $_own_module
				) {
					continue;
				}

				$modules[ $key ] = __( $value['name'] );
			}

			if ( (bool) $id) {
				$page_list = array_diff_key(
					Helper_Page::parse_to_list($pages),
					array_flip( $this->_get_childrens($id, $relations) )
				);
			} else {
				$page_list = Helper_Page::parse_to_list($pages);
			}

			$this->template
				->set_filename('structure/edit')
				->set('errors', $errors)
				->set('page_wrapper', $page_wrapper)
				->set('pages', $page_list )
				->set('base_uri_list', Helper_Page::parse_to_base_uri($pages) )
				->set('modules', $modules);
		} else {
			Request::current()->redirect( $list_url );
		}
	}

	public function action_delete()
	{
		$list_url = Route::url('admin', array(
			'controller' => 'structure',
		));

		$id = (int) Request::current()->param('id');
		$page_wrapper = ORM_Helper::factory('page', $id);

		$hasnt_module = Helper_Page::instance()
			->not_equal($page_wrapper->orm(), 'type', 'module');
		$has_name = ! empty($page_wrapper->orm()->name);

		if ( ! $this->acl->is_allowed($this->user, $page_wrapper->orm(), 'edit') OR ! $hasnt_module OR $has_name) {
			Request::current()->redirect( $list_url );
		}

		try {
			$old_deleter_id = $page_wrapper->orm()->deleter_id;
			$page_wrapper
				->save( array( 'deleter_id' => $this->user->id, 'deleted' => date('Y-m-d H:i:s') ) )
				->delete( FALSE );

			self::clear_structure_cache();
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( TRUE );

			try {
				$page_wrapper->save( array( 'deleter_id' => $old_deleter_id ) );
			} catch (ORM_Validation_Exception $e) {
				$errors = array_merge( $errors, $e->errors( TRUE ) );
			}

			$this->template
				->set_filename('layout/error')
				->set('errors', $errors)
				->set('title', __('Error'));
		}

		if (empty($errors)) {
			Request::current()->redirect( $list_url );
		}
	}

	public function action_position()
	{
		$id = (int) Request::current()->param('id');
		$mode = Request::current()->query('mode');
		
		$errors = array();
		try {
			if ( $mode !== 'fix' ) {
				$wrapper = ORM_Helper::factory('page', $id);
				
				if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit') )
					throw new HTTP_Exception_404();
				
				switch ($mode) {
					case 'up':
						$wrapper->position_move('position', ORM_Position::MOVE_PREV);
						break;
					case 'down':
						$wrapper->position_move('position', ORM_Position::MOVE_NEXT);
						break;
				}
			} else {
				ORM_Helper::factory('page')
					->position_fix('position');
			}

			self::clear_structure_cache();
			
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( TRUE );
			$this->template
				->set_filename('layout/error')
				->set('errors', $errors)
				->set('title', __('Error'));
		}

		if (empty($errors)) {
			Request::current()->redirect(Route::url('admin', array(
				'controller' => 'structure',
			)));
		}

	}

	public function action_clear_cache()
	{
		self::clear_structure_cache();
		
		Request::current()->redirect(Route::url('admin', array(
			'controller' => 'structure',
		)));
	}

	public static function clear_structure_cache()
	{
		if ( ! DONT_USE_CACHE) {
			Cache::instance('file-struct')
				->delete_all(TRUE);
			Helper_Page::clear_cache();
		}
	}

	public function parse_to_tree($page_list)
	{
		$childrens = array();
		foreach ($page_list as $item) {
			$childrens[ $item->id ] = array();
			if ( isset($childrens[ $item->parent_id ])) {
				$childrens[ $item->parent_id ][] = $item->id;
			}
		}

		$return = array();
		foreach ($page_list as $item) {
			if ( $item->parent_id != 0 )
				continue;
			
			$return[ $item->id ] = array(
				'object'    => $item,
				'childrens' => $this->_tree_childrens($item->id, $page_list, $childrens),
			);
		}
		return $return;
	}

	private function _tree_childrens($id, $page_list, $childrens)
	{
		$return = array();
		if ( empty( $childrens[ $id ] ) )
			return $return;
		
		foreach ($childrens[ $id ] as $child_id) {
			$item = $page_list[ $child_id ];
			$return[ $item->id ] = array(
				'object'    => $item,
				'childrens' => $this->_tree_childrens($item->id, $page_list, $childrens),
			);
		}
		return $return;
	}

	private function _get_childrens($id, $relations, $self_include = TRUE)
	{
		$return = array();
		$proc_ids = array( $id );

		$stop = FALSE;
		while ( ! $stop) {
			$childrens = array();
			foreach ($proc_ids as $v) {
				if (in_array($v, $return))
					continue;
				$childrens = array_merge($childrens, array_keys($relations, $v));
			}

			$proc_ids = $childrens;
			if (empty($proc_ids)) {
				$stop = TRUE;
			} else {
				$return = array_merge($return, $childrens);
			}
		}
		if ($self_include) {
			$return[] = $id;
		}
		return $return;
	}

} 
