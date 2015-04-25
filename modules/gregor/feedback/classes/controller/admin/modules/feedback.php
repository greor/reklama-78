<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Modules_Feedback extends Controller_Admin_Front {

	public $template = 'modules/feedback/list';
	public $inner_layout = 'layout/inner';

	protected $top_menu_item = 'modules';

	protected $title = 'Feedback';

	protected function get_aside_view()
	{
		$menu_items = array_merge(
			$this->module_config->get('left_menu'),
			$this->_ex_menu_items
		);

		return parent::get_aside_view()
				->set('menu_items', $menu_items)
				->set('replace', array(
					'{PAGE_ID}' =>	$this->module_page_id,
				));
	}

	public function action_index()
	{
		$orm = ORM::factory('feedback')
			->where('page_id', '=', $this->module_page_id)
			->order_by('created', 'desc');

		$paginator_orm = clone $orm;
		$paginator = new Paginator('admin/layout/paginator');
		$paginator
			->per_page(20)
			->count( $paginator_orm->count_all() );
		unset($paginator_orm);

		$feedback = $orm
			->paginator( $paginator )
			->find_all();

		$this->sub_title = __('Feedback list');
		$this->template
			->set_filename('modules/feedback/list')
			->set('feedback', $feedback)
			->set('paginator', $paginator);
	}

	public function action_view()
	{
		$orm = ORM::factory('feedback')
			->where('id', '=', (int) Request::current()->param('id'))
			->and_where('page_id', '=', $this->module_page_id)
			->find();

		if ( ! $orm->loaded() OR ! $this->acl->is_allowed($this->user, $orm, 'view')){
			throw new HTTP_Exception_404();
		}

		try {
			$orm->new = 0;
			$orm->save();
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( '' );
		}

		$this->sub_title = __('Feedback list');
		$this->template
			->set_filename('modules/feedback/view')
			->set('orm', $orm);
	}

	public function action_config()
	{
		$query_array = array(
			'pid' => $this->module_page_id,
		);
		$list_url = Route::url( 'modules', array(
			'controller' => 'feedback',
			'query'      => Helper_Page::make_query_string($query_array),
		));

		if ($this->is_cancel) {
			Request::current()->redirect($list_url);
		}

		$config_wrapper = ORM_Helper::factory('feedback_Config');
		$config_wrapper->orm()
			->where('page_id', '=', $this->module_page_id)
			->limit(1)
			->find();

		if ( $config_wrapper->orm()->loaded() AND ! $this->acl->is_allowed($this->user, $config_wrapper->orm(), 'edit')){
			throw new HTTP_Exception_404();
		}

		if ( ! $config_wrapper->orm()->loaded()) {
			try {
				$config_wrapper->save(array(
					'page_id'    => $this->module_page_id,
					'creator_id' => $this->user->id,
				));
			} catch (Exception $e) {
				Kohana::$log->add(
					Log::ERROR,
					'File: :file. Exception occurred: :exception',
					array(
						':file'      => __FILE__.':'.__LINE__,
						':exception' => $e->getMessage()
					)
				);
			}
		}

		$errors = array();
		$submit = Request::$current->post('submit');
		if ( ! empty($submit) ) {
			try {
				$values = array(
					'updated'    => date('Y-m-d H:i:s'),
					'updater_id' => $this->user->id,
				);
				$config_wrapper->save( $values + Request::current()->post() );
			} catch (ORM_Validation_Exception $e) {
				$errors = $e->errors( '' );
				if ( ! empty($errors['_files'])) {
					$errors = array_merge($errors, $errors['_files']);
					unset($errors['_files']);
				}
			}
		}

		if ( ! empty($errors) OR $submit != 'save_and_exit') {
			$this->sub_title = __('Config');
			$this->template
				->set_filename('modules/feedback/config')
				->set('errors', $errors)
				->set('config', $config_wrapper->orm());
		} else {
			Request::current()->redirect( $list_url );
		}
	}
} 
