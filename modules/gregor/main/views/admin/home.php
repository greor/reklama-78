<?php defined('SYSPATH') or die('No direct script access.'); ?>
	<div class="span9">
		<div id="main">
			<div class="company_logo">
<?php 
			if ( ! empty($logo['src'])) {
				echo HTML::image($MEDIA.$logo['src']);
			}
?>
			</div>
		</div>
	</div>
