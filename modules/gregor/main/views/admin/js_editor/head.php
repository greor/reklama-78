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
	$('textarea.text_editor_br').each(function(i, e){
		CKEDITOR.replace(e,{
	        filebrowserUploadUrl : '/uploader?type=Files',
	        width : 640,
	        height: 500,
	        enterMode: CKEDITOR.ENTER_BR
	    });
	});
});
</script>