<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Thumbnail helper
 *
 */
class Kohana_Thumb {

	/**
	 * @var string Root directory for calculate paths
	 */
	public static $docroot = DOCROOT;

	/**
	 * @var boolean Use lazy creating of thumb on HTTP request
	 */
	public static $lazy_create = TRUE;

	/**
	 * @var  Config_Group  Config group object
	 */
	protected static $_config;

	/**
	 * @var  string  Thumb route template
	 */
	protected static $_route_tpl;



	/**
	 * Get a URI for thumb.
	 *
	 * @param   string   group of thumb config
	 * @param   string   URI of source image
	 * @param   boolean  Use lazy creating of thumb
	 * @return  boolean|string URI for thumb or FALSE if failure
	 */
	public static function uri($group, $src_uri, $lazy_create = NULL)
	{
		if (empty($src_uri))
			return FALSE;

		($lazy_create === NULL) and $lazy_create = self::$lazy_create;

		(self::$_config === NULL) and self::_load_config();
		(self::$_route_tpl === NULL) and self::_set_route_tpl();

		$config = self::$_config->get($group);

		if ( ! $config)
			return FALSE;


		if ($lazy_create === TRUE)
		{
			$path = Arr::get($config, 'path', '');
			$len = strlen($path);

			if ($len)
			{
				if (substr($src_uri, 0, $len) === $path)
				{
					$src_uri = substr($src_uri, $len);
				}
				else
				{
					// File path not started from base path
					return FALSE;
				}
			}
			$src_uri = trim($src_uri, '/');

			return rtrim(sprintf(self::$_route_tpl, $group, $src_uri), '/');
		}
		else
		{
			$thumb = self::create($group, $src_uri);
			if ($thumb)
			{
				return self::to_uri($thumb);
			}
		}
		return FALSE;
	}

	/**
	 * Get a URI for thumb directory.
	 *
	 * @param   string   group of thumb config
	 * @param   string   URI of source image direcroty
	 * @return  boolean|string URI for directory or FALSE if failure
	 */
	public static function dir_uri($group, $src_uri)
	{
		(self::$_config === NULL) and self::_load_config();
		(self::$_route_tpl === NULL) and self::_set_route_tpl();

		$config = self::$_config->get($group);

		if ( ! $config)
			return FALSE;

		$path = Arr::get($config, 'path', '');
		$len = strlen($path);

		if ( ! empty($src_uri) AND $len)
		{
			if (substr($src_uri, 0, $len) === $path)
			{
				$src_uri = substr($src_uri, $len);
			}
			else
			{
				// Src path not started from base path
				return FALSE;
			}
		}
		$src_uri = trim($src_uri, '/');

		return rtrim(sprintf(self::$_route_tpl, $group, $src_uri), '/').'/';
	}

	/**
	 * Generate thumb.
	 *
	 * @param   string   group of thumb config
	 * @param   string   path to source file
	 * @param   boolean  force process even if the thumb file already exists
	 * @return  string|boolean Realpath of created file or FALSE if failure
	 */
	public static function create($group, $file, $force = FALSE)
	{
		if (empty($file))
			return FALSE;

		$file = str_replace('/', DIRECTORY_SEPARATOR, $file);

		if (strpos($file, '.'.DIRECTORY_SEPARATOR) !== FALSE)
			// File is invalid: "./" and "../" not allowed
			return FALSE;

		(self::$_config === NULL) and self::_load_config();
		(self::$_route_tpl === NULL) and self::_set_route_tpl();

		$config = self::$_config->get($group);

		if ( ! $config)
			return FALSE;

		// Detect realpath for base group path
		$path = rtrim(Arr::get($config, 'path', ''), '/');
		if ( ! $path)
		{
			$realpath = realpath(self::$docroot);
		}
		else if ($realpath = realpath($path))
		{
			// Path finded
		}
		else if ($realpath = realpath(self::$docroot.$path))
		{
			// Path finded
		}
		else if ($realpath = realpath(DOCROOT.$path))
		{
			// Path finded
		}
		else
		{
			// Path not exists
			return FALSE;
		}
		$path = $realpath;

		// Detect realpath for src file
		if ($realpath = realpath($file))
		{
			// File finded
		}
		else if ($realpath = realpath(self::$docroot.$file))
		{
			// File finded
		}
		else if ($realpath = realpath(DOCROOT.$file))
		{
			// File finded
		}
		else
		{
			// File not found
			return FALSE;
		}
		$file = $realpath;

		if (is_file($file) AND is_dir($path) AND strpos($file, $path) === 0)
		{
			$thumb_path = self::$docroot.sprintf(self::$_route_tpl, $group, str_replace($path, '', $file));
			$thumb_path = str_replace('/', DIRECTORY_SEPARATOR, $thumb_path);

			if ($force === TRUE OR ! is_file($thumb_path))
			{
				$img = Image::factory($file);

				foreach($config as $key => $params)
				{
					switch ($key)
					{
						case 'resize':
							$params += array(
								'width'  => NULL,
								'height' => NULL,
								'master' => NULL
							);
							$img->resize(
								$params['width'],
								$params['height'],
								$params['master']
							);
						break;

						case 'crop':
							$params += array(
								'width'    => NULL,
								'height'   => NULL,
								'offset_x' => NULL,
								'offset_y' => NULL
							);
							$img->crop(
								$params['width'],
								$params['height'],
								$params['offset_x'],
								$params['offset_y']
							);
						break;

						case 'truncate':
							$params += array(
								'color'  => NULL,
								'distance'  => NULL,
								'top'   	=> NULL,
								'right' 	=> NULL,
								'bottom' 	=> NULL,
								'left' 		=> NULL
							);
							$img->truncate(
								$params['color'],
								$params['distance'],
								$params['top'],
								$params['right'],
								$params['bottom'],
								$params['left']
							);
						break;

						default:
							if (is_callable($key))
							{
								call_user_func($key, $img, $params);
							}
					}
				}

				// Make directory writable
				Ku_Dir::make_writable(dirname($thumb_path));

				// Save file by requsted path
				if ($img->save($thumb_path, Arr::get($config, 'quality', 90)) === FALSE)
				{
					return FALSE;
				}
			}
			return realpath($thumb_path);
		}
		return FALSE;
	}

	/**
	 * Transform realpath of thumb to URI
	 *
	 * @param string $realpath Realpath to thumb
	 * @return boolean|string URI for thumb or FALSE if failure
	 */
	public static function to_uri($realpath)
	{
		if ( ! $realpath OR substr($realpath, 0, strlen(self::$docroot)) !== self::$docroot)
			return FALSE;
		return trim(str_replace('\\', '/', substr($realpath, strlen(self::$docroot))), '/');
	}

	/**
	 * Load thumb config to local cache
	 *
	 * @return void
	 */
	protected static function _load_config()
	{
		self::$_config = Kohana::$config->load('thumb');
	}

	/**
	 * Assign route template to local cache
	 *
	 * @return void
	 */
	protected static function _set_route_tpl()
	{
		// Compile route template to using "sprintf"
		self::$_route_tpl = Route::get('thumb')->uri(array('group' => '%1$s', 'file' => '%2$s'), FALSE);
	}


	private function __construct()
	{
		// It's static class
	}

} // End Kohana_Thumb