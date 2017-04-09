<?php 
// Shortcode show container for table
if(!function_exists ('ew_style_table')){
	function ew_style_table($atts,$content){
		extract(shortcode_atts(array(
			'class'  	=> '',
			'width'   	=> ''
		),$atts));
		$class_str = trim("table_style $class");
		$style = !empty($width) ? " style='width:{$width};'" : '';
		return "<div class='{$class_str}'{$style}>".do_shortcode($content)."</div>";
	}
}
add_shortcode('ew_style_table','ew_style_table');
?>