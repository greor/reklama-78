<?php defined('SYSPATH') or die('No direct access allowed.'); 
	array_push($values, FALSE);
?>
<div class="control-group  <?php if (isset($errors[ $field ])) echo 'error' ?>">
	<label class="control-label" for="<?php echo $field; ?>_field">
<?php
			echo __($labels[ $field ]),
				in_array($field, $required) ? '<span class="required">*</span>' : '',
				'&nbsp;:';
?>
	</label>
<?php 
	foreach ($values as $id => $orm):
?>
		<div class="controls kr-dyn-list kr-dyn-list-images">
			<div class="btn-group kr-dyn-btn-group">
<?php
				if ($orm === FALSE):
?>			
					<button class="btn btn-primary kr-dyn-creator" type="button" title="<?php echo __('Add item'); ?>"><i class="icon-plus"></i></button>
<?php
				endif; 
?>
				<button class="btn btn-danger kr-dyn-deleter" type="button" title="<?php echo __('Remove item'); ?>"><i class="icon-remove"></i></button>
			</div>
<?php
			if ($orm === FALSE) {
				echo Form::file("{$field}[]", array(
					'id'     => "{$field}_field",
					'accept' => 'image/*'
				));
			} elseif ( ! empty($orm->$remote_field)) {
				$original = $orm_helper->file_uri($remote_field, $orm->$remote_field);
				$thumb = Thumb::uri('admin_image_300', $original);
				echo HTML::anchor($original, HTML::image($thumb), array(
					'class' => 'js-photo-gallery',
					'target' => '_blank',
					'title' => '',
				));
				echo Form::hidden("{$field}[]", $orm->id);
			}
?>
		</div>
<?php 
	endforeach;
	
	if (isset($errors[ $field ])): 
?>
		<p class="help-block"><?php echo HTML::chars($errors[ $field ]); ?></p>
<?php 
	endif; 
?>
</div>
