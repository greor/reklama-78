<?php defined('SYSPATH') or die('No direct script access.');

	$phone_block = Helper_Block::get_block('phone_footer');
	$email_block = Helper_Block::get_block('email_footer');

	if (empty($phone_block['text']) AND empty($email_block['text'])) {
		return;
	}
?>

<div class="col-sm-3">
	<div class="widget widget-contact">
		<h3 class="widget-title"><?php echo __('Contacts'); ?></h3>
		<ul>
<?php
		if ( ! empty($phone_block['text'])) {
			echo '<li><span>', HTML::chars($phone_block['title']), '</span>', $phone_block['text'], '</li>';
		} 
		if ( ! empty($email_block['text'])) {
			echo '<li><span>', HTML::chars($email_block['title']), '</span>', HTML::mailto(strip_tags($email_block['text'])), '</li>';
		} 
?>		
		</ul>
	</div>
</div>