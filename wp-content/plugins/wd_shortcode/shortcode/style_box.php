<?php 
if(!function_exists ('ew_style_box')){
	function ew_style_box($atts, $content = null) {
		extract(shortcode_atts(array(
			'type' 	=> 	'',
		), $atts));
		return '<div class="box-style ' .$type.'">
					<div class="top-left"><div class="top-right"></div></div>
						<div class="contentcenter">'.do_shortcode($content).'</div>
					<div class="bot-left"><div class="bot-right"></div></div>
				</div>';
	}
}
add_shortcode('ew_style_box','ew_style_box');

if(!function_exists ('ew_framed_box')){
	function ew_framed_box($atts, $content = null) {
		extract(shortcode_atts(array(
			'width' => '',
			'height' => '',
			'bgcolor' => '',
			'textcolor' => '',
			'custom_class'=> ''	
		), $atts));
		
		$width = $width?'width:'.$width.'px;':'';
		$height = $height?'height:'.$height.'px;':'';
		$custom_class = $custom_class?' '.$custom_class:'';
		
		$bgcolor = $bgcolor?'background-color:'.$bgcolor.';':'';
		$textcolor = $textcolor?'color:'.$textcolor.';':'';
		if( !empty($height) || !empty($bgcolor) || !empty($textcolor) || !empty($width) || !empty($height)){
			$content_style = ' style="'.$bgcolor.$textcolor.$width.$height.'"';
		}else{
			$content_style = '';
		}
		global $layout;
		$classArr = explode(" ", $custom_class);
		$key = array_search('featured', $classArr);

		if( ($key!==false) && strcmp($layout,'1column')==0 ){
			return '<div class="box-style framebox-style'.$custom_class.'"'.$content_style.'><div class="featured-inner"><div class="featured-inner1">'.do_shortcode($content).'</div></div></div>';
		}
		
		return '<div class="box-style framebox-style'.$custom_class.'"'.$content_style.'>' . do_shortcode($content) . '</div>';
	}
}
add_shortcode('ew_framed_box','ew_framed_box');

if(!function_exists ('ew_block')){
	function ew_block($atts, $content = null) {
		extract(shortcode_atts(array(
			'border_color' 		=> '',
			'background_color' 	=> '',
			'custom_class'		=>	''
		), $atts));
		$custom_class = $custom_class?' '.$custom_class:'';
		$background_color = $background_color?'background-color:'.$background_color.';':'';
		$border_color = $border_color?'border:1px solid '.$border_color.';':'';
		if( !empty($background_color) || !empty($border_color)){
			$content_style = ' style="'.$background_color.$border_color.'"';
		}else{
			$content_style = '';
		}
		
		return '<div class="block'.$custom_class.'"'.$content_style.'>' . do_shortcode($content) . '</div>';
	}
}
//add_shortcode('block','ew_block');
?>