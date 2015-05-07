<?php defined('SYSPATH') or die('No direct script access.'); 

	if (empty($elements))
		return;
?>
	<div class="bannercontainer">
		<div class="banner">
			<ul>
<?php
			foreach ($elements as $_item): 
?>				
				<li data-transition="slotfade-horizontal">
					<img src="<?php echo $_item['background'];?>" width="1920" height="635" alt="<?php echo HTML::chars($_item['title']); ?>">

<?php
					if (empty($_item['image'])): 
						if ( ! empty($_item['text'])): 
							$_data_x = empty($_item['settings']['text']['data-x']) ? '245' : $_item['settings']['text']['data-x'];
							$_data_y = empty($_item['settings']['text']['data-y']) ? '285' : $_item['settings']['text']['data-y'];
?>					
							<div class="tp-caption title-white text-center sft"  data-x="<?php echo $_data_x; ?>" data-y="<?php echo $_data_y; ?>" data-speed="700" data-start="1700" data-easing="easeOutBack">
<?php
								echo $_item['text']; 
?>							
							</div>
<?php
						endif;
						if ( ! empty($_item['url'])):
							$_data_x = empty($_item['settings']['url']['data-x']) ? '450' : $_item['settings']['url']['data-x'];
							$_data_y = empty($_item['settings']['url']['data-y']) ? '450' : $_item['settings']['url']['data-y'];
?>					
							<div class="tp-caption sfb"  data-x="<?php echo $_data_x; ?>" data-y="<?php echo $_data_y; ?>" data-speed="700" data-start="2200" data-easing="easeOutBack">
								<a class="btn btn-default" href="<?php echo $_item['url']; ?>" target="_blank">Узнать больше <i class="fa fa-arrow-right"></i></a>
							</div>
<?php
						endif;
					else: 
?>					
						<div class="tp-caption sfl"  data-x="0" data-y="75" data-speed="700" data-start="1700" data-easing="easeOutBack">
							<img src="<?php echo $_item['image']; ?>" width="786" height="449" alt="<?php echo HTML::chars($_item['title']); ?>">
						</div>
						<div class="tp-caption title sft" data-x="830" data-y="180" data-speed="700" data-start="2200" data-easing="easeOutBack">
<?php
						echo $_item['title']; 
?>						
						</div>
							
<?php
						if ( ! empty($_item['text'])): 
							$_data_x = empty($_item['settings']['text']['data-x']) ? '830' : $_item['settings']['text']['data-x'];
							$_data_y = empty($_item['settings']['text']['data-y']) ? '270' : $_item['settings']['text']['data-y'];
?>
							<div class="tp-caption text-white sfr" data-x="<?php echo $_data_x; ?>" data-y="<?php echo $_data_y; ?>" data-speed="700" data-start="2700" data-easing="easeOutBack">
<?php
								echo $_item['text']; 
?>
							</div>
<?php
						endif; 
?>							
<?php
						if ( ! empty($_item['url'])):
							$_data_x = empty($_item['settings']['url']['data-x']) ? '830' : $_item['settings']['url']['data-x'];
							$_data_y = empty($_item['settings']['url']['data-y']) ? '390' : $_item['settings']['url']['data-y'];
?>		
					
							<div class="tp-caption sfb" data-x="<?php echo $_data_x; ?>" data-y="<?php echo $_data_y; ?>" data-speed="700" data-start="3200" data-easing="easeOutBack">
								<a class="btn btn-default" href="<?php echo $_item['url']; ?>" target="_blank">Подробнее <i class="fa fa-arrow-right"></i></a>
							</div>
<?php
						endif;
					endif; 
?>					
				</li>
<?php
			endforeach; 
?>			

			</ul>
		</div>
	</div>
	<script type="text/javascript">
		s.initList.push(function(){
			$(".banner").revolution({
				delay: 9000,
				startwidth: 1170,
				startheight: 635,
				startWithSlide: 0,
				
				fullScreenAlignForce: "off",
				autoHeight: "off",
				minHeight: "off",
				
				shuffle: "off",
				
				onHoverStop: "on",
				
				thumbWidth: 100,
				thumbHeight: 50,
				thumbAmount: 3,
				
				hideThumbsOnMobile: "off",
				hideNavDelayOnMobile: 1500,
				hideBulletsOnMobile: "off",
				hideArrowsOnMobile: "off",
				hideThumbsUnderResoluition: 0,
				
				hideThumbs: 0,
				hideTimerBar: "on",
				
				keyboardNavigation: "on",
				
				navigationType: "bullet",
				navigationArrows: "solo",
				navigationStyle: "round",
				
				navigationHAlign: "center",
				navigationVAlign: "bottom",
				navigationHOffset: 0,
				navigationVOffset: 30,
				
				soloArrowLeftHalign: "left",
				soloArrowLeftValign: "center",
				soloArrowLeftHOffset: 20,
				soloArrowLeftVOffset: 0,
				
				soloArrowRightHalign: "right",
				soloArrowRightValign: "center",
				soloArrowRightHOffset: 20,
				soloArrowRightVOffset: 0,
				
				
				touchenabled: "off",
				swipe_velocity: "0.7",
				swipe_max_touches: "1",
				swipe_min_touches: "1",
				drag_block_vertical: "false",
				
				stopAtSlide: -1,
				stopAfterLoops: -1,
				hideCaptionAtLimit: 0,
				hideAllCaptionAtLilmit: 0,
				hideSliderAtLimit: 0,
				
				dottedOverlay: "none",
				
				spinned: "spinner4",
				
				fullWidth: "off",
				forceFullWidth: "off",
				fullScreen: "off",
				fullScreenOffsetContainer: "#topheader-to-offset",
				fullScreenOffset: "0px",
				
				panZoomDisableOnMobile: "off",
				
				simplifyAll: "off",
				
				shadow: 0
			});
		});
	</script>