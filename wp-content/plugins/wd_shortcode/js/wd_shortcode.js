jQuery(document).ready(function($){
	"use strict";
	
	var _wd_shortcode_button_data;
	jQuery(".wd-shortcode-button").hover(
		function(){
			_wd_shortcode_button_data = jQuery(this).attr('style');
			jQuery(this).attr('style',jQuery(this).attr('data-style_hover'));
		},
		function(){
			jQuery(this).attr('style',_wd_shortcode_button_data);
		}
	);
	
	var _wd_shortcode_list_cats_data;
	jQuery('.wd_child_categories_shortcode .cat_button a').hover(
		function(){
			_wd_shortcode_list_cats_data = jQuery(this).attr('style');
			var _style_hover = jQuery(this).attr('data-hover');
			if( typeof _wd_shortcode_list_cats_data != 'undefined' && typeof _style_hover != 'undefined' )
				jQuery(this).attr('style',_style_hover);
		},
		function(){
			if( typeof _wd_shortcode_list_cats_data != 'undefined')
				jQuery(this).attr('style',_wd_shortcode_list_cats_data);
		}
	);
	
	/* Fix min-height VC tabs */
	$('.vc_tta-tabs .vc_tta-tabs-list .vc_tta-tab').bind('click', function(){
		wd_update_tab_content_min_height();
	});
	
	$(window).bind('load resize', function(){
		wd_update_tab_content_min_height();
	});
	
	function wd_update_tab_content_min_height(){
		setTimeout(function(){
			$('.vc_tta-tabs .vc_tta-panels').each(function(){
				$(this).find('.vc_tta-panel').css('min-height', 0);
				var min_height = $(this).find('.vc_tta-panel.vc_active').height();
				$(this).find('.vc_tta-panel').css('min-height', min_height);
			});
		}, 1000);
	}
	
	$(window).bind('load', function(){
		setTimeout(function(){
			$('.vc_tta-tabs ul > li.vc_tta-tab.vc_active > a').trigger('click');
		}, 200);
	});
});


jQuery.fn.wd_product_shortcode_load_more = function(atts){
	"use strict";
	
	if( _sc_ajax_uri.length == 0 )
		return;
	var ajax_url = _sc_ajax_uri;
	var _this = jQuery(this);
	_this.find('.btn_load_more').addClass('loading');
	jQuery.ajax({
		 type : "post",
		 dataType : "html",
		 url : ajax_url,
		 data : {action: "wd_product_shortcode_load_more",atts:atts},
		 error: function(xhr,err){
			
		 },
		 success: function(response) {
			_this.find('.featured_product_wrapper_inner ul.products').append(response);
			_this.find('.featured_product_wrapper_inner ul.products li').addClass('product');
			_this.find('.btn_load_more').attr('data-paged',++atts.paged);
			var is_end_page = _this.find('span.wd_flag_end_page').length > 0?true:false;
			if(is_end_page){
				_this.find('.btn_load_more').remove();
				_this.find('span.wd_flag_end_page').remove();
			}
			else{
				_this.find('.btn_load_more').removeClass('loading');
			}
			if( typeof qs == "function"){
				qs({
					itemClass		: '.products li.product.type-product.status-publish  .product_thumbnail_wrapper' 
					,inputClass		: 'input.hidden_product_id' 
				});
			}
			if( typeof wd_qs_prettyPhoto == 'function' )
				wd_qs_prettyPhoto();
			if( typeof wd_bind_added_to_cart == 'function' )
				wd_bind_added_to_cart();
			var columns = (atts.columns.length > 0)?atts.columns:4;
			_this.wd_product_shortcode_update_first_last_class(columns);
		 }
	  }); 
}
	
jQuery.fn.wd_product_shortcode_update_first_last_class = function(columns){
	"use strict";
	
	/* This is wrapper id (Random id) */
	var count = 0;
	var class_name = "";
	jQuery(this).find("ul.products li").removeClass("first last");
	jQuery(this).find("ul.products li").each(function(index,element){
		count++;
		if (count==1){
			class_name = "first";
		}
		else{
			if(count==columns){
				class_name = "last";
				count = 0;
			}
			else{
				class_name = "";
			}
		}
		jQuery(element).addClass(class_name);
		
	});
}

jQuery.fn.wd_shortcode_generate_product_slider = function(options, max_col){
	"use strict";
	
	var $_this = jQuery(this);
	if( typeof options === 'undefined' )
		options = {};
	if( typeof max_col === 'undefined' || parseInt(max_col) != max_col )
		max_col = 5;
		
	var defaults = {
		loop : true
		,nav : false
		,dots : false
		,navSpeed: 1000
		,rtl:jQuery('body').hasClass('rtl')
		,slideBy: 1
		,margin : 10
		,navRewind: false
		,autoplay: false
		,autoplayTimeout: 5000
		,autoplayHoverPause: true
		,autoplaySpeed: false
		,mouseDrag: true
		,touchDrag: true
		,responsiveBaseElement: $_this
		,responsiveRefreshRate: 400
		,responsive:{
					0:{
						items : 1
					},
					361:{
						items : 2
					},
					579:{
						items : 3
					},
					767:{
						items : 4
					},
					930:{
						items : max_col
					}
				}
		,onInitialized: function(){
			$_this.addClass('loaded').removeClass('loading');
			if( typeof wd_qs_prettyPhoto == 'function' )
				wd_qs_prettyPhoto();
		}
	};
	
	jQuery.extend(defaults, options);
	
	var owl = $_this.find('.products').owlCarousel(defaults);
	
	$_this.on('click', '.next', function(e){
		e.preventDefault();
		owl.trigger('next.owl.carousel');
	});

	$_this.on('click', '.prev', function(e){
		e.preventDefault();
		owl.trigger('prev.owl.carousel');
	});
	
	var in_wd_tab = $_this.parents('.tabbable').length > 0;
	var in_vc_tab = $_this.parents('.wpb_tabs').length > 0;
	var in_new_vc_tab = $_this.parents('.vc_tta-tabs').length > 0;
	
	if( in_wd_tab || in_vc_tab || in_new_vc_tab ){
		if( in_wd_tab ){
			var wd_tab = $_this.parents(".tabbable");
			wd_tab.find('ul.nav.nav-tabs > li').bind('click',function(){
				$_this.addClass('loading').removeClass('loaded');
				setTimeout(function(){
					var tab_id = wd_tab.find('ul.nav-tabs > li.active > a').attr('href');
					var carousel = jQuery(tab_id).find('.owl-carousel').data('owlCarousel');
					carousel._width = jQuery(tab_id).find('.owl-carousel').width();
					carousel.invalidate('width');
					carousel.refresh();
					$_this.addClass('loaded').removeClass('loading');
				},500);
			});
		}
		
		if( in_vc_tab ){
			var vc_tab = $_this.parents('.wpb_tabs');
			vc_tab.find('ul.wpb_tabs_nav > li > a').bind('click',function(){
				$_this.addClass('loading').removeClass('loaded');
				setTimeout(function(){
					var tab_id = vc_tab.find('ul.wpb_tabs_nav > li.ui-tabs-active > a').attr('href');
					var carousel = jQuery(tab_id).find('.owl-carousel').data('owlCarousel');
					carousel._width = jQuery(tab_id).find('.owl-carousel').width();
					carousel.invalidate('width');
					carousel.refresh();
					$_this.addClass('loaded').removeClass('loading');
				},500);
			});
		}
		
		if( in_new_vc_tab ){
			var vc_tab = $_this.parents('.vc_tta-tabs');
			vc_tab.find('ul > li.vc_tta-tab > a').bind('click',function(){
				$_this.addClass('loading').removeClass('loaded');
				setTimeout(function(){
					var tab_id = vc_tab.find('ul > li.vc_tta-tab.vc_active > a').attr('href');
					var carousel = jQuery(tab_id).find('.owl-carousel').data('owlCarousel');
					carousel._width = jQuery(tab_id).find('.owl-carousel').width();
					carousel.invalidate('width');
					carousel.refresh();
					$_this.addClass('loaded').removeClass('loading');
				},500);
			});
		}
		
	}
	
}