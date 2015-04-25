<?php defined('SYSPATH') OR die('No direct access allowed.');
?>
<?php
if ( ! empty($xml))
{
	echo ($xml === TRUE) ? '<?xml version="1.0"?>'."\n" : $xml."\n";
}
?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang ?>" lang="<?php echo $lang ?>">
<head>
<?php echo $head ?>

</head>
<?php echo $frameset ?>
</html>