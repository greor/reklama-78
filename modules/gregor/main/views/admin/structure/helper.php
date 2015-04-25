<?php defined('SYSPATH') or die('No direct access allowed.');

function draw_sub($childrens, $reference, $tpl, $inactive)
{
	static $depth;
	$depth = (isset($depth)) ? ++$depth : 0;
	
	$ACL = $reference['ACL'];
	$USER = $reference['USER'];
	$modules = $reference['modules'];
	$base_uri_list = $reference['base_uri_list'];
	$status_codes = $reference['status_codes'];
	$page_types = $reference['page_types'];
	$tpl_array = $reference['tpl_array'];

	$_list = array();
	foreach ($childrens as $_item) {
		$item = $_item['object'];
		$tpl_array = $reference['tpl_array'];

		
		$_status_icon = ($item->level > 1) 
			? '<div class="marker"></div>'
			: '';
		switch ($item->status) {
			case $status_codes['inactive']:
				$_status_icon .= '<i class="icon-ban-circle icon"></i> ';
				break;
			case $status_codes['active']:
				$_status_icon .= '<i class="icon-eye-open icon"></i> ';
				break;
		}

		
		if ( strpos($base_uri_list[ $item->id ], 'http') === 0 ) {
			$_uri = $base_uri_list[ $item->id ];
		} else {
			$_uri = URL::base().$base_uri_list[ $item->id ];
		}
		$_title = $item->title;
			
		
		$_inactive = $inactive || ($item->status == $status_codes['inactive']);
		if ($_inactive) {
			$_attr = ' class="inactive" title="Неактивно"';
			$_link = '<span>'.$_uri.'</span>';
		} else {
			$_attr = '';
			$_link = HTML::anchor($_uri, $_uri, array(
				'target' => '_blank',
			));
		}
		
		$__list = array();
		if ($ACL->is_allowed($USER, $item, 'edit')) {
			if ( ! Helper_Page::instance()->not_equal($item, 'type', 'module') OR ! empty($item->name)) {
				unset($tpl_array['delete_tpl']);
			}
			foreach($tpl_array as $__key => $__tpl) {
				$__list[] = str_replace('--ID--', $item->id, $__tpl);
			}
		}
		$_actions = implode('', $__list);

		
		if ($item->type == 'module') {
			$_descr = __( $modules[ $item->data ]['name'] );
		} else {
			$_descr = $page_types[ $item->type ];
		}

		
		$_childrens = '';
		if ( ! empty($_item['childrens'])) {
			$_childrens = draw_sub(
				$_item['childrens'],
				$reference,
				$tpl,
				$_inactive
			);
		};
		
		$_list[] = str_replace(
			array('{ATTR}', '{STATUS_ICONS}', '{ACTIONS}', '{TITLE}', '{LINK}', '{DESCRIPTION}', '{CHILDRENS}'),
			array($_attr, $_status_icon, $_actions, $_title, $_link, $_descr, $_childrens),
			$tpl
		);
		
	}
	
	$class = ($depth > 0)
		? 'sub'
		: 'list';
	
	$depth--;
	
	return '<ul class="'.$class.'">'.implode('', $_list).'</ul>';
}