<?php defined('SYSPATH') or die('No direct access allowed.');

	$labels = $orm->labels();
	$query_array = array(
		'pid' => $MODULE_PAGE_ID
	);
	$p = Request::current()->query( Paginator::QUERY_PARAM );
	if ( ! empty($p)) {
		$query_array[ Paginator::QUERY_PARAM ] = $p;
	}

	$action = Route::url('modules', array(
		'controller' => 'feedback',
		'query'      => Helper_Page::make_query_string($query_array),
	));
?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal" >
<?php

/**** text ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'text',
			'errors'   => array(),
			'labels'   => $labels,
			'required' => array(),
			'controls' => Form::textarea('text', $orm->text, array(
				'id'      => 'text_field',
				'class'   => 'text_editor_native',
			)),
		));

?>
		<div class="form-actions">
			<a href="<?php echo $action; ?>" class="btn" name="back" value="back"><?php echo __('Back'); ?></a>
		</div>
	</form>
