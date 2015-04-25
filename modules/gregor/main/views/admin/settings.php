<?php defined('SYSPATH') or die('No direct script access.'); 
	if (isset($menu)):
?>
		<ul class="menu">
<?php 
		foreach ($menu as $item):
			if ( ! $ACL->is_allowed( $USER, $item['name'], 'read' ) ) continue;
?>
			<li>
				<a href="<?php echo $item['url']; ?>">
<?php 
					echo HTML::chars($item['title']); 
?>
				</a>
			</li>
<?php 
		endforeach;
?>
		</ul>
<?php 
	endif;
?>
