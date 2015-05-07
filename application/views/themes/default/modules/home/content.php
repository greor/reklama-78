<?php defined('SYSPATH') or die('No direct script access.');

// 	echo View_Theme::factory('modules/home/modules/promo', array('elements' => $promo));
	
	echo View_Theme::factory('modules/home/blocks/welcome');
	
	echo View_Theme::factory('modules/home/blocks/project');
	
	echo View_Theme::factory('modules/home/modules/services', array('elements' => $services));
	
	echo View_Theme::factory('modules/home/modules/clients', array('elements' => $clients));

?>

