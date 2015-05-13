<?php defined('SYSPATH') or die('No direct access allowed.');

	$query_array = array(
		'pid' => $MODULE_PAGE_ID
	);
	$p = Request::current()->query( Paginator::QUERY_PARAM );
	if ( ! empty($p)) {
		$query_array[ Paginator::QUERY_PARAM ] = $p;
	}
	$category = $wrapper->orm();
	$labels = $category->labels();
	$required = $category->required_fields();

	if ($category->loaded()) {
		$action = Route::url('modules', array(
			'controller' => 'photo',
			'action'     => 'edit_category',
			'id'         => $category->id,
			'query'      => Helper_Page::make_query_string($query_array),
		));
	} else {
		$action = Route::url('modules', array(
			'controller' => 'photo',
			'action'     => 'edit_category',
			'query'      => Helper_Page::make_query_string($query_array),
		));
	}

	echo View_Admin::factory('layout/error')
		->set('errors', $errors);
?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal" >
<?php

/**** status ****/

		$_status = Kohana::$config
			->load('_photo.status');
		if ( in_array($category->code, $not_deleted_categories) ) {
			unset($_status[0]); // Inactive
		}
		echo View_Admin::factory('form/wrapper', array(
			'field'		=>	'status',
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
			'controls'	=>	Form::select('status',  $_status, (int) $category->status, array(
				'id'       => 'status_field',
				'class'    => 'input-xlarge',
			)),
		));

/**** group ****/
		
// 		echo View_Admin::factory('form/wrapper', array(
// 			'field'    => 'group',
// 			'errors'   => $errors,
// 			'labels'   => $labels,
// 			'required' => $required,
// 			'controls' => Form::select('group', Kohana::$config->load('_photo.groups'), $category->group, array(
// 				'id'      => 'group_field',
// 				'class'   => 'input-xlarge',
// 			)),
// 		));
		
/**** title ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'		=>	'title',
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
			'controls'	=>	Form::input('title', $category->title, array(
				'id'       => 'title_field',
				'class'    => 'input-xlarge',
			)),
		));

/**** uri ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'		=>	'uri',
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
			'controls'	=>	Form::input('uri', $category->uri, array(
				'id'       => 'uri_field',
				'class'    => 'input-xlarge',
			)),
		));
		
/**** public_date ****/
		
		if ( $category->loaded() AND ! empty($category->public_date) ) {
			$public_date_ts = strtotime($category->public_date);
			$public_date_date = date('Y-m-d', $public_date_ts);
			$public_date_time = date('H:i', $public_date_ts);
		} else {
			$public_date_date = date('Y-m-d');
			$public_date_time = date('H:i');
		}
		echo View_Admin::factory('form/wrapper', array(
			'field'     => 'public_date',
			'errors'    => $errors,
			'labels'    => $labels,
			'required'  => $required,
			'controls'  => Form::input('multiple_date', $public_date_date, array(
				'id'       => 'public_date_field',
				'class'    => 'multiple_date',
			)).Form::input('multiple_time', $public_date_time, array(
				'class'    => 'multiple_time',
			)),
		));
		
/**** image ****/
		
		echo View_Admin::factory('form/image_wrapper', array(
			'field'      => 'image',
			'orm_helper' => $wrapper,
			'errors'     => $errors,
			'labels'     => $labels,
			'required'   => $required,
			'help_text'  => '360x240px',
		));
		
/**** additional params block ****/

		echo View_Admin::factory('form/seo', array(
			'item'		=>	$category,
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
		));

?>
		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
			<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
	</form>
	<script>
		$(document).ready(function(){
			$('.multiple_time').timepicker({});
			$('.multiple_date').datepicker({
				dateFormat: 'yy-mm-dd'
			});
			$('.duration_time').timepicker({
				timeFormat: 'hh:mm:ss',
				showSecond: true
			});
		});
	</script>
