/*
 *
 *		CUSTOM.JS
 *
 */

(function($){
	"use strict";
	
	s.initList = s.initList || [];
	s.dom = {};
	s.dom.body = $('body');
	
	s.subString = function(data, from, to){
		var start_pos = data.indexOf(from)+from.length,
			end_pos = data.lastIndexOf(to);
		if (start_pos > -1 && end_pos > start_pos) {
			return data.substring(start_pos, end_pos);
		}
		return false;
	};
	
	// DETECT TOUCH DEVICE //
	s.is_touch_device = function(){
		return !!('ontouchstart' in window) || ( !! ('onmsgesturechange' in window) && !! window.navigator.maxTouchPoints);
	};
	
	// SHOW/HIDE MOBILE MENU //
	s.show_hide_mobile_menu = function(){
		$("#mobile-menu-button").on("click", function(e) {
			e.preventDefault();
			$("#mobile-menu").slideToggle(300);
		});	
	};
	
	// MOBILE MENU //
	s.mobile_menu = function(){
		if ($(window).width() < 992) {
			if ($("#menu").length > 0) {
				if ($("#mobile-menu").length < 1) {
					$("#menu").clone().attr({
						id: "mobile-menu",
						class: ""
					}).insertAfter("header");
					$("#mobile-menu .megamenu > a").on("click", function(e) {
						e.preventDefault();
						$(this).toggleClass("open").next("div").slideToggle(300);
					});
					$("#mobile-menu .dropdown > a").on("click", function(e) {
						e.preventDefault();
						$(this).toggleClass("open").next("ul").slideToggle(300);
					});
				}
			}
		} else {
			$("#mobile-menu").hide();
		}
	};
	
	// SEARCH FORM // 
	s.search_form = function(context){
		var $button = context.find('.search-button'),
			$container = $button.siblings('#search-container');
		$button.on("click", function(e) { 
			e.preventDefault();
			$container.insertBefore("header");
			if( ! $button.hasClass("open")) {
				$container.slideDown(200).addClass("open");
				$button.addClass("open");
			} else {
				$container.slideUp(200).removeClass("open");
				$button.removeClass("open");	
			}
		});
	};
	
	// PROGRESS BARS // 
	s.progress_bars = function(){
		$(".progress .progress-bar:in-viewport").each(function() {
			if ( ! $(this).hasClass("animated")) {
				$(this).addClass("animated");
				$(this).animate({
					width: $(this).attr("data-width") + "%"
				}, 2000);
			}
		});
	};
	
	// CHARTS //
	s.pie_chart = function(){
		$(".pie-chart:in-viewport").each(function() {
			$(this).easyPieChart({
				animate: 1500,
				lineCap: "square",
				lineWidth: $(this).attr("data-line-width"),
				size: $(this).attr("data-size"),
				barColor: $(this).attr("data-bar-color"),
				trackColor: $(this).attr("data-track-color"),
				scaleColor: "transparent",
				onStep: function(from, to, percent) {
					$(this.el).find('.pie-chart-details .value').text(Math.round(percent));
				}
			});
		});
	};
	
	// COUNTER //
	s.counter = function(){
		$(".counter .counter-value:in-viewport").each(function() {
			if ( ! $(this).hasClass("animated")) {
				$(this).addClass("animated");
				$(this).jQuerySimpleCounter({
					start: 0,
					end: $(this).attr("data-value"),
					duration: 2000
				});	
			}
		});
	};
	
	// ANIMATE CHARTS //
	s.animate_charts = function(){
		$(".chart-container .animate-chart:in-viewport").each(function() {
			if( ! $(this).hasClass("animated")) {
				$(this).addClass("animated");
				
				// LINE CHART //
				var data = {
					labels : ["JAN 2014","","","","","","","AUG 2014","","","","","","FEB 2015"],
					datasets : [
						{
							fillColor: "transparent",
							strokeColor: "transparent",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [0,0,0,0,0,0,0,0,0,0,0,0,0,0]
						},
						{
							fillColor: "transparent",
							strokeColor: "transparent",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [100,100,100,100,100,100,100,100,100,100,100,100,100,100]
						},
						{
							fillColor: "transparent",
							strokeColor: "#3b3e43",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [35,60,30,40,30,55,45,75,45,55,35,55,45,60]
						},
						{
							fillColor: "transparent",
							strokeColor: "#a2a5ab",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [30,30,25,28,25,35,28,30,35,65,30,45,40,50]
						},
						{
							fillColor: "transparent",
							strokeColor: "#efdbbd",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [10,5,15,10,15,5,15,25,20,15,25,5,30,20]
						},
						{
							fillColor: "transparent",
							strokeColor: "#bca480",
							pointColor: "transparent",
							pointStrokeColor: "transparent",
							data : [15,18,21,15,20,15,5,20,15,18,21,15,20,35]
						}							
            		]
				}
				
				var line_chart = new Chart(document.getElementById("line-chart").getContext("2d")).Line(data, { 
					responsive: true,
					showTooltips: false,
					bezierCurve: false
				});
				
				// BAR CHARTS //
				var data = {
					labels: ["FIRST SEM", "SECOND SEM", "THIRD SEM"],
					datasets: [
						{
							label: "",
							fillColor: "transparent",
							strokeColor: "transparent",
							highlightFill: "transparent",
							highlightStroke: "transparent",
							data: [100, 100, 100]
						},
						{
							label: "",
							fillColor: "#3b3e43",
							strokeColor: "#3b3e43",
							highlightFill: "#3b3e43",
							highlightStroke: "#3b3e43",
							data: [65, 60, 55]
						},
						{
							label: "",
							fillColor: "#a2a5ab",
							strokeColor: "#a2a5ab",
							highlightFill: "#a2a5ab",
							highlightStroke: "#a2a5ab",
							data: [60, 50, 45]
						},
						{
							label: "",
							fillColor: "#efdbbd",
							strokeColor: "#efdbbd",
							highlightFill: "#efdbbd",
							highlightStroke: "#efdbbd",
							data: [50, 55, 85]
						},
						{
							label: "",
							fillColor: "#bca480",
							strokeColor: "#bca480",
							highlightFill: "#bca480",
							highlightStroke: "#bca480",
							data: [55, 75, 70]
						}
					]
				};
				
				var bar_charterfw  = new Chart(document.getElementById("bar-chart").getContext("2d")).Bar(data, { 
					responsive: true,
					showTooltips: false,
					barShowStroke: true,
					barStrokeWidth: 1,
					barValueSpacing: 5,
					barDatasetSpacing: 10
				});
				
				// PIE CHART
				var data = [					
					{
						value: 15,
						color: "#efdbbd",
						highlight: "#efdbbd",
						label: "#efdbbd"
					},
					{
						value: 50,
						color:"#3b3e43",
						highlight: "#3b3e43",
						label: "#3b3e43"
					},				
					{
						value: 15,
						color: "#a2a5ab",
						highlight: "#a2a5ab",
						label: "#a2a5ab"
					},										
					{
						value: 20,
						color: "#bca480",
						highlight: "#bca480",
						label: "#bca480"
					}
	            ];
				
				var pie_chart = new Chart(document.getElementById("pie-chart").getContext("2d")).Pie(data, { 
					responsive: true,
					showTooltips: false,
					animationSteps: 30
				});
			}
		});
	};
	
	// SHOW/HIDE GO TOP //
	s.show_hide_go_top = function(){
		if ($(window).scrollTop() > $(window).height()/2) {
			$("#go-top").fadeIn(300);
		} else {
			$("#go-top").fadeOut(300);
		}
	};
	
	// GO TOP //
	s.go_top = function(){
		$("#go-top").on("click", function () {
			$("html, body").animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	};
	
	// FULL SCREEN //
	s.full_screen = function(){
		$(".full-screen").css("height", $(window).height() + "px");
	};
	
	// ANIMATIONS //
	s.animations = function(){
		var animations = new WOW({
			boxClass: 'wow',
			animateClass: 'animated',
			offset: 100,
			mobile: false,
			live: true
		})
		animations.init();
	};
	
	// SMOOTH SCROLLING //
	s.smooth_scrolling = function(){
		$(".scrollspy-menu a").on("click", function (e) {
			var target = this.hash,
			$target = $(target);
			
			$("html, body").stop().animate( {
				'scrollTop': $target.offset().top
			}, 900, 'swing', function () {
				window.location.hash = target;
			});
		});
	};
	
	// FANCYBOX //
	s.fancybox = function(){
		$(".fancybox").fancybox({});
		$(".fancybox-portfolio-gallery").attr("rel","group").fancybox({
			prevEffect: 'none',
			nextEffect: 'none'
		});
		$(".fancybox-blog-gallery").attr("rel","group").fancybox({
			prevEffect: 'none',
			nextEffect: 'none'
		});
	};
	
	
	// DOCUMENT READY //
	$(document).ready(function(){
		
		// MENU //
		$(".menu").superfish({
			delay: 500,
			animation: {
				opacity: 'show',
				height: 'show'
			},
			speed: 'normal',
			cssArrows: false
		});
		
		// SCROLLSPY //
		$("body").scrollspy({
			target: '.scrollspy-menu'
		});
		
		// PLACEHOLDER //
		$("input, textarea").placeholder();
		
		//SHOW/HIDE MOBILE MENU //
		s.show_hide_mobile_menu();
		
		// MOBILE MENU //
		s.mobile_menu();
		
		// SHOW/HIDE GO TOP
		s.show_hide_go_top();
		
		// GO TOP //
		s.go_top();
		
		// ANIMATIONS //
		s.animations();
		
		// FULL SCREEN //
		s.full_screen();
		
		// SMOOTH SCROLLING
		s.smooth_scrolling();

		
		if (s.initList.length > 0) {
			for (var i = 0; i < s.initList.length; i++) {
				try {
					if (s[s.initList[i]]) {
						s[s.initList[i]].apply(s, [s.dom.body]);
					} else if ($.isFunction(s.initList[i])) {
						s.initList[i].apply(s, [s.dom.body]);
					}
				} catch (e) {;}
			}
			s.initList = [];
		}
		
	});

	
	// WINDOW SCROLL //
	$(window).scroll(function(){
		
		s.show_hide_go_top();
		
		if ($(this).scrollTop() > 200){  
			$("header").addClass("header-sticky");
		} else {
			$("header").removeClass("header-sticky");
		}
		
		if ($(this).scrollTop() > 200){  
			$(".elements-menu").addClass("elements-menu-sticky");
		} else {
			$(".elements-menu").removeClass("elements-menu-sticky");
		}
		
	});
	
	// WINDOW RESIZE //
	$(window).smartresize(function(e) {
		s.mobile_menu();
		s.full_screen();
	});
	
})(window.jQuery);