<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Uploader extends Controller {

	protected $acl;
	protected $user;

	protected $template = "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction({FUNCTION}, '{SRC}', '');</script>";

	protected $max_filename_length = 127;

	public function before()
	{
		Session::$default = 'admin';

		parent::before();

		$this->acl = A2::instance('admin/a2');

		if ( ! $this->acl->logged_in() OR empty($_FILES['upload']))
		{
			Request::initial()->redirect(
				Route::url('error', array( 'action' => '403' ))
			);
		}

		$this->user = $this->acl->get_user();
	}

	public function action_index()
	{
		$value = $_FILES['upload'];

		if (is_array($value) AND Ku_Upload::valid($value) AND Ku_Upload::not_empty($value))
		{
			$md5 = md5($value['name']);
			$save_path = DOCROOT.'upload'.DIRECTORY_SEPARATOR.'editor'.DIRECTORY_SEPARATOR
				.date('Y').DIRECTORY_SEPARATOR.substr($md5, 0, 2).DIRECTORY_SEPARATOR.substr($md5, 2, 2).DIRECTORY_SEPARATOR;

			Ku_Dir::make_writable($save_path);
			$filename = Ku_File::safe_name($value['name'], TRUE, $this->max_filename_length);

			$prefix = uniqid().'_';
			while (file_exists($save_path.$prefix.$filename))
			{
				$prefix = uniqid().'_';
			}

			$filename = Ku_Upload::save($value, $prefix.$filename, $save_path);
			$filename = 'upload'.str_replace(
							array( realpath( DOCROOT.'upload' ), DIRECTORY_SEPARATOR ),
							array( '', '/' ),
							$filename
						);

			if ( ! $filename)
			{
				Kohana::$log->add(
						Log::ERROR,
						'Exception occurred: :exception. [:file][:line] ',
						array(
							':file'      => Debug::path(__FILE__),
							':line'      => __LINE__,
							':exception' => 'File not saved'
						)
				);
			}

			echo str_replace(
				array( '{FUNCTION}', '{SRC}' ),
				array( Request::initial()->query('CKEditorFuncNum'), URL::base().$filename ),
				$this->template
			);
		}
	}
}