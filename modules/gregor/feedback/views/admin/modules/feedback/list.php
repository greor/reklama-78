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
	if ( $feedback->count() > 0): 
		$query_array = array(
			'pid' => $MODULE_PAGE_ID
		);
		$p = Request::current()->query( Paginator::QUERY_PARAM );
		if ( ! empty($p)) {
			$query_array[ Paginator::QUERY_PARAM ] = $p;
		}
		$open_tpl = Route::url('modules', array(
			'controller' => 'feedback',
			'action'     => 'view',
			'id'         => '{id}',
			'query'      => Helper_Page::make_query_string($query_array),
		));
?>

		<table class="table table-bordered table-striped kr-table-news-cats">
			<colgroup>
				<col class="span1">
				<col class="span6">
				<col class="span2">
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('ID'); ?></th>
					<th><?php echo __('Sended'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
			foreach ($feedback as $item):
?>
				<tr class="<?php echo $item->new ? 'gray' : ''; ?>">
					<td class="kr-id"><?php echo $item->id ?></td>
					<td class="kr-sended"><?php echo HTML::chars($item->created) ?></td>
					<td class="kr-action">
<?php 
						echo HTML::anchor(str_replace('{id}', $item->id, $open_tpl), '<i class="icon-folder-open"></i>', array(
							'class' => 'btn',
							'title' => __('View'),
						));
?>					
					</td>
				</tr>
<?php 
			endforeach;
?>
			</tbody>
		</table>
<?php
		$query_array = array(
			'pid' => $MODULE_PAGE_ID
		);
		$link = Route::url('modules', array(
			'controller' => 'feedback',
			'query'      => Helper_Page::make_query_string($query_array),
		));
		echo $paginator->render( $link );
	endif;

