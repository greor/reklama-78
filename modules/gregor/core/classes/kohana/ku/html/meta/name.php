<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Meta_Name extends Ku_HTML_Meta {

	protected $_required = array('name', 'content');
	protected $_readonly = array('name');
	protected $_ignored = array('http-equiv');

	public function __construct($name, $content, array $attributes = NULL)
	{
		$attributes['name'] = $name;
		parent::__construct($content, $attributes);
	}

} // End Ku_HTML_Meta_Name