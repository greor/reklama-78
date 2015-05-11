<?php defined('SYSPATH') or die('No direct script access.'); 

	if (empty($paginator['next'])) {
		return;
	}
	
?>
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<p class="text-center">
				<!-- load-more -->
<?php
				echo HTML::anchor($paginator['next'], __('More'), array(
					'class' => 'btn btn-black load-more'
				)); 
?>				
				<!-- /load-more -->
				</p>
			</div>
		</div>
	</div>
	
<?php
	if ( ! $IS_AJAX): 
?>	
	<script type="text/javascript">
	s.initList.push(function(){
		$(".load-more").on("click", function(e) {
			e.preventDefault();
			var $this = $(this);
			$.ajax({
				type: "POST",
				url: $(".load-more").attr("href"),
				dataType: "html",
				cache: false,
				success: function(data) {
					var $loadMore = $(s.subString(data, '<!-- load-more -->', '<!-- /load-more -->'));

					if ($loadMore.length > 0) {
						$this.attr('href', $loadMore.attr('href'));
						$loadMore = null;
					} else {
						$this.hide();
					}
					
					$(".isotope").append(s.subString(data, '<!-- isotope -->', '<!-- /isotope -->'));	
					$(".isotope").imagesLoaded(function() {
						$(".isotope").isotope("reloadItems").isotope();
					});
				}
			});
		});
	});
	</script>
<?php
	endif;
	 
