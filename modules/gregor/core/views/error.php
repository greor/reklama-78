<!DOCTYPE html>
<html>

<head>
	<title>Error - <?php echo HTML::chars($title),' [' ,$code, ']' ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head>

<body>
	<h1><?php echo $title ?> [ <?php echo $code ?> ]:</h1>
	<h2><?php echo HTML::chars($message) ?></h2>
</body>
</html>