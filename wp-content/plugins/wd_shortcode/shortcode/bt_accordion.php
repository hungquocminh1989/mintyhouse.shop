<?php 
if(!function_exists ('accordions')){
	function accordions($atts,$content){
		$iden_accordion_box = 'accordions_'.rand();
		$tabs_match = preg_match_all('/\[accordion_item\s*?title="(.*?)"\](.*?)\[\/accordion_item\]/ism',$content,$match);
		if( $tabs_match && is_array($match) && count($match) > 0 ){
			$_accordions_html = '';
			$_init_class = ' in';
			for( $_count = 0 ; $_count < count($match[0]) ; $_count++ ){
				$collapsed_class = $_count == 0 ? "" : "collapsed";
				$_accordions_title = '';
				$_accordions_contents = '';
				$_content_id = $iden_accordion_box."_inside_".$_count;
				$_accordions_contents_html = do_shortcode($match[2][$_count]);
				$match[1][$_count] = strlen( $match[1][$_count] ) <= 0 ? 'Accordion title' : $match[1][$_count];
				$_accordions_title .= "\n\t\t<div class=\"accordion-heading\">";
				$_accordions_title .= "\n\t\t\t<a class=\"accordion-toggle {$collapsed_class}\" href=\"#{$_content_id}\" data-parent=\"#{$iden_accordion_box}\" data-toggle=\"collapse\">{$match[1][$_count]}</a>";
				$_accordions_title .= "\n\t\t</div>";
				
				$_accordions_contents .= "\n\t\t<div class=\"accordion-body collapse {$_init_class}\" id=\"{$_content_id}\">";
				$_accordions_contents .= "\n\t\t\t<div class=\"accordion-inner\">{$_accordions_contents_html}</div>";
				$_accordions_contents .= "\n\t\t</div>";
				
				$_accordions_html .= "\n\t<div class=\"accordion-group\">";
				$_accordions_html .= $_accordions_title.$_accordions_contents;
				$_accordions_html .= "\n\t</div>";
				
				$_init_class = '';
				
			}

		}
		$result = "<div class='accordion' id='{$iden_accordion_box}'>".$_accordions_html."</div>";
		
		return $result;
	}
}
add_shortcode('accordions','accordions');

if(!function_exists ('accordion_box')){
	function accordion_box($atts,$content){
		extract(shortcode_atts(array(
			'title'	=>	''
		),$atts));
		$result .= "<h3><a href='#'>{$title}</a></h3>";
		$result .= "<div>".do_shortcode($content)."</div>";
		return $result;
	}
}
add_shortcode('accordion_box','accordion_box');
?>