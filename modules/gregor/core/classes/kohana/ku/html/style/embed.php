<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Style_Embed extends Ku_HTML_Style {

	protected $_tag = 'style';
	protected $_empty = FALSE;
	protected $_required;
	protected $_ignored = array('href');

	public function __construct($content, array $arrtibutes = NULL)
	{
		parent::__construct($arrtibutes);
		$this->_cdata = $content;
	}

} // End Kohana_Ku_HTML_Style_Embed