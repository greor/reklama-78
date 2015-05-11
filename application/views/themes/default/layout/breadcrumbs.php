<?php defined('SYSPATH') or die('No direct script access.');?>

<div id="page-header" class="dark-1">  
	<div class="container">
		<div class="row">
			<div class="col-sm-6">
				<strong><?php echo HTML::chars($BREADCRUMBS_TITLE); ?></strong>
			</div>
			<div class="col-sm-6">
<?php
			if ( ! empty($BREADCRUMBS)) {
				echo '<ol class="breadcrumb">';
				echo '<li><a href="', URL::base(), '">Главная</a></li>';
				
				$last_i = count($BREADCRUMBS) - 1;
				foreach ($BREADCRUMBS as $i => $_item) {
					
					if ($i == $last_i) {
						echo '<li class="active">';
						echo HTML::chars($_item['title']);
					} else {
						echo '<li>';
						if ( ! empty($_item['link'])) {
							echo HTML::anchor($_item['link'], $_item['title']);
						} else {
							echo HTML::chars($_item['title']);
						}
					}
					
					echo '</li>';
				}
				
				echo '</ol>';
			}
?>			
			</div>
		</div>
	</div>
</div>