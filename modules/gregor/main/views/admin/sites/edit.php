<?php defined('SYSPATH') or die('No direct access allowed.');

	$orm = $wrapper->orm();

	$labels = array(
		'noname_type' => 'Type',
		'noname_name' => 'Site name',
		'noname_url'  => 'URL',
		'noname_mmt'  => 'MMT',
		'noname_code' => 'Code',
	);
	$labels = $labels + $orm->labels();
	$required = $orm->required_fields();

	if ( $orm->loaded() ) {
		$action = Route::url('admin', array(
			'controller' => 'sites',
			'action'     => 'edit',
			'id'         => $orm->id,
		));
	} else {
		$action = Route::url('admin', array(
			'controller' => 'sites',
			'action'     => 'edit',
		));
	}

	echo View_Admin::factory('layout/error')
		->set('errors', $errors);
?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal">
<?php

/**** active ****/

		if ($ACL->is_allowed($USER, $orm, 'active_change')) {
			echo View_Admin::factory('form/wrapper', array(
				'field'    => 'active',
				'errors'   => $errors,
				'labels'   => $labels,
				'required' => $required,
				'controls' => Form::hidden('active', '').Form::checkbox('active', '1', (bool) $orm->active, array(
					'id' => 'active_field',
				)),
			));
		}

/**** name ****/

		if ( ! $ACL->is_allowed($USER, $orm, 'edit_name') ) {
			echo View_Admin::factory('form/wrapper', array(
				'field'    => 'noname_name',
				'errors'   => $errors,
				'labels'   => $labels,
				'required' => $required,
				'controls' => Form::input('noname_name', $orm->name, array(
					'id'       => 'noname_name_field',
					'class'    => 'input-xlarge',
					'readonly' => 'readonly',
				)),
			));
		} else {
			echo View_Admin::factory('form/wrapper', array(
				'field'    => 'name',
				'errors'   => $errors,
				'labels'   => $labels,
				'required' => $required,
				'controls' => Form::input('name', $orm->name, array(
					'id'    => 'name_field',
					'class' => 'input-xlarge',
				)),
			));
		}

/**** image ****/
		
		echo View_Admin::factory('form/image_wrapper', array(
			'field'      => 'logo',
			'orm_helper' => $wrapper,
			'errors'     => $errors,
			'labels'     => $labels,
			'required'   => $required,
		));
		
/**** url ****/

		if ( ! $ACL->is_allowed($USER, $orm, 'edit_url') ) {
			echo View_Admin::factory('form/wrapper', array(
				'field'    => 'noname_url',
				'errors'   => $errors,
				'labels'   => $labels,
				'required' => $required,
				'controls' => Form::input('noname_url', $orm->url, array(
					'id'       => 'noname_url_field',
					'class'    => 'input-xlarge',
					'readonly' => 'readonly',
				)),
			));
		} else {
			echo View_Admin::factory('form/wrapper', array(
				'field'    => 'url',
				'errors'   => $errors,
				'labels'   => $labels,
				'required' => $required,
				'controls' => Form::input('url', $orm->url, array(
					'id'    => 'url_field',
					'class' => 'input-xlarge',
				)),
			));
		}

/**** vk_api_id ****/

// 		if ( $ACL->is_allowed($USER, $orm, 'edit_vk_api_id') ) {
// 			echo View_Admin::factory('form/wrapper', array(
// 				'field'    => 'vk_api_id',
// 				'errors'   => $errors,
// 				'labels'   => $labels,
// 				'required' => $required,
// 				'controls' => Form::input('vk_api_id', $orm->vk_api_id, array(
// 					'id'    => 'vk_api_id_field',
// 					'class' => 'input-xlarge',
// 				)),
// 			));
// 		}

/**** vk_group_id ****/

// 		if ( $ACL->is_allowed($USER, $orm, 'edit_vk_group_id') ) {
// 			echo View_Admin::factory('form/wrapper', array(
// 				'field'    => 'vk_group_id',
// 				'errors'   => $errors,
// 				'labels'   => $labels,
// 				'required' => $required,
// 				'controls' => Form::input('vk_group_id', $orm->vk_group_id, array(
// 					'id'    => 'vk_group_id_field',
// 					'class' => 'input-xlarge',
// 				)),
// 			));
// 		}

/**** fb_app_id ****/

// 		if ( $ACL->is_allowed($USER, $orm, 'edit_fb_app_id') ) {
// 			echo View_Admin::factory('form/wrapper', array(
// 				'field'    => 'fb_app_id',
// 				'errors'   => $errors,
// 				'labels'   => $labels,
// 				'required' => $required,
// 				'controls' => Form::input('fb_app_id', $orm->fb_app_id, array(
// 					'id'    => 'fb_app_id_field',
// 					'class' => 'input-xlarge',
// 				)),
// 			));
// 		}

/**** fb_group_link ****/

// 		if ( $ACL->is_allowed($USER, $orm, 'edit_fb_group_link') ) {
// 			echo View_Admin::factory('form/wrapper', array(
// 				'field'    => 'fb_group_link',
// 				'errors'   => $errors,
// 				'labels'   => $labels,
// 				'required' => $required,
// 				'controls' => Form::input('fb_group_link', $orm->fb_group_link, array(
// 					'id'    => 'fb_group_link_field',
// 					'class' => 'input-xlarge',
// 				)),
// 			));
// 		}

/**** tw_widget ****/

// 		if ( $ACL->is_allowed($USER, $orm, 'edit_tw_widget') ) {
// 			echo View_Admin::factory('form/wrapper', array(
// 				'field'    => 'tw_widget',
// 				'errors'   => $errors,
// 				'labels'   => $labels,
// 				'required' => $required,
// 				'controls' => Form::input('tw_widget', $orm->tw_widget, array(
// 					'id'    => 'tw_widget_field',
// 					'class' => 'input-xlarge',
// 				)),
// 			));
// 		}

/**** vkontakte_link ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'vkontakte_link',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('vkontakte_link', $orm->vkontakte_link, array(
				'id'    => 'vkontakte_link_field',
				'class' => 'input-xlarge',
			)),
		));

/**** twitter_link ****/
	
		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'twitter_link',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('twitter_link', $orm->twitter_link, array(
				'id'    => 'twitter_link_field',
				'class' => 'input-xlarge',
			)),
		));
	
/**** facebook_link ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'facebook_link',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('facebook_link', $orm->facebook_link, array(
				'id'    => 'facebook_link_field',
				'class' => 'input-xlarge',
			)),
		));
	
/**** google_link ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'google_link',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('google_link', $orm->google_link, array(
				'id'    => 'google_link_field',
				'class' => 'input-xlarge',
			)),
		));
	
/**** youtube_link ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'youtube_link',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('youtube_link', $orm->youtube_link, array(
				'id'    => 'youtube_link_field',
				'class' => 'input-xlarge',
			)),
		));
	
/**** instagram_link ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'instagram_link',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('instagram_link', $orm->instagram_link, array(
				'id'    => 'instagram_link_field',
				'class' => 'input-xlarge',
			)),
		));
	
/**** odnoklassniki_link ****/

// 		echo View_Admin::factory('form/wrapper', array(
// 			'field'    => 'odnoklassniki_link',
// 			'errors'   => $errors,
// 			'labels'   => $labels,
// 			'required' => $required,
// 			'controls' => Form::input('odnoklassniki_link', $orm->odnoklassniki_link, array(
// 				'id'    => 'odnoklassniki_link_field',
// 				'class' => 'input-xlarge',
// 			)),
// 		));

/**** additional params block ****/

		echo View_Admin::factory('form/seo', array(
			'item'		=>	$orm,
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
		));
?>
		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
<?php 
			if ( $USER->role == 'super' ): 
?>
				<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
<?php 
			endif; 
?>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
	</form>