<?php defined('SYSPATH') or die('No direct script access.'); 

	$block = Helper_Block::get_block('welcome');
	
	if (empty($block) OR empty($block['image'])) {
		return;
	}
?>

	<section class="full-section parallax welcome-block" style="background-image:url(<?php echo $block['image']; ?>);">
		<div class="full-section-content">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">	
<?php
					if ( ! empty($block['title'])) {
						echo '<h1 class="text-center">', HTML::chars($block['title']), '</h1>';
					} 
					if ( ! empty($block['text'])) {
						echo '<div class="welcome-text">', $block['text'], '</div>';
					} 
					if ( ! empty($block['link'])) {
						echo '<br><p class="text-center">', HTML::anchor($block['link'], __('Learn more').' <i class="fa fa-arrow-right"></i>', array(
							'class' => 'btn btn-default'
						)), '</p>';
					}
?>					
					</div>
				</div>
			</div>
		</div>
	</section>
