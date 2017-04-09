<?php 
if(!function_exists ('quote')){
	function quote($atts,$content){
		extract(shortcode_atts(array(
			'class'=>''
		),$atts));
		$result="<div class='quote-style {$class}'><span>".do_shortcode($content)."</span><span class='end'></span></div>";
		return $result;
	}
}
add_shortcode('quote','quote');
?>