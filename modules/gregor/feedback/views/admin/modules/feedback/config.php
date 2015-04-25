<?php defined('SYSPATH') or die('No direct access allowed.');?>

	<div class="row">
		<div class="span9 kr-page-selector">
			<form class="form-inline">
				<label for="page_select"><?php echo __('Page'); ?>:</label>
<?php 
				echo Form::select('page_id', $MODULE_PAGES, $MODULE_PAGE_ID, array(
					'id' => 'page_select'
				)); 
?>
			</form>
			<script>
				$(document).ready(function(){
					$('#page_select').change(function(){
						var _page = $('option:selected', '#page_select').val();
						window.location = window.location.pathname + '?pid=' + _page;
					});
				});
			</script>
		</div>
	</div>
<?php

	$labels = $config->labels();
	$required = $config->required_fields();

	$query_array = array(
		'pid' => $MODULE_PAGE_ID
	);
	$query = empty($query_array) ? NULL : http_build_query($query_array);
	$action = Route::url('modules', array(
		'controller' => 'feedback',
		'action'     => 'config',
		'query'      => Helper_Page::make_query_string($query_array),
	));

	echo View_Admin::factory('layout/error')
		->set('errors', $errors)->render();
?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal" >
<?php

/**** email ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'email',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('email', $config->email, array(
				'id'      => 'email_field',
				'class'   => 'input-xlarge',
			)),
		));

/**** send_email ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'send_email',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::hidden('send_email', '').Form::checkbox('send_email', '1', (bool) $config->send_email, array(
				'id' => 'send_email_field',
			)),
		));

?>
		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
			<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
	</form>
