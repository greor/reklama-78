<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<?php
	$error_text = array();
	$is_error = FALSE;
	$is_require = FALSE;

	if ( ! is_array($field) )
	{
		$field_arr = array( $field );
	}
	else
	{
		$field_arr = $field;
		$field = reset( $field_arr );
	}

	foreach ( $field_arr as $item )
	{
		if ( isset( $errors[ $item ] ) )
		{
			$is_error = TRUE;
			$error_text[] = HTML::chars( $errors[ $item ] );
		}

		if ( in_array($item, $required) )
		{
			$is_require = TRUE;
		}

	}
?>

<div class="control-group <?php if ( $is_error ) echo 'error' ?>">
	<label class="control-label" for="<?php echo $field; ?>_field">
		<?php
			echo __($labels[ $field ]),
				$is_require ? '<span class="required">*</span>' : '';
		?> :
	</label>
	<div class="controls <?php echo empty($controls_class) ? '' : $controls_class; ?>">

		<?php echo $controls; ?>

		<?php if ( $is_error ): ?>
			<p class="help-block"><?php echo implode('<br>', $error_text); ?></p>
		<?php endif; ?>
	</div>
</div>
