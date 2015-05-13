<!doctype html>
<html>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0">

	<title><?php echo HTML::chars($TITLE); ?></title>
    
    <link rel="shortcut icon" href="<?php echo $MEDIA; ?>images/icons/favicon.png">
    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,cyrillic,latin-ext' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/css/bootstrap.min.css"> 
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/miuiconfont/miuiconfont.css">
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/js/animations/animate.min.css">
	
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/css/custom.css">
	<link rel="stylesheet" href="<?php echo $MEDIA; ?>assets/css/pages-style.css">
</head>

<body>
	<div id="page-wrapper">
		<header>
			<div class="container">
				<div class="row">
					<div class="col-sm-2">
						<a id="logo" href="<?php echo URL::base(); ?>">
							<img src="<?php echo $MEDIA; ?>images/backgrounds/logo.png" alt="">
						</a>
					</div>
				</div>
			</div>
		</header>
		<div class="content">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						<div class="headline style-3">
							<h2><?php echo $code; ?></h2>
							<p><?php echo $message; ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
