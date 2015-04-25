<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Error extends Controller_Admin_Front {

	public $template = 'error';

	protected $title = 'Internal Server Error';

	public function before()
	{
		$this->ttl = 0;

		parent::before();

		$this->template->page = URL::site( rawurldecode(Request::$initial->uri()) );
		$this->template->code = $this->request->action();
		$this->template->message = '';

		if (Request::$initial == Request::$current) {
			$this->request->action(404);
		}

		$this->response->status((int) $this->request->action());
	}

	public function action_403()
	{
		$this->title = '403 Forbidden';
		$this->response->status(403);
		$this->template->message = '403 Forbidden';
	}

	public function action_404()
	{
		$this->title = '404 Not Found';
		$this->response->status(404);
		$this->template->message = '404 Not Found';
	}

	public function action_500()
	{
		$this->title = '500 Internal Server Error';
		$this->response->status(500);
		$this->template->message = '500 Internal Server Error';
	}

	public function action_503()
	{
		$this->title = '503 Service Unavailable';
		$this->response->status(503);
		$this->template->message = '503 Service Unavailable';
	}

} 
