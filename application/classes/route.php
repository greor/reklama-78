<?php defined('SYSPATH') or die('No direct script access.');

class Route extends Kohana_Route {
	
	/**
	 * @var  string  Route name
	 */
	public $route_name;
	
	public function __construct($uri = NULL, $regex = NULL, $name = NULL)
	{
		if ( ! empty($name))
		{
			$this->route_name = $name;
		}
		
		parent::__construct($uri, $regex);
	}
	
	public static function remove_route($name)
	{
		if (isset(Route::$_routes[$name])) {
			unset(Route::$_routes[$name]);
		}
	}
	
	/**
	 * Generates a URI for the current route based on the parameters given.
	 *
	 *     // Using the "default" route: "users/profile/10"
	 *     $route->uri(array(
	 *         'controller' => 'users',
	 *         'action'     => 'profile',
	 *         'id'         => '10'
	 *     ));
	 *
	 * @param   array   $params URI parameters
	 * @return  string
	 * @throws  Kohana_Exception
	 * @uses    Route::REGEX_Key
	 */
	public function uri(array $params = NULL)
	{
		// Start with the routed URI
		$uri = $this->_uri;
	
		if (strpos($uri, '<') === FALSE AND strpos($uri, '(') === FALSE)
		{
			// This is a static route, no need to replace anything
	
			if ( ! $this->is_external())
				return $uri;
	
			// If the localhost setting does not have a protocol
			if (strpos($this->_defaults['host'], '://') === FALSE)
			{
				// Use the default defined protocol
				$params['host'] = Route::$default_protocol.$this->_defaults['host'];
			}
			else
			{
				// Use the supplied host with protocol
				$params['host'] = $this->_defaults['host'];
			}
	
			// Compile the final uri and return it
			return rtrim($params['host'], '/').'/'.$uri;
		}
	
		while (preg_match('#\([^()]++\)#', $uri, $match))
		{
			// Search for the matched value
			$search = $match[0];
	
			// Remove the parenthesis from the match as the replace
			$replace = substr($match[0], 1, -1);
	
			while (preg_match('#'.Route::REGEX_KEY.'#', $replace, $match))
			{
				list($key, $param) = $match;
	
				if (isset($params[$param]))
				{
					// Replace the key with the parameter value
					$replace = str_replace($key, $params[$param], $replace);
				}
				else
				{
					// This group has missing parameters
					$replace = '';
					break;
				}
			}
	
			// Replace the group in the URI
			$uri = str_replace($search, $replace, $uri);
		}
	
		while (preg_match('#'.Route::REGEX_KEY.'#', $uri, $match))
		{
			list($key, $param) = $match;
	
			if ( ! isset($params[$param]))
			{
				// Look for a default
				if (isset($this->_defaults[$param]))
				{
					$params[$param] = $this->_defaults[$param];
				}
				else
				{
					// Ungrouped parameters are required
					throw new Kohana_Exception('Required route parameter not passed: :param', array(
						':param' => $param,
					));
				}
			}
	
			$uri = str_replace($key, $params[$param], $uri);
		}
	
		// Trim all extra slashes from the URI
		$uri = preg_replace('#//+#', '/', rtrim($uri, '/'));
	
		if ($this->is_external())
		{
			// Need to add the host to the URI
			$host = $this->_defaults['host'];
	
			if (strpos($host, '://') === FALSE)
			{
				// Use the default defined protocol
				$host = Route::$default_protocol.$host;
			}
	
			// Clean up the host and prepend it to the URI
			$uri = rtrim($host, '/').'/'.$uri;
		}
	
		return $uri;
	}
	
}