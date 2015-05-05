<?php defined('SYSPATH') or die('No direct script access.');

class Model_Block extends ORM_Base {
	
	protected $_active_column = 'active';

	public function labels()
	{
		return array(
			'name'            => 'Name',
			'title'           => 'Title',
			'code'            => 'Code',
			'text'            => 'Text',
			'image'           => 'Image',
			'link'            => 'Link',
			'active'          => 'Active',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array( 'digit' ),
			),
			'name' => array(
				array( 'not_empty' ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			'title' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'code' => array(
				array( 'not_empty' ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			'image' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'link' => array(
				array( 'min_length', array( ':value', 3 ) ),
				array( 'max_length', array( ':value', 255 ) ),
				array( 'url' ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'trim' ),
			),
			'name' => array(
				array( 'strip_tags' ),
			),
			'title' => array(
				array( 'strip_tags' ),
			),
			'code' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
			'link' => array(
				array( 'strip_tags' ),
			),
		);
	}
}
