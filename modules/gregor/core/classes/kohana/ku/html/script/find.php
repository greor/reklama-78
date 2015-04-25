<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_Ku_HTML_Script_Find extends Ku_HTML_Script_File {

	public function render()
	{
		if ($this->_file)
		{
			$file = $this->_file;
			$pathinfo = pathinfo($file);
			$path = Kohana::find_file(
				$pathinfo['dirname'],
				$pathinfo['filename'],
				$pathinfo['extension'] ? $pathinfo['extension'] : 'js'
			);
			if($path)
			{
				$this->_file = $path;
			}
			else
			{
				$this->_file = NULL;
			}
			$html = parent::render();
			$this->_file = $file;
			return $html;
		}
		return parent::render();
	}

} // End Kohana_Ku_HTML_Script_Find