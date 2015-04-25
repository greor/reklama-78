<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Error extends Controller_Template {

	protected $config = 'site';
	protected $media;
	protected $title;

	public $template = 'error';

	public function before()
	{
		$template = $this->template;
		$this->template = NULL;
		parent::before();
		if ($this->auto_render === TRUE) {
			$this->template = View_Theme::factory($template);
		}

		$this->config = Kohana::$config->load($this->config)->as_array();
		$this->media = '/media/'.$this->config['theme'].'/';

		if (Request::$initial === Request::$current) {
			$this->request->action(404);
		}

		if (Request::initial()->is_ajax() === TRUE) {
			$this->response->status((int) $this->request->action());
			$this->response->body(isset($message) ? $message : 'Page not found');
		}
	}

	public function after()
	{
		$referrer = Request::current()->referrer();
		if ($referrer === NULL OR ( $_SERVER['HTTP_HOST'] != parse_url($referrer, PHP_URL_HOST) )) {
			$referrer = URL::base();
		}
		$this->template
			->set('TITLE', $this->title)
			->set('MEDIA', $this->media)
			->set('back_url', $referrer);
		
		parent::after();
	}

	public function action_404()
	{
		$this->response->status(404);
		$this->title = __('404 Page not found');
		$this->template->code = 404;
		$this->template->message = __('Page Not found');
	}

	public function action_503()
	{
		$this->response->status(503);
		$this->title = __('503 Maintenance Mode');
		$this->template->code = 503;
		$this->template->message = __('Maintenance Mode');
	}

	public function action_500()
	{
		$this->response->status(500);
		$this->title = __('500 Internal Server Error');
		$this->template->code = 500;
		$this->template->message = __('Internal Server Error');
	}
}
