<?php defined('SYSPATH') or die('No direct script access.'); 

	$block = Helper_Block::get_block('month_project');

	if (empty($block)) {
		return;
	}
	
	$style = empty($block['image']) ? '' : "background-image:url({$block['image']})";
?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="info-box parallax" style="<?php echo $style; ?>">
<?php
					if ( ! empty($block['name'])) {
						echo '<h4><strong>', HTML::chars($block['name']), '</strong></h4><br>';
					} 
					if ( ! empty($block['title'])) {
						echo '<h2>', HTML::chars($block['title']), '</h2>';
					} 
					if ( ! empty($block['text'])) {
						echo '<div class="info-text">', $block['text'], '</div>';
					} 
					if ( ! empty($block['link'])) {
						echo HTML::anchor($block['link'], 'Подробнее', array(
							'class'	=> 'btn btn-default',
							'target' => '_blank'
						));
					} 
?>					
				</div>
			</div>
		</div>
	</div>
