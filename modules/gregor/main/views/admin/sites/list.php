<?php defined('SYSPATH') or die('No direct access allowed.');
	
	if ($sites->count() > 0):
		$edit_tpl = Route::url('admin', array(
			'controller' => 'sites',
			'action' => 'edit',
			'id' => '{id}',
		));
	
		$delete_tpl = Route::url('admin', array(
			'controller' => 'sites',
			'action' => 'delete',
			'id' => '{id}',
		));
		$url_base = URL::base();
?>
		<table class="table table-bordered table-striped kr-table-sites">
			<colgroup>
				<col class="span1">
				<col class="span6">
				<col class="span2">
			</colgroup>
			<thead>
			<tr>
				<th><?php echo __('ID'); ?></th>
				<th><?php echo __('Name'); ?></th>
				<th><?php echo __('Actions'); ?></th>
			</tr>
			</thead>
			<tbody>
<?php 
			foreach ($sites as $item):
?>
				<tr>
					<td class="kr-id"><?php echo $item->id; ?></td>
					<td class="kr-title">
<?php 
						if ( $ACL->is_allowed($USER, $item, 'edit') ) {
						
							$title = $item->name;
							if ( (bool) $item->active) {
								$title = '<i class="icon-eye-open"></i>&nbsp;'.$title;
							}
							echo HTML::anchor(str_replace('{id}', $item->id, $edit_tpl), $title);
							
						} else {
							if ( (bool) $item->active) {
								echo '<i class="icon-eye-open"></i>';
							}
							echo HTML::chars($item->name);
						} 
?>
					</td>
					<td class="kr-action">
<?php 
					if ($ACL->is_allowed($USER, $item, 'edit')) {
						echo HTML::anchor(str_replace('{id}', $item->id, $edit_tpl), '<i class="icon-edit"></i>', array(
							'class' => 'btn edit_button',
							'title' => __('Edit'),
						));
					}
					if ($ACL->is_allowed($USER, $item, 'delete')) {
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
		$link = Route::url('admin', array(
			'controller' => 'sites',
		));
		echo $paginator->render( $link );
	endif;
?>

