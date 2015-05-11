<?php defined('SYSPATH') or die('No direct script access.');

	if (empty($elements)) {
		return;
	}
?>
	<div class="widget widget-recent-posts">
		<h3 class="widget-title"><?php echo __('New on the site');?></h3>
		<ul>
<?php
		foreach ($elements as $_item) {
			echo '<li>';
			
			if ( ! empty($_item['link'])) {
				echo HTML::anchor($_item['link'], $_item['title'], array(
					'class' => 'post-title'
				));
			} else {
				echo '<span class="post-title">', HTML::chars($_item['title']), '</span>';
			}
			
			echo '<p class="post-date">', date('Y-m-d', strtotime($_item['public_date'])), '</p>';
			
			echo '</li>';
		} 
?>		
		</ul>
	</div>
	
