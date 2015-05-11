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
			<div class="col-sm-12">
				<div class="isotope col-3 clearfix">
				<!-- isotope -->
<?php
				foreach ($news as $_item): 
?>				
					<div class="isotope-item">
						<div class="blog-article">
<?php
						if ( ! empty($_item['image'])) {
							echo '<div class="blog-article-thumbnail">', HTML::image($_item['image'], array(
								'alt' => $_item['title']
							)), '</div>';
						}
?>						
							<div class="blog-article-details">
								<h6>
<?php
								echo date('Y-m-d H:i', strtotime($_item['public_date']));
?>								
								</h6>
								<h4>
<?php
								if ( ! empty($_item['link'])) {
									echo HTML::anchor($_item['link'], $_item['title']);
								} else {
									echo HTML::chars($_item['title']);
								}
?>								
								</h4>
							</div>
<?php
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
				endforeach; 
?>					
				<!-- /isotope -->
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	s.initList.push(function(){
		$(".isotope").imagesLoaded( function() {
			var $holder = $(".isotope");
			$holder.isotope({
				itemSelector: '.isotope-item',
				layoutMode: 'masonry',
				transitionDuration: '0.8s'
			});
			
			$("body").resize(function () {
				$holder.isotope();
			});
		});
	});
	</script>
	
<?php
	$link = URL::base().Page_Route::uri($PAGE_ID, 'news');
	echo $paginator->render( $link );
?>
