<?php defined('SYSPATH') or die('No direct script access.');

class Model_Page extends ORM_Base {

	const LEVEL_START = 1;

	/**
	 * @var array
	 */
	protected $_sorting = array('level' => 'ASC', 'parent_id' => 'ASC', 'position' => 'ASC');

	/**
	 * Deleted column name
	 * @var string
	 */
	protected $_deleted_column = 'delete_bit';

	protected $_belongs_to = array(
		'site' => array(),
	);

	public $uri_full;

	public function labels()
	{
		return array(
			'uri'				=>	'URI segment',
			'title'				=>	'Title',
			'image'				=>	'Image',
			'text'				=>	'Text',
			'type'				=>	'Page type',
			'status'			=>	'Page status',
			'data'				=>	'Page data',
			'parent_id'			=>	'Parent page',
			'position'			=>	'Position',
			'level'				=>	'Level',
			'title_tag'			=>	'Title tag',
			'keywords_tag'		=>	'Keywords tag',
			'description_tag'	=>	'Desription tag',
			'sm_changefreq'		=>	'Changefreq',
			'sm_priority'		=>	'Priority',
			'sm_separate_file'	=>	'Separate file',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array( 'digit' ),
			),
			'name' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'uri' => array(
				array( 'not_empty' ),
				array( 'alpha_dash' ),
				array( 'max_length', array( ':value', 255 ) ),
				array( array($this, 'unique_uri'), array( ':validation' ) ),
			),
			'title' => array(
				array( 'not_empty' ),
				array( 'min_length', array( ':value', 2 ) ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			'image' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'type' => array(
				array( 'not_empty' ),
				array( 'in_array', array( ':value', array( 'static', 'module', 'page', 'url' ) ) ),
			),
			'data' => array(
				array( array('Model_Page', 'check_data'), array( ':value', ':data' ) ),
			),
			'status' => array(
				array( 'not_empty' ),
				array( 'digit' ),
				array( 'range', array( ':value', 0, 2 ) ),
			),
			'parent_id' => array(
				array( 'not_empty' ),
				array( 'digit' ),
				array( array($this, 'not_matches'), array(':validation', ':field', 'id') ),
			),
			'position' => array(
				array( 'digit' ),
			),
			'level' => array(
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
			'sm_changefreq' => array(
				array( 'in_array', array(':value', array('always','hourly','daily','weekly','monthly','yearly','never')) ),
			),
			'sm_priority' => array(
				array( 'range', array(':value', 0, 1) ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'UTF8::trim' ),
			),
			'title' => array(
				array( 'strip_tags' ),
			),
			'data' => array(
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
			'sm_separate_file' => array(
				array(array($this, 'checkbox'))
			),
		);
	}

	/**
	 * Checks if a field not matches the value of another field.
	 *
	 * @param   array   $array  array of values
	 * @param   string  $field  field name
	 * @param   string  $match  field name to match
	 * @return  boolean
	 */
	public static function not_matches($array, $field, $match)
	{
		return ($array[$field] !== $array[$match]);
	}

	// добавить проверку (зменить типы с чисел на 4строки)
	public static function check_data($value, $data)
	{
		switch ($data['type'])
		{
			case 'static':
				return TRUE;
			case 'module':
				return Valid::not_empty($value) AND Valid::alpha_dash($value) AND ($value != '-');
			case 'page':
				return Valid::not_empty($value) AND Valid::digit($value) AND ($value != '-') AND ( $data['id'] != $value );
			case 'url':
				return Valid::not_empty($value) AND Model_Page::check_link($value) AND ($value != '-');
		}

		return FALSE;
	}

// проверить при добавлении. возможно надо будет удалить строку с delete_Bit
	public static function unique_uri($array)
	{
		$loaded = FALSE;
		if ( empty($array['id']) ) {
			$loaded = ORM::factory('page')
				->select('id', 'parent_id', 'uri')
				->where('parent_id', '=', $array['parent_id'])
				->and_where('uri', '=', $array['uri'])
				->and_where('delete_bit', '=', 0)
				->find()
				->loaded();
		}

		return ! $loaded;
	}

	public static function check_link($link)
	{
		if (strpos($link, '//') !== FALSE)
			return Valid::url($link);
		elseif (strpos($link, '/') === 0)
			return TRUE;
	}

}
