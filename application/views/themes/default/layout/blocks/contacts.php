<?php defined('SYSPATH') or die('No direct script access.');

	$phone_block = Helper_Block::get_block('phone');
	$email_block = Helper_Block::get_block('email');

	if (empty($phone_block['text']) AND empty($email_block['text'])) {
		return;
	}
?>

	<div class="widget widget-contact">
		<h3 class="widget-title"><?php echo __('Contacts'); ?></h3>
		<ul>
<?php
		if ( ! empty($phone_block['text'])) {
			echo '<li><span>', HTML::chars($phone_block['title']), '</span>', $phone_block['text'], '</li>';
		} 
		if ( ! empty($email_block['text'])) {
			$_text = str_replace(array('<br>', '<br/>', '<br />'), ' ', $email_block['text']);
			$_email = explode(' ', $_text);
			if ( ! empty($_email)) {
				foreach ($_email as & $_v) {
					$_v = HTML::mailto(strip_tags($_v), NULL, array(
						'class' => 'email-link'
					));
				}
				
				echo '<li><span>', HTML::chars($email_block['title']), '</span>', implode('<br>', $_email), '</li>';
			}
		} 
?>		
		</ul>
	</div>
