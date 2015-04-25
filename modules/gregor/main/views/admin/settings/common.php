<?php defined('SYSPATH') or die('No direct access allowed.');?>

<?php echo View_Admin::factory('layout/error')->bind('errors', $errors)->render(); ?>

<form method="post" action="<?php echo $action; ?>" class="form-horizontal kr-form-horizontal">
<?php foreach ($settings as $name => $item):?>
		<?php $id = $name.'_field'; ?>
	<div class="control-group<?php if (isset($errors[$name])) echo ' error' ?>">
		<label class="control-label" for="<?php echo $id; ?>"><?php echo HTML::chars($item['title']); ?>:</label>
		<div class="controls">
			<?php
			switch ($item['type'])
			{
				case 'text':
					echo Form::input($name, $item['value'], array(
						'id'    => $id,
						'class' => 'input-xlarge',
					));
					break;
				case 'checkbox':
					echo Form::hidden($name, '');
					echo Form::checkbox($name, '1', (bool) $item['value'], array(
						'id' => $id,
					));
				break;
			}
			?>
			<?php if (isset($errors[$name])) echo '<p class="help-block">'.HTML::chars($errors[$name]).'</p>'; ?>
		</div>
	</div>
<?php endforeach;?>
	<div class="form-actions">
		<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
		<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
		<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
	</div>
</form>