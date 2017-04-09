<?php 
if(!function_exists ('bt_slides_funcs ')){
	function bt_slides_funcs($atts,$content){
		extract(shortcode_atts(array(
			'id'=>'0'
			,'type'=>'carousel'
			,'auto'=>'0'
		),$atts));
		if( (int)$id  <= 0)
			return '';
		$_rand_id = 'carousel_'.rand();
		$portfolio_sliders = get_post_meta($id,THEME_SLUG.'_portfolio_slider',true);
		$portfolio_sliders = unserialize($portfolio_sliders);

		$portfolio_slider_config = get_post_meta($id,THEME_SLUG.'_portfolio_slider_config',true);
		$portfolio_slider_config = unserialize($portfolio_slider_config);	
		if( is_array($portfolio_slider_config) && count($portfolio_slider_config) > 0 ){
			$portfolio_slider_config_width = (int) $portfolio_slider_config['portfolio_slider_config_width'];
			$portfolio_slider_config_height = (int) $portfolio_slider_config['portfolio_slider_config_height'];
		}
		
		$control_html = $slides_html = '';
		$active_class = "active";

		
		
		foreach( $portfolio_sliders as $index => $_this_slider ){
			$img_html = '';
			$crop_img = $_this_slider['image_url'];
			if( $portfolio_slider_config_width > 0 ){
				$img_html .= " width=\"{$portfolio_slider_config_width}\"";
				$crop_img = print_thumbnail($_this_slider['image_url'],true,'',$portfolio_slider_config_width,'','',false,true);
			}	
			if( $portfolio_slider_config_height > 0 ){
				$img_html .= " height=\"{$portfolio_slider_config_height}\"";
				$crop_img = print_thumbnail($_this_slider['image_url'],true,'','',$portfolio_slider_config_height,'',false,true);
			}	
			if( $portfolio_slider_config_height > 0 &&  $portfolio_slider_config_width > 0 )
				$crop_img = print_thumbnail($_this_slider['image_url'],true,'',$portfolio_slider_config_width,$portfolio_slider_config_height,'',false,true);
				
			$control_html .= "<li data-target=\"#{$_rand_id}\" data-slide-to=\"{$index}\" class=\"{$active_class}\"></li>";
            $slides_html .= "
				<div class=\"item {$active_class}\">
						<img src=\"{$crop_img}\" alt=\"{$_this_slider['alt']}\" title=\"{$_this_slider['title']}\" {$img_html}>
                    <div class=\"carousel-caption\">
						<a href=\"{$_this_slider['url']}\"><h4>{$_this_slider['slide_title']}</h4></a>
						<p>{$_this_slider['slide_content']}</p>
                    </div>
                </div>";		
			
			$active_class = '';
		}
		$control_html = "<ol class=\"carousel-indicators\">".$control_html."</ol>";
		$slides_html = "<div class=\"carousel-inner\">".$slides_html."</div>";
		 
		$end_html = "
		    <a class=\"left carousel-control\" href=\"#{$_rand_id}\" data-slide=\"prev\">&lsaquo;</a>
            <a class=\"right carousel-control\" href=\"#{$_rand_id}\" data-slide=\"next\">&rsaquo;</a>
		";
		$result = "<div class='carousel slide' id='{$_rand_id}'>{$control_html}{$slides_html}{$end_html}</div>";
		return $result;
	}
}
//add_shortcode('slideshow','bt_slides_funcs');
?>