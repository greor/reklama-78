<?php defined('SYSPATH') or die('No direct script access.');

class ORM_Helper_Photo extends ORM_Helper {

	protected $_safe_delete_field = 'delete_bit';

	protected $_position_fields = array(
		'position' => array(
			'group_by' => array('category_id'),
		),
	);
	
	protected $_file_fields = array(
		'image' => array(
			'path' => "upload/images/photo",
			'uri'  => NULL,
			'on_delete' => ORM_File::ON_DELETE_RENAME,
			'on_update' => ORM_File::ON_UPDATE_RENAME,
		),
	);

	public function file_rules()
	{
		return array(
			'image' => array(
				array('Ku_File::valid'),
				array('Ku_File::size', array(':value', '3M')),
				array('Ku_File::type', array(':value', 'jpg, jpeg, bmp, png, gif')),
			),
		);
	}

	public function __construct($model, $id = NULL)
	{
		parent::__construct($model, $id);
	
		$this->_file_fields['image']['allowed_src_dirs'][] = DOCROOT.'upload'.DIRECTORY_SEPARATOR.'multiupload';
	}
}
