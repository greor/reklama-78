<?php defined('SYSPATH') or die('No direct script access.');

class View_Theme extends View {

	/**
	 * Returns a new View object. If you do not define the "file" parameter,
	 * you must call [View::set_filename].
	 *
	 *     $view = View_Theme::factory($file);
	 *
	 * @param   string  view filename
	 * @param   array   array of values
	 * @return  View_Theme
	 */
	public static function factory($file = NULL, array $data = NULL)
	{
		return new View_Theme($file, $data);
	}

	public function set_filename($file)
	{
		$config = Kohana::$config->load('site');
		if (($path = Kohana::find_file('views', 'themes/'.$config['theme'].'/'.$file)) !== FALSE)
		{
			$this->_file = $path;

			return $this;
		}

		// By default try use common view
		return parent::set_filename($file);
	}

} // End Kohana_View_Theme