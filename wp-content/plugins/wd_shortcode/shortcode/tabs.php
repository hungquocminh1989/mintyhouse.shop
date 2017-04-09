<?php 
if(!function_exists ('ew_tab_item')){
	function ew_tab_item($atts,$content){
		extract(shortcode_atts(array(
			'title'			=>	'',
		),$atts));
		$class = " class='".trim('tab-item')."'";
		$id = 'tab-'.rand(0,1000);
		return "<li{$class}><a href='#{$id}'><span><span>{$title}</span></span></a><div id='{$id}' class='tab-post-content'><div class='top-left'><div class='top-right'></div></div><div class='contentcenter'>".do_shortcode($content)."</div><div class='bot-left'><div class='bot-right'></div></div></div></li>";
	}
}
add_shortcode('ew_tab_item','ew_tab_item');

// Show jquery ui tabs
if(!function_exists ('ew_tabs')){
	function ew_tabs($atts,$content){
		extract(shortcode_atts(array(
			//'custom_class'	=>	'',
		),$atts));
		$id = 'multitabs-'.rand(0,1000);
		$result .= "<div class='tabs-style id='{$id}'>";
		$result .= do_shortcode($content)."</div>";
		
		return $result;
	}
}
add_shortcode('ew_tabs','ew_tabs');
?>