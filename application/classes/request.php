<?php defined('SYSPATH') or die('No direct script access.');

class Request extends Kohana_Request {

	/**
	 * Matched page
	 * @var ORM
	 */
	public $page;

	public static function factory($uri = TRUE, HTTP_Cache $cache = NULL, $injected_routes = array())
	{
		$request = parent::factory($uri, $cache, $injected_routes);
		
		// for admin routes
		$exluded_routes = array('admin', 'admin_error', 'modules');
		if ( $request->route() !== NULL AND in_array($request->route()->route_name, $exluded_routes)) {
			return $request;
		}
		
		ORM_Base::$filter_mode = ORM_Base::FILTER_FRONTEND;
		if ($request->route() !== NULL) {
			return $request;
		}
		
		$request_uri = $request->uri();
		$request->page = $page = Page_Route::page( $request->uri() );

		if ( $page !== NULL ) {
			$routes = array();
			
			if ($page['type'] == 'module' AND ! Helper_Module::is_stanalone($page['data'])) {
				
				$routes_config = Kohana::$config->load('routes/'.$page['data'])->as_array();
				$request->set_module_routes($routes_config, $page['uri_full'], $page['id'], $cache);
				
			} else {
				
				if ( Helper_Module::is_stanalone($page['data']) ) {
					/*
					 * For controllers which no need admin side (only public contoller) 
					 * and have one action (by default is 'index')
					 */
					$name = $page['id'].'<->standalone_page';
					$uri_callback = $page['uri_full'];
					$defaults = array(
						'directory'  => 'standalone',
						'controller' => $page['data'],
					);
				} else {
					/*
					 * For simple static pages
					 */
					$name = $page['id'].'<->std_page';
					$uri_callback = $page['uri_full'];
					$defaults = array(
						'controller' => 'page',
						'action'     => $page['type'],
					);
				}
				
				$route = new Route( $uri_callback );
				$route->defaults($defaults);
				$routes[ $name ] = $route;

				Route::set($name, $uri_callback)
					->defaults($defaults);

				$processed_uri = Request::process_uri($request_uri, $routes);
				if ($processed_uri !== NULL) {
					$request->set_dinamic_route( $processed_uri, $cache );
				}
			}

		} elseif ( strpos($request_uri, '_module') === 0 ) {
			/*
			 * For example, if need to use action which independent of page (page_id) 
			 */
			$request_uri_arr = explode('/', str_replace('_module/', '', $request_uri));

			$module_name = array_shift($request_uri_arr);
			$uri_base = '_module/'.$module_name;
			unset($request_uri_arr);

			$routes_config = Kohana::$config->load('routes/'.$module_name)->as_array();
			$request->set_module_routes($routes_config, $uri_base, $module_name, $cache);
		} else {
			Kohana::$log->add(
				Log::ERROR,
				'Page for :uri not found. [:file][:line] ',
				array(
					':file' => Debug::path(__FILE__),
					':line' => __LINE__,
					':uri'  => $request->uri()
				)
			);

			throw new HTTP_Exception_404();
		}

		return $request;
	}

	public function set_module_routes($routes_config, $uri_base, $prefix, $cache)
	{
		$routes = array();

		foreach ($routes_config as $name => $route) {
			$name = $prefix.'<->'.$name;
			$uri_callback = $uri_base.Arr::get( $route, 'uri_callback' );
			$regex = Arr::get( $route, 'regex' );
			$defaults = Arr::get( $route, 'defaults' );

			$route = new Route( $uri_callback, $regex );
			$route->defaults($defaults);
			$routes[ $name ] = $route;

			Route::set($name, $uri_callback, $regex)
				->defaults($defaults);
		}

		$processed_uri = Request::process_uri($this->uri(), $routes);
		if ($processed_uri !== NULL) {
			$this->set_dinamic_route( $processed_uri, $cache );
		}
	}

	public function set_dinamic_route( $processed_uri, $cache )
	{
		// Store the matching route
		$this->_route = $processed_uri['route'];
		$params = $processed_uri['params'];

		// Is this route external?
		$this->_external = $this->_route->is_external();

		if (isset($params['directory'])) {
			// Controllers are in a sub-directory
			$this->_directory = $params['directory'];
		}

		// Store the controller
		$this->_controller = $params['controller'];

		if (isset($params['action'])) {
			// Store the action
			$this->_action = $params['action'];
		} else {
			// Use the default action
			$this->_action = Route::$default_action;
		}

		// These are accessible as public vars and can be overloaded
		unset($params['controller'], $params['action'], $params['directory']);

		// Params cannot be changed once matched
		$this->_params = $params;

		// Apply the client
		$this->_client = new Request_Client_Internal(array('cache' => $cache));
	}

	public function set_param($name, $value)
	{
		$this->_params[$name] = $value;
	}
}