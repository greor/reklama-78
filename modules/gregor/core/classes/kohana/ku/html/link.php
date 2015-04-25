<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Link extends Ku_HTML_Element {

	protected $_tag = 'link';
	protected $_empty = TRUE;

	public function __construct($link = NULL, array $attributes = NULL)
	{
		($link !== NULL) and $attributes['href'] = $link;
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
} // End Kohana_Ku_HTML_Link