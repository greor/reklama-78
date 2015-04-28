<?php defined('SYSPATH') or die('No direct access allowed.');

	if ( $elements->count() > 0 ): 
		$query_array = array();
		$delete_tpl = Route::url('modules', array(
			'controller' => 'clients',
			'action'     => 'delete',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
		));
	
		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$edit_tpl = Route::url('modules', array(
			'controller' => 'clients',
			'action'     => 'edit',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
		));
	
		$query_array['mode'] = 'up';
		$up_tpl	= Route::url('modules', array(
			'controller' => 'clients',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
		));
	
		$query_array['mode'] = 'down';
		$down_tpl = Route::url('modules', array(
			'controller' => 'clients',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
		));
?>
		<table class="table table-bordered table-striped">
			<colgroup>
				<col class="span1">
				<col class="span2">
				<col class="span4">
				<col class="span2">
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('ID'); ?></th>
					<th><?php echo __('Image'); ?></th>
					<th><?php echo __('Title'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
			$wrapper = ORM_Helper::factory('client');
			foreach ($elements as $item):
?>
				<tr>
					<td><?php echo $item->id ?></td>
					<td>
<?php 
					if ( ! empty($item->image)) {
						$img_size = getimagesize(DOCROOT.$wrapper->file_path('image', $item->image));
						
						if ($img_size[0] > 100 OR $img_size[1] > 100) {
							$thumb = Thumb::uri('admin_image_100', $wrapper->file_uri('image', $item->image));
						} else {
							$thumb = $wrapper->file_uri('image', $item->image);
						}
						
						if ($img_size[0] > 300 OR $img_size[1] > 300) {
							$flyout = Thumb::uri('admin_image_300', $wrapper->file_uri('image', $item->image));
						} else {
							$flyout = $wrapper->file_uri('image', $item->image);
						}
						
						echo HTML::anchor($flyout, HTML::image($thumb, array(
							'title' => ''
						)), array(
							'class' => 'js-photo-gallery',
							
						));
						
					} else {
						echo __('No image');						
					}
?>
					</td>
					<td>
<?php
					if ( (bool) $item->active) {
						echo '<i class="icon-eye-open"></i>&nbsp;';
					}
					echo HTML::chars($item->title);
?>
					</td>
					<td>
<?php 
					if ($ACL->is_allowed($USER, $item, 'edit')) {
						echo HTML::anchor(str_replace('{id}', $item->id, $down_tpl), '<i class="icon-arrow-down"></i>', array(
							'class' => 'btn',
							'title' => __('Move down'),
						));
						echo HTML::anchor(str_replace('{id}', $item->id, $up_tpl), '<i class="icon-arrow-up"></i>', array(
							'class' => 'btn',
							'title' => __('Move up'),
						));
						echo HTML::anchor(str_replace('{id}', $item->id, $edit_tpl), '<i class="icon-edit"></i>', array(
							'class' => 'btn',
							'title' => __('Edit'),
						));
						echo HTML::anchor(str_replace('{id}', $item->id, $delete_tpl), '<i class="icon-remove"></i>', array(
							'class' => 'btn delete_button',
							'title' => __('Delete'),
						));
					}
?>
					</td>
				</tr>
<?php 
			endforeach;
?>
			</tbody>
		</table>
<?php
		$link = Route::url('modules', array(
			'controller' => 'clients',
		));
		echo $paginator->render( $link );
	endif;
