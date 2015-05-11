<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('layout/breadcrumbs');
	
	if ( ! empty($page['text'])):
?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="headline style-1">
					<div class="page-text"><?php echo $page['text']; ?></div>
				</div>
			</div>
		</div>
	</div>
	
<?php 
	endif;
?>
	<div class="container">
		<div class="row">
			<div class="col-sm-9">
<?php
			foreach ($posts as $_item): 
?>			
				<div class="blog-article">
<?php
				if ( ! empty($_item['image'])) {
					echo '<div class="blog-article-thumbnail">', HTML::image($_item['image'], array(
						'alt' => $_item['title']
					)), '</div>';
				}
?>				
					<div class="blog-article-details">
						<h4>
<?php
							echo date('Y-m-d H:i', strtotime($_item['public_date']));
?>						
						</h4>
						<h2>
<?php
							if ( ! empty($_item['link'])) {
								echo HTML::anchor($_item['link'], $_item['title']);
							} else {
								echo HTML::chars($_item['title']);
							}
?>		
						</h2>
					</div>
<?php
					if ( ! empty($_item['announcement'])) {
						echo '<div class="page-text">', $_item['announcement'], '</div>';
					} 
					if ( ! empty($_item['link'])) {
						echo HTML::anchor($_item['link'], __('Continue reading'), array(
							'class' => 'btn btn-default'
						));
					}
?>					
				</div>
<?php
			endforeach; 
?>				
			</div>
			
			<div class="col-sm-3">
<?php
				echo $widget_service; 
				echo $widget_recent; 
?>
			</div>
		</div>
	</div>
	
<?php
	$link = URL::base().Page_Route::uri($PAGE_ID, 'blog');
	echo $paginator->render( $link );
?>
	
