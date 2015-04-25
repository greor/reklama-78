<?php defined('SYSPATH') or die('No direct script access.');

class Page_Route extends Kohana {

	protected static $dynamic_routes = array();

	/*
	 * if $page_id === FALSE, then $base_uri == ''. uses for hidden modules controllers
	 */
	public static function uri($page_id, $name, array $params = NULL)
	{
		if ( empty(self::$dynamic_routes) ) {
			foreach ( Kohana::list_files('config'.DIRECTORY_SEPARATOR.'routes') as $file_key => $file ) {
				$_tmp = explode(DIRECTORY_SEPARATOR, $file_key);
				$config_file_name = array_pop( $_tmp );

				$routes_config = Kohana::$config->load('routes/'.str_replace('.php', '', $config_file_name))
					->as_array();

				foreach ($routes_config as $key => $value) {
					$route = new Route(
						Arr::get( $value, 'uri_callback' ),
						Arr::get( $value, 'regex' )
					);
					$route->defaults( Arr::get( $value, 'defaults' ) );

					self::$dynamic_routes[ $key ] = $route;
				}
			}
		}

		if (isset( self::$dynamic_routes[ $name ] )) {
			$base_uri =  ( $page_id !== FALSE ) ? Page_Route::dynamic_base_uri($page_id) : '_module/'.$name;
			$route_uri = self::$dynamic_routes[ $name ]->uri( $params );

			return $base_uri.$route_uri;
		}

		return NULL;
	}

	public static function dynamic_base_uri( $page_id )
	{
		$uri = NULL;
		$cache_key = __METHOD__.$page_id;

		if ( ! DONT_USE_CACHE) {
			try {
				$uri = Cache::instance('file-struct')
					->get( $cache_key );
			} catch (ErrorException $e) {};
		}


		if ( $uri === NULL ) {
			$page_orm = ORM::factory('page');

			$stop = FALSE;
			while ( ! $stop) {
				$page = $page_orm
					->clear()
					->select( 'id', 'parent_id', 'uri' )
					->where( 'id', '=', $page_id )
					->and_where( 'status', '>', 0 )
					->find();

				if ( $page->loaded() ) {
					$uri = $page->uri.'/'.$uri;
					if ( $page->parent_id != 0 ) {
						$page_id = $page->parent_id;
					} else {
						$stop = TRUE;
					}
				} else {
					$stop = TRUE;
				}
			}

			if ( ! DONT_USE_CACHE) {
				try {
					Cache::instance('file-struct')
						->set($cache_key, $uri, Date::DAY);
				} catch (ErrorException $e) {};
			}
		}

		return trim( $uri, '/' ) ;
	}

	/**
	 *
	 * Return page for $uri
	 * @param string $uri URI of the request
	 */

	public static function page($uri)
	{
		$matched = NULL;
		$cache_key = __METHOD__.$uri;

		if ( ! DONT_USE_CACHE) {
			try {
				$matched = Cache::instance('file-struct')
					->get( $cache_key );
			} catch (ErrorException $e) {};
		}

		if ( $matched === NULL ) {
			$uri = trim($uri, '/');
			$uri = explode('/', $uri);

			$parent_id = 0;
			$path = '';

			$page_orm = ORM::factory('page');
			foreach ($uri as $k => $segment) {
				$page = $page_orm
					->clear()
					->select( 'id', 'parent_id', 'uri', 'type', 'data', 'title', 'name' )
					->where( 'uri', '=', $segment )
					->and_where( 'status', '>', 0 )
					->and_where( 'parent_id', '=', $parent_id )
					->find();

				if ( $page->loaded() ) {
					if ( empty( $path ) ) {
						$path = $segment;
					} else {
						$path = $path.'/'.$segment;
					}

					$matched = array(
						'id'		=>	$page->id,
						'name'		=>	$page->name,
						'parent_id'	=>	$page->parent_id,
						'title'		=>	$page->title,
						'uri'		=>	$page->uri,
						'uri_full'	=>	$path,
						'type'		=>	$page->type,
						'data'		=>	$page->data,
					);
					$parent_id = $page->id;
				} else {
					break;
				}
			}

			if ( ! DONT_USE_CACHE) {
				try {
					Cache::instance('file-struct')
						->set($cache_key, $matched, Date::DAY);
				} catch (ErrorException $e) {};
			}
		}

		return $matched;
	}

	public static function page_by_name($name)
	{
		$return = NULL;
		$cache_key = __METHOD__.$name;

		if ( ! DONT_USE_CACHE) {
			try {
				$return = Cache::instance('file-struct')
					->get( $cache_key );
			} catch (ErrorException $e) {};
		}

		if ( $return === NULL ) {
			$return = ORM::factory('page')
				->select('id', 'parent_id', 'title')
				->where('name', '=', $name)
				->and_where('status', '>', 0)
				->find();

			$return = array(
				'id' => $return->id,
				'parent_id' => $return->parent_id,
				'title' => $return->title,
			);

			if ( ! DONT_USE_CACHE) {
				try {
					Cache::instance('file-struct')
						->set($cache_key, $return, Date::DAY);
				} catch (ErrorException $e) {};
			}
		}

		return $return;
	}

	public static function page_by_id($page_id)
	{
		$return = NULL;
		$cache_key = __METHOD__.$page_id;

		if ( ! DONT_USE_CACHE) {
			try {
				$return = Cache::instance('file-struct')
					->get( $cache_key );
			} catch (ErrorException $e) {};
		}

		if ( $return === NULL ) {
			$page = ORM::factory('page')
				->select( 'id', 'parent_id', 'uri', 'title', 'name' )
				->where( 'id', '=', $page_id )
				->and_where( 'status', '>', 0 )
				->find();

			if ($page->loaded()) {
				$return = array(
					'id'		=>	$page->id,
					'parent_id'	=>	$page->parent_id,
					'title'		=>	$page->title,
					'name'		=>	$page->name,
					'url'		=>	self::dynamic_base_uri($page->id),
				);
			} else {
				$return = array();
			}

			if ( ! DONT_USE_CACHE) {
				try {
					Cache::instance('file-struct')
						->set($cache_key, $return, Date::DAY);
				} catch (ErrorException $e) {};
			}
		}

		return $return;
	}
	
}

