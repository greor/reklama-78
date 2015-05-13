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
				<div class="isotope col-3 clearfix photo-list">
				<!-- isotope -->
<?php
				foreach ($albums as $_item): 
?>				
				
					<div class="isotope-item categ-1 categ-3">
						<div class="portfolio-item">
							<div class="portfolio-item-thumbnail">
<?php
								echo HTML::image($_item['image'], array(
									'alt' => $_item['title']
								));
?>							
								<div class="portfolio-item-hover">
<?php
									echo '<h3>', HTML::anchor($_item['link'], $_item['title']), '</h3>'; 
?>									
								</div>
							</div>
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
	$link = URL::base().Page_Route::uri($PAGE_ID, 'photo');
	echo $paginator->render( $link );
?>
