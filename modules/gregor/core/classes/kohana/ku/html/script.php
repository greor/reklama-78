<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Script extends Ku_HTML_Element {

	protected $_tag = 'script';
	protected $_empty = TRUE;
	protected $_short = FALSE;
	protected $_required = array('src');

	public function __construct($link, array $attributes = NULL)
	{
		$attributes['src'] = $link;
		parent::__construct($attributes);
	}

	public function render()
	{
		if (isset($this->src) AND 
		    (empty($this->src) OR (strpos($this->src, '://') === FALSE AND $this->src[0] !== '/'))
		   )
		{
			// Add the base URL
			$this->src = URL::base().$this->src;
		}
		
		if (empty($this->type))
		{
			$this->type = 'text/javascript';
		}
		return parent::render();
	}
} // End Ku_HTML_Script