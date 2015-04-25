<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
<head>
	<meta charset="utf-8">
	<title><?php echo HTML::chars($TITLE); ?></title>
	<meta name="keywords" content="<?php echo HTML::chars($PAGE_META['keywords_tag']) ?>" />
	<meta name="description" content="<?php echo HTML::chars($PAGE_META['description_tag']) ?>" />
<?php
	if ( ! empty($page_header)) {
		foreach ($page_header as $item) {
			echo "<{$item['tag']} {$item['attr']}>";
		}
	}
	if ( ! empty($og_tags)) {
		foreach ($og_tags as $k => $v) {
			echo "<meta property=\"og:{$k}\" content=\"{$v}\" />";
		}
	}
?>
</head>
<body>
<?php 
	echo $content; 
?>
</body>
</html>
