<?php defined('SYSPATH') or die('No direct script access.');

	$block = Helper_Block::get_block('about_footer');
	
	if (empty($block) OR empty($block['text'])) {
		return;
	}
	
?>

<div class="col-sm-6">
	<div class="widget widget-text">
		<h3 class="widget-title"><?php echo $block['title']; ?></h3>
		<div class="about-text"><?php echo $block['text'];?></div>
	</div>
</div>