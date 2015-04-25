<?php defined('SYSPATH') or die('No direct script access.');

class Model_Setting extends ORM {

	public function labels()
	{
		return array(
			'name'	=>	'Name',
			'value'	=>	'Value',
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
				array( 'min_length', array( ':value', 1 ) ),
				array( 'max_length', array( ':value', 50 ) ),
				array( 'alpha_dash' ),
			),
			'value' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'UTF8::trim' ),
			),
		);
	}

}
