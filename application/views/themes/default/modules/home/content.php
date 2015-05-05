<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('modules/home/modules/promo', array('elements' => $promo));
	
	echo View_Theme::factory('modules/home/blocks/project');

?>

