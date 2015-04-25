<?php defined('SYSPATH') or die('No direct script access.'); ?>
	<div class="span3 kr-aside">
<?php 
	if ( ! empty($aside)) {
		echo $aside;
	}
?>
	</div>
	<div id="main" class="span9 kr-main">
<?php 
		if ( ! empty($title)): 
?>
		<div class="page-header kr-page-header">
			<h1><?php echo HTML::chars($title); ?>
<?php 
			if ( ! empty($sub_title)){
				echo '&nbsp;<small>', $sub_title, '</small>';
			}
?>
			</h1>
		</div>
<?php 
		endif; 
		echo $content;
?>
	</div>
