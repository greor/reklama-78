<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Script_Embed extends Ku_HTML_Script {

	protected $_tag = 'script';
	protected $_empty = FALSE;
	protected $_required;
	protected $_ignored = array('src');

	public function __construct($content, array $arrtibutes = NULL)
	{
		parent::__construct(NULL, $arrtibutes);
		$this->_cdata = $content;
	}

} // End Kohana_Ku_HTML_Script_Embed