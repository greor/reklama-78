<?php defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Controller_Thumb extends Controller {

	public function action_index()
	{
		$group = $this->request->param('group');

		if ( ! $group)
			throw new HTTP_Exception_404;

		$config =  Kohana::$config->load('thumb')->get($group);

		if ( ! $config)
			throw new HTTP_Exception_404;;

		$file = $this->request->param('file');

		if ( ! $file)
			throw new HTTP_Exception_404;

		$path = Arr::get($config, 'path', '');
		if ( ! empty($path))
		{
			$path = rtrim($path, '/').'/';
		}

		$thumb = Thumb::create($group, $path.$file);

		if ($thumb)
		{
			// Get the extension from the filename
			$ext = strtolower(pathinfo($thumb, PATHINFO_EXTENSION));
			$mime = File::mime_by_ext($ext);
			$this->response->headers('Content-Type', $mime ? $mime : 'image/jpeg');
			$this->response->body(file_get_contents($thumb));
		}
		else
		{
			throw new HTTP_Exception_500;
		}
	}

} // End Thumb