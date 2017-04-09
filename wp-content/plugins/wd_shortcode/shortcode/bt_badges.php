<?php 
if(!function_exists ('badge')){
	function badge($atts,$content){
		extract(shortcode_atts(array(
			'type' 			=>  '',
		),$atts));
		$type = (!empty($type)) ? " badge-{$type}" : '';
		return "<span class='badge{$type}'>".do_shortcode($content)."</span>";	
	}
}
add_shortcode('badge','badge');


?>