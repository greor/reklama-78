<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Feedback extends Controller_Front {

	public $template = 'modules/feedback/index';
	private $labels;
	public $auto_render = FALSE;
	protected $without_layout = TRUE;
	
	public function before()
	{
		parent::before();
		
		$this->template = View_Theme::factory($this->template);
		$this->labels = array(
			'captcha'	=>	'Защитный код',
			'name'		=>	'Ваша фамилия и имя',
			'email'		=>	'Адрес эл.почты',
			'text'		=>	'Сообщение',
		);
	}

	public function action_index()
	{
		$this->template
			->set('labels', $this->labels);
	}

	public function action_send()
	{
		$respons = array(
			'dont_hide_form' => TRUE,
			'success'        => FALSE,
			'errors'         => array(),
		);
		$ex_validation = Validation::factory(Request::current()->post())
			->labels($this->labels)
			->rules('captcha', array(
				array('not_empty'),
				array('Captcha::valid'),
			))
			->rules('name', array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			))
			->rules('email', array(
				array('not_empty'),
				array('email'),
			))
			->rules('text', array(
				array('not_empty'),
			));

		$text = '';
		foreach ( Request::current()->post() as $_key => $_val ) {
			if ( $_key == 'captcha' OR ! array_key_exists($_key, $this->labels) OR empty($_val) )
				continue;
			$text .= "\r\n".$this->labels[ $_key ].': '.$_val;
		}

		$feedback_wrapper = ORM_Helper::factory('feedback');
		try {
			$feedback_wrapper->save(array(
				'page_id' => $this->page_id,
				'text'    => $text,
			), $ex_validation);
			$respons['success'] = TRUE;
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( '' );
			if ( ! empty($errors['_external'])) {
				$errors = array_merge($errors, $errors['_external']);
				unset($errors['_external']);
			}
			$respons['errors'] = $errors;
		} catch (Exception $e) {
			Kohana::$log->add(
				Log::ERROR,
				'Feedback form send error. File: :file. Exception occurred: :exception',
				array(
					':file'      => __FILE__.':'.__LINE__,
					':exception' => $e->getMessage()
				)
			);
		}
		Ku_AJAX::send('json', $respons);
	}
	
} 