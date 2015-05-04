<?php defined('SYSPATH') or die('No direct script access.');

	if ( empty($menu))
		return;
?>

	<nav>
		<a id="mobile-menu-button" href="#"><i class="fa fa-bars"></i></a>
		<ul class="menu clearfix" id="menu">
<?php
		foreach ($menu as $_item) {
			$_class = array();
			if ($_item['is_active']) {
				$_class[] = 'active';
			}
			if ( ! empty($_item['sub'])) {
				$_class[] = 'dropdown';
			}
			
			echo '<li class="', implode(' ', $_class), '">', HTML::anchor($_item['uri'], $_item['title']);
			
			if ( ! empty($_item['sub'])) {
				echo '<ul>';
				foreach ($_item['sub'] as $_sub) {
					echo '<li>', HTML::anchor($_sub['uri'], $_sub['title']), '</li>';
				}
				echo '</ul>';
			}
			
			echo '</li>';
		}
		 
?>
		</ul>
	</nav>