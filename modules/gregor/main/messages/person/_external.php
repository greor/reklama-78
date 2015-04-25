<?php
return array(
	'password' => array(
		'Model_Person::check_password'	=> '\':field\' field must contain symbols (Model_Person)',
		'Model_Admin::check_password'	=> '\':field\' field must contain symbols (Model_Admin)',
	),
	'password_confirm' => array(
		'matches'	=> '\':field\' field must be same \'Password\' field',
	),
);