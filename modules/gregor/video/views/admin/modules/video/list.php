<?php defined('SYSPATH') or die('No direct access allowed.');

	$query_array = array(
		'pid' => $MODULE_PAGE_ID
	);
	$query = Helper_Page::make_query_string($query_array);
	$up_link = 	Route::url('modules', array(
		'controller' => 'video',
		'query'      => $query
	));
	
	$_groups = Kohana::$config
		->load('_video.groups');
?>
	<div class="row">
		<div class="span9 kr-page-selector">
			<form class="form-inline">
				<label for="page_select"><?php echo __('Page'); ?>:</label>
<?php 

				echo Form::select('page_id', $MODULE_PAGES, $MODULE_PAGE_ID, array(
					'id'       => 'page_select',
					'data-uri' => Route::url('modules', array(
						'controller' => 'video',
					))
				)); 
?>
			</form>
			<script>
				$(document).ready(function(){
					$('#page_select').change(function(){
						var $this = $(this),
							_page = $('option:selected', $this).val();
						window.location = $this.data('uri')+'?pid='+_page;
					});
				});
			</script>
		</div>
	</div>
	<div class="row kr-staff-header">
		<div class="span9">
			<ul class="breadcrumb kr-breadcrumb">
				<li>
					<i class="icon-folder-open"></i>
					<a href="<?php echo $up_link; ?>"><?php echo __('Category up')?></a>
					<span class="divider">/</span>
				</li>
<?php 
				if ($breadcrumbs->loaded()) {
					$_title = $breadcrumbs->title.' ( '.Arr::get($_groups, $breadcrumbs->group, $breadcrumbs->group).' )';
					echo '<li class="active">', HTML::chars($_title), '</li>';
				} elseif ( Request::current()->query('cid') == 0 ) {
					echo '<li class="active">', __('No category'), '</li>';
				}
?>
			</ul>
		</div>
	</div>
<?php 
	if ( $elements->count() > 0 ): 
		$query_array['cid'] = $category_id;
		$query = Helper_Page::make_query_string($query_array);
		$delete_tpl = Route::url('modules', array(
			'controller' => 'video',
			'action'     => 'delete',
			'id'         => '{id}',
			'query'      => $query
		));

		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$query = Helper_Page::make_query_string($query_array);
		$edit_tpl = Route::url('modules', array(
			'controller' => 'video',
			'action'     => 'edit',
			'id'         => '{id}',
			'query'      => $query
		));
		
		$query_array['mode'] = 'up';
		$query = Helper_Page::make_query_string($query_array);
		$up_tpl	= Route::url('modules', array(
			'controller' => 'video',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => $query,
		));
		
		$query_array['mode'] = 'down';
		$query = Helper_Page::make_query_string($query_array);
		$down_tpl = Route::url('modules', array(
			'controller' => 'video',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => $query,
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
			$wrapper = ORM_Helper::factory('video');
			foreach ($elements as $item):
?>
			<tr>
				<td><?php echo $item->id ?></td>
				<td>
<?php
				if ($item->image) {
					$original = $wrapper->file_uri('image', $item->image);
					$thumb = Thumb::uri('admin_image_100', $original);

					echo HTML::anchor(URL::base().$original, HTML::image($thumb), array(
						'class' => 'js-photo-gallery',
						'target' => '_blank',
						'title'=> $item->image
					));
				} else {
					echo __('No img');
				}
?>				
				</td>
				<td>
<?php
					if ( (bool) $item->active) {
						echo '<i class="icon-eye-open"></i>&nbsp;';
					} 
					echo HTML::chars($item->title)
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
		'controller' => 'video',
		'action'     => 'category',
		'id'         => Request::current()->param('id'),
		'query'      => Helper_Page::make_query_string(array(
			'pid' => $MODULE_PAGE_ID,
			'cid' => $category_id,
		)),
	));

	echo $paginator->render( $link );
endif;
