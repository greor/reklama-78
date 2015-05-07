<?php defined('SYSPATH') or die('No direct access allowed.'); 

	$item = $orm_helper->orm();
	 
?>

	<div class="control-group <?php if (isset($errors[ $field ])) echo 'error' ?>">
		<label class="control-label" for="<?php echo $field; ?>_field">
<?php
			echo __($labels[ $field ]),
				(in_array($field, $required) ? '<span class="required">*</span>' : ''),
				'&nbsp;:&nbsp;';
?>
		</label>
		<div class="controls <?php echo empty($controls_class) ? '' : $controls_class; ?>">
<?php 
			if ( empty($image_only) OR $image_only !== TRUE) {
				echo Form::file($field, array(
					'id' => $field.'?>_field',
					'accept' => 'image/*'
				));
				if ( ! empty($help_text)) {
					echo '<p class="help-block help-text"><small><strong>',
						HTML::chars($help_text),
						'</strong></small></p>';
				}
				
				if (isset($errors[ $field ])) {
					echo '<p class="help-block">', HTML::chars($errors[ $field ]), '</p>';
				}
			}
			
			if ( ! empty($item->$field)) {
				
				$img_size = getimagesize(DOCROOT.$orm_helper->file_path($field, $item->$field));
				
				echo '<div class="js-photo-gallery-holder">';
				
				if ($img_size[0] > 100 OR $img_size[1] > 100) {
					$thumb = Thumb::uri('admin_image_100', $orm_helper->file_uri($field, $item->$field));
				} else {
					$thumb = $orm_helper->file_uri($field, $item->$field);
				}
				
				if ($img_size[0] > 300 OR $img_size[1] > 300) {
					$flyout = Thumb::uri('admin_image_300', $orm_helper->file_uri($field, $item->$field));
				} else {
					$flyout = $orm_helper->file_uri($field, $item->$field);
				}
				
				echo HTML::anchor($flyout, HTML::image($thumb, array(
					'title' => $img_size[0].'x'.$img_size[1]
				)), array(
					'class' => 'js-photo-gallery'
				));
				
				echo '</div>';
				
				if ( empty($image_only) OR $image_only !== TRUE) {
					echo '<label class="checkbox" for="', $field, '_field_delete">';
					echo Form::checkbox('delete_fields['.$field.']', '1', FALSE, array(
						'id' => $field.'_field_delete',
					)), __('Delete image');
					echo '</label>';
				}
			}
			
			
?>
		</div>
	</div>