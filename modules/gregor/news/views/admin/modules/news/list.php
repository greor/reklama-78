<?php defined('SYSPATH') or die('No direct access allowed.');

	$query_array = array(
		'pid' => $MODULE_PAGE_ID
	);
	$query = Helper_Page::make_query_string($query_array);
	$up_link = 	Route::url('modules', array(
		'controller' => 'news',
		'query'      => $query
	));
?>
	<div class="row">
		<div class="span5">
			<form class="form-inline" action="<?php echo Request::current()->url(); ?>" method="get">
<?php
				$filter_query = Request::current()->query('filter');
				echo Form::input('filter[title]', $filter_query['title']);
?>
				<button class="btn" type="submit"><i class="icon-search">&nbsp;</i></button>
				<button class="btn btn-clear" type="submit"><i class="icon-trash">&nbsp;</i></button>
				<script>
				$(function(){
					$('.btn-clear').click(function(e){
						$(e.currentTarget)
							.closest('.form-inline')
								.find(':input')
									.not(':button, :submit, :reset, :hidden')
									.val('')
									.removeAttr('checked')
									.removeAttr('selected');
					});
				});
				</script>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="span9">
			<ul class="breadcrumb kr-breadcrumb">
				<li>
					<i class="icon-folder-open"></i>
					<a href="<?php echo $up_link; ?>"><?php echo __('Category up')?></a>
					<span class="divider">/</span>
				</li>
<?php 
				if ($breadcrumbs->loaded()) {
					echo '<li class="active">', HTML::chars($breadcrumbs->title), '</li>';
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
			'controller' => 'news',
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
			'controller' => 'news',
			'action'     => 'edit',
			'id'         => '{id}',
			'query'      => $query
		));
?>

		<table class="table table-bordered table-striped">
			<colgroup>
				<col class="span1">
				<col class="span6">
				<col class="span2">
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
			foreach ($elements as $item):
?>
			<tr>
				<td><?php echo $item->id ?></td>
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
		'controller' => 'news',
		'action'     => 'category',
		'id'         => Request::current()->param('id'),
		'query'      => Helper_Page::make_query_string(array(
			'pid' => $MODULE_PAGE_ID,
			'cid' => $category_id,
		)),
	));

	echo $paginator->render( $link );
endif;
