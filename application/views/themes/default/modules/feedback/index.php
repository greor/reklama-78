<?php defined('SYSPATH') or die('No direct script access.');

	echo View_Theme::factory('layout/breadcrumbs');
	
?>

 	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="headline style-3">
					<h5><?php echo $SITE['title_tag']; ?></h5>
					<h2><?php echo HTML::chars($page['title']); ?></h2>
					<div class="page-text"><?php echo $page['text']; ?></div>
				</div>
			</div>
		</div>
	</div>
            
	<div class="container">
		<div class="row">
 			<div class="col-sm-offset-1 col-sm-6 form-holder">
				<form id="contact-form" name="contact-form" action="<?php echo $action_link; ?>" method="post">
					<fieldset>
<?php
					$msg = '';
					if ( ! empty($response)) {
						
						if ($response['success']) {
							$msg = '<div class="alert alert-success">Ваше сообщение отправлено!</div>';
						} else {
							
							foreach ($response['errors'] as $_row) {
								$msg .= '<div class="alert alert-danger">'.$_row.'</div>';
							}
						}
					} 
?>					
						<div id="alert-area"><?php echo $msg; ?></div>
 						<input class="col-xs-12" id="name" type="text" name="name" placeholder="<?php echo HTML::chars($labels['name']); ?>">
						<input class="col-xs-12" id="email" type="text" name="email" placeholder="<?php echo HTML::chars($labels['email']); ?>">
						<textarea class="col-xs-12" id="text" name="text" rows="8" cols="25" placeholder="<?php echo HTML::chars($labels['text']); ?>"></textarea>
						
						<div class="captcha-holder">
<?php
							echo Captcha::instance(); 
?>						
							<input class="col-3" id="captcha" type="text" name="captcha" placeholder="<?php echo HTML::chars($labels['captcha']); ?>">
						</div>
						
						<input class="btn btn-default" id="submit" type="submit" name="submit" value="<?php echo __('Submit');?>">
					</fieldset>
				</form>
			</div>
			<div class="col-sm-5 contacts-holder">
<?php
			echo View_Theme::factory('layout/blocks/contacts');
?>			
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	s.initList.push(function(){
		var $captchaHolder = $('.captcha-holder'),
			captchaLink = '/captcha/default';
		$captchaHolder.find('img').on('click', function(){
			var $this = $(this);
			$(this).attr('src', captchaLink+'?_rnd='+((new Date()).getTime()));
		});

		var $alertArea = $('#alert-area');;
		$('#contact-form').validate({
			rules: {
				name: {
					required: true
				},
				email: {
					required: true,
					email: true
				},
				text: {
					required: true,
					minlength: 10
				},
				captcha: {
					required: true
				},
			},
			messages: {
				name: {
					required: "Пожалуйста, укажите ваше имя"
				},
				email: {
					required: "Пожалуйста, укажите ваш email",
					email: "Пожалуйста, введите корректный e-mail"
				},
				text: {
					required: "Пожалуйста, введите текст сообщения!"
				},
				captcha: {
					required: "Пожалуйста, введите защитный код!"
				}
			},
				
			// SUBMIT //
			submitHandler: function(form) {
				var result,
					$form = $(form);
				
				$form.ajaxSubmit({
					type: "POST",
					dataType: "json",
					data: $form.serialize(),
					url: $form.attr('action'),
					success: function(data) {
						result = '';
						if (data.success) {
							result = '<div class="alert alert-success">Ваше сообщение отправлено!</div>';
							$form.clearForm();
						} else {
							for (key in data.errors) {
								result += '<div class="alert alert-danger">'+data.errors[key]+'</div>';
							}
						}
						$alertArea.html(result);
					},
					error: function() {
						result = '<div class="alert alert-danger">Ошибка отправки сообщения, обратитесь к администратору!</div>';
						$alertArea.html(result);
					}
				});
			}
		});
		
	});

	</script>
