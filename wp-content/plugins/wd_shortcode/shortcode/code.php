<?php
if(!function_exists ('ew_code')){
	function ew_code($atts,$content = false){
		extract(shortcode_atts(array(
		),$atts));
		//add_filter('the_content','ew_do_shortcode',1001);
		return "<div class='border-code'><div class='background-code'><pre class='code'>".htmlspecialchars($content)."</pre></div></div>";
	}
} 
add_shortcode('code','ew_code');
?>