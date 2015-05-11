<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('layout/breadcrumbs');
	
	foreach ($service as $_item):
?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="portfolio-item classic">
					<div class="row">
<?php
					if ( ! empty($_item['image'])):
?>					
						<div class="col-sm-6">
							<div class="portfolio-item-thumbnail">
<?php
								echo HTML::image($_item['image'], array(
									'width' => '555',
									'height' => '300',
									'alt' => $_item['title'],
								)); 
?>							
							</div>
						</div>
						<div class="col-sm-6">
							<div class="portfolio-item-description">
<?php
								if ( ! empty($_item['link'])) {
									echo '<h3>', HTML::anchor($_item['link'], $_item['title']), '</h3><br>';
								} else {
									echo '<h3>', HTML::chars($_item['title']), '</h3><br>';
								}
								if ( ! empty($_item['announcement'])) {
									echo '<div class="page-text">', $_item['announcement'], '</div>';
								}
								if ( ! empty($_item['link'])) {
									echo HTML::anchor($_item['link'], __('Read more'), array(
										'class' => 'btn btn-default'
									));
								}
?>								
							</div>
						</div>
<?php
					else: 
?>					
						<div class="col-sm-12">
							<div class="portfolio-item-description">
<?php
								if ( ! empty($_item['link'])) {
									echo '<h3>', HTML::anchor($_item['link'], $_item['title']), '</h3><br>';
								} else {
									echo '<h3>', HTML::chars($_item['title']), '</h3><br>';
								}
								if ( ! empty($_item['announcement'])) {
									echo '<div class="page-text">', $_item['announcement'], '</div>';
								}
								if ( ! empty($_item['link'])) {
									echo HTML::anchor($_item['link'], __('Read more'), array(
										'class' => 'btn btn-default'
									));
								}
?>								
							</div>
						</div>
<?php
					endif; 
?>					
					
					</div>
				</div>
			</div>
		</div>
	</div>
	
<?php
	endforeach; 

