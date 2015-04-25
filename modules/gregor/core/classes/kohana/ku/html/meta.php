<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_Ku_HTML_Meta extends Ku_HTML_Element {

	protected $_tag = 'meta';
	protected $_empty = TRUE;
	protected $_required = array('content');

	public function __construct($content, array $attributes = NULL)
	{
		$attributes['content'] = $content;
		parent::__construct($attributes);
	}
} // End Ku_HTML_Meta