<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<?php
	if ( empty($group_class) )
	{
		$group_class = '';
	}
?>

<div class="control-group <?php echo $group_class; ?> <?php if (isset($errors[ $field ])) echo 'error' ?>">
	<label class="control-label" for="<?php echo $field; ?>_field">
		<?php
			echo __($labels[ $field ]),
				in_array($field, $required) ? '<span class="required">*</span>' : '';
		?> :
	</label>
	<div class="controls <?php echo empty($controls_class) ? '' : $controls_class; ?>">

		<?php echo $controls; ?>

		<?php if (isset($errors[ $field ])): ?>
			<p class="help-block"><?php echo HTML::chars($errors[ $field ]); ?></p>
		<?php endif; ?>
	</div>
</div>
