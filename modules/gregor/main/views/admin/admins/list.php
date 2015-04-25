<?php defined('SYSPATH') or die('No direct access allowed.');

	if ($admins->count() > 0): 
		$active_tpl = Route::url('admin', array(
			'controller' => 'admins',
			'action' => 'active',
			'id' => '{id}',
		));
	
		$edit_tpl = Route::url('admin', array(
			'controller' => 'admins',
			'action' => 'edit',
			'id' => '{id}',
		));
	
		$delete_tpl = Route::url('admin', array(
			'controller' => 'admins',
			'action' => 'delete',
			'id' => '{id}',
		));
?>

		<table class="table table-bordered table-striped kr-table-sites">
			<colgroup>
				<col class="span1">
				<col class="span2">
				<col class="span2">
				<col class="span1">
				<col class="span1">
				<col class="span2">
			</colgroup>
			<thead>
				<tr>
					<th><?php echo __('ID'); ?></th>
					<th><?php echo __('Login'); ?></th>
					<th><?php echo __('Role'); ?></th>
					<th><?php echo __('Logins'); ?></th>
					<th><?php echo __('Last login'); ?></th>
					<th><?php echo __('Actions'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php 
			foreach ($admins as $item):
				if ( (bool) $item->active) {
					$active_action_class = 'icon-eye-close';
					$active_action_title = __('Deactivate');
				} else {
					$active_action_class = 'icon-eye-open';
					$active_action_title = __('Activate');
				}
?>
				<tr>
					<td class="kr-id"><?php echo $item->id; ?></td>
					<td class="kr-title">
<?php 
					if ($ACL->is_allowed($USER, $item, 'edit')) {
						$_title = $item->username;
						if ( (bool) $item->active) {
							$_title = '<i class="icon-eye-open"></i>&nbsp;'.$_title;
						}
						echo HTML::anchor(str_replace('{id}', $item->id, $edit_tpl), $_title);
					} else {
						if ( (bool) $item->active) {
							echo '<i class="icon-eye-open"></i>&nbsp;';
						}
						echo HTML::chars($item->username);
					}
?>
					</td>
					<td class="kr-role"><?php echo $item->role; ?></td>
					<td class="kr-logins"><?php echo $item->logins; ?></td>
					<td class="kr-last-login"><?php echo $item->last_login ? date('Y-m-d H:i', $item->last_login) : '---' ?></td>
					<td class="kr-action">
<?php 
					if ( $ACL->is_allowed($USER, $item, 'edit') ) {
						if ( $USER->id != $item->id ) {
							echo HTML::anchor(str_replace('{id}', $item->id, $active_tpl), '<i class="'.$active_action_class.'"></i>', array(
								'class' => 'btn edit_button',
								'title' => $active_action_title,
 							));
						}
						
						echo HTML::anchor(str_replace('{id}', $item->id, $edit_tpl), '<i class="icon-edit"></i>', array(
							'class' => 'btn edit_button',
							'title' => __('Edit'),
						));
						
						if ( $USER->id != $item->id ) {
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
		$link = Route::url( 'admin', array(
			'controller' => 'admins',
		));
		echo $paginator->render( $link );
	endif;
?>
