<?php defined('SYSPATH') or die('No direct script access.'); 

	if (empty($paginator['items'])) {
		return;
	}

?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<ul class="pagination text-center">
<?php
				foreach ($paginator['items'] as $_item) {
					
					echo empty($_item['current']) 
						? '<li>'
						: '<li class="active">';
					
					echo HTML::anchor($_item['link'], $_item['title']);
					echo '</li>';
				} 
?>				
				</ul>
			</div>
		</div>
	</div>
	
	