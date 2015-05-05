<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Block {
	
	public static function get_block($code)
	{
		$element = ORM::factory('block')
			->where('code', '=', $code)
			->find();
		
		if ( ! $element->loaded()) {
			return array();
		}
		
		$element = $element->as_array();
		
		if ( ! empty($element['image'])) {
			$helper = ORM_Helper::factory('block');
			
			$element['image'] = URL::base().$helper->file_uri('image', $element['image']);
			
		}
		
		return $element;
	}
	
}