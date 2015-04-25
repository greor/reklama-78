<?php defined('SYSPATH') or die('No direct access allowed.');

	$replace = empty($replace) ? array() : $replace;
	$c_url = URL::site(Request::$current->uri());
	$left_arr = '';

	if ( ! empty($menu_items)): 
?>
	<div class="well">
		<ul class="nav nav-list">
<?php 
		foreach ($menu_items as $name => $item) {
			if ( $item === NULL ) {
		 		echo '<li class="divider"></li>';
		 		continue;
			}
		
			if ($c_url == $item['link']) {
				$item['link'] = '#';
			} else {
				foreach ($replace as $marker => $value) {
					$item['link'] = str_replace($marker, $value, $item['link']);
				}
			}
		
			if ($name == 'back') {
				$icon = '<i class="'.Arr::get($item, 'icon', 'icon-circle-arrow-left').'"></i>';
				echo '<li>', HTML::anchor($item['link'], $icon.$item['title']), '</li>';
				continue;
			}
		
			$li_class = ( ! $item['link'] OR $c_url == $item['link']) ? ' active' : '';
			$icon = empty($item['icon']) ? '' : '<i class="'.$item['icon'].'"></i>';
			echo '<li class="nav-header', $li_class, '">', 
				 HTML::anchor($item['link'], $icon.$item['title'], array(
				 	'target' => empty($item['target']) ? '_self' : $item['target'],
				 )), '</li>';

			if ( ! empty($item['sub'])) {
				foreach ($item['sub'] as $_name => $_item) {
					if (empty($_item['link'])) {
						$_item['link'] = '#';
					} else {
						foreach ($replace as $marker => $value) {
							$_item['link'] = str_replace($marker, $value, $_item['link']);
						}
					}
						
					$_li_class = ( ! $_item['link'] OR $c_url == $_item['link']) ? ' class="active"' : '';
					$_icon = '<i class="'.Arr::get($_item, 'icon', 'icon-cog').'"></i>';
					echo '<li', $_li_class, '>',
						 HTML::anchor($_item['link'], $_icon.$_item['title'], array(
							'target' => empty($_item['target']) ? '_self' : $_item['target'],
						 )), '</li>';
				}
				echo '<li class="divider"></li>';
			}
		}
?>
		</ul>
	</div>
<?php 
	endif; 
?>
