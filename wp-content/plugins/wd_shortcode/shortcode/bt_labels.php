<?php 
if(!function_exists ('label')){
	function label($atts,$content){
		extract(shortcode_atts(array(
			'type' 			=>  '',
		),$atts));
		$type = (!empty($type)) ? " label-{$type}" : '';
		return "<span class='label{$type}'>".do_shortcode($content)."</span>";	
	}
}
add_shortcode('label','label');


?>