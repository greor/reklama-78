<?php defined('SYSPATH') or die('No direct script access.');

class Model_Feedback_Config extends ORM_Base {

	protected $_table_name = 'feedback_config';
	protected $_deleted_column = 'delete_bit';
	public function labels()
	{
		return array(
			'email'				=>	'E-mail',
			'send_email'		=>	'Send e-mail',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array( 'digit' ),
			),
			'page_id' => array(
				array( 'not_empty' ),
				array( 'digit' ),
			),
			'email' => array(
				array( 'email' ),
				array( 'email_domain' ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'trim' ),
			),
			'send_email' => array(
				array(array($this, 'checkbox'))
			),
		);
	}

}
