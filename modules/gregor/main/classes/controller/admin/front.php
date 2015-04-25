<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Front extends Controller_Template {

	/* Common admin part config */
	protected $admin_config = 'admin/admin';
	protected $module_config = FALSE;

	/* Page templates */
	public $layout = 'layout/main';
	public $inner_layout = 'layout/inner';
	public $aside = 'layout/aside';

	/* Template vars */
	protected $body_class;
	protected $title;
	protected $sub_title;

	protected $get = array();

	protected $breadcrumbs = array();

	/* Top menu */
	protected $top_menu_item;
	protected $_ex_menu_items = array();

	/* Lenked module pages */
	protected $module_pages = array();
	protected $module_page_id;

	/* Cancel button pressed */
	protected $is_cancel;

	/* Module controller flag */
	protected $is_module = FALSE;

	protected $acl;
	protected $user;
	protected $site;

	public function before()
	{
		Session::$default = 'admin';

		$template = $this->template;
		$this->template = NULL;
		parent::before();
		if ($this->auto_render === TRUE) {
			$this->template = View_Admin::factory($template);
		}
		
		if ( ! Kohana::$is_cli ) {
			$this->acl = A2::instance('admin/a2');
			if ( ! $this->acl->logged_in() AND Request::$current->controller() != 'auth' ) {
				Session::instance()->set('back_url', $_SERVER['REQUEST_URI']);
				Request::$current->redirect(Route::url('admin', array('controller' => 'auth')));
			}
			$this->user = $this->acl->get_user();
		}
		$this->site = ORM::factory('site')
			->find()
			->as_array();
		
		ORM_Base::$filter_mode = ORM_Base::FILTER_BACKEND;
		if ( Route::name( Request::current()->route() ) == 'modules' ) {
			$_pages = ORM::factory('page')
				->where('status', '>', 0)
				->and_where('type', '=', 'module')
				->and_where('data', '=', Request::current()->controller())
				->find_all();
			
			$this->module_page_id = (int) Request::current()->query('pid');
			
			if ($_pages->count() > 0) {
				if ($this->module_page_id == 0) {
					$this->module_page_id = $_pages->rewind()->current()->id;
				}
				foreach ($_pages as $_item) {
					$_link = URL::base().Page_Route::dynamic_base_uri($_item->id);
					$this->module_pages[ $_item->id ] = $_item->title." [ {$_link} ]";
				}
			}
		}
		$this->admin_config = Kohana::$config
			->load($this->admin_config)
			->as_array();

		if (Route::name($this->request->route()) == 'modules') {
			$this->module_config = empty($this->module_config) 
				? $this->request->controller() 
				: $this->module_config;
			$this->module_config = Kohana::$config
				->load('admin/modules/'.$this->module_config);
			$this->acl_module();
		}

		$this->check_empty_post_files();
		$this->check_deleted_post_fields();

		$this->is_cancel = (Request::$current->post('cancel') == 'cancel');
		$this->title = empty( $this->title ) ? 
			__($this->admin_config['title']) : 
			__($this->title);
	}

	public function after()
	{
		View::set_global('USER', $this->user);
		View::set_global('CONFIG', $this->admin_config);
		View::set_global('SITE', $this->site);
		View::bind_global('TITLE', $this->title);
		View::bind_global('BREADCRUMBS', $this->breadcrumbs);
		View::bind_global('ACL', $this->acl);
		View::set_global('MEDIA', $this->admin_config['media']);
		View::set_global('BODY_CLASS', $this->body_class);
		View::set_global('MODULE_PAGES', $this->module_pages);
		View::set_global('MODULE_PAGE_ID', $this->module_page_id);
		View::set_global('IS_AJAX', (bool) Request::initial()->is_ajax());

		if ($this->auto_render === TRUE) {
			$this->template
				->set('title', __($this->title))
				->set('sub_title', __($this->sub_title))
				->set('aside', $this->get_aside_view());

			$this->render_layout();
		}

		parent::after();
		
		$this->response
			->headers('cache-control', 'no-cache')
			->headers('expires', gmdate('D, d M Y H:i:s', time()).' GMT');
	}
	
	protected function render_layout()
	{
		$this->render_inner_layout();
		$content = $this->template->render();
		
		$this->template = View_Admin::factory($this->layout)
			->set('content', $content)
			->set('top_menu', $this->get_top_menu());
	}

	protected function render_inner_layout()
	{
		if (empty($this->inner_layout))
			return;
		$content = $this->template->render();
		$this->template
			->set_filename($this->inner_layout)
			->set('content', $content);
	}

	protected function get_aside_view()
	{
		return $this->aside ? 
			View_Admin::factory($this->aside) : 
			'';
	}

	protected function get_top_menu()
	{
		$return = array();
		if ( empty($this->top_menu_item) ) {
			$this->top_menu_item = Request::$current
				->controller();
		}

		foreach (Kohana::$config->load('admin/top_menu') as $title => $controller_name) {
			$return[] = array(
				'title' => $title,
				'uri'   => Route::url('admin', array( 'controller' => $controller_name )),
				'class' => ($this->top_menu_item == $controller_name) ? 'active' : '',
				'name' => $controller_name,
			);
		}

		return $return;
	}

	protected function check_empty_post_files()
	{
		if ( ! empty($_FILES)) {
			$new_files = array();
			foreach ($_FILES as $field => $value) {
				if ( $value['error'] === UPLOAD_ERR_NO_FILE )
					continue;
				$new_files[ $field ] = $value;
			}
			$_FILES = $new_files;
		}
	}

	protected function check_deleted_post_fields()
	{
		if ( Request::current()->post('delete_fields') !== NULL ) {
			foreach (Request::current()->post('delete_fields') as $field => $v) {
				if ( (bool) $v) {
					$_FILES[ $field ] = '';
				}
			}
		}
	}

	protected function unique_transliterate($str, $orm, $field_name)
	{
		$_temp = UTF8::transliterate_to_ascii($str);
		$_temp = UTF8::strtolower($_temp);
		$_temp = preg_replace('/[^-a-z0-9_]+/u', '-', $_temp);
		$_temp = trim($_temp, '-');

		while( $this->row_exist($orm, $field_name, $_temp) ) {
			$_temp = $_temp.'-'.uniqid();
		}

		return $_temp;
	}

	protected function row_exist($orm, $field_name, $field_value)
	{
		$search_orm = clone $orm;
		$search_orm->clear();

		if ( $orm->loaded() ) {
			$orm = $search_orm
				->where($field_name, '=', $field_value)
				->and_where('id', '!=', $orm->id)
				->find();
		} else {
			$orm = $search_orm
				->where($field_name, '=', $field_value)
				->find();
		}

		return $orm->loaded();
	}

	protected function acl_roles($all = FALSE)
	{
		$roles = Kohana::$config->load('admin/a2.roles');
		if ( ! $all) {
			unset($roles['user']);
			unset($roles['super']);
		}
		$roles = array_keys($roles);

		return array_combine($roles, $roles);
	}

	protected function acl_module()
	{
		if ( Kohana::$is_cli )
			return;

		$a2_config = $this->module_config
			->get('a2');
		$this->acl_inject($a2_config);

		if ( ! $this->acl->is_allowed($this->user, Request::current()->controller().'_controller', 'access'))
			throw new HTTP_Exception_404();
	}

	protected function acl_inject($conf)
	{
		$acl = $this->acl;
		if ( isset($conf['resources'])) {
			foreach ($conf['resources'] as $resource => $parent) {
				$acl->add_resource($resource,$parent);
			}
		}

		if ( isset($conf['rules'])) {
			foreach ( array('allow','deny') as $method) {
				if ( isset($conf['rules'][$method])) {
					foreach ( $conf['rules'][$method] as $rule) {
						$role = $resource = $privilege = $assertion = NULL;
						extract($rule);
						if ($assertion) {
							if ( is_array($assertion)) {
								$assertion = count($assertion) === 2
									? new $assertion[0]($assertion[1])
									: new $assertion[0];
							} else {
								$assertion = new $assertion;
							}
						}
						if ($method === 'allow') {
							$acl->allow($role,$resource,$privilege,$assertion);
						} else {
							$acl->deny($role,$resource,$privilege,$assertion);
						}
					}
				}
			}
		}
	}

	protected function delete_element($wrapper, $where = NULL) 
	{
		$return = TRUE;
		if ( ! empty($where)) {
			$orm = $wrapper->orm()->reset();
			foreach ($where as $condition) {
				$orm->where($condition[0], $condition[1], $condition[2]);
			}
			$items = $orm->find_all();
			foreach ($items as $_orm) {
				$wrapper->orm($_orm);
				$return = $return AND $this->delete_element($wrapper);
			}
		} else {
			$errors = array();
			try {
				$old_deleter_id = $wrapper->orm()->deleter_id;
				$wrapper
					->save( array( 'deleter_id' => $this->user->id, 'deleted' => date('Y-m-d H:i:s') ) )
					->delete( FALSE, $where );
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
				if ( ! empty($errors['_files'])) {
					$errors = array_merge($errors, $errors['_files']);
					unset($errors['_files']);
				}
			
				try {
					$wrapper->save( array( 'deleter_id' => $old_deleter_id ) );
				} catch (ORM_Validation_Exception $e) {
					$errors = array_merge( $errors, $e->errors( '' ) );
				}
			
				$this->template
					->set_filename('layout/error')
					->set('errors', $errors)
					->set('title', __('Error'));
			}
			$return = empty($errors);
		}
		return $return;
	}
	
	
	protected function download_file_to_tmp($src)
	{
		sleep(1);
		$dest_dir = str_replace('/', DIRECTORY_SEPARATOR, DOCROOT.'upload/tmp/');
		if ( ! is_dir($dest_dir)) {
			mkdir($dest_dir, 0755, TRUE);
		}
		if ( FALSE !== $data = file_get_contents( $src ) ) {
			$dest_file = $dest_dir.UTF8::strtolower( uniqid().'_'.basename($src));
			if ( FALSE !== file_put_contents( $dest_file, $data ) ) {
				return $dest_file;
			}
		}
		return FALSE;
	}

	protected function _save_photo_album($fields, $files, $prefix = '')
	{
		$report = array(
			'errors'  => array(),
			'success' => array(),
			'files'	  => array(
				'errors'  => array(),
				'success' => array(),
			),
		);

		try {
			$album_orm = ORM::factory('photo_Album')->values($fields)->save();
			$report['success'][] = array(
				'target' => 'Photo album',
				'msg'    => 'Photo album was saved to DB ['.$album_orm->id.']',
			);
		} catch (ORM_Validation_Exception $e) {
			$_errors = $e->errors('');
			if ( ! empty($_errors['_files'])) {
				$_errors = array_merge($_errors, $_errors['_files']);
			}
			$report['errors'][] = array(
				'target' => 'Photo album',
				'msg'    => implode("\r\n", $_errors),
			);
		} catch (Exception $e) {
			$report['errors'][] = array(
				'target' => 'Photo album',
				'msg'    => $e->getMessage(),
			);
		}

		if ( ! $album_orm->saved()) {
			foreach ($files as $item) {
				$report['files']['errors'][] = array(
					'target' => $prefix.$item,
					'msg'    => 'Not downloaded',
				);
			}
			return array(
				'id'     => NULL,
				'report' => $report,
			);
		}

		$photo_wrapper = ORM_Helper::factory('photo');
		$count = 1;

		foreach ($files as $item) {
			$photo = $this->download_file_to_tmp($prefix.$item);
			if ( $photo === FALSE ) {
				$report['files']['errors'][] = array(
					'target' => $prefix.$item,
					'msg'    => 'Download error',
				);
				continue;
			} else {
				$report['files']['success'][] = array(
					'target' => $prefix.$item,
					'msg'    => 'Download success',
				);
			}
			$photo_wrapper->orm()->clear();

			try {
				$photo_wrapper->save(array(
					'owner'    => 'photo_album',
					'owner_id' => $album_orm->id,
					'title'    => "Фото {$count}",
					'image'    => $photo,
					'active'   => 1,
				));
				$count++;
				$report['files']['success'][] = array(
					'target' => $prefix.$item,
					'msg'    => 'File was saved to DB ['.$photo_wrapper->orm()->id.']',
				);
			} catch (ORM_Validation_Exception $e) {
				$_errors = $e->errors( '' );
				if ( ! empty($_errors['_files'])) {
					$_errors = array_merge($_errors, $_errors['_files']);
				}
				$report['files']['errors'][] = array(
					'target' => $prefix.$item,
					'msg'    => implode("\r\n", $_errors),
				);
			} catch (Exception $e) {
				$report['files']['errors'][] = array(
					'target' => $prefix.$item,
					'msg'    => $e->getMessage(),
				);
			}
		};

		return array(
			'id'     => $album_orm->id,
			'report' => $report,
		);
	}

	// Выставляем успешно загруженные файлы, как "не загруженные"
	protected function _report_files_rollback($report)
	{
		$new_errors = array();
		foreach ($report['success'] as $item) {
			$new_errors[] = array(
				'target' => $item['target'],
				'msg'    => 'Rollbacked',
			);
		}
		return array_merge($report['errors'], $new_errors);
	}


} 