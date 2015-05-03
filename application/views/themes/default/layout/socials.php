<?php defined('SYSPATH') or die('No direct script access.');?>

<div id="footer-top">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="widget widget-social">
					<div class="social-media">
<?php
					if ( ! empty($SITE['vkontakte_link'])) {
						echo HTML::anchor($SITE['vkontakte_link'], '<i class="fa fa-vk"></i>', array(
							'class' => 'vk',
							'target' => '_blank',
						));
					}
					if ( ! empty($SITE['facebook_link'])) {
						echo HTML::anchor($SITE['facebook_link'], '<i class="fa fa-facebook"></i>', array(
							'class' => 'facebook',
							'target' => '_blank',
						));
					}
					if ( ! empty($SITE['twitter_link'])) {
						echo HTML::anchor($SITE['twitter_link'], '<i class="fa fa-twitter"></i>', array(
							'class' => 'twitter',
							'target' => '_blank',
						));
					}
					if ( ! empty($SITE['google_link'])) {
						echo HTML::anchor($SITE['google_link'], '<i class="fa fa-google-plus"></i>', array(
							'class' => 'google',
							'target' => '_blank',
						));
					}
					if ( ! empty($SITE['youtube_link'])) {
						echo HTML::anchor($SITE['youtube_link'], '<i class="fa fa-youtube-play"></i>', array(
							'class' => 'youtube',
							'target' => '_blank',
						));
					}
					if ( ! empty($SITE['instagram_link'])) {
						echo HTML::anchor($SITE['instagram_link'], '<i class="fa fa-instagram"></i>', array(
							'class' => 'instagram',
							'target' => '_blank',
						));
					}
?>
					</div>   
				</div>
			</div>
		</div>
	</div>
</div>
