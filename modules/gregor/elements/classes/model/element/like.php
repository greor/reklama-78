<?php defined('SYSPATH') or die('No direct script access.');

class Model_Element_Like extends Kohana_ORM {

	protected $_table_name = 'elements_likes';
	
	protected $_belongs_to = array(
		'element' => array(
			'model' => 'element',
			'foreign_key' => 'element_id',
		),
	);

	public function labels()
	{
		return array(
			'element_id'  => 'Element ID',
			'count'       => 'Like count',
			'ip'          => 'Ip',
			'user_agent'  => 'User agent',
			'expires'     => 'Expires',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array( 'digit' ),
			),
			'element_id' => array(
				array( 'not_empty' ),
				array( 'digit' ),
			),
			'count' => array(
				array( 'digit' ),
			),
			'ip' => array(
				array( 'not_empty' ),
				array( 'ip' ),
			),
			'expires' => array(
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
		);
	}

	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (mt_rand(1, 100) === 1) {
			$this->delete_expired();
		}

		if ($this->expires < time() AND $this->_loaded) {
			$this->delete();
		}
	}
	

	public function delete_expired()
	{
		DB::delete($this->_table_name)
			->where('expires', '<', time())
			->execute($this->_db);

		return $this;
	}
}
