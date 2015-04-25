<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Script_File extends Ku_HTML_Script_Embed {

	protected $_file;

	public function file($file = NULL)
	{
		if ($value === NULL)
		{
			return $this->_file;
		}
		else
		{
			$this->_file = $file;
		}
		return $this;
	}

	public function __construct($file, array $arrtibutes = NULL)
	{
		parent::__construct(NULL, $arrtibutes);
		$this->_file = $file;
	}

	public function render()
	{
		if ($this->_file AND is_file($this->_file))
		{
			$this->_cdata = file_get_contents($this->_file);
		}
		return parent::render();
	}

} // End Kohana_Ku_HTML_Script_File