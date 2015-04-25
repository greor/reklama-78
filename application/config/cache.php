<?php defined('SYSPATH') or die('No direct script access.');

$config = array (
	'file'    => array(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache/other',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	),
	'file-struct'    => array(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache/struct',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	),
	'page-helper'    => array(
		'driver'             => 'file',
		'cache_dir'          => APPPATH.'cache/page-helper',
		'default_expire'     => 3600,
		'ignore_on_delete'   => array(
			'.gitignore',
			'.git',
			'.svn'
		)
	),
);

foreach ($config as $item)
{
	if ( $item['driver'] === 'file' AND ! is_dir($item['cache_dir'])) {
		Ku_Dir::make_writable($item['cache_dir']);
		
		try {
			$realpath = str_replace('/', DIRECTORY_SEPARATOR, $item['cache_dir']);
			$realpath = realpath($realpath);
			chmod($realpath, 0777);
		} catch (Exception $e) {
			Kohana::$log->add(
					Log::ERROR,
					'Exception occurred: :exception. [:file][:line] ',
					array(
						':file'      => Debug::path(__FILE__),
						':line'      => __LINE__,
						':exception' => $e->getMessage()
					)
			);
		}
	}
}

return $config;