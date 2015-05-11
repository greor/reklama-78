<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('layout/breadcrumbs');
	
?>
 	<div class="container">
		<div class="row">
			<div class="col-sm-9">
				<div class="blog-article">
					<div class="blog-article-thumbnail">
<?php
						echo HTML::image($service['image'], array(
							'alt' => $service['title']
						)); 
?>					
					</div>
					<div class="blog-article-details">
						<h2><?php echo HTML::chars($service['title']); ?></h2>
					</div>
					<div class="page-text"><?php echo $service['text']; ?></div>
				</div>
			</div>
			
			<div class="col-sm-3">
<?php
			echo $widget_service; 
			echo $widget_recent; 
?>				
			</div>
		</div>
	</div>
	
	
	
	
	
