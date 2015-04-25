<?php defined('SYSPATH') or die('No direct access allowed.');?>

<fieldset class="fieldset-bordered">
	<legend>Sitemap</legend>
<?php 
/**** sm_changefreq ****/

	$_set = Arr::path($item->table_columns(), 'sm_changefreq.options', array());
	$_options = @array_combine($_set, $_set);
	$_options = (empty($_options)) ? array() : $_options;
	
	echo View_Admin::factory('form/wrapper')
		->set('field', 		'sm_changefreq')
		->set('errors', 	$errors)
		->set('labels', 	$labels)
		->set('required', 	$required)
		->set('controls', 	Form::select('sm_changefreq',  array('' => '') + $_options, $item->sm_changefreq, array(
			'id'    => 'sm_changefreq_field',
			'class' => 'input-medium',
		)));

/**** sm_priority ****/

	echo View_Admin::factory('form/wrapper')
		->set('field', 		'sm_priority')
		->set('errors', 	$errors)
		->set('labels', 	$labels)
		->set('required', 	$required)
		->set('controls', 	Form::input('sm_priority', $item->sm_priority, array(
			'id'    => 'sm_priority_field',
			'class' => 'input-medium',
		)));

/**** sm_separate_file ****/

	echo View_Admin::factory('form/wrapper', array(
		'field'		=>	'sm_separate_file',
		'errors'	=>	$errors,
		'labels'	=>	$labels,
		'required'	=>	$required,
		'controls'	=>	Form::hidden('sm_separate_file', '').
						Form::checkbox('sm_separate_file', '1', (bool) $item->sm_separate_file, array(
							'id' => 'sm_separate_file_field',
						)),
	));
?>
</fieldset>