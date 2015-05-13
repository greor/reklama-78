<?php defined('SYSPATH') or die('No direct script access.');?>

<?php
	echo View_Theme::factory('layout/breadcrumbs'); 
?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="headline style-3">
					<strong><?php echo $SITE['title_tag']; ?></strong>
					<h1><?php echo HTML::chars($page->title)?></h1>
<?php 
					echo $page->text; 
?>
				</div>
			</div>
		</div>
	</div>
            

