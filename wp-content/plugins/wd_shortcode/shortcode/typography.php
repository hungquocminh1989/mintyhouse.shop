<?php 
if(!function_exists ('heading_function')){
	function heading_function($atts, $content = null){
		extract(shortcode_atts(array(
			'size' 		=> '1',
		), $atts));
		return "<div class='heading-title-block heading-{$size}'><h{$size}>".do_shortcode($content)."</h{$size}></div>";
	}
}
add_shortcode('heading','heading_function');



if(!function_exists ('checklist_function')){
	function checklist_function($atts, $content){
		extract(shortcode_atts(array(
			'icon' 		=> 'none',
		), $atts));
		
		$icon = trim($icon);
		
		$match = preg_match('/.*?<ul>(.*?)<\/ul>.*?/ism',$content,$content_match);
		if( $match ){
			$math = preg_match_all('/<li>(.*?)<\/li>/ism',$content,$content_match);
			if( $math ){
				$new_string = "<li><i class=\"{$icon}\"></i>";
				$content = str_replace ( "<li>" , $new_string , $content );
			}
		}
		

		return "<div class='fa checklist-block shortcode-icon-list shortcode-icon-{$icon}'>".do_shortcode($content)."</div>";
	}
}
add_shortcode('checklist','checklist_function');
?>