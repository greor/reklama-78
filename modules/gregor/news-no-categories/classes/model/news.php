<?php defined('SYSPATH') or die('No direct script access.');

class Model_News extends ORM_Base {
	
	protected $_table_name = 'news_no_categories';
	protected $_sorting = array('public_date' => 'DESC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	public function labels()
	{
		return array(
			'title'           => 'Title',
			'uri'             => 'URI',
			'image'           => 'Image',
			'announcement'    => 'Announcement',
			'text'            => 'Text',
			'active'          => 'Active',
			'title_tag'       => 'Title tag',
			'keywords_tag'    => 'Keywords tag',
			'description_tag' => 'Desription tag',
			'public_date'     => 'Date',
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
			'uri' => array(
				array( 'min_length', array( ':value', 2 ) ),
				array( 'max_length', array( ':value', 100 ) ),
				array( 'alpha_dash' ),
			),
			'image' => array(
				array( 'max_length', array( ':value', 255 ) ),
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
			'public_date' => array(
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
