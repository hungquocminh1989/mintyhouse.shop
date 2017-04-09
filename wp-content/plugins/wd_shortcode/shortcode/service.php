<?php

if(!function_exists ('getSrcFromImage')){
	function getSrcFromImage($image=''){
		$retData = '';
		if(strlen($image)>0){
			$math = preg_match('/src="(.*?)"/ism',$image,$match);
			$retData = $match[1];
		}
		return $retData;
	}
}	

if(!function_exists ('ew_service_item')){
	function ew_service_item($atts,$content = false){
		wp_reset_query();
		global $post,$layout;
		if($post->post_type=='page' || $post->post_type=='page'){
			extract(shortcode_atts(array(
				'display_style'			=>	'service-style1',
				'items_id'	=>	''
			),$atts));
			//add_filter('the_content','ew_do_shortcode',1001);
			if(strlen($items_id)>0){
				$curService = get_post($items_id);
				$post_content = $curService->post_content;
				$post_title = $curService->post_title;
				
				$math = preg_match('/\[ew_listing.*?\[\/ew_listing\]/ism',$post_content,$match);
				$serviceListing = '';
				if($math){
					$serviceListing = $match[0];
				}
				$post_excerpt = $curService->post_excerpt;
				$excerptArr = explode("\n",$post_excerpt);

				$post_excerpt = str_replace("\n",'<br>',$post_excerpt);
				$bigThumb = get_the_post_thumbnail($items_id, 'large'); 
				$bigThumbSrc = getSrcFromImage($bigThumb);
				$sizeArr = array();
				if($layout == '1column'){
					$sizeArr = array(290,160);
					$thumbnail = get_the_post_thumbnail($items_id,array(290,160));
					$thumbnailSrc = getSrcFromImage($thumbnail);
				}else{
					$sizeArr = array(190,103);
					$thumbnail = get_the_post_thumbnail($items_id,array(190,103));
					$thumbnailSrc = getSrcFromImage($thumbnail);
				}
				
				$shortLightBox = "[ew_img_video width_thumb='{$sizeArr[0]}' height_thumb='{$sizeArr[1]}' title='{$post_title}' type='image' src_thumb='{$thumbnailSrc}' src_zoom_img='{$bigThumbSrc}' class='image-style3']";
				
				$htmlListing = do_shortcode($serviceListing);
				if($display_style == 'service-style1'){
					$lightBoxHtml = do_shortcode($shortLightBox);
					$retHtml = 	"<div class='service-wrapper-style1'>
									<div class='head-service style1'><span class='head-service-title'>{$post_title}</span></div>
									<div class='excerpt-service style1'><span class='excerpt-service'>{$post_excerpt}</span></div>
									<div class='thumbnail-service'>{$lightBoxHtml}</div>
									<div class='list-service style1'>{$htmlListing}</div>
								</div>";
				}else if($display_style == 'service-style2'){
					$meta = get_post_meta($items_id, THEME_SLUG.'ew_service_custom_logo', true);
					$metaLink = get_post_meta($items_id, THEME_SLUG.'ew_service_custom_link', true);
					if(strlen($metaLink) <= 0){
						$metaLink = get_permalink($items_id);
					}
					$image = '';
					if($meta){
						$image = wp_get_attachment_image_src($meta, array(32,32)); 
						$image = $image[0]; 
						$image = "<img src=\"{$image}\" width=\"32\" height=\"32\" alt=\"{$post_title}\" title=\"{$post_title}\">";
					}
					$retHtml = 	"<div class='service-wrapper-style2'>
									<div class='head-service style2'><span class='icon'>{$image}</span><span class='head-service-title'>{$post_title}</span></div>
									<div class='excerpt-service style2'><span class='excerpt-service'>{$post_excerpt}</span></div>
									<a href='{$metaLink}'><span class=''><span>Learn more</span></span></a>
								</div>";
			
				}else if($display_style == 'service-style3'){
					$meta = get_post_meta($items_id, THEME_SLUG.'ew_service_custom_logo', true);
					$image = '';
					if($meta){
						$image = wp_get_attachment_image_src($meta, array(32,32)); 
						$image = $image[0]; 
						$image = "<img src=\"{$image}\" width=\"32\" height=\"32\" alt=\"{$post_title}\" title=\"{$post_title}\">";
					}
					$retHtml = 	"<div class='service-wrapper-style3'>
									<div class='head-service style3'><span class='icon'>{$image}</span><span class='head-service-title'>{$post_title}</span></div>
									<div class='excerpt-service style1'><span class='excerpt-service'>{$post_excerpt}</span></div>
								</div>";
				
				
				}else if($display_style == 'service-style4'){
					$meta = get_post_meta($items_id, THEME_SLUG.'ew_service_custom_logo', true);
					$image = '';
					if($meta){
						$image = wp_get_attachment_image_src($meta, array(32,32)); 
						$image = $image[0]; 
						$image = "<img src=\"{$image}\" width=\"32\" height=\"32\" alt=\"{$post_title}\" title=\"{$post_title}\">";
					}
					$retHtml = 	"<div class='service-wrapper-style4'>
									<div class='head-service style4'><span class='icon'>{$image}</span><span class='head-service-title'>{$post_title}</span></div>
									<div class='excerpt-service style4'><span class='excerpt-service'>{$post_excerpt}</span></div>
								</div>";
				
				}
				
				
				
			}
			wp_reset_query();
			return $retHtml;
		}else
			return '';
		//return "<div class='border-code'><div class='background-code'><pre class='code'>".htmlspecialchars($content)."</pre></div></div>";
	}
} 
add_shortcode('ew_service_item','ew_service_item');
?>