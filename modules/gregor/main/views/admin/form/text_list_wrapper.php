<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<div class="control-group <?php if (isset($errors[ $list_field ])) echo 'error' ?>">
	<label class="control-label" for="<?php echo $list_field; ?>_field">
		<?php
			echo __($labels[ $text_field ]),
				in_array($list_field, $required) ? '<span class="required">*</span>' : '';
		?>&nbsp;:
	</label>


	<div class="controls">
		<?php echo Form::input( $text_field, $item->$text_field); ?>
		<br>

		<?php
			echo Form::hidden( $list_field.'_switcher', '');
			echo Form::checkbox($list_field.'_switcher', '1', (bool) $item->$list_field, array(
				'id' => $list_field.'_switcher_field',
			));
		?>

		<label for="<?php echo $list_field; ?>_switcher_field" class="kr-inline list_hide">
			<?php echo __('from list'); ?>
		</label>

		<?php
			echo Form::select( $list_field, $list, (int) $item->$list_field, array(
				'id'    => $list_field.'_field',
				'class' => 'input-xlarge list_hide',
			));
		?>
		<?php if (isset($errors[ $list_field ])): ?>
			<p class="help-block"><?php echo HTML::chars($errors[ $list_field ]); ?></p>
		<?php endif; ?>
	</div>

	<script>
		$(document).ready(function(){
			$('#<?php echo $list_field; ?>_switcher_field').click(function(){
				if ($(this).is(':checked'))
				{
					$('.list_hide').show();
				}
				else
				{
					$('.list_hide').hide();
				}

			}).triggerHandler('click');

		});
	</script>
</div>