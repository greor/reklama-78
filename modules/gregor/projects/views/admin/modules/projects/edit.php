<?php defined('SYSPATH') or die('No direct access allowed.');
	
	$query_array = array();
	$p = Request::current()->query( Paginator::QUERY_PARAM );
	if ( ! empty($p)) {
		$query_array[ Paginator::QUERY_PARAM ] = $p;
	}

	$element = $wrapper->orm();
	$labels = $element->labels();
	$required = $element->required_fields();

	if ( $element->loaded() ) {
		$action = Route::url('modules', array(
			'controller' => 'projects',
			'action'     => 'edit',
			'id'         => $element->id,
			'query'      => Helper_Page::make_query_string($query_array)
		));
	} else {
		$action = Route::url('modules', array(
			'controller' => 'projects',
			'action'     => 'edit',
			'query'      => Helper_Page::make_query_string($query_array)
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

/**** title ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'title',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('title', $element->title, array(
				'id'      => 'title_field',
				'class'   => 'input-xlarge',
			)),
		));

/**** image ****/

		echo View_Admin::factory('form/image_wrapper', array(
			'field'      => 'image',
			'orm_helper' => $wrapper,
			'errors'     => $errors,
			'labels'     => $labels,
			'required'   => $required,
			'help_text'  => '195x195px',
		));

?>

	<div class="form-actions">
		<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
		<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
		<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
	</div>

</form>
