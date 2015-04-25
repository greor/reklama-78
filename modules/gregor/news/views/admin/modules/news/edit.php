<?php defined('SYSPATH') or die('No direct access allowed.');

	$element = $wrapper->orm();
	$labels = $element->labels();
	$required = $element->required_fields();
	$category_id = (int) Request::current()->query('cid');

	$query_array = array(
		'pid' => $MODULE_PAGE_ID,
		'cid' => $category_id,
	);
	$p = Request::current()->query( Paginator::QUERY_PARAM );
	if ( ! empty($p)) {
		$query_array[ Paginator::QUERY_PARAM ] = $p;
	}
	$query = Helper_Page::make_query_string($query_array);

	if ($element->loaded()) {
		$action = Route::url('modules', array(
			'controller' => 'news',
			'action'     => 'edit',
			'id'         => $element->id,
			'query'      => $query,
		));
	} else {
		$action = Route::url('modules', array(
			'controller' => 'news',
			'action'     => 'edit',
			'query'      => $query,
		));
	}

	echo View_Admin::factory('layout/error')
		->set('errors', $errors);

?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal" >
<?php

/**** active ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'active',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::hidden('active', '').Form::checkbox('active', '1', (bool) $element->active, array(
				'id' => 'active_field',
			)),
		));

/**** category_id ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'category_id',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::select('category_id', array( 0 => __('No category') ) + $categories, (int) $element->category_id, array(
				'id'      => 'category_id_field',
				'class'   => 'input-xlarge',
			)),
		));

/**** public_date ****/

		if ( $element->loaded() AND ! empty($element->public_date) ) {
			$public_date_ts = strtotime($element->public_date);
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

/**** title ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'     => 'title',
			'errors'    => $errors,
			'labels'    => $labels,
			'required'  => $required,
			'controls'  => Form::input('title', $element->title, array(
				'id'       => 'title_field',
				'class'    => 'input-xlarge',
			)),
		));

/**** image ****/

		echo View_Admin::factory('form/image_wrapper', array(
			'field'      => 'image',
			'orm_helper' => $wrapper,
			'errors'     => $errors,
			'labels'     => $labels,
			'required'   => $required,
		));
		

/**** announcement ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'announcement',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::textarea('announcement', $element->announcement, array(
				'id'      => 'announcement_field',
				'class'   => 'text_editor_native',
			)),
		));

/**** text ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'text',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::textarea('text', $element->text, array(
				'id'      => 'text_field',
				'class'   => 'text_editor',
			)),
		));

/**** additional params block ****/

		echo View_Admin::factory('form/seo', array(
			'item'     => $element,
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
		));
?>
		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
			<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
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
</form>
