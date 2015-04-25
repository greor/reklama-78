<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Sites extends Controller_Admin_Front {

	public $template = 'sites/list';
	protected $top_menu_item = 'settings';
	private $force_site_id = FALSE;

	protected $title = 'Sites';

	public function before()
	{
		parent::before();

		if ( ! empty($this->user) AND $this->user->role != 'super' ) {
			$this->force_site_id = $this->site['id'];
			$this->request->action('edit');
		}
	}

	protected function get_aside_view()
	{
		$menu_items = Kohana::$config
			->load('admin/sites.left_menu');

		return parent::get_aside_view()
			->set('menu_items', $menu_items);
	}

	public function action_index()
	{
		if ( ! $this->acl->is_allowed( $this->user, 'sites', 'read' )) {
			$this->request->redirect( Route::url('admin') );
		}

		$orm = ORM::factory('site');
		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);

		$sites = $orm
			->paginator( $paginator )
			->find_all();

		$this->title = __('Site manager');
		$this->template
			->set('sites', $sites)
			->set('paginator', $paginator);
	}

	public function action_edit()
	{
		$list_url = Route::url( 'admin', array(
			'controller' => 'sites',
		));
		if ($this->is_cancel) {
			Request::current()->redirect($list_url);
		}
		
		$id = $this->force_site_id 
			? (int) $this->force_site_id
			: (int) Request::current()->param('id');

		if ( (bool) $id) {
			$wrapper = ORM_Helper::factory('site', $id);
			if ( ! $this->acl->is_allowed( $this->user, $wrapper->orm(), 'edit' )) {
				Request::current()->redirect( $list_url );
			}
			$this->sub_title = __('Edit site');
		} else {
			$wrapper = ORM_Helper::factory('site');
			if ( ! $this->acl->is_allowed( $this->user, $wrapper->orm(), 'add' )) {
				Request::current()->redirect( $list_url );
			}
			$this->sub_title = __('Add site');
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

				if ( ! $this->acl->is_allowed($this->user, $orm, 'active_change') ) {
					unset($values['active']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_name') ) {
					unset($values['name']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_url') ) {
					unset($values['url']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_vk_api_id') ) {
					unset($values['vk_api_id']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_vk_group_id') ) {
					unset($values['vk_group_id']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_fb_app_id') ) {
					unset($values['fb_app_id']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_fb_group_link') ) {
					unset($values['fb_group_link']);
				}
				if ( ! $this->acl->is_allowed($this->user, $orm, 'edit_tw_widget') ) {
					unset($values['tw_widget']);
				}

				$wrapper->save($values + $_FILES);
				$this->clear_site_cache();
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit')
		{
			$this->title = __('Sites');
			$this->template
				->set_filename('sites/edit')
				->set('errors', $errors)
				->set('wrapper', $wrapper);
		} else {
			Request::current()->redirect( $list_url );
		}

	}

	public function action_delete()
	{
		$list_url = Route::url('admin', array(
			'controller' => 'sites',
		));

		$id = (int) Request::current()->param('id');
		$wrapper = ORM_Helper::factory('site', $id);
		if ( ! $this->acl->is_allowed( $this->user, $wrapper->orm(), 'delete' )) {
			Request::current()->redirect( $list_url );
		}

		try {
			$old_deleter_id = $wrapper->orm()->deleter_id;
			$wrapper
				->save( array( 'deleter_id' => $this->user->id, 'deleted' => date('Y-m-d H:i:s') ) )
				->delete( FALSE );
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( TRUE );

			try {
				$wrapper->save( array( 'deleter_id' => $old_deleter_id ) );
				$this->clear_site_cache();
			} catch (ORM_Validation_Exception $e) {
				$errors = array_merge( $errors, $e->errors( '' ) );
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

	private function clear_site_cache() {}

} 
