<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $TITLE; ?></title>

	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/bootstrap/css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/bootstrap/css/bootstrap-responsive.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/jquery-ui-bootstrap/jquery-ui-1.8.16.custom.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/jquery-ui-timepicker/style.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>vendor/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>css/admin.css" type="text/css" />

	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jquery/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jquery-ui/jquery-ui-i18n.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jquery-ui/themeswitchertool.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jquery-ui-timepicker/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/flyout/jquery.flyout.min.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/ckeditor-4.6.6/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/paginator3000/paginator3000.js"></script>

	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/plupload/plupload.full.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/plupload/i18n/ru.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<script type="text/javascript" src="<?php echo $MEDIA; ?>vendor/jsrender.js"></script>
<?php 
	echo View_Admin::factory('js_editor/head'); 
?>

	<script type="text/javascript" src="<?php echo $MEDIA; ?>js/admin.js"></script>

</head>
<body class="<?php echo $BODY_CLASS; ?>">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="<?php echo URL::site() ?>">
<?php 
					echo HTML::chars($SITE['name']);
?>
				</a>
				<div class="nav-collapse">
					<ul class="nav">
<?php 
					foreach ($top_menu as $item):
						if ( $item['name'] != 'logout' AND ! $ACL->is_allowed( $USER, $item['name'], 'read' ) ) 
							continue;
?>
						<li class="<?php echo $item['class']; ?>">
							<a href="<?php echo $item['uri']; ?>">
<?php 
							echo HTML::chars($item['title']); 
?>
							</a>
						</li>
<?php 
					endforeach;
?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="container kr-container">
		<div class="row kr-main-row">
<?php 
			echo $content; 
?>
		</div>
	</div>
	<div id="footer">
		<div class="row">
			&copy; <?php echo date('Y')?>
		</div>
	</div>
</body>
</html>
