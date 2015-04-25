<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Page {

	public static $instance;

	public static function instance()
	{
		if ( ! Helper_Page::$instance) {
			Helper_Page::$instance = new Helper_Page();
		}
		return Helper_Page::$instance;
	}

	/*
	 * check condition $page_orm->$field != $value
	 * return TRUE if condition evenly for $page_orm and all childrens
	 *
	 */
	public function not_equal( ORM $page_orm, $field, $value, $recursive = TRUE )
	{
		$result = NULL;

		if ( ! DONT_USE_CACHE) {
			$cache_key = $page_orm->id.$field.$value;
			try {
				$result = Cache::instance('page-helper')
					->get( $cache_key );
			} catch (ErrorException $e) {};
		}

		if ( $result === NULL ) {
			$result = $this->_not_equal($page_orm, $field, $value, $recursive);
			if ( ! DONT_USE_CACHE) {
				try {
					Cache::instance('page-helper')
						->set($cache_key, $result, Date::DAY);
				} catch (ErrorException $e) {};
			}
		}

		return $result;
	}

	private function _not_equal(ORM $page_orm, $field, $value, $recursive)
	{
		if ( $page_orm->$field == $value )
			return FALSE;

		$res = TRUE;
		if ($recursive === TRUE) {
			$childrens = ORM::factory( $page_orm->object_name() )
				->where('parent_id', '=', $page_orm->id)
				->find_all();
			foreach ( $childrens as $item ) {
				$res = $this->_not_equal($item, $field, $value, $recursive);
				if ( $res === FALSE )
					break;
			}
		}

		return $res;
	}

	public static function clear_cache()
	{
		Cache::instance('page-helper')
			->delete_all(TRUE);
	}

	/**
	 * @param array $page_list ( order by level asc, parent_id asc)
	 * @param string $delimiter
	 */
	public static function parse_to_list($page_list, $delimiter = '&nbsp;&#9658;&nbsp;')
	{
		$return = array();
		foreach ($page_list as $item) {
			if ( isset($return[ $item->parent_id ])) {
				$base_name = $return[ $item->parent_id ];
				$return[ $item->id ] = $base_name.$delimiter.$item->title;
			} else {
				$return[ $item->id ] = $item->title;
			}
		}
		return $return;
	}

	/**
	 * @param array $page_list ( order by level asc )
	 */
	public static function parse_to_base_uri($page_list)
	{
		$return = array();
		foreach ($page_list as $item) {
			if ($item->type == 'url') {
				$return[ $item->id ] = $item->data;
			} elseif ( isset($return[ $item->parent_id ]) ) {
				$base_uri = $return[ $item->parent_id ];
				$return[ $item->id ] = $base_uri.'/'.$item->uri;
			} else {
				$return[ $item->id ] = $item->uri;
			}
		}
		return $return;
	}

	public static function make_query_string(array $array)
	{
		return empty($array)
			? NULL
			: http_build_query($array);
	}
}