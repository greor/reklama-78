<?php defined('SYSPATH') or die('No direct access allowed.'); 

if (empty($options))
{
	$options = array();
}
if (empty($caption))
{
	$caption = 'Select item...';
}

foreach ($options as $key => $value)
{
	$options[$key] = '[#'.$key.'] '.$value;
}

$options = array('' => __($caption)) + $options;

if (empty($selected))
{
	$selected[] = '';
}
?>
<div class="control-group  <?php if (isset($errors[ $field ])) echo 'error' ?>">
	<label class="control-label" for="<?php echo $field; ?>_field">
		<?php
			echo __($labels[ $field ]),
				in_array($field, $required) ? '<span class="required">*</span>' : '';
		?> :
	</label>

	<?php foreach ($selected as $id => $val):?>
		<div class="controls kr-dyn-list">
			<?php
				if (empty($attrs))
				{
					$attrs = array();
				}
				if (empty($attrs['class']))
				{
					$attrs['class'] = '';
				}
				$attrs['class'] .= 'input-xlarge kr-dyn-select';
				$attrs['class'] .= trim($attrs['class']);

				echo Form::select($name.'[]', $options, $id, $attrs);
			?>
			<div class="btn-group kr-dyn-btn-group">
				<button class="btn btn-primary kr-dyn-creator" type="button" title="<?php echo __('Add item'); ?>"><i class="icon-plus"></i></button>
				<button class="btn btn-danger kr-dyn-deleter" type="button" title="<?php echo __('Remove item'); ?>"><i class="icon-remove"></i></button>
			</div>
		</div>
	<?php endforeach;?>

	<?php if (isset($errors[ $field ])): ?>
		<p class="help-block"><?php echo HTML::chars($errors[ $field ]); ?></p>
	<?php endif; ?>
</div>
