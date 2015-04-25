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
			<?php
				$orm_helper->orm( $item );
				$original = $orm_helper->file_uri( $field );
				$thumb = Thumb::uri('admin_image_300', $original);
				$original = URL::base().$original;
			?>
			<a class="js-photo-gallery" href="<?php echo $original; ?>" title="" target="_blank">
				<?php echo HTML::image($thumb); ?>
			</a>

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