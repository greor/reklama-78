<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<?php
	$is_error = FALSE;
	$is_require = FALSE;

	if ( isset( $errors[ $type_field ] ) OR isset( $errors[ $data_field ] ) )
	{
		$is_error = TRUE;
	}
?>



<div class="control-group <?php if ( $is_error ) echo ' error' ?>">

	<label class="control-label" for="<?php echo $type_field; ?>_field">
		<?php
			echo __($labels[ $type_field ]),
				in_array($type_field, $required) ? '<span class="required">*</span>' : '';
		?> :
	</label>

	<div class="controls">
		<?php
			$_page_types = Kohana::$config->load('_pages.type');

			if ( ! $ACL->is_allowed($USER, $page, 'link_module'))
			{
				unset($_page_types['module']);
			}

			echo Form::select('type', $_page_types, $page->type, array(
				'id'    => 'type_field',
				'class' => 'input-xlarge',
			));
			echo Form::hidden('data', $page->data);

			$_empty_row = array( '-' => '' );

			echo Form::select('_modules', $_empty_row + $modules, '-', array(
				'id' => 'type_module',
				'class' => 'input-xlarge kr-hidden',
			));
			echo Form::select('_pages', $_empty_row + $pages_list, '-', array(
				'id' => 'type_page',
				'class' => 'input-xlarge kr-hidden',
			));
			echo Form::input('_redirect_url', '', array(
				'id'    => 'type_url',
				'class' => 'input-xlarge kr-hidden',
			));
		?>

		<?php if (isset($errors[ $type_field ])) echo '<p class="help-block">'.HTML::chars($errors[ $type_field ]).'</p>'; ?>
		<?php if (isset($errors[ $data_field ])) echo '<p class="help-block">'.HTML::chars($errors[ $data_field ]).'</p>'; ?>
	</div>

	<script>
		$(document).ready(function(){

			var _type_field = $('#type_field'),
				_container = _type_field.closest('.controls'),
				_data_field = $('input[name="data"]', _container);

			function _hide_controls()
			{
				$('.kr-hidden', _container).each(function(i, e){
					var _this = $(e);
					if (_this.is('input'))
					{
						_this.val('');
					}
					else if (_this.is('select'))
					{
						_this.find("option:first")
							.attr("selected", "selected");
					}
					_this.hide();
				});
			}

			$('.kr-hidden', _container).change(function(){
				var _this = $(this),
					_val = '';

				if (_this.is('input'))
				{
					_val = _this.val();
				}
				else if (_this.is('select'))
				{
					_val = $('option:selected', _this).val();
				}

				_data_field.val( _val );
			});

			_type_field.change(function(){
				_hide_controls();
				_data_field.val('-');

				var _type = $('option:selected', _type_field).val();
				$('#type_'+_type).show();

				if ( $('option:selected', _type_field).val() == 'url' )
				{
					$('input[name="uri"]').closest('.control-group').hide();
				}
				else
				{
					$('input[name="uri"]').closest('.control-group').show();
				}
			});


			_hide_controls();

			var _cur_type = $('option:selected', _container).val();
			var _cur_element = $('#type_'+_cur_type, _container);

			if (_cur_element.length)
			{
				var _val = '';
				if (_cur_element.is('input'))
				{
					_cur_element.val( _data_field.val() );
				}
				else if (_cur_element.is('select'))
				{
					_cur_element.find('option[value="' + _data_field.val() + '"]')
						.attr('selected', 'selected');
				}

				_cur_element.show();

				if ( $('option:selected', _type_field).val() == 'url' )
				{
					$('input[name="uri"]').closest('.control-group').hide();
				}
				else
				{
					$('input[name="uri"]').closest('.control-group').show();
				}
			}
		});
	</script>
</div>