<?php defined('SYSPATH') or die('No direct script access.'); ?>

	<section class="full-section parallax" id="section-9">
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<div class="counter">
						<i class="miu-icon-other_conversation_review_comment_bubble_talk_outline_stroke"></i>
						<div class="counter-value" data-value="139"></div>
						<div class="counter-details">
							<p>Довольных клиентов</p>
						</div>
					</div>
				</div>
				
				<div class="col-sm-3">
					<div class="counter">
						<i class="miu-icon-editor_alarm_clock_outline_stroke"></i>
						<div class="counter-value" data-value="24"></div>
						<div class="counter-details">
							<p>Часов в день</p>
						</div>
					</div>
				</div>
				
				<div class="col-sm-3">
					<div class="counter">
						<i class="miu-icon-editor_setting_gear_outline_stroke"></i>
						<div class="counter-value" data-value="1127"></div>
						<div class="counter-details">
							<p>Проектов</p>
						</div>
					</div>
				</div>
				
				<div class="col-sm-3">
					<div class="counter">
						<i class="miu-icon-editor_documents_files_outline_stroke"></i>
						<div class="counter-value" data-value="253"></div>
						<div class="counter-details">
							<p>Сотрудников</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<script type="text/javascript">
	s.initList.push(function(){
		$(window).scroll(function(){
			$(".counter .counter-value:in-viewport").each(function() {
				var $this = $(this);
				if ( ! $this.hasClass("animated")) {
					$this.addClass("animated");
					$this.jQuerySimpleCounter({
						start: 0,
						end: $this.attr("data-value"),
						duration: 2000
					});	
				}
			});
		});
	});
	</script>
