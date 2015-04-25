<?php defined('SYSPATH') or die('No direct script access.');

class Model_Element extends ORM_Base {
	
	protected $_sorting = array('page_id' => 'ASC', 'position' => 'ASC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	protected $_has_many = array(
		'like_hash' => array(
			'model' => 'elements_Like',
			'foreign_key' => 'element_id',
		),
	);

	public function labels()
	{
		return array(
			'like'            => 'Like',
			'title'           => 'Title',
			'uri'             => 'URI',
			'image'           => 'Image',
			'text'            => 'Text',
			'active'          => 'Active',
			'position'        => 'Position',
			'title_tag'       => 'Title tag',
			'keywords_tag'    => 'Keywords tag',
			'description_tag' => 'Desription tag',
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
			'like' => array(
				array( 'numeric' ),
			),
			'title' => array(
				array( 'not_empty' ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			'uri' => array(
				array( 'min_length', array( ':value', 2 ) ),
				array( 'max_length', array( ':value', 100 ) ),
				array( 'alpha_dash' ),
			),
			'image' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'position' => array(
				array( 'digit' ),
			),
			'title_tag' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'keywords_tag' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'description_tag' => array(
				array( 'max_length', array( ':value', 255 ) ),
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
			'uri' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
			'title_tag' => array(
				array( 'strip_tags' ),
			),
			'keywords_tag' => array(
				array( 'strip_tags' ),
			),
			'description_tag' => array(
				array( 'strip_tags' ),
			),
		);
	}
}
