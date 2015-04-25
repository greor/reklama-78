<?php defined('SYSPATH') or die('No direct access allowed.');

	$page = $page_wrapper->orm();
	$labels = $page->labels();
	$required = $page->required_fields();

	if ( $page->loaded() ) {
		$action = Route::url('admin', array(
			'controller' => 'structure',
			'action'     => 'edit',
			'id'         => $page->id,
		));
	} else {
		$action = Route::url('admin', array(
			'controller' => 'structure',
			'action'     => 'edit',
		));
	}

	echo View_Admin::factory('layout/error')->bind('errors', $errors);
?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal" >
<?php

/**** parent_id ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'parent_id',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::select('parent_id', array(0 => __('- Root page -')) + $pages, (int) $page->parent_id, array(
				'id'     => 'parent_id_field',
				'class'  => 'input-xlarge',
			)),
		));			

/**** uri ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'uri',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('uri', $page->uri, array(
				'id'     => 'uri_field',
				'class'  => 'input-xlarge',
			)).'<label>URL: <div id="uri_preview" base="/base_uri/"></div></label>',
		));

/**** status ****/

		if ($ACL->is_allowed($USER, $page, 'status_change')) {
			echo View_Admin::factory('form/wrapper', array(
				'field'    => 'status',
				'errors'   => $errors,
				'labels'   => $labels,
				'required' => $required,
				'controls' => Form::select('status',  Kohana::$config->load('_pages.status'), (int) $page->status, array(
					'id'     => 'status_field',
					'class'  => 'input-xlarge',
				)),
			));				
		}

/**** page_type ****/

		if ($ACL->is_allowed($USER, $page, 'page_type_change')) {
			echo View_Admin::factory('form/page_type', array(
				'type_field' => 'type',
				'data_field' => 'data',
				'page'       => $page,
				'errors'     => $errors,
				'labels'     => $labels,
				'required'   => $required,
				'modules'    => $modules,
				'pages_list' => $pages,
			));
		}

/**** title ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'title',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('title', $page->title, array(
				'id'     => 'title_field',
				'class'  => 'input-xlarge',
			)),
		));
		
		
/**** image ****/

// 		echo View_Admin::factory('form/image_wrapper', array(
// 			'field'      => 'image',
// 			'orm_helper' => $page_wrapper,
// 			'errors'     => $errors,
// 			'labels'     => $labels,
// 			'required'   => $required,
// 		));


/**** text ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'text',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::textarea('text', $page->text, array(
				'id'     => 'text_field',
				'class'  => 'text_editor',
			)),
		));

/**** additional params block ****/

		echo View_Admin::factory('form/seo', array(
			'item'		=>	$page,
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
		));


/**** sitemap params block ****/

		echo View_Admin::factory('form/sitemap', array(
			'item'		=>	$page,
			'errors'	=>	$errors,
			'labels'	=>	$labels,
			'required'	=>	$required,
		));
?>
		<script>
			$(document).ready(function(){
				var base_uri_list = eval(<?php echo json_encode($base_uri_list); ?>);
				
				$('#uri_field')
					.change(function(){
						var _prev_container = $('#uri_preview');
						_prev_container.html( _prev_container.attr('base')+$(this).val() );
					})
					.keyup(function(){
						$(this).triggerHandler('change');
					})
					.triggerHandler('change');
	
				$('#parent_id_field')
					.change(function(){
						var _id = $(':selected', $(this)).val(),
							_base = (_id > 0) ? '/'+base_uri_list[_id]+'/' : '/';
						$('#uri_preview').attr('base', _base);
						$('#uri_field').triggerHandler('change');
					})
					.triggerHandler('change');
			});
		</script>
	
		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
			<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
	</form>
