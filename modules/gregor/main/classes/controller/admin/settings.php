<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Settings extends Controller_Admin_Front {

	public $template = 'settings';

	public function before()
	{
		parent::before();

		if ( $this->user->role != 'super' ) {
			$this->request->redirect(
				Route::url('admin', array( 'controller' => 'sites' ))
			);
		}
	}

	public function action_index()
	{
		$this->title = __('Settings');
		$this->template
			->set('menu', Kohana::$config->load('admin/settings.menu') );
	}

	public function action_common()
	{
		$this->template->set_filename('settings/common');

		$this->title = __('Common settings');
		$errors = array();
		
		$model = ORM::factory('setting');
		$names = Kohana::$config
			->load('admin/settings.settings_name');

		try
		{
			$values = $model
				->where('delete_bit', '=', 0)
				->find_all()
				->as_array('name');

			$user = Auth_Admin::instance()->get_user();

			if (Request::$current->post('submit'))
			{
				foreach ($names as $name => $config)
				{
					$value = Request::$current->post($name);

					if ($config['type'] == 'checkbox')
					{
						$value = $model->checkbox($value);
					}

					$model->clear()
						->where('name', '=', $name)
						->and_where('delete_bit', '=', 0)
						->find();

					$model->user($user);

					$model->site_id = SITE_ID;
					$model->name = $name;
					$model->value = $value;

					$model->save();
				}

				Request::current()->redirect(Route::url('admin', array( 'controller' => 'settings' )));
			}
		}
		catch (Kohana_Exception $e)
		{
			if (get_class($e) == 'ORM_Validation_Exception')
			{
				foreach ($e->objects() as $validation)
				{
					if ( is_object($validation) AND (get_class($validation) == 'Validation') )
					{
						$errors = $errors + $validation->errors('settings');
					}
				}
			}
			else
			{
				Log::instance()->add(Log::ERROR, Kohana_Exception::text($e));
				$errors[] = __(Kohana::message('common', 'errors.save'));

				if (DEVELOPER)
				{
					throw $e;
				}
			}
		}


		$settings = array();
		foreach ($names as $name => $config)
		{
			if (array_key_exists($name, $values))
			{
				$value = $values[$name]->value;
			}
			else
			{
				$value = NULL;
			}

			$item = array(
				'value'	=>	$value,
				'title'	=>	$config['title'],
				'type'	=>	$config['type'],
			);

			if ($item['type'] == 'checkbox')
			{
				$item['value'] = $model->checkbox($item['value']);
			}

			$settings[$name] = $item;
		}

		$this->template
			->set('errors', $errors)
			->set('action', Route::url('admin', array( 'controller' => 'settings', 'action' => 'common')))
			->set('title', __('Common settings'))
			->set('settings', $settings);


	}
} 
