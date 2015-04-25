<?php defined('SYSPATH') or die('No direct script access.');

class Model_Site extends ORM_Base {

	/**
	 * @var array
	 */
	protected $_sorting = array( 'name' => 'ASC' );

	/**
	 * Deleted column name
	 * @var string
	 */
	protected $_deleted_column = 'delete_bit';
	/**
	 * Deleted column name
	 * @var string
	 */
	protected $_active_column = 'active';
	
	public function labels()
	{
		return array(
			'url' 					=>	'URL',
			'name' 					=>	'Site name',
			'logo' 					=>	'Logo',
			'vkontakte_link'		=>	'Vk.com (link)',
			'twitter_link'			=>	'Twitter.com (link)',
			'facebook_link'			=>	'Facebook.com (link)',
			'youtube_link'			=>	'Youtube.com (link)',
			'odnoklassniki_link'	=>	'Odnoklassniki.com (link)',
			'google_link'			=>	'Google+ (link)',
			'instagram_link'		=>	'Instagram.com (link)',
			'active' 				=>	'Active',
			
			'vk_api_id' 			=>	'VK appID',
			'vk_group_id' 			=>	'VK group ID',
			'fb_app_id' 			=>	'Facebook appID',
			'fb_group_link' 		=>	'Facebook group link',
			'tw_widget' 			=>	'Twitter widget',
			
			'title_tag' 			=>	'Title tag',
			'keywords_tag' 			=>	'Keywords tag',
			'description_tag' 		=>	'Desription tag',
		);
	}

	public function rules()
	{
		return array(
			'id' => array(
				array( 'digit' ),
			),
			'url' => array(
// 				array( 'not_empty' ),
				array( 'min_length', array( ':value', 4 ) ),
				array( 'max_length', array( ':value', 255 ) ),
				array( 'url' ),
			),
			'name' => array(
				array( 'not_empty' ),
				array( 'min_length', array( ':value', 4 ) ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			'logo' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'vkontakte_link' => array(
				array( 'url' ),
			),
			'twitter_link' => array(
				array( 'url' ),
			),
			'facebook_link' => array(
				array( 'url' ),
			),
			'youtube_link' => array(
				array( 'url' ),
			),
			'odnoklassniki_link' => array(
				array( 'url' ),
			),
			'google_link' => array(
				array( 'url' ),
			),
			'instagram_link' => array(
				array( 'url' ),
			),
			
			'vk_api_id' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'vk_group_id' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'fb_app_id' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'fb_group_link' => array(
				array( 'url' ),
				array( 'max_length', array( ':value', 255 ) ),
			),
			
			'title_tag' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'keywords_tag' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
			'description_tag' => array(
				array( 'max_length', array( ':value', 255 ) ),
			),
		);
	}

	public function filters()
	{
		return array(
			TRUE => array(
				array( 'UTF8::trim' ),
			),
			'title_tag' => array(
				array( 'strip_tags' ),
			),
			'keywords_tag' => array(
				array( 'strip_tags' ),
			),
			'description_tag' => array(
				array( 'strip_tags' ),
			),
			'active' => array(
				array(array($this, 'checkbox'))
			),
		);
	}

}