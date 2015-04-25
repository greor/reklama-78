<?php defined('SYSPATH') OR die('No direct access allowed.');

class Ku_Cacher {

	public static $dependent_groups = array();

	/**
	 * Retrieve a cached value entry by id from default group.
	 *
	 *     // Retrieve cache entry from default group
	 *     $data = Ku_Cacher::get('foo');
	 *
	 *     // Retrieve cache entry from default group and return 'bar' if miss
	 *     $data = Ku_Cacher::get('foo', 'bar');
	 *
	 * @param   string   id of cache to entry
	 * @param   string   default value to return if cache miss
	 * @return  mixed
	 * @throws  Kohana_Cache_Exception
	 */
	public static function get($id, $default = NULL)
	{
		return Cache::instance()->get($id, $default);
	}

	/**
	 * Retrieve a cached value entry by id from specified group.
	 *
	 *     // Retrieve cache entry from sqlite group
	 *     $data = Ku_Cacher::group_get('sqlite', 'foo');
	 *
	 *     // Retrieve cache entry from sqlite group and return 'bar' if miss
	 *     $data = Ku_Cacher::group_get('sqlite', 'foo', 'bar');
	 *
	 *     // Retrieve cache entry from memcache group
	 *     $data = Ku_Cacher::group_get('memcache', 'foo');
	 *
	 * @param   string   the name of the cache group to use
	 * @param   string   id of cache to entry
	 * @param   string   default value to return if cache miss
	 * @return  mixed
	 * @throws  Kohana_Cache_Exception
	 */
	public static function group_get($group, $id, $default = NULL)
	{
		return Cache::instance($group)->get($id, $default);
	}

	/**
	 * Set a value to default group cache with id and lifetime
	 *
	 *     $data = 'bar';
	 *
	 *     // Set 'bar' to 'foo' in default group, using default expiry
	 *     Ku_Cacher::set('foo', $data);
	 *
	 *     // Set 'bar' to 'foo' in default group for 30 seconds
	 *     Ku_Cacher::set('foo', $data, 30);
	 *
	 *     // Set 'bar' to 'foo' in default group for 10 minutes
	 *     if (Ku_Cacher::set('foo', $data, 600))
	 *     {
	 *          // Cache was set successfully
	 *          return
	 *     }
	 *
	 * @param   string   id of cache entry
	 * @param   string   data to set to cache
	 * @param   integer  lifetime in seconds [Optional]
	 * @param   array    tags [Optional]
	 * @return  boolean
	 */
	public static function set($id, $data, $lifetime = NULL, array $tags = NULL)
	{
		return Ku_Cacher::group_set(Cache::$default, $id, $data, $lifetime, $tags);
	}


	/**
	 * Set a value to specified group cache with id and lifetime
	 *
	 *     $data = 'bar';
	 *
	 *     // Set 'bar' to 'foo' in sqlite group, using default expiry
	 *     Ku_Cacher::group_set('sqlite', 'foo', $data);
	 *
	 *     // Set 'bar' to 'foo' in default group for 30 seconds
	 *     Ku_Cacher::group_set(NULL, 'foo', $data, 30);
	 *
	 *     // Set 'bar' to 'foo' in 'file' group for 10 minutes
	 *     if (Ku_Cacher::group_set('file', 'foo', $data, 600))
	 *     {
	 *          // Cache was set successfully
	 *          return
	 *     }
	 *
	 * @param   string   the name of the cache group to use
	 * @param   string   id of cache entry
	 * @param   string   data to set to cache
	 * @param   integer  lifetime in seconds [Optional]
	 * @param   array    tags [Optional]
	 * @return  boolean
	 */
	public static function group_set($group, $id, $data, $lifetime = NULL, array $tags = NULL)
	{
		$cache = Cache::instance($group);
		if ($cache instanceof Kohana_Cache_Tagging)
		{
			return $cache->set_with_tags($id, $data, $lifetime, $tags);
		}
		else
		{
			return $cache->set($id, $data, $lifetime);
		}
	}

	/**
	 * Delete a cache entry based on id from default group
	 *
	 *     // Delete 'foo' entry from the default group
	 *     Ku_Cacher::delete('foo');
	 *
	 * @param   string   id to remove from cache
	 * @return  boolean
	 */
	public static function delete($id)
	{
		return Ku_Cacher::group_delete(Cache::$default, $id);
	}


	/**
	 * Delete a cache entry based on id from specified group
	 *
	 *     // Delete 'foo' entry from the default group
	 *     Ku_Cacher::group_delete(NULL, 'foo');
	 *
	 *     // Delete 'foo' entry from the memcache group
	 *     Ku_Cacher::group_delete('memcache', 'foo')
	 *
	 * @param   string   the name of the cache group to use
	 * @param   string   id to remove from cache
	 * @return  boolean
	 */
	public static function group_delete($group, $id)
	{
		($group === NULL) and $group = Cache::$default;
		if (isset(Ku_Cacher::$dependent_groups[$group]))
		{
			foreach (Ku_Cacher::$dependent_groups[$group] as $dep_group)
			{
				Cache::instance($dep_group)->delete($id);
			}
		}
		return Cache::instance($group)->delete($id);
	}

	/**
	 * Delete all cache entries.
	 *
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 *
	 *     // Delete all cache entries in the default group
	 *     Ku_Cacher::delete_all();
	 *
	 *     // Delete all cache entries in the memcache group
	 *     Ku_Cacher::delete_all('memcache');
	 *
	 * @param   string   the name of the cache group to use
	 * @return  boolean
	 */
	public static function delete_all($group = NULL)
	{
		return Ku_Cacher::group_delete_all($group);
	}

	/**
	 * Delete all cache entries in the specified group.
	 *
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 *
	 *     // Delete all cache entries in the default group
	 *     Ku_Cacher::group_delete_all(NULL);
	 *
	 *     // Delete all cache entries in the memcache group
	 *     Ku_Cacher::group_delete_all('memcache');
	 *
	 * @param   string   the name of the cache group to use
	 * @return  boolean
	 */
	public static function group_delete_all($group)
	{
		($group === NULL) and $group = Cache::$default;
		if (isset(Ku_Cacher::$dependent_groups[$group]))
		{
			foreach (Ku_Cacher::$dependent_groups[$group] as $dep_group)
			{
				Cache::instance($dep_group)->delete_all();
			}
		}
		return Cache::instance($group)->delete_all();
	}

	/**
	 * Delete cache entries based on a tag from default group
	 *
	 * @param   string   tag
	 * @param   integer  timeout [Optional]
	 */
	public static function delete_tag($tag)
	{
		Ku_Cacher::group_delete_tag(Cache::$default, $tag);
	}

	/**
	 * Delete cache entries based on a tag from specified group
	 *
	 * @param   string   the name of the cache group to use
	 * @param   string   tag
	 * @param   integer  timeout [Optional]
	 */
	public static function group_delete_tag($group, $tag)
	{
		($group === NULL) and $group = Cache::$default;
		if (isset(Ku_Cacher::$dependent_groups[$group]))
		{
			foreach (Ku_Cacher::$dependent_groups[$group] as $dep_group)
			{
				$cache = Cache::instance($dep_group);
				if ($cache instanceof Kohana_Cache_Tagging)
				{
					$cache->delete_tag($tag);
				}
			}
		}
		$cache = Cache::instance($group);
		if ($cache instanceof Kohana_Cache_Tagging)
		{
			$cache->delete_tag($tag);
		}
	}

	/**
	 * Find cache entries based on a tag in the default group
	 *
	 * @param   string   tag
	 * @return  array
	 */
	public static function find($group, $tag)
	{
		return Ku_Cacher::group_find(Cache::$default, $tag);
	}

	/**
	 * Find cache entries based on a tag in the specified group
	 *
	 * @param   string   the name of the cache group to use
	 * @param   string   tag
	 * @return  array
	 */
	public static function group_find($group, $tag)
	{
		($group === NULL) and $group = Cache::$default;
		if ($cache instanceof Kohana_Cache_Tagging)
		{
			return $cache->find($tag);
		}
		else
		{
			return array();
		}
	}


	/**
	 * Delete cache entries based on a array tags from default group
	 *
	 * @param   array   tags
	 */
	public static function delete_tags(array $tags)
	{
		Ku_Cacher::group_delete_tags(Cache::$default, $tags);
	}

	/**
	 * Delete cache entries based on a array tags from specified group
	 *
	 * @param   string  the name of the cache group to use
	 * @param   array   tags
	 */
	public static function group_delete_tags($group, array $tags)
	{
		if (empty($tags))
			return;

		if (count($tags) === 1)
		{
			Ku_Cacher::group_delete_tag($group, current($tags));
			return;
		}

		($group === NULL) and $group = Cache::$default;
		if (isset(Ku_Cacher::$dependent_groups[$group]))
		{
			foreach (Ku_Cacher::$dependent_groups[$group] as $dep_group)
			{
				$cache = Cache::instance($dep_group);
				if ($cache instanceof Kohana_Cache_Tagging)
				{
					foreach ($tags as $tag)
					{
						$cache->delete_tag($tag);
					}
				}
			}
		}


		$cache = Cache::instance($group);
		if ($cache instanceof Kohana_Cache_Tagging)
		{
			foreach ($tags as $tag)
			{
				$cache->delete_tag($tag);
			}
		}
	}

} // End Ku_Loader
