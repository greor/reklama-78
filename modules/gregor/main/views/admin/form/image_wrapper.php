<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<?php $item = $orm_helper->orm(); ?>

<div class="control-group <?php if (isset($errors[ $field ])) echo 'error' ?>">
	<label class="control-label" for="<?php echo $field; ?>_field">
		<?php
			echo __($labels[ $field ]),
				in_array($field, $required) ? '<span class="required">*</span>' : '';
		?> :
	</label>
	<div class="controls <?php echo empty($controls_class) ? '' : $controls_class; ?>">

		<?php if ( empty($image_only) OR $image_only !== TRUE): ?>

			<input type="file" id="<?php echo $field; ?>_field" name="<?php echo $field; ?>" accept="image/*" />
			<?php if (isset($errors[ $field ])): ?>
				<p class="help-block"><?php echo HTML::chars($errors[ $field ]); ?></p>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( ! empty($item->$field) AND ! empty($item->id)):?>
			<div class="js-photo-gallery-holder">
			<?php
				$img_size = getimagesize(DOCROOT.$orm_helper->file_path('image', $item->image));
				
				if ($img_size[0] > 100 OR $img_size[1] > 100) {
					$thumb = Thumb::uri('admin_image_100', $orm_helper->file_uri('image', $item->image));
				} else {
					$thumb = $orm_helper->file_uri('image', $item->image);
				}
				
				if ($img_size[0] > 300 OR $img_size[1] > 300) {
					$flyout = Thumb::uri('admin_image_300', $orm_helper->file_uri('image', $item->image));
				} else {
					$flyout = $orm_helper->file_uri('image', $item->image);
				}
				
				echo HTML::anchor($flyout, HTML::image($thumb), array(
					'class' => 'js-photo-gallery'
				));
			?>
			</div>

			<?php if ( empty($image_only) OR $image_only !== TRUE): ?>
				<label class="checkbox" for="<?php echo $field; ?>_field_delete">
					<?php
						echo Form::checkbox('delete_fields['.$field.']', '1', FALSE, array(
										'id' => $field.'_field_delete',
									)),
							__('Delete image');
					?>
				</label>
			<?php endif;?>

		<?php endif;?>
	</div>
</div>