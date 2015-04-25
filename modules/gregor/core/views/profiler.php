<style>
#kohana-stat {
	display: none;
	position: absolute;
	top: 0;
	left: 0;
	z-index: 998;
	background: #fff;
}
#kohana-stat-show {
	display: block;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 999;
	background: #fff;
	opacity: 0.7;
	padding: 5px;
	border: 1px solid #000;
	font-size: 12px;
	color: #000;
}
</style>
<a href="#" id="kohana-stat-show" title="Show/hide Kohana profiler">profiler</a>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#kohana-stat-show").click(function(){
		var div = $("#kohana-stat");
		div.toggleClass("show");
		if (div.hasClass("show"))
		{
			div.show();
			$(this).text("close [x]");
		}
		else
		{
			div.hide();
			$(this).text("profiler");
		}
		return false;
	});
});
</script>
<div id="kohana-stat"><?php echo View::factory('profiler/stats') ?></div>