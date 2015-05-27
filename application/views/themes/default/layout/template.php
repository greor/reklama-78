<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">
	<meta name="keywords" content="<?php echo HTML::chars($PAGE_META['keywords_tag']) ?>">
	<meta name="description" content="<?php echo HTML::chars($PAGE_META['description_tag']) ?>">

	<title><?php echo HTML::chars($TITLE); ?></title>
    
	<link rel="shortcut icon" href="<?php echo $MEDIA; ?>images/icons/favicon.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo $MEDIA; ?>images/icons/apple-touch-57x57.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $MEDIA; ?>images/icons/apple-touch-72x72.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $MEDIA; ?>images/icons/apple-touch-114x114.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $MEDIA; ?>images/icons/apple-touch-144x144.png">

	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,cyrillic,latin-ext' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/css/bootstrap.min.css"> 
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/miuiconfont/miuiconfont.css">
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/js/animations/animate.min.css">
<?php
	foreach ($CSS as $_css) {
		echo HTML::style($_css);
	} 
?>
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/css/custom.css?v=2">
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/css/pages-style.css?v=2">
	
	<script type="text/javascript">
	var s = {};
	s.initList = [];
	</script>
</head>
<body>

	<div id="page-wrapper">
		<header>
			<div class="container">
				<div class="row">
					<div class="col-sm-3">
						<a id="logo" href="<?php echo URL::base(); ?>">
							<img src="<?php echo $MEDIA; ?>images/backgrounds/logo.jpg" alt="">
						</a>
						<span class="site-name"><?php echo $SITE['name']?></span>
					</div>
					<div class="col-sm-9">
<?php
// 						echo View_Theme::factory('layout/search');
						echo View_Theme::factory('menu/top', array('menu' => $menu));
?>
					</div>
				</div>
			</div>
		</header>
        <div class="content">
<?php 
		echo $content; 
?>
		</div>
        <footer>
<?php
			echo View_Theme::factory('layout/socials'); 
?>
			<div id="footer">
				<div class="container">
					<div class="row">
<?php
						echo View_Theme::factory('menu/bottom', array('menu' => $menu)); 
						echo View_Theme::factory('layout/blocks/about'); 
						
						
						echo '<div class="col-sm-3">', View_Theme::factory('layout/blocks/contacts'), '</div>'; 
?>
					</div>
				</div>
			</div>
<?php
			echo View_Theme::factory('layout/copyright'); 
?>	
        </footer>
    </div>
	<a id="go-top"><i class="miu-icon-circle_arrow-up_glyph"></i></a>

	<script src="<?php echo $MEDIA; ?>assets/js/jquery-2.1.3.min.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/smartresize.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/viewport/jquery.viewport.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/menu/hoverIntent.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/menu/superfish.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/placeholders/jquery.placeholder.min.js" type="text/javascript"></script>
	<script src="<?php echo $MEDIA; ?>assets/js/animations/wow.min.js" type="text/javascript"></script>
<?php
	foreach ($JS as $_js) {
		echo HTML::script($_js);
	} 
?>
	<script src="<?php echo $MEDIA; ?>assets/js/custom.js" type="text/javascript"></script>
</body>
</html>
