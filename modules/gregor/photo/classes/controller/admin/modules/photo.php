<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Photo extends Controller_Admin_Front {

	public $template = 'modules/photo/list';
	public $inner_layout = 'layout/inner';

	protected $top_menu_item = 'modules';
	protected $title = 'Photo';
	protected $category_id;

	private $not_deleted_categories = array();

	public function before()
	{
		parent::before();
		$this->category_id = (int) Request::current()->query('cid');
	}

	protected function get_aside_view()
	{
		$category_resource_name = ORM::factory('photo_Category')->object_name();
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
		$orm = ORM::factory('photo_Category')
			->where('page_id', '=', $this->module_page_id);
		
		$this->apply_category_filter($orm);
		
		$categories = $orm->find_all();
		
		if ($this->acl->is_allowed($this->user, $orm, 'add')) {
			$this->left_menu_add_category();
		}
		$this->sub_title = __('Albums');
		$this->template
			->set_filename('modules/photo/list_category')
			->set('categories', $categories)
			->set('not_deleted_categories', $this->not_deleted_categories);
	}

	public function action_category()
	{
		$this->category_id = $id = (int) Request::current()->param('id');
		$category_orm = ORM::factory('photo_Category')
			->where('page_id', '=', $this->module_page_id)
			->and_where('id', '=', $this->category_id)
			->find();
		if ( ! $category_orm->loaded()) {
			throw new HTTP_Exception_404();
		}
		
		$element_orm = ORM::factory('photo')
			->where('category_id', '=', $this->category_id);
		
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
			if ($this->acl->is_allowed($this->user, ORM::factory('photo_Category'), 'edit')) {
				$this->left_menu_add_category();
			}
			$this->left_menu_add_photo();
		}
		$this->sub_title = __('Photo list');
		$this->template
			->set_filename('modules/photo/list')
			->set('elements', $elements)
			->set('category_id', $this->category_id)
			->set('breadcrumbs', $this->get_breadcrumbs($this->category_id))
			->set('paginator', $paginator);
	}

	private function apply_category_filter($orm)
	{
		$_groups = Kohana::$config
			->load('_photo.groups');
		$filter_query = $this->request->query('filter');
		$city = Arr::get($filter_query, 'city', key($_groups));
		$orm->where('group', '=', $city);
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
			'controller' => 'photo',
			'action'     => 'category',
			'id'         => $this->category_id,
			'query'      => Helper_Page::make_query_string($query_array),
		));

		if ($this->is_cancel) {
			Request::current()->redirect( $list_url );
		}

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('photo');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('id', '=', $id)
				->find();

			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit photo');
		} else {
			$this->sub_title = __('Add photo');
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
			$_categories = ORM::factory('photo_Category')
				->where('page_id', '=', $this->module_page_id)
				->find_all()
				->as_array('id');
			$categories = array_map(array($this, 'map_categories'), $_categories);
			
			if ( ! (bool) $id) {
				$wrapper->orm()->category_id = $this->category_id;
			}
			$this->template
				->set_filename('modules/photo/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper)
				->set('categories', $categories);
		} else {
			Request::current()->redirect($list_url);
		}
	}
	
	private function map_categories($item)
	{
		static $_groups;
		
		$_groups = empty($_groups)
			? Kohana::$config->load('_photo.groups')
			: $_groups;
		
		return $item->title.' ( '.Arr::get($_groups, $item->group, $item->group).' )';
	}

	public function action_delete()
	{
		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('photo');
		$wrapper->orm()
			->where('id', '=', $id)
			->find();

		if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit') ) {
			throw new HTTP_Exception_404();
		}

		if ($this->delete_element($wrapper)) {
			Request::current()->redirect( Route::url('modules', array(
				'controller' => 'photo',
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
			'controller' => 'photo',
			'query'      => Helper_Page::make_query_string($query_array),
		));

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('photo_Category');
		if ( (bool) $id) {
			$wrapper->orm()
				->where('page_id', '=', $this->module_page_id)
				->and_where('id', '=', $id)
				->find();
			if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				throw new HTTP_Exception_404();
			}
			$this->sub_title = __('Edit album');
		} else {
			$this->sub_title = __('Add album');
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

				/* public_date */
				$multiple_date = Request::current()->post('multiple_date');
				$multiple_time = Request::current()->post('multiple_time');
				Request::current()->post('public_date', $multiple_date.' '.$multiple_time);
				
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
				->set_filename('modules/photo/edit_category')
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

		$wrapper = ORM_Helper::factory('photo_Category');
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
				'controller' => 'photo',
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
		$wrapper = ORM_Helper::factory('photo');

		try {
			if ( $mode !== 'fix' ) {
				$wrapper->orm()
					->where('category_id', '=', $this->category_id)
					->and_where('id', '=', $id)
					->find();
				if ( ! $wrapper->orm()->loaded() OR ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit'))
					throw new HTTP_Exception_404();
				
				switch ($mode) {
					case 'up':
						$wrapper->position_move('position', ORM_Position::MOVE_PREV);
						break;
					case 'down':
						$wrapper->position_move('position', ORM_Position::MOVE_NEXT);
						break;
					case 'first':
						$wrapper->position_first( 'position' );
						break;
					case 'last':
						$wrapper->position_last( 'position' );
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
			
			if ( ! empty($this->category_id)) {
				$query_array = array(
					'pid' => $this->module_page_id,
					'cid' => $this->category_id,
				);
				$p = Request::current()->query( Paginator::QUERY_PARAM );
				if ( ! empty($p)) {
					$query_array[ Paginator::QUERY_PARAM ] = $p;
				}
				$list_url = Route::url( 'modules', array(
					'controller' => 'photo',
					'action'     => 'category',
					'id'         => $this->category_id,
					'query'      => Helper_Page::make_query_string($query_array),
				));
			} else {
				$list_url = Route::url('modules', array(
					'controller' => 'photo',
					'query'      => Helper_Page::make_query_string(array(
						'pid' => $this->module_page_id,
					)),
				));
			}
			
			Request::current()->redirect( $list_url );
		}
	}

	public function action_multiupload()
	{
		Session::instance()->delete('multiuploaded');
		
		$_categories = ORM::factory('photo_Category')
			->order_by('title', 'asc')
			->find_all()
			->as_array('id');
		$categories = array_map(array($this, 'map_categories'), $_categories);
		
		$this->template
			->set_filename('modules/photo/multiupload')
			->set('categories', $categories);
	}
	
	public function action_upload()
	{
		$category_orm = ORM::factory('photo_Category')
			->where('id', '=', (int) $_REQUEST['category_id'])
			->find();
	
		if ( ! $category_orm->loaded() OR ! $this->acl->is_allowed($this->user, $category_orm, 'edit')) {
			throw new HTTP_Exception_404();
		}
	
		// HTTP headers for no cache etc
		header("Content-Type: application/json; charset=utf-8");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	
		$targetDir = DOCROOT.'upload'.DIRECTORY_SEPARATOR.'multiupload';
	
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 3600; // Temp file age in seconds
		@set_time_limit(5 * 60);
	
		// Get parameters
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
	
		if ( ! preg_match('/^jpe?g$/s', $ext)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 105, "message": "Invalid file type."}, "id" : "id"}');
		}
	
		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
	
		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists($targetDir.DIRECTORY_SEPARATOR.$fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);
	
			$count = 1;
			while (file_exists($targetDir.DIRECTORY_SEPARATOR.$fileName_a.'_'.$count.$fileName_b)) {
				$count++;
			}
	
			$fileName = $fileName_a.'_'.$count.$fileName_b;
		}
	
		$filePath = $targetDir.DIRECTORY_SEPARATOR.$fileName;
	
		// Create target dir
		if (!file_exists($targetDir)) {
			@mkdir($targetDir);
		}
	
		// Remove old temp files
		if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = $targetDir.DIRECTORY_SEPARATOR.$file;
	
				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
					@unlink($tmpfilePath);
				}
			}
	
			closedir($dir);
		} else {
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		}
	
	
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		}
	
		if (isset($_SERVER["CONTENT_TYPE"])) {
			$contentType = $_SERVER["CONTENT_TYPE"];
		}
	
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");
	
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else {
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					}
					fclose($in);
					fclose($out);
					@unlink($_FILES['file']['tmp_name']);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
				}
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
			}
		} else {
			// Open temp file
			$out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");
	
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				}
	
				fclose($in);
				fclose($out);
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
		}
	
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);
			$add_to_head = ! empty($_REQUEST['add_to_head']);
			$save_result = $this->save_uploaded_file($filePath, $_REQUEST['category_id'], $add_to_head);
			if ($save_result !== TRUE) {
				die('{"jsonrpc" : "2.0", "error" : {"code": 104, "message": '.json_encode($save_result).'}, "id" : "id"}');
			}
		}
	
		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	}
	
	public function action_group_operation()
	{
		$this->auto_render = FALSE;
	
		$ids = $this->request->post('ids');
		$operation = (string) $this->request->post('operation');
	
		if (empty($ids) OR ! is_array($ids) OR empty($operation) OR ! method_exists($this, 'group_operation_'.$operation)) {
			throw new HTTP_Exception_404();
		}
	
		$category_orm = ORM::factory('photo_Category')
			->where('id', '=', $this->category_id)
			->find();
	
		if ( ( ! $category_orm->loaded() OR ! $this->acl->is_allowed($this->user, $category_orm, 'edit'))) {
			throw new HTTP_Exception_404();
		}
	
		try {
			$this->{'group_operation_'.$operation}($ids, $category_orm);
		} catch (Exception $e) {
			$code = $e->getCode();
			$this->response
				->status(array_key_exists($code, Response::$messages) ? $code : 500)
				->body(Kohana_Exception::text($e));
			return;
		}
	
		$this->response->body('OK');
	}
	
	private function save_uploaded_file($file_path, $category_id, $add_to_head)
	{
		$photo_wrapper = ORM_Helper::factory('photo');
		try {
			$photo_wrapper->save(array(
				'category_id' => $category_id,
				'title'       => basename($file_path),
				'image'       => $file_path,
				'active'      => TRUE,
				'creator_id'  => $this->user->id,
			));
			
			if ($add_to_head)
			{
				$config = Arr::get($photo_wrapper->position_fields(), 'position');
				$_pos = Session::instance()->get('multiuploaded', $config['step']);
				$photo_wrapper->position_set('position', $_pos);
				Session::instance()->set('multiuploaded', $_pos + $config['step']);
			}
			
		} catch (ORM_Validation_Exception $e) {
			return implode( '. ', $e->errors('') );
		} catch (Exception $e) {
			Kohana::$log->add(
				Log::ERROR,
				'Multiupload error. Exception occurred: :exception',
				array(
					':exception' => $e->getMessage()
				)
			);
			return 'Internal error';
		}
		return TRUE;
	}
	
	private function get_breadcrumbs($category_id)
	{
		return ORM::factory('photo_Category', $category_id);
	}
	
	private function left_menu_add_category()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'photo' => array(
				'sub' => array(
					'add_category' => array(
						'title'    => __('Add album'),
						'link'     => Route::url('modules', array(
							'controller' => 'photo',
							'action'     => 'edit_category',
							'query'      => 'pid={PAGE_ID}',
						)),
					),
				),
			),
		));
	}
	
	private function left_menu_add_photo()
	{
		$this->_ex_menu_items = array_merge_recursive($this->_ex_menu_items, array(
			'photo' => array(
				'sub' => array(
					'add_photo' => array(
						'title'   => __('Add photo'),
						'link'    => Route::url('modules', array(
							'controller' => 'photo',
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
					'controller' => 'photo',
					'action'     => 'position',
					'query'      => 'cid={CATEGORY_ID}&pid={PAGE_ID}&mode=fix',
				)),
			),
		));
	}
	
	private function group_operation_move_up(array $ids, Model_Photo_Category $category)
	{
		$this->_group_operation_move('up', $ids, $category);
	}
	
	private function group_operation_move_down(array $ids, Model_Photo_Category $category)
	{
		$this->_group_operation_move('down', $ids, $category);
	}
	
	private function group_operation_move_first(array $ids, Model_Photo_Category $category)
	{
		$this->_group_operation_move('first', $ids, $category);
	}
	
	private function group_operation_move_last(array $ids, Model_Photo_Category $category)
	{
		$this->_group_operation_move('last', $ids, $category);
	}
	
	private function _group_operation_move($operation, array $ids, Model_Photo_Category $category)
	{
		$directions = array(
			'up'    => ORM_Position::MOVE_PREV,
			'down'  => ORM_Position::MOVE_NEXT,
			'first' => ORM_Position::MOVE_FIRST,
			'last'  => ORM_Position::MOVE_LAST,
		);
	
		if ( ! isset($directions[$operation]))
			throw new HTTP_Exception_404();
	
		$direction = $directions[$operation];
	
		if ($operation === 'first' OR $operation === 'down') {
			$ids = array_reverse($ids);
		}
	
		foreach ($ids as $id) {
			$wrapper = ORM_Helper::factory('photo');
			$item = $wrapper->orm()
				->where('category_id', '=', $category->id)
				->and_where('id', '=', $id)
				->find();
	
			if ( ! $item->loaded())
				continue;
	
			$wrapper->position_move('position', $direction);
		}
	}
} 
