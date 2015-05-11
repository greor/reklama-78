<?php defined('SYSPATH') or die('No direct script access.');

	if (empty($service)) {
		return;
	}
?>
	<div class="widget widget-categories">
		<h3 class="widget-title"><?php echo __('Services'); ?></h3>
		<ul>
<?php
		foreach ($service as $_item) {
			echo '<li>', HTML::anchor($_item['link'], $_item['title']), '</li>';
			
			
		} 
?>		
		</ul>
	</div>