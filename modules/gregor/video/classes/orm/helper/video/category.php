<?php defined('SYSPATH') or die('No direct script access.');

class ORM_Helper_Video_Category extends ORM_Helper {

	protected $_safe_delete_field = 'delete_bit';
	protected $_on_delete_cascade = array( 'video' );

}
