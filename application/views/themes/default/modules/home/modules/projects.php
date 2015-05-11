<?php defined('SYSPATH') or die('No direct script access.'); 

	if (empty($elements)) {
		return;
	}

?>

	<div class="container">
		<div class="row">
			<div id="projects" class="clients-holder col-sm-12">
				<div class="headline style-2">
					<h2><?php echo __('Projects'); ?></h2>
				</div>
				<ul class="clients-list">
<?php
				foreach ($elements as $_item) {
					echo '<li>', HTML::image($_item['image'], array(
						'width' => '195',
						'height' => '195',
						'alt' => $_item['title'],
						'title' => $_item['title'],
					)), '</li>';
				} 
?>			
				</ul>
			</div>
		</div>
	</div>
            
	<script type="text/javascript">
	s.initList.push(function(){
		var $holder = $("#projects"),
			$bxSlider = $holder.find('.clients-list'),
			imgWidth = 195,
			holderWidth = $holder.width();
	
		var bxSlider = $bxSlider.bxSlider({
			slideWidth: holderWidth,
			minSlides: Math.floor(holderWidth/imgWidth),
		    maxSlides: Math.floor(holderWidth/imgWidth),
		    pager: false,
			speed: 800,
			infiniteLoop: true,
			hideControlOnEnd: false
		});
	
		$(window).smartresize(function(){
			holderWidth = $holder.width();
			bxSlider.reloadSlider({
				slideWidth: holderWidth,
				minSlides: Math.floor(holderWidth/imgWidth),
			    maxSlides: Math.floor(holderWidth/imgWidth),
			    pager: false,
				speed: 800,
				infiniteLoop: true,
				hideControlOnEnd: false
			});
		});
	});
	</script>
	
	<br><br>