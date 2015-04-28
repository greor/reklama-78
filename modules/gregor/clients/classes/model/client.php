<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client extends ORM_Base {
	
	protected $_sorting = array('position' => 'ASC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	public function labels()
	{
		return array(
			'title'    => 'Title',
			'image'    => 'Image',
			'active'   => 'Active',
			'position' => 'Position',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array( 'digit' ),
			),
			'title' => array(
				array( 'not_empty' ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			'image' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'position' => array(
				array( 'digit' ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'trim' ),
			),
			'title' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
		);
	}
}
