<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<?php
$hide_toggle = FALSE;
if	(	! empty($item->title_tag) OR
		! empty($item->keywords_tag) OR
		! empty($item->description_tag)
	)
{
	$hide_toggle = TRUE;
}
echo View_Admin::factory('form/wrapper', array(
		'field'		=>	'meta_tags',
		'errors'	=>	array(),
		'labels'	=>	array( 'meta_tags' => __('Additional params') ),
		'required'	=>	array(),
		'controls'	=>	Form::hidden('meta_tags', '0').
						Form::checkbox('meta_tags', '1', $hide_toggle, array(
							'class' => 'toggle_switcher',
						)),
	))
	->render();

echo View_Admin::factory('form/wrapper', array(
		'field'			=>	'title_tag',
		'group_class'	=>	'hide_toggle_invert meta_tags',
		'errors'		=>	$errors,
		'labels'		=>	$labels,
		'required'		=>	$required,
		'controls'		=>	Form::input('title_tag', $item->title_tag, array(
								'id'    => 'title_tag_field',
								'class' => 'input-xlarge',
							)),
	))
	->render();

echo View_Admin::factory('form/wrapper', array(
		'field'			=>	'keywords_tag',
		'group_class'	=>	'hide_toggle_invert meta_tags',
		'errors'		=>	$errors,
		'labels'		=>	$labels,
		'required'		=>	$required,
		'controls'		=>	Form::input('keywords_tag', $item->keywords_tag, array(
								'id'    => 'keywords_tag_field',
								'class' => 'input-xlarge',
							)),
	))
	->render();

echo View_Admin::factory('form/wrapper', array(
		'field'			=>	'description_tag',
		'group_class'	=>	'hide_toggle_invert meta_tags',
		'errors'		=>	$errors,
		'labels'		=>	$labels,
		'required'		=>	$required,
		'controls'		=>	Form::textarea('description_tag', $item->description_tag, array(
								'id'    => 'description_tag_field',
								'class' => 'text_editor_native',
							)),
	))
	->render();
?>