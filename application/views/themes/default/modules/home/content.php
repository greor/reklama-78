<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('modules/home/blocks/welcome');
	
	echo View_Theme::factory('modules/home/modules/promo', array('elements' => $promo));
	
	echo View_Theme::factory('modules/home/blocks/project');
	
	echo View_Theme::factory('modules/home/modules/projects', array('elements' => $projects));
	
	echo View_Theme::factory('modules/home/modules/services', array('elements' => $services));
	
	echo View_Theme::factory('modules/home/modules/clients', array('elements' => $clients));
	
	echo View_Theme::factory('modules/home/blocks/fun');
	
	echo View_Theme::factory('modules/home/modules/elements', array('elemenets' => $elemenets))

?>
