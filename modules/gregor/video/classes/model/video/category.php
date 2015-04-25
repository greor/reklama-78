<?php defined('SYSPATH') or die('No direct script access.');

class Model_Video_Category extends ORM_Base {

	const STATUS_OFF = 0;
	const STATUS_HIDDEN = 1;
	const STATUS_ON = 2;

	protected $_table_name = 'video_categories';
	protected $_sorting = array('public_date' => 'DESC');
	protected $_deleted_column = 'delete_bit';

	protected $_has_many = array(
		'video' => array(
			'model'       => 'video',
			'foreign_key' => 'category_id',
		),
	);

	public function labels()
	{
		return array(
			'group'           => 'City',
			'uri'             => 'URI',
			'title'           => 'Title',
			'status'          => 'Status',
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
			'page_id' => array(
				array( 'not_empty' ),
				array( 'digit' ),
			),
			'parent_id' => array(
				array( 'digit' ),
			),
			'uri' => array(
				array( 'min_length', array( ':value', 2 ) ),
				array( 'max_length', array( ':value', 100 ) ),
				array( 'alpha_dash' ),
			),
			'group' => array(
				array( 'not_empty' ),
				array( 'in_array', array( ':value', array('msk','spb') ) ),
			),
			'title' => array(
				array( 'not_empty' ),
				array( 'min_length', array( ':value', 2 ) ),
				array( 'max_length', array( ':value', 100 ) ),
			),
			'status' => array(
				array( 'not_empty' ),
				array( 'digit' ),
				array( 'range', array( ':value', 0, 2 ) ),
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
				array('trim'),
			),
			'uri' => array(
				array('Ku_Text::slug'),
			),
			'title' => array(
				array( 'strip_tags' ),
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
			$this
				->where($this->_object_name.'.status', '>', 0)
				->and_where($this->_object_name.'.public_date', '<=', date('Y-m-d H:i:00'));
		}
	}
}
