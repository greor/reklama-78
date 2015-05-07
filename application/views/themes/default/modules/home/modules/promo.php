<?php defined('SYSPATH') or die('No direct script access.'); 

	if (empty($elements))
		return;
?>
	<div class="container promo-holder">
		<div class="row">
			<div class="col-sm-12">
				<div class="info-slider">
					<ul>
<?php
					foreach ($elements as $_item): 
?>						
						<li>
							<div class="row">
								<div class="col-sm-6">
									<div class="headline style-1">
										<strong><?php echo $SITE['title_tag']; ?></strong>
<?php
										if ( ! empty($_item['title'])) {
											echo '<h2>', HTML::chars($_item['title']),  '</h2>';
										} 
?>										
									</div>
<?php	
									if ( ! empty($_item['text'])) {
										echo '<div class="promo-text">', $_item['text'], '</div>';
									} 
?>									
								</div>
								<div class="col-sm-6">
<?php
								 echo HTML::image($_item['image'], array(
								 	'alt' => $_item['title']
								 )); 
?>								
								</div>
							</div>
						</li>
<?php
					endforeach; 
?>						
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	s.initList.push(function(){
		$(".info-slider ul").bxSlider({
			mode: 'fade',
			speed: 800,
			infiniteLoop: true,
			hideControlOnEnd: false,
			pager: true,
			pagerType: 'full',
			controls: true,
			auto: true,
			pause: 5000,
			autoHover: true,
			useCSS: false
		});
	});
	</script>