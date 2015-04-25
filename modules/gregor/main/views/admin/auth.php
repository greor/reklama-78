<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $TITLE; ?></title>

	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/bootstrap/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/bootstrap/css/bootstrap-responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>css/admin.css" type="text/css" />

	<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jquery/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/bootstrap/js/bootstrap.js"></script>
	<style>
		html {height: 100%;}
	</style>
</head>
<body class="auth">
	<div class="container">
		<div class="row">
			<div class="span4">
				<div class="central">
					<img class="start_logo" src="<?php echo $MEDIA.$logo['src']; ?>" />
					<div class="well">
						<form action="" method="post" class="auth">
							<h1><?php echo __('Authentication'); ?></h1>
<?php 
							if (isset($error)):
?>
								<div style="color:red;"><?php echo HTML::chars($error); ?></div><br>
<?php 
							endif;
?>
							<label for="login"><?php echo __('Login'); ?></label>
							<input class="input" type="text" name="login" id="login" /><br>
							<label for="password"><?php echo __('Password'); ?></label>
							<input class="input" type="password" name="password" id="password" /><br>
							<div style="float:left;padding-top:5px;">
								<label for="remember" class="auth-remember"><?php echo __('Remember me'); ?></label>
								<input type="checkbox" name="remember" id="remember" />
							</div>
							<input class="btn btn-large btn-primary" type="submit" name="submit" value="<?php echo __('Sign in'); ?>" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
