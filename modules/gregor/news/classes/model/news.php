<?php defined('SYSPATH') or die('No direct script access.');

class Model_News extends ORM_Base {

	protected $_sorting = array('public_date' => 'DESC');
	protected $_deleted_column = 'delete_bit';
	protected $_active_column = 'active';

	protected $_belongs_to = array(
		'category' => array(
			'model'       => 'news_category',
			'foreign_key' => 'category_id',
		),
	);

	public function labels()
	{
		return array(
			'category_id'     => 'Category',
			'title'           => 'Title',
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
			'category_id' => array(
				array( 'not_empty' ),
				array( 'digit' ),
			),
			'title' => array(
				array( 'not_empty' ),
				array( 'max_length', array( ':value', 255 ) ),
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

	public function apply_mode_filter()
	{
		parent::apply_mode_filter();
		if($this->_filter_mode == ORM_Base::FILTER_FRONTEND) {
			$this->where($this->_object_name.'.public_date', '<=', date('Y-m-d H:i:00'));
		}
	}
}
