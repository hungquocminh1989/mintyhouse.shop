<?php 
if(!function_exists ('tooltip')){
	function tooltip($atts,$content){
		extract(shortcode_atts(array(
			'style'=>'top'
			,'tooltip_content' => 'Tooltip Content'
		),$atts));
		$result="<a href=\"#\" rel=\"tooltip\" data-placement=\"{$style}\" data-original-title=\"{$tooltip_content}\">".do_shortcode($content)."</a>";
		return $result;
	}
}
add_shortcode('tooltip','tooltip');
?>