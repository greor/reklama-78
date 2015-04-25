<style>
#kohana_error {
	position: absolute;
	top: 0;
	right: 50px;
	z-index: 998;
	background: #fff;
}
#kohana-error-show {
	display: block;
	position: fixed;
	top: 0;
	right: 0;
	z-index: 999;
	background: #fff;
	opacity: 0.7;
	padding: 5px;
	border: 1px solid #000;
	font-size: 12px;
	color: #000;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function(){
	var div = $("#kohana_error").addClass("show");
	if (div.length)
	{
		$('body').append(div)
			.prepend('<a href="#" id="kohana-error-show" title="Show/hide Kohana error">close [x]</a>');
		jQuery("#kohana-error-show").click(function(){
			div.toggleClass("show");
			if (div.hasClass("show"))
			{
				div.show();
				$(this).text("close [x]");
			}
			else
			{
				div.hide();
				$(this).text("error");
			}
			return false;
		});
	}
});
</script>
