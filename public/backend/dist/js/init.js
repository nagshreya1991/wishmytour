/** *************Init JS*********************
	
    TABLE OF CONTENTS
	---------------------------
	1.Ready function
	2.Load function
	3.Full height function
	4.flintstone function
	5.Chat App function
	6.Resize function
 ** ***************************************/
 
 "use strict"; 
/*****Ready function start*****/
$(document).ready(function(){
	flintstone();
	$('.preloader-it > .la-anim-1').addClass('la-animate');
});
/*****Ready function end*****/

/*****Load function start*****/
$(window).load(function(){
	$(".preloader-it").delay(500).fadeOut("slow");
	/*Progress Bar Animation*/
	var progressAnim = $('.progress-anim');
	if( progressAnim.length > 0 ){
		for(var i = 0; i < progressAnim.length; i++){
			var $this = $(progressAnim[i]);
			$this.waypoint(function() {
			var progressBar = $(".progress-anim .progress-bar");
			for(var i = 0; i < progressBar.length; i++){
				$this = $(progressBar[i]);
				$this.css("width", $this.attr("aria-valuenow") + "%");
			}
			}, {
			  triggerOnce: true,
			  offset: 'bottom-in-view'
			});
		}
	}
});
/*****Load function* end*****/

/***** Full height function start *****/
var setHeightWidth = function () {
	var height = $(window).height();
	var width = $(window).width();
	$('.full-height').css('height', (height));
	$('.page-wrapper').css('min-height', (height));
	

	
	/*Vertical Tab Height Cal Start*/
	var verticalTab = $(".vertical-tab");
	if( verticalTab.length > 0 ){ 
		for(var i = 0; i < verticalTab.length; i++){
			var $this =$(verticalTab[i]);
			$this.find('ul.nav').css(
			  'min-height', ''
			);
			$this.find('.tab-content').css(
			  'min-height', ''
			);
			height = $this.find('ul.ver-nav-tab').height();
			$this.find('ul.nav').css(
			  'min-height', height + 40
			);
			$this.find('.tab-content').css(
			  'min-height', height + 40
			);
		}
	}
	/*Vertical Tab Height Cal End*/
};
/***** Full height function end *****/

/***** flintstone function start *****/
var $wrapper = $(".wrapper");
var flintstone = function(){
	
	/*Counter Animation*/
	var counterAnim = $('.counter-anim');
	if( counterAnim.length > 0 ){
		counterAnim.counterUp({ delay: 10,
        time: 1000});
	}
	
	/*Tooltip*/
	if( $('[data-toggle="tooltip"]').length > 0 )
		$('[data-toggle="tooltip"]').tooltip();
	
	/*Popover*/
	if( $('[data-toggle="popover"]').length > 0 )
		$('[data-toggle="popover"]').popover()
	
	
	/*Sidebar Collapse Animation*/
	var sidebarNavCollapse = $('.fixed-sidebar-left .side-nav  li .collapse');
	var sidebarNavAnchor = '.fixed-sidebar-left .side-nav  li a';
	$(document).on("click",sidebarNavAnchor,function (e) {
		if ($(this).attr('aria-expanded') === "false")
				$(this).blur();
		$(sidebarNavCollapse).not($(this).parent().parent()).collapse('hide');
	});

	/*Sidebar Navigation*/
	$(document).on('click', '#toggle_nav_btn,#open_right_sidebar,#setting_panel_btn', function (e) {
		$(".dropdown.open > .dropdown-toggle").dropdown("toggle");
		return false;
	});
	$(document).on('click', '#toggle_nav_btn', function (e) {
		$wrapper.removeClass('open-right-sidebar open-setting-panel').toggleClass('slide-nav-toggle');
		return false;
	});
	
	/*Refresh Init Js*/
	var refreshMe = '.refresh';
	$(document).on("click",refreshMe,function (e) {
		var panelToRefresh = $(this).closest('.panel').find('.refresh-container');
		var dataToRefresh = $(this).closest('.panel').find('.panel-wrapper');
		var loadingAnim = panelToRefresh.find('.la-anim-1');
		panelToRefresh.show();
		setTimeout(function(){
			loadingAnim.addClass('la-animate');
		},100);
		function started(){} //function before timeout
		setTimeout(function(){
			function completed(){} //function after timeout
			panelToRefresh.fadeOut(800);
			setTimeout(function(){
				loadingAnim.removeClass('la-animate');
			},800);
		},1500);
		  return false;
	});
	
	/*Fullscreen Init Js*/
	$(document).on("click",".full-screen",function (e) {
		$(this).parents('.panel').toggleClass('fullscreen');
		$(window).trigger('resize');
		return false;
	});
	
	/*Nav Tab Responsive Js*/
	$(document).on('show.bs.tab', '.nav-tabs-responsive [data-toggle="tab"]', function(e) {
		var $target = $(e.target);
		var $tabs = $target.closest('.nav-tabs-responsive');
		var $current = $target.closest('li');
		var $parent = $current.closest('li.dropdown');
			$current = $parent.length > 0 ? $parent : $current;
		var $next = $current.next();
		var $prev = $current.prev();
		$tabs.find('>li').removeClass('next prev');
		$prev.addClass('prev');
		$next.addClass('next');
		return;
	});
};
/***** flintstone function end *****/



var boxLayout = function() {
	if((!$wrapper.hasClass("rtl-layout"))&&($wrapper.hasClass("box-layout")))
		$(".box-layout .fixed-sidebar-right").css({right: $wrapper.offset().left + 300});
		else if($wrapper.hasClass("box-layout rtl-layout"))
			$(".box-layout .fixed-sidebar-right").css({left: $wrapper.offset().left});
}
boxLayout();	

/**Only For Setting Panel Start**/

/*Fixed Slidebar*/
var fixedHeader = function() {
	if($(".setting-panel #switch_3").is(":checked")) {
		$wrapper.addClass("scrollable-nav");
	} else {
		$wrapper.removeClass("scrollable-nav");
	}
};
fixedHeader();	
$(document).on('change', '.setting-panel #switch_3', function () {
	fixedHeader();
	return false;
});	

/*Theme Color Init*/
$(document).on('click', '.theme-option-wrap > li', function (e) {
	$(this).addClass('active-theme').siblings().removeClass('active-theme');
	$wrapper.removeClass (function (index, className) {
		return (className.match (/(^|\s)theme-\S+/g) || []).join(' ');
	}).addClass($(this).attr('id')+'-active');
	return false;	
});

/*Primary Color Init*/
var primaryColor = 'input:radio[name="radio-primary-color"]';
if( $('input:radio[name="radio-primary-color"]').length > 0 ){
	$(primaryColor)[0].checked = true;
	$(document).on('click',primaryColor, function (e) {
		$wrapper.removeClass (function (index, className) {
			return (className.match (/(^|\s)pimary-color-\S+/g) || []).join(' ');
		}).addClass($(this).attr('id'));
		return;
	});
}

/*Reset Init*/
$(document).on('click', '#reset_setting', function (e) {
	$('.theme-option-wrap > li').removeClass('active-theme').first().addClass('active-theme');
	$wrapper.removeClass (function (index, className) {
		return (className.match (/(^|\s)theme-\S+/g) || []).join(' ');
	}).addClass('theme-1-active');
	if($(".setting-panel #switch_3").is(":checked"))
		$('.setting-panel .layout-switcher .switchery').trigger('click');
		$('#pimary-color-green').trigger('click');
	return false;	
});

	
/*Switchery Init*/
var elems = Array.prototype.slice.call(document.querySelectorAll('.setting-panel .js-switch'));
$('.setting-panel .js-switch').each(function() {
	new Switchery($(this)[0], $(this).data());
});

/*Only For Setting Panel end*/

/***** Resize function start *****/
$(window).on("resize", function () {
	setHeightWidth();
	boxLayout();
}).resize();
/***** Resize function end *****/

