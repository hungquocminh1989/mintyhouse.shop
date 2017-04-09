<?php 
if(!function_exists ('ew_lightbox')){
	function ew_lightbox($atts){
		extract(shortcode_atts(array(
			'href'		=> 	'',
			'title' 	=> 	'Lightbox',
			'group'		=>	'',
			'photo'		=>	'',
			'lb_with'	=>	'text',
			'use_iframe'=>	'false',
			'width'		=>	150,
			'height'	=>	150,
			'button_text'=>	__('Lightbox Button','wpdance')
		),$atts));
		
		$rel = (!empty($group)) ? " rel='{$group}'" : '';
		$iframe_class = ($use_iframe == 'true') ? " iframe" : '';
		// Parse href for youtube, vimeo or image
		if(strstr($href,'youtube.com') || strstr($href,'youtu.be')){
			$class_fancy = ' youtube';
			if(wp_parse_youtube_link($href))
				$big_url = 'http://www.youtube.com/watch?v='.  wp_parse_youtube_link($href);
			else
				$big_url = $href;
		}
		elseif(strstr($href,'vimeo.com')){
			$class_fancy = ' vimeo';
			$big_url = $href;
		}
		else{
			$class_fancy = '';
			$big_url = $href;
		}

		if($use_iframe == 'true')
			$class_fancy = '';

		if($lb_with == 'text')
			return "<a class='lb_control{$class_fancy}{$iframe_class}' href='{$big_url}'{$rel} title='{$title}'>{$title}</a>";
		elseif($lb_with == 'img'){
			if($photo)
				return "<a class='lb_control{$class_fancy}{$iframe_class}' href='{$big_url}'{$rel} title='{$title}'><img src='{$photo}'/></a>";
			else{
				return "<a class='lb_control{$class_fancy}{$iframe_class}' href='{$big_url}'{$rel} title='{$title}'>".get_thumbnail_video($href,$width,$height)."</a>";
			}	
		}	
		elseif($lb_with == 'button')
			return "<a class='btn_lightbox lb_control{$class_fancy}{$iframe_class}' href='{$big_url}'{$rel} title='{$title}'><span><span>".$button_text."</span></span></a>";
	}
}
add_shortcode('ew_lightbox','ew_lightbox');
?>