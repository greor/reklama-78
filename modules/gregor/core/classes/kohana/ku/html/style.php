<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Style extends Ku_HTML_Element {

	protected $_tag = 'link';
	protected $_empty = TRUE;
	protected $_required = array('href');
	protected $_readonly = array('rel', 'type');

	public function __construct($link, array $attributes = NULL)
	{
		$attributes['href'] = $link;
		$attributes['rel'] = 'stylesheet';
		$attributes['type'] = 'text/css';
		parent::__construct($attributes);
	}
	
	public function render()
	{
		if (isset($this->href) AND 
		    (empty($this->href) OR (strpos($this->href, '://') === FALSE AND $this->href[0] !== '/'))
		   )
		{
			// Add the base URL
			$this->href = URL::base().$this->href;
		}
		return parent::render();
	}
} // End Kohana_Ku_HTML_Style