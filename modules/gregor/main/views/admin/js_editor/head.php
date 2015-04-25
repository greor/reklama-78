<?php defined('SYSPATH') or die('No direct access allowed.'); ?>

<script language="JavaScript">
$(document).ready(function() {
	$('textarea.text_editor').each(function(i, e){
		CKEDITOR.replace(e,{
	        filebrowserUploadUrl : '/uploader?type=Files',
	        width : 640,
	        height: 500
	    });
	});
});
</script>