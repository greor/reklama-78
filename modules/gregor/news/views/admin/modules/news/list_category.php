<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
	<div class="row">
		<div class="span9 kr-page-selector">
			<form class="form-inline">
				<label for="page_select"><?php echo __('Page'); ?>:</label>
<?php 
				echo Form::select('page_id', $MODULE_PAGES, $MODULE_PAGE_ID, array(
					'id' => 'page_select'
				)); 
?>
			</form>
			<script>
				$(document).ready(function(){
					$('#page_select').change(function(){
						var _page = $('option:selected', '#page_select').val();
						window.location = window.location.pathname + '?pid=' + _page;
					});
				});
			</script>
		</div>
	</div>
<?php 
	if ( $categories->count() > 0): 
	
		$query_array = array(
			'pid' => $MODULE_PAGE_ID
		);
		$query = Helper_Page::make_query_string($query_array);

		$open_tpl = Route::url('modules', array(
			'controller' => 'news',
			'action'     => 'category',
			'id'         => '{id}',
			'query'      => $query,
		));
		$delete_tpl = Route::url('modules', array(
			'controller' => 'news',
			'action'     => 'delete_category',
			'id'         => '{id}',
			'query'      => $query,
		));

		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$query = Helper_Page::make_query_string($query_array);
		$edit_tpl = Route::url('modules', array(
			'controller' => 'news',
			'action' => 'edit_category',
			'id' => '{id}',
			'query' => $query,
		));

		$query_array['mode'] = 'up';
		$query = Helper_Page::make_query_string($query_array);
		$up_tpl	= Route::url('modules', array(
			'controller' => 'news',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => $query,
		));

		$query_array['mode'] = 'down';
		$query = Helper_Page::make_query_string($query_array);
		$down_tpl = Route::url('modules', array(
			'controller' => 'news',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => $query,
		));
?>
		<table class="table table-bordered table-striped">
			<colgroup>
				<col class="span1">
				<col class="span5">
				<col class="span3">
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('ID'); ?></th>
					<th><?php echo __('Title'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
			if ($ACL->is_allowed($USER, NULL, 'show_no_category')):
?>
				<tr>
					<td>0</td>
					<td>
						<i style="display:inline-block;height:14px;width:14px;"></i>&nbsp;
<?php 
						echo __('No category') 
?>
					</td>
					<td>
<?php
						echo HTML::anchor(str_replace('{id}', 0, $open_tpl), '<i class="icon-folder-open"></i>', array(
							'class' => 'btn',
							'title' => __('Open'),
						)); 
?>					
					</td>
				</tr>
<?php 
			endif; 
			foreach ($categories as $item):
?>
				<tr>
					<td><?php echo $item->id ?></td>
					<td>
<?php
						switch ($item->status) {
							case 0:
								echo '<i class="icon-ban-circle"></i> ';
								break;
							case 1:
								echo '<i class="icon-eye-close"></i> ';
								break;
							case 2:
								echo '<i class="icon-eye-open"></i> ';
								break;
						}
			
						echo HTML::chars($item->title);
?>
					</td>
					<td>
<?php
						echo HTML::anchor(str_replace('{id}', $item->id, $open_tpl), '<i class="icon-folder-open"></i>', array(
							'class' => 'btn',
							'title' => __('Open'),
						));
						
						if ( $ACL->is_allowed($USER, $item, 'edit') ) {
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
							
							if ( ! in_array($item->uri, $not_deleted_categories)) {
								echo HTML::anchor(str_replace('{id}', $item->id, $delete_tpl), '<i class="icon-remove"></i>', array(
									'class' => 'btn delete_button',
									'title' => __('Delete'),
								));
							}
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
	endif;
