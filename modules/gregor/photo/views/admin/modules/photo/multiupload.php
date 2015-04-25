<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div class="row">
	<div class="span4">
		<div class="form-inline">
			<label for="album_select"><?php echo __('Album'); ?>:</label>
<?php
				$url = Route::url('modules', array(
					'controller' => 'photo',
					'action'     => 'upload'
				));

				echo Form::select('category_id', $categories, FALSE, array('id'=>'album_select'));
?>
		</div>
	</div>
	<div class="span5">
		<div class="form-inline">
			<label for="add_to_head" class="checkbox-label"><?php echo __('Add to head'); ?></label>
<?php
			echo Form::checkbox('add_to_head', 1, FALSE, array(
				'id' => 'add_to_head'
			)); 
?>			
		</div>
	</div>
</div>
<div id="js-multiupload-holder" data-url="<?php echo $url; ?>">Loading, please wait</div>

