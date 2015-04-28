<?php defined('SYSPATH') or die('No direct access allowed.');

	$query_array = array(
		'pid' => $MODULE_PAGE_ID
	);
	$query = Helper_Page::make_query_string($query_array);
	$up_link = 	Route::url('modules', array(
		'controller' => 'photo',
		'query'      => $query
	));
	
// 	$_groups = Kohana::$config
// 		->load('_photo.groups');
?>
	<div class="row">
		<div class="span9 kr-page-selector">
			<form class="form-inline">
				<label for="page_select"><?php echo __('Page'); ?>:</label>
<?php 

				echo Form::select('page_id', $MODULE_PAGES, $MODULE_PAGE_ID, array(
					'id'       => 'page_select',
					'data-uri' => Route::url('modules', array(
						'controller' => 'photo',
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
					<a href="<?php echo $up_link; ?>"><?php echo __('Album up')?></a>
					<span class="divider">/</span>
				</li>
<?php 
				if ($breadcrumbs->loaded()) {
// 					$_title = $breadcrumbs->title.' ( '.Arr::get($_groups, $breadcrumbs->group, $breadcrumbs->group).' )';
					$_title = $breadcrumbs->title;
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
			'controller' => 'photo',
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
			'controller' => 'photo',
			'action'     => 'edit',
			'id'         => '{id}',
			'query'      => $query
		));
		
		$query_array['mode'] = 'up';
		$query = Helper_Page::make_query_string($query_array);
		$up_tpl	= Route::url('modules', array(
			'controller' => 'photo',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => $query,
		));
		
		$query_array['mode'] = 'down';
		$query = Helper_Page::make_query_string($query_array);
		$down_tpl = Route::url('modules', array(
			'controller' => 'photo',
			'action'     => 'position',
			'id'         => '{id}',
			'query'      => $query,
		));
		
		$query_array['mode'] = 'first';
		$query = empty($query_array) ? NULL : http_build_query($query_array);
		$first_tpl =  Route::url('modules', array(
			'controller' => 'photo',
			'action' => 'position',
			'id' => '{id}',
			'query'	=> $query,
		));
		
		$query_array['mode'] = 'last';
		$query = empty($query_array) ? NULL : http_build_query($query_array);
		$last_tpl =  Route::url('modules', array(
			'controller' => 'photo',
			'action' => 'position',
			'id' => '{id}',
			'query'	=> $query,
		));
?>

		<table class="table table-bordered table-striped">
			<colgroup>
				<col class="span1">
				<col class="span1">
				<col class="span2">
				<col class="span2">
				<col class="span3">
			</colgroup>
			<thead>
				<tr>
					<th class="align-center">
<?php 
						echo Form::checkbox('select_all', NULL, FALSE, array(
							'class' => 'select-all', 
							'title' => __('Select all'), 
							'autocomplete' => 'off'
						)); 
?>
					</th>
					<th><?php echo __('ID'); ?></th>
					<th><?php echo __('Image'); ?></th>
					<th><?php echo __('Title'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
			$wrapper = ORM_Helper::factory('photo');
			foreach ($elements as $item):
?>
			<tr>
				<td class="align-center">
<?php 
				echo Form::checkbox('select_item', $item->id, FALSE, array(
					'class' => 'select-item', 
					'title' => __('Select item'), 
					'autocomplete' => 'off'
				)); 
?>	
				</td>
				<td><?php echo $item->id ?></td>
				<td>
<?php
				if ($item->image) {
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
					echo __('No photo');
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
						echo HTML::anchor(str_replace('{id}', $item->id, $last_tpl), '<i class="icon-arrow-last"></i>', array(
							'class' => 'btn',
							'title' => __('Move last'),
						));
						echo HTML::anchor(str_replace('{id}', $item->id, $down_tpl), '<i class="icon-arrow-down"></i>', array(
							'class' => 'btn',
							'title' => __('Move down'),
						));
						echo HTML::anchor(str_replace('{id}', $item->id, $up_tpl), '<i class="icon-arrow-up"></i>', array(
							'class' => 'btn',
							'title' => __('Move up'),
						));
						echo HTML::anchor(str_replace('{id}', $item->id, $first_tpl), '<i class="icon-arrow-first"></i>', array(
							'class' => 'btn',
							'title' => __('Move first'),
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
		<tfoot>
			<tr>
				<td colspan="5">
					<form action="#" class="form-inline action-with-selection" autocomplete="off">
						<label for="action_with_selected_items">
<?php
							echo __('Action with selected items');
?>
						</label>
<?php
						$opt_list = array(
							'' => __('Select action'),
							'move_up'    => __('Move selected items up'),
							'move_down'  => __('Move selected items down'),
							'move_first' => __('Move selected items first'),
							'move_last'  => __('Move selected items last'),
						);
						echo Form::select('action_with_selected_items', $opt_list, NULL, array(
							'id' => 'action_with_selected_items', 
							'class' => 'action-with-selected-items'
						)), '&nbsp;', Form::submit('do_action', __('Execute'), array(
							'class' => 'btn btn-disabled'
						));
?>
					</form>
		
				</td>
			</tr>
		</tfoot>
	</table>
	
	<div class="alert alert-error alert-block" style="display: none;">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<h4 class="alert-title"></h4>
		<span class="alert-content"></span>
	</div>
	<script>
<?php
	$query_array = array(
		'pid' => $MODULE_PAGE_ID,
		'cid' => $category_id,
	);
	$query = http_build_query($query_array);
	
	$group_operation_link = Route::url('modules', array(
		'controller' => 'photo',
		'action' => 'group_operation',
		'query' => $query,
	));
?>
	$(document).ready(function(){
		var table = $('.table'),
			select_all = $('.select-all', table.children('thead')),
			selectors = $('.select-item', table.children('tbody')),
			form = $('.action-with-selection', table.children('tfoot')),
			submit = $(':submit', form),
			action_url = '<?php echo $group_operation_link ?>',
			in_process = false;

		function allow_action(){
			submit
				.removeClass('btn-disabled')
				.addClass('btn-primary');
		}
		function disallow_action(){
			submit
				.removeClass('btn-primary')
				.addClass('btn-disabled');
		}
		function show_error_alert(message, title){
			var alert = $('.alert-error').clone();
			alert.find('.alert-title').text(title || 'Ошибка');
			alert.find('.alert-content').html(message);
			alert.insertAfter(table);
			alert.slideDown();
		}
		
		select_all.on('change', function(){
			selectors.prop("checked", this.checked);
			if (this.checked) {
				allow_action();
			} else {
				disallow_action();
			}
		});
		selectors.on('change', function(){
			if ( ! this.checked) {
				select_all[0].checked = false;
				if (selectors.filter(':checked').length === 0) {
					disallow_action();
				}
			} else {
				allow_action();
			}
		});
		form.on('submit', function(e){
			e.preventDefault();
			if (in_process) return;
			var action = $('select', this).val();
			if ( ! action) return;
			var items = [];
			selectors.each(function(){
				if (this.checked) {
					items.push(this.value);
				}
			});
			if ( ! items.length) return;
			in_process = true;
			$.ajax({
				url: action_url,
				type: "POST",
				async:true,
				cache:false,
				dataType: "html",
				data: {operation: action, ids: items}
			}).done(function(){
				window.location.reload();
			}).fail(function(jqXHR, textStatus, errorThrown) {
				in_process = false;
				if (textStatus !== "abort" && jqXHR.readyState > 0) {
					var message = "Не удалось выполнить запрос",
						title = "Ошибка";
					if (jqXHR.status == 404) {
						message = "<b>Запрашиваемая страница не найдена</b>";
						title = "404 ошибка";
					}
					if (textStatus === "error") {
						message += "<br>[ <b>" + jqXHR.status + "</b> ]  " + errorThrown;
					}
					show_error_alert(message, title);
				}
			});
		});
	});
	</script>
<?php
	$link = Route::url('modules', array(
		'controller' => 'photo',
		'action'     => 'category',
		'id'         => Request::current()->param('id'),
		'query'      => Helper_Page::make_query_string(array(
			'pid' => $MODULE_PAGE_ID,
			'cid' => $category_id,
		)),
	));

	echo $paginator->render( $link );
endif;
