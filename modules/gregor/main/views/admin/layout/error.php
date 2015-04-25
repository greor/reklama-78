<?php defined('SYSPATH') or die('No direct access allowed.');

	if ( ! empty($errors) AND is_array($errors)): 
?>
		<div class="alert alert-error">
<?php
		foreach ($errors as $key => $error) {
			echo '<p>'.$error.'</p>';
		}
?>
		</div>
<?php 
	endif; 
?>
