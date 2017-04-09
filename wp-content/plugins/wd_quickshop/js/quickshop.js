/**
 * WD QuickShop
 *
 * @license commercial software
 * @copyright (c) 2013 Codespot Software JSC - WPDance.com. (http://www.wpdance.com)
 */


var qs = null;
var wd_qs_prettyPhoto = null;
(function($) {
	"use strict";

	// disable QuickShop:
	//if(jQuery('body').innerWidth() <1000)
	//	EM_QUICKSHOP_DISABLED = true;

	jQuery.noConflict();
	jQuery(function ($) {
			//insert quickshop popup
			function qs_prettyPhoto(){
				 $('.em_quickshop_handler').prettyPhoto({
					deeplinking: false
					,opacity: 0.9
					,social_tools: false
					,default_width: 900
					,default_height:450
					,theme: 'pp_woocommerce'
					,changepicturecallback : function(){
						$("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );
						$('.pp_inline').find('form.variations_form').wc_variation_form();
						$('.pp_inline').find('form.variations_form .variations select').change();
						jQuery('body').trigger('wc_fragments_loaded');	
						
						$('.pp_woocommerce').addClass('loaded');

						var $_this = jQuery('#wd_quickshop_wrapper');
						
						var owl = $_this.find('.qs-thumbnails').owlCarousel({
								items : 4
								,loop : true
								,nav : false
								,dots : false
								,navSpeed : 1000
								,slideBy: 1
								,margin:10
								,rtl:jQuery('body').hasClass('rtl')
								,navRewind: false
								,autoplay: false
								,autoplayTimeout: 5000
								,autoplayHoverPause: false
								,autoplaySpeed: false
								,mouseDrag: true
								,touchDrag: true
								,responsiveBaseElement: $_this
								,responsiveRefreshRate: 400
								,onInitialized: function(){
									$_this.addClass('loaded').removeClass('loading');
								}
							});
							$_this.on('click', '.next', function(e){
								e.preventDefault();
								owl.trigger('next.owl.carousel');
							});

							$_this.on('click', '.prev', function(e){
								e.preventDefault();
								owl.trigger('prev.owl.carousel');
							});
					}
				});
			}
			qs_prettyPhoto();
			wd_qs_prettyPhoto = qs_prettyPhoto;
			
		function hide_element( jquery_obj ){
			/*TweenMax.to( jquery_obj , 0, {	css:{
													//opacity : 0
													//,display : 'none'
												}			
											,ease:Power2.easeInOut
										}
						);*/
		}
		
		
		// quickshop init
		function _qsJnit() {
			var selectorObj = arguments[0];
			var listprod = $(selectorObj.itemClass);	// selector chon tat ca cac li chua san pham tren luoi
			var baseUrl = '';
			
			listprod.live('mouseover',function(){
				var _ul_prods = $(this).parents("ul.products");
				var _div_prods = $(this).parents("div.products");
				var has_quickshop = true;
				if( typeof _ul_prods !== "undefined" ){
					has_quickshop = (_ul_prods.hasClass('no_quickshop') == false);
				}else{
					has_quickshop = (_div_prods.hasClass('no_quickshop') == false);
				}
				if( has_quickshop ){
					var qsHandlerImg = $(this).find('.em_quickshop_handler img');
					var qsHandler = $(this).find('.em_quickshop_handler');
					
					var _ajax_uri = _qs_ajax_uri + "?ajax=true&action=load_product_content&product_id="+jQuery(this).siblings(selectorObj.inputClass).val();
					qsHandler.attr('href', _ajax_uri );
					
					qsHandler.css({
										top: Math.round(( $(this).height() - qsHandler.height() )/2) +'px'
										,left:  Math.round(( $(this).width() - qsHandler.width() )/2)  +'px'
									});
				}
			});
			
			listprod.live('mouseout',function(){
				//hide_element($(value).find('.em_quickshop_handler'));
			});	
			
			$('#real_quickshop_handler').click(function(event){
				event.preventDefault();
			});

			$('.wd_quickshop.product').live('mouseover',function(){
				if( !$(this).hasClass('active') ){
					$(this).addClass('active');
					$('#qs-zoom,.wd-qs-cloud-zoom,.cloud-zoom, .cloud-zoom-gallery').CloudZoom({});	

				}
			});
			
		}

		if (typeof EM_QUICKSHOP_DISABLED == 'undefined' || !EM_QUICKSHOP_DISABLED)
		
			/*************** Disable QS in Main Menu *****************/
			jQuery('ul.menu').find('ul.products').addClass('no_quickshop');
			/*************** Disable QS in Main Menu *****************/		
		
			_qsJnit({
				itemClass		: '.products li.product.type-product.status-publish  .product_thumbnail_wrapper,.products div.product.type-product.status-publish  .product_thumbnail_wrapper' //selector for each items in catalog product list,use to insert quickshop image
				,inputClass		: 'input.hidden_product_id' //selector for each a tag in product items,give us href for one product
			});
			qs = _qsJnit;
	});
})(jQuery);

