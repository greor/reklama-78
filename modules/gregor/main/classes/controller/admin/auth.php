<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Auth extends Controller_Template {

	protected $acl;

	public $template = 'auth';
	public $title;
	protected $admin_config = 'admin/admin';

	public function before()
	{
		Session::$default = 'admin';

		$template = $this->template;
		$this->template = NULL;
		parent::before();
		if ($this->auto_render === TRUE) {
			$this->template = View_Admin::factory($template);
		}

		$this->acl = A2::instance('admin/a2');
		
		$this->admin_config = Kohana::$config
			->load($this->admin_config)
			->as_array();
		$this->title = empty( $this->title ) ? 
			$this->admin_config['title'] : 
			$this->title;
		$this->title = __($this->title);
	}

	public function after()
	{
		View::bind_global('TITLE', $this->title);
		View::set_global('MEDIA', $this->admin_config['media']);

		parent::after();

		$this->response
			->headers('cache-control', 'no-cache')
			->headers('expires', gmdate('D, d M Y H:i:s', time()).' GMT');
	}

	public function action_index()
	{
		if ($this->request->post('submit')) {
			$login = $this->request
				->post('login');
			$password = $this->request
				->post('password');
			$ip = Request::$client_ip;
			$user_agent = Request::$user_agent;

			$fail_login_checker = new Auth_Admin_Checker($login, $ip);
			if ($fail_login_checker->check()) {
				$admin = ORM::factory('admin')
					->where('username', '=', $login )
					->and_where('delete_bit', '=', 0 )
					->and_where('active', '=', 1 )
					->find();
				try {
					if ( $this->acl->auth()->login($admin, $password, (bool) $this->request->post('remember')) ) {
						$url = Session::instance()
							->get('back_url');
						$this->request->redirect( empty($url) ? Route::url('admin') : $url );
					} else {
						$fail_login_checker->add($password, $user_agent);
						$this->template
							->set('error', __('Authentication error'));
					}
				} catch (ORM_Validation_Exception $e) {
					Log::instance()->add(Log::ERROR, $e->errors( '' ).'['.__FILE__.'::'.__LINE__.']');
				}
			} else {
				$this->template
					->set('error', __('To many failed login attempts. Please, wait :minutes minutes and try again.', array(
						':minutes' => ceil($fail_login_checker->fail_interval() / 60)
					)));
			}
		}
		$this->title = __('Authentication').' | '.$this->title;
		$this->template
			->set('logo', $this->admin_config['logo']);
	}
} 
