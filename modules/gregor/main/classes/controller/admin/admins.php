<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Admins extends Controller_Admin_Front {

	public $template = 'admins/list';
	protected $_exclude_admins = array( 'superadmin' );

	protected $title = 'Users';

	protected function get_aside_view()
	{
		$menu_items = Kohana::$config
			->load('admin/admins.left_menu');

		return parent::get_aside_view()
			->set('menu_items', $menu_items);
	}

	public function action_index()
	{
		if ( ! $this->acl->is_allowed( $this->user, 'admins', 'read' )) {
			$this->request->redirect( Route::url('admin') );
		}

		$orm = ORM::factory('admin')
			->where('delete_bit', '=', 0)
			->and_where('username', 'NOT IN', $this->_exclude_admins);

		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(10)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);

		$admins = $orm
			->paginator( $paginator )
			->find_all();

		$this->sub_title = __('Admin list');

		$this->template
			->set_filename('admins/list')
			->set('admins', $admins)
			->set('roles', $this->acl_roles())
			->set('paginator', $paginator);
	}

	public function action_edit()
	{
		$list_url = Route::url( 'admin', array(
			'controller' => 'admins',
		));

		if ($this->is_cancel) {
			Request::current()->redirect( $list_url );
		}

		$id = (int) Request::current()->param('id');
		if ( (bool) $id) {
			$wrapper = ORM_Helper::factory('admin', $id);
			if ( ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
				Request::current()->redirect( $list_url );
			}
			if (in_array($wrapper->orm()->username, $this->_exclude_admins)) {
				Request::current()->redirect( $list_url );
			}
			$this->sub_title = __('Edit admin');
		} else {
			$wrapper = ORM_Helper::factory('admin');
			$this->sub_title = __('Add admin');
			if ( ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'add')) {
				Request::current()->redirect( $list_url );
			}
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
				$post = Request::current()->post();

				if ( ! empty($post['password'])) {
					$ex_validation = Model_Admin::get_password_validation( Request::current()->post() );
				} else {
					$ex_validation = NULL;
					unset($post['password']);
				}
				$wrapper->save($post , $ex_validation);
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
				if ( ! empty($errors['_external'])) {
					$errors = array_merge($errors, $errors['_external']);
					unset($errors['_external']);
				}
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$sites = ORM::factory('site')
				->find_all()
				->as_array('id', 'name');

			$this->template
				->set_filename('admins/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper)
				->set('roles', $this->acl_roles())
				->set('sites', $sites);
		} else {
			Request::current()->redirect( $list_url );
		}
	}

	public function action_delete()
	{
		$list_url = Route::url('admin', array(
			'controller' => 'admins',
		));

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('admin', $id);
		if ( ! $this->acl->is_allowed($this->user, $wrapper->orm(), 'edit')) {
			Request::current()->redirect( $list_url );
		}

		try {
			if (in_array($wrapper->orm()->username, $this->_exclude_admins)) {
				Request::current()->redirect( $list_url );
			}

			$old_deleter_id = $wrapper->orm()->deleter_id;
			$wrapper
				->save( array( 'deleter_id' => $this->user->id, 'deleted' => date('Y-m-d H:i:s') ) )
				->delete( FALSE );
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( TRUE );
			try {
				$wrapper->save( array( 'deleter_id' => $old_deleter_id ) );
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

	public function action_active()
	{
		$list_url = Route::url('admin', array(
			'controller' => 'admins',
		));

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('admin', $id);

		if ( ! $this->acl->is_allowed( $this->user, $wrapper->orm(), 'edit' )) {
			Request::current()->redirect( $list_url );
		}

		try {
			if (in_array($wrapper->orm()->username, $this->_exclude_admins)) {
				Request::current()->redirect( $list_url );
			}
			$active = (bool) $wrapper->orm()->active ? FALSE : TRUE;

			$wrapper->save(array('active' => $active));
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( TRUE );
			$this->template
				->set_filename('layout/error')
				->set('errors', $errors)
				->set('title', __('Error'));
		}

		if (empty($errors)) {
			Request::current()->redirect( $list_url );
		}
	}

} 
