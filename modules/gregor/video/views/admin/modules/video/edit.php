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
			'controller' => 'video',
			'action'     => 'edit',
			'id'         => $element->id,
			'query'      => $query,
		));
	} else {
		$action = Route::url('modules', array(
			'controller' => 'video',
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


/**** text ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'text',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::textarea('text', $element->text, array(
				'id'      => 'text_field',
				'class'   => 'text_editor_native',
			)),
		));

?>
		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
			<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
</form>
