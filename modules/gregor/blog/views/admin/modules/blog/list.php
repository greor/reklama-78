<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

	<div class="row">
		<div class="span5">
			<form class="form-inline" action="<?php echo Request::current()->url(); ?>" method="get">
<?php
				$filter_query = Request::current()->query('filter');
				echo Form::input('filter[title]', $filter_query['title'], array(
					'placeholder' => HTML::chars(__('Title'))
				));
?>
				<button class="btn" type="submit"><i class="icon-search">&nbsp;</i></button>
				<button class="btn btn-clear" type="submit"><i class="icon-trash">&nbsp;</i></button>
				<script>
					$(function(){
						$('.btn-clear').click(function(e){
							$(e.currentTarget)
								.closest('.form-inline')
									.find(':input').not(':button, :submit, :reset, :hidden')
										.val('')
										.removeAttr('checked')
										.removeAttr('selected');
						});
					});
				</script>
			</form>
		</div>
	</div>

<?php
	if ( $elements->count() > 0 ): 
		$query_array = array();
		$delete_tpl = Route::url('modules', array(
			'controller' => 'blog',
			'action'     => 'delete',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
		));
	
		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$edit_tpl = Route::url('modules', array(
			'controller' => 'blog',
			'action'     => 'edit',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
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
					echo HTML::chars($item->title);
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
			'controller' => 'blog',
		));
		echo $paginator->render( $link );
	endif;
