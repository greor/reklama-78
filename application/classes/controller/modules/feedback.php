<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Modules_Feedback extends Controller_Front {

	public $template = 'modules/feedback/index';
	private $labels;
	
	public function before()
	{
		parent::before();
		
		$this->labels = array(
			'captcha'	=>	'Защитный код',
			'name'		=>	'Ваша фамилия и имя',
			'email'		=>	'Адрес эл.почты',
			'text'		=>	'Сообщение',
		);
	}

	public function action_index()
	{
		$response = Session::instance()->get('contacts_form', array());
		
		$page = ORM::factory('page', $this->page_id);
		$action_link = URL::base().Page_Route::uri($this->page_id, 'feedback', array(
			'action' => 'send'
		));
		$this->template
			->set('response', $response)
			->set('page', $page->as_array())
			->set('action_link', $action_link)
			->set('labels', $this->labels);
		
		
		$this->switch_on_plugin('forms_validate');
		$this->switch_on_plugin('forms_submit');
	}

	public function action_send()
	{
		$this->auto_render = FALSE;
		$this->without_layout = TRUE;
		$response = array(
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
			$response['success'] = TRUE;
		} catch (ORM_Validation_Exception $e) {
			$errors = $e->errors( '' );
			if ( ! empty($errors['_external'])) {
				$errors = array_merge($errors, $errors['_external']);
				unset($errors['_external']);
			}
			$response['errors'] = $errors;
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
		
		if ($this->request->is_ajax()) {
			Ku_AJAX::send('json', $response);
		} else {
			Session::instance()->set('contacts_form', $response);
			$this->request->redirect(
				URL::base().Page_Route::uri($this->page_id, 'feedback')
			);
		}
	}
	
} 