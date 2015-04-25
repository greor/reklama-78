<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<div class="control-group">
	<table class="kr-checkbox_table">
		<tr>
			<td><?php echo __($labels['day_0']); ?></td>
			<td><?php echo __($labels['day_1']); ?></td>
			<td><?php echo __($labels['day_2']); ?></td>
			<td><?php echo __($labels['day_3']); ?></td>
			<td><?php echo __($labels['day_4']); ?></td>
			<td><?php echo __($labels['day_5']); ?></td>
			<td><?php echo __($labels['day_6']); ?></td>
		</tr>
		<tr>
			<td>
				<input type="hidden" name="day_0" value="" />
				<input type="checkbox" name="day_0" <?php echo ($schedule->day_0 ? 'checked' : ''); ?> />
			</td>
			<td>
				<input type="hidden" name="day_1" value="" />
				<input type="checkbox" name="day_1" <?php echo ($schedule->day_1 ? 'checked' : ''); ?> />
			</td>
			<td>
				<input type="hidden" name="day_2" value="" />
				<input type="checkbox" name="day_2" <?php echo ($schedule->day_2 ? 'checked' : ''); ?> />
			</td>
			<td>
				<input type="hidden" name="day_3" value="" />
				<input type="checkbox" name="day_3" <?php echo ($schedule->day_3 ? 'checked' : ''); ?> />
			</td>
			<td>
				<input type="hidden" name="day_4" value="" />
				<input type="checkbox" name="day_4" <?php echo ($schedule->day_4 ? 'checked' : ''); ?> />
			</td>
			<td>
				<input type="hidden" name="day_5" value="" />
				<input type="checkbox" name="day_5" <?php echo ($schedule->day_5 ? 'checked' : ''); ?> />
			</td>
			<td>
				<input type="hidden" name="day_6" value="" />
				<input type="checkbox" name="day_6" <?php echo ($schedule->day_6 ? 'checked' : ''); ?> />
			</td>
		</tr>
	</table>
</div>