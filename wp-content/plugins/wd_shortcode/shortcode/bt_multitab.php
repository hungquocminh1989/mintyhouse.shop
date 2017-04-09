<?php 
// Show jquery ui tabs
if(!function_exists ('tabs')){
	function tabs($atts,$content){
		extract(shortcode_atts(array(
			/*'style'	=>	'default',*/
		),$atts));
		$id = 'multitabs_'.rand();
		$inside_id = $id.'_inside';
		$result = "<div class='tabbable' id='{$id}'>\n\t";
		
		$tabs_match = preg_match_all('/\[tab_item\s*?title="(.*?)"\](.*?)\[\/tab_item\]/ism',$content,$match);
		if( $tabs_match && is_array($match) && count($match) > 0 ){
			$_title_contents = '';
			$_tabs_contents = '';
			$_init_class_title = 'active';
			$_init_class_content = 'active in';
			for( $_count = 0 ; $_count < count($match[0]) ; $_count++ ){
				$_content_id = $inside_id.$_count;
				$_inside_content = do_shortcode($match[2][$_count]);
				$match[1][$_count] = strlen( $match[1][$_count] ) <= 0 ? 'Tab title' : $match[1][$_count];
				if($_count == count($match[0])-1)
					$_title_contents .= "\n\t\t\t<li class=\"{$_init_class_title} last\"><a href=\"#{$_content_id}\" data-toggle=\"tab\">{$match[1][$_count]}</a></li>";
				else
					$_title_contents .= "\n\t\t\t<li class=\"{$_init_class_title}\"><a href=\"#{$_content_id}\" data-toggle=\"tab\">{$match[1][$_count]}</a></li>";
				$_tabs_contents .= "\n\t\t\t<div class=\"tab-pane fade {$_init_class_content}\" id=\"{$_content_id}\">{$_inside_content}</div>";
				$_init_class_title = $_init_class_content = '';
			}
			$_title_contents = "\n\t\t<ul class=\"nav nav-tabs\" id=\"{$inside_id}\">".$_title_contents."\n\t\t</ul>";
			$_tabs_contents = "\n\t\t<div class=\"tab-content\" id=\"{$inside_id}Content\">".$_tabs_contents."\n\t\t</div>";
		}
		$result .= $_title_contents.$_tabs_contents."\n\t</div>";
		
		return $result;
	}
}
add_shortcode('tabs','tabs');
?>