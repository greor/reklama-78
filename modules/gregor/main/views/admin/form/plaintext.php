<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<div class="control-group">
	<label class="control-label" for="<?php echo $name; ?>_field">
		<?php echo __($labels[ $name ]); ?> :
	</label>
	<div class="controls">
		<span id="<?php echo $name; ?>" class="plaintext">
			<?php echo $text; ?>
		</span>
	</div>
</div>
