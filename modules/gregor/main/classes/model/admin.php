<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Admin extends ORM implements Acl_Role_Interface {

	/**
	 * @see Acl_Role_Interface::get_role_id()
	 */
	public function get_role_id()
	{
		return $this->role;
	}

	public function labels()
	{
		return array(
			'active'			=> 'Active',
			'email'				=> 'E-mail',
			'username'			=> 'Login',
			'password'			=> 'Password',
			'password_confirm'	=> 'Password confirm',
			'role'				=> 'Role',

			'logins'			=> 'Logins',
			'last_login'		=> 'Last login',
			'attempts'			=> 'Attempts',
			'last_attempt'		=> 'Last attempt',
		);
	}

	public function rules()
	{
		return array(
				'id' => array(
					array( 'digit' ),
				),
				'username' => array(
					array('not_empty'),
					array('max_length', array(':value', 32)),
					array( array($this, 'unique_ext'), array('username', ':value') ),
				),
				'password' => array(
					array('not_empty'),
					array('Model_Admin::check_password'),
				),
				'email' => array(
					array('not_empty'),
					array('email'),
					array( array($this, 'unique_ext'), array('email', ':value') ),
				),
				'logins' => array(
					array( 'digit' ),
				),
				'last_login' => array(
					array( 'digit' ),
				),
				'attempts' => array(
					array( 'digit' ),
				),
				'last_attempt' => array(
					array( 'digit' ),
				),
				'role' => array(
					array( 'not_empty' ),
				),
		);
	}

	public function filters()
	{
		return array(
			'password' => array(
				array( array( A1::instance('admin/a1'), 'hash' ) )
			)
		);
	}

	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 8))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}

	public static function check_password($value)
	{
		return	(bool) preg_match('/[a-zA-Z]*/', (string) $value)
				AND (bool) preg_match('/[0-9]*/', (string) $value) 
				AND (bool) preg_match('/[-_,.\/?!+=*@#]*/', (string) $value);
	}

}