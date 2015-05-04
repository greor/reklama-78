<?php defined('SYSPATH') or die('No direct script access.'); ?>

	<div class="control-group">
		<label class="control-label" for="<?php echo $field; ?>_field">
<?php
			echo __($labels[ $field ]), '&nbsp;:&nbsp;';
?>
		</label>
		<div class="controls">
<?php
		$values = unserialize($values);
		$values = empty($values) ? array() : $values;
		$_settings = array(
			'title' => array('data-x', 'data-y'),
			'text' => array('data-x', 'data-y'),
			'url' => array('data-x', 'data-y'),
		); 
		
		foreach ($_settings as $_row => $_conf) {
			echo '<div class="rev-slidet-settings"><strong>', __($labels[$_row]), '</strong>';
			foreach ($_conf as $_c) {
				$_val = empty($values[$_row][$_c]) ? '' : $values[$_row][$_c];
				echo '<label>', Form::input("settings[{$_row}][{$_c}]", $_val, array(
					'class' => 'small'
				)), '&nbsp;' ,$_c, '</label>';
			}
			echo '</div>';
		}
?>
		</div>
	</div>
