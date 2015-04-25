<?php defined('SYSPATH') or die('No direct access allowed.');

	$orm = $wrapper->orm();

	$labels = $orm->labels();
	$required = $orm->required_fields();

	if ( $orm->loaded() ) {
		$action = Route::url('admin', array(
			'controller' => 'admins',
			'action'     => 'edit',
			'id'         => $orm->id,
		));
	} else {
		$action = Route::url('admin', array(
			'controller' => 'admins',
			'action'     => 'edit',
		));
	}

	echo View_Admin::factory('layout/error')->set('errors', $errors);
?>
	<form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" class="form-horizontal kr-form-horizontal">
<?php

/**** active ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'active',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::hidden('active', '').Form::checkbox('active', '1', (bool) $orm->active, array(
				'id' => 'active_field',
			)),
		));

/**** role ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'role',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::select('role', $roles, $orm->role, array(
				'id'    => 'role_field',
				'class' => 'input-xlarge',
			)),
		));
		
/**** email ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'email',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('email', $orm->email, array(
				'id'    => 'email_field',
				'class' => 'input-xlarge',
			)),
		));
		
/**** username ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'username',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::input('username', $orm->username, array(
				'id'    => 'username_field',
				'class' => 'input-xlarge',
			)),
		));

/**** password ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'password',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::password('password', '', array(
				'id'    => 'password_field',
				'class' => 'input-xlarge',
			)),
		));

/**** password_confirm ****/

		echo View_Admin::factory('form/wrapper', array(
			'field'    => 'password_confirm',
			'errors'   => $errors,
			'labels'   => $labels,
			'required' => $required,
			'controls' => Form::password('password_confirm', '', array(
				'id'    => 'password_confirm_field',
				'class' => 'input-xlarge',
			)),
		));
?>

		<div class="form-actions">
			<button class="btn btn-primary" type="submit" name="submit" value="save" ><?php echo __('Save'); ?></button>
			<button class="btn btn-primary" type="submit" name="submit" value="save_and_exit" ><?php echo __('Save and Exit'); ?></button>
			<button class="btn" name="cancel" value="cancel"><?php echo __('Cancel'); ?></button>
		</div>
	</form>
