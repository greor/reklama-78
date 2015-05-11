<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('layout/breadcrumbs');
	
?>
 	<div class="container">
		<div class="row">
			<div class="col-sm-9">
				<div class="blog-article">
<?php
					if ( ! empty($action['image'])) {
						
						echo '<div class="blog-article-thumbnail">', HTML::image($action['image'], array(
							'alt' => $action['title']
						)), '</div>'; 
					}
?>					
					<div class="blog-article-details">
						<h2><?php echo HTML::chars($action['title']); ?></h2>
					</div>
					<div class="page-text"><?php echo $action['text']; ?></div>
				</div>
			</div>
			
			<div class="col-sm-3">
<?php
			echo $widget_recent; 
			echo $widget_service;
?>				
			</div>
		</div>
	</div>
	
	
	
	
	
