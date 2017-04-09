<?php
if(!function_exists ('faq')){
	function faq($atts,$content=null){
		extract(shortcode_atts(array(
			'title'=>'',
		),$atts));
		return "<div class='faq'>
	<h1 class='cufon faq-title'>{$title}</h1>
	<p class='faq-content'>{$content}</p>
	</div>";
	}
} 
add_shortcode('faq','faq');
?>