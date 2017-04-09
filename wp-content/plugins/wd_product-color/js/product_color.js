/**
 * WD Products Color Filter
 *
 * @license commercial software
 * @copyright (c) 2013 Codespot Software JSC - WPDance.com. (http://www.wpdance.com)
 */



(function($) {
	
	jQuery.noConflict();
	
	jQuery(function ($) {
	
		$('.wd_pc_colorpicker').wpColorPicker(); 
	
		/************** Image Upload ****************/
		
		jQuery('.wd_pc_upload_image_button').click(function() {
			formfield = jQuery(this).siblings('.wd_pc_custom_image');  
			preview = jQuery(this).siblings('.wd_pc_preview_image');  
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');  
			window.send_to_editor = function(html) {  
				var _current_obj = jQuery('img',html);
				
				console.log(_current_obj.prop("tagName"));
				
				if( _current_obj.length <= 0 ){
					_current_obj = jQuery(html);
				}
				
				console.log(_current_obj.prop("tagName"));
					
				if( _current_obj.prop("tagName") == 'IMG' ){
					imgurl = _current_obj.attr('src');  
					classes = _current_obj.attr('class');  

					if(typeof classes !== 'undefined' && classes.length > 0 ){
						var id = classes.replace(/(.*?)wp-image-/, '');  
						
						var data = {
							action	: 'wd_pc_find_media_thumbnail',
							img_id 	: id
						};						
						jQuery.post(ajaxurl, data, function(response) {
							if( response.length > 0 && parseInt(response) != 0 ){
								imgurl = response;
							}
							preview.attr('src', imgurl);  
						});
						formfield.val(id);  
					}
				}else{
					alert("Please choose one image");
				}
				tb_remove();  
			}  
			return false;  
		});  	
		
		jQuery('.wd_pc_clear_image_button').click(function() {  
			jQuery(this).parent().siblings('.wd_pc_custom_image').val(' ');  
			jQuery(this).parent().siblings('.wd_pc_preview_image').attr('src', '');  
			return false;  
		});  		
		
	});
	
})(jQuery);

