<?php defined('SYSPATH') or die('No direct script access.');

	if ( empty($menu))
		return;
?>

	<div class="col-sm-3">
		<div class="widget widget-pages">
			<ul>
<?php
			foreach ($menu as $_item) {
				echo '<li>', HTML::anchor($_item['uri'], $_item['title']), '</li>';
			}
		 
?>			
			</ul>
		</div>
	</div>