<?php defined('SYSPATH') or die('No direct script access.'); 

	if (empty($elemenets)) {
		return;
	}

?>


	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="isotope col-3 clearfix">
<?php
				foreach ($elemenets as $_elements): 
					foreach ($_elements as $_item): 
				
?>				
						<div class="isotope-item">
							<div class="blog-article wow fadeIn">
<?php
							if ( ! empty($_item['image']) AND is_array($_item['image'])):
?>						
								<div class="blog-article-thumbnail"> 
									<img src="<?php echo $MEDIA; ?>images/blog/image-8.jpg" alt="">                                
									<div class="blog-article-hover">
										<a class="fancybox-blog-gallery zoom-action" href="images/blog/image-8.jpg">
											<i class="fa fa-eye"></i>
										</a>
									</div>
								</div>
<?php
							endif; 
?>							
								<div class="blog-article-details">
									<h6>
<?php
										echo date('Y-m-d H:i', strtotime($_item['public_date']));
?>								
									</h6>
									<h4>
<?php
									if ( ! empty($_item['link'])) {
										echo HTML::anchor($_item['link'], $_item['title']);
									} else {
										echo '<strong>', HTML::chars($_item['title']), '</strong>';
									}
?>								
									</h4>
<?php
									switch ($_item['element_type']) {
										case 'news':
											echo '<p>', HTML::anchor($_item['list_link'], __('News')),  '</p>';
											break;
										case 'actions':
											echo '<p>', HTML::anchor($_item['list_link'], __('Action')),  '</p>';
											break;
										case 'blog':
											echo '<p>', HTML::anchor($_item['list_link'], __('Blog')),  '</p>';
											break;
									}
?>									
								</div>
<?php
								if ( ! empty($_item['announcement'])) {
									echo '<div class="blog-text">', $_item['announcement'], '</div>';
								}
	
								if ( ! empty($_item['link'])) {
									echo HTML::anchor($_item['link'], __('Read more'), array(
										'class' => 'btn btn-default'
									));
								}
?>							
							</div>
						</div>
<?php
					endforeach; 
				endforeach; 
?>					
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	s.initList.push(function(){
		$(".isotope").imagesLoaded( function() {
			var $holder = $(".isotope");
			$holder.isotope({
				itemSelector: '.isotope-item',
				layoutMode: 'masonry',
				transitionDuration: '0.8s'
			});
			
			$("body").resize(function () {
				$holder.isotope();
			});
		});
	});
	</script>