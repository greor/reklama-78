<?php
return array(
	'type' => array(
		'data'	=>	'Page type value must be input.'
	),

	'data' => array(
		'check_data'	=>	'Page type value must be input.'
	),

	'errors' => array(
		'element_exist' 	=> "Element already exist. Element mast has unique name, and unique pair 'Parent page'-'URI'",
	),

	'parent_id' => array(
		'not_matches' 	=> "':field' must not relate to oneself",
	),

	'uri' => array(
		'unique_uri' 	=> "Page with this url already exist",
	),
);