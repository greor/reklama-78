<?php defined('SYSPATH') or die('No direct script access.'); 

	foreach ($elements as $_i => $_item):
		$_even = ! (bool) ($_i % 2);
	
		if ($_even):
			echo '<!-- container --><div class="container"><div class="row">';
		endif;
		
		$_animation = $_even ? 'icon-right fadeInLeft' : 'icon-left fadeInRight';
?>
		<div class="col-sm-6">
			<div class="services-boxes style-3 wow <?php echo $_animation; ?>">
<?php
				if ( ! empty($_item['icon'])) {
					echo HTML::image($_item['icon'], array(
						'class' => 'service-icon',
						'alt' => $_item['title'],
						'title' => $_item['title'],
					));
				} 
?>						
				<div class="services-boxes-content">
					<h3>
<?php
					if ( ! empty($_item['link'])) {
						echo HTML::anchor($_item['link'], $_item['title']);
					} else {
						echo '<span>', HTML::chars($_item['title']), '</span>';
					}
?>							
					</h3>
<?php
					if ( ! empty($_item['announcement'])) {
						echo '<p>', $_item['announcement'], '</p>';
					}
?>							
				</div>
			</div>
		</div>
<?php
		if ( ! $_even):
			echo '</div></div><!-- /container -->';
		endif;
	endforeach; 
?>	

