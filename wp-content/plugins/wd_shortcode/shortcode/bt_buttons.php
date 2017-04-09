<?php 
if(!function_exists ('button')){
	function button($atts,$content){
		extract(shortcode_atts(array(
			'size'			=>	'default',
			'color'			=>	'#ffffff',
			'background'	=>	'#f34948',
			'border_color'	=>	'#f34948',
			'background_hover'	=>	'#ffffff',
			'color_hover'			=>	'#f34948',
			'border_color_hover'	=>	'#f34948',
			'link'			=>	'',
			
		),$atts));	
		
		$types_arr = array(
			'largest' => 'btn-largest'
			,'large' => 'btn-large'
			,'medium' => 'btn-medium'
			,'small' => 'btn-small'
			,'mini' => 'btn-mini'
		);
		
		$size = (!empty($size)) ? "btn-{$size}" : '';
		$type = (!empty($type)) ? "btn-{$type}" : '';
		$color = (!empty($color)) ? "{$color}" : '#ffffff';
		$background = (!empty($background)) ? "{$background}" : '#f34948';
		$border_color = (!empty($border_color)) ? "{$border_color}" : '#f34948';
		$color_hover = (!empty($color_hover)) ? "{$color_hover}" : '#f34948';
		$background_hover = (!empty($background_hover)) ? $background_hover : '#ffffff';	
		$border_color_hover = (!empty($border_color_hover)) ? "{$border_color_hover}" : '#f34948';

		if( strlen($link) > 0 ){
			return "<a data-style_hover='color:{$color_hover};background:{$background_hover};border-color:{$border_color_hover}' style='color:{$color};background-color:{$background};border-color:{$border_color}' href='$link' class='wd-shortcode-button btn {$size}'>".do_shortcode($content)."</a>";
		}
		return "<button data-style_hover='color:{$color_hover};background:{$background_hover};border-color:{$border_color_hover}' style='color:{$color};background-color:{$background};border-color:{$border_color}' class='wd-shortcode-button btn {$size}'>".do_shortcode($content)."</button>";	
	}
}
add_shortcode('button','button');

/*if(!function_exists ('button_group')){
	function button_group($atts,$content){
		extract(shortcode_atts(array(
			'vertical' => 0
		),$atts));
		$_vertical = '';
		if( $vertical == 1 )
			$_vertical = " btn-group-vertical";
			
		return "<div class='btn-toolbar'><div class='btn-group{$_vertical}'>".do_shortcode($content)."</div></div>";
	}
}
add_shortcode('button_group','button_group');*/
?>