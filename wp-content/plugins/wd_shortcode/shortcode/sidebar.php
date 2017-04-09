<?php 
/////////// Shortcode show sidebar //////////////
if(!function_exists ('ew_sidebar')){
	function ew_sidebar($atts){
		extract(shortcode_atts(array(
			'id_area'  		=> 'primary-widget-area-left',
			'ul_class'   	=> '',
			'id_sidebar'	=> ''
		),$atts));
		return ew_get_sidebar($id_area,$ul_class,$id_sidebar);
	}
}
add_shortcode('ew_sidebar','ew_sidebar');
?>