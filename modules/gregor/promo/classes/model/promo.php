<?php defined('SYSPATH') or die('No direct script access.');

class Model_Promo extends ORM_Base {
	
	protected $_table_name = 'promo';
	protected $_sorting = array('position' => 'ASC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	public function labels()
	{
		return array(
			'title'           => 'Title',
			'url'             => 'Link',
			'image'           => 'Image',
			'text'            => 'Text',
			'active'          => 'Active',
			'position'        => 'Position',
			'public_date'     => 'Start date',
			'hidden_date'     => 'End date',
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
			'url' => array(
				array( 'not_empty' ),
				array( 'min_length', array( ':value', 3 ) ),
				array( 'max_length', array( ':value', 255 ) ),
				array( 'url' ),
			),
			'image' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'position' => array(
					array( 'digit' ),
			),
			'public_date' => array(
				array( 'date' ),
			),
			'hidden_date' => array(
				array( 'date' ),
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
			'url' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
		);
	}
}
