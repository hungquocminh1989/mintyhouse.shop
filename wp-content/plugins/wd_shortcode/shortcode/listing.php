<?php 
if(!function_exists ('ew_listing')){
	function ew_listing($atts,$content){
		extract(shortcode_atts(array(
			'custom_class'	=>	'',
			'style_class'	=> 	''
		),$atts));
		//$id = 'multitabs-'.rand(0,1000);
		$class = "class='listing-style $style_class";
		
		if($custom_class)
			$class .= " {$custom_class}";
		$class .= "'";	
		$result .= "<div $class>";
		$result .= do_shortcode($content)."</div>";
		return $result;
	}
}
add_shortcode('ew_listing','ew_listing');
?>