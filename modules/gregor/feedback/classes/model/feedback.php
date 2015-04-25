<?php defined('SYSPATH') or die('No direct script access.');

class Model_Feedback extends ORM_Base {

	protected $_table_name = 'feedback';
	protected $_sorting = array('page_id' => 'ASC', 'created' => 'DESC');
	protected $_deleted_column = 'delete_bit';

	public function labels()
	{
		return array(
			'text'	=>	'Message text',
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
			'text' => array(
				array( 'not_empty' ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'trim' ),
			),
			'new' => array(
				array(array($this, 'checkbox'))
			),
		);
	}
}
