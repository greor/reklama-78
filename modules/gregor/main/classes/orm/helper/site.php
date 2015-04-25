<?php
class ORM_Helper_Site extends ORM_Helper {

	/**
	 * Name of field what marks record as "deleted"
	 * @var string
	 */
	protected $_safe_delete_field = 'delete_bit';

	protected $_file_fields = array(
		'logo' => array(
			'path' => "upload/images/logo",
			'uri'  => NULL,
			'on_delete' => ORM_File::ON_DELETE_RENAME,
			'on_update' => ORM_File::ON_UPDATE_RENAME,
			'allowed_src_dirs' => array(),
		),
	);
	
	public function file_rules()
	{
		return array(
			'logo' => array(
				array('Ku_File::valid'),
				array('Ku_File::size', array(':value', '3M')),
				array('Ku_File::type', array(':value', 'jpg, jpeg, bmp, png, gif')),
			),
		);
	}
	
	
	public static function clear_site_cache()
	{
//		Controller_Admin_Structure::clear_structure_cache();
//		if ( ! DONT_USE_CACHE) {
//  			Cache::instance('sites')->delete_all(TRUE);
//		}
	}

}