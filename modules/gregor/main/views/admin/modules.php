<?php defined('SYSPATH') or die('No direct script access.'); ?>
	<ul class="menu" style="list-style: none; font-size: 1.2em;">
<?php 
		$i = 0; 
		foreach ($modules as $code => $item):
			$i++;
?>
			<li>
				<a href="<?php echo HTML::chars($item['url']); ?>">
					<span class="badge" style="float:left; margin-right: 3em;"><?php echo $i; ?></span>
<?php 
					echo HTML::chars($item['name']); 
?>
				</a>
			</li>
<?php 
		endforeach;
?>
	</ul>
