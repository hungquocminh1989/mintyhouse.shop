<?php

	if(!function_exists('banner_shortcode_function')){
		function banner_shortcode_function($atts,$content){
			extract(shortcode_atts(array(
				'title'							=> ""
				,'link_url'						=> "#" 
				,'bg_image' 					=> ""
				,'bg_color'						=> "none"
				,'bg_hover'						=> "#000000"
				,'bg_text'						=> ""
				,'position_text_bottom'			=> "30px"
				,'show_label'					=> "no"
				,'label_text'					=> "save off"
				,'label_style'					=> "big onsale two_word"
				,'responsive'					=> "normal"
			),$atts));
			ob_start();
			?>
			
			<div class="shortcode_wd_banner" onclick="location.href='<?php echo esc_url($link_url);?>'" style="background-color:<?php echo $bg_color;?>;">
			<a title="<?php echo esc_attr($title);?>" href="<?php echo esc_url($link_url);?>">
				<div class="shortcode_wd_banner_inner">
					<div class="wd_banner_background_image_wrapper">
						<img alt="<?php echo esc_attr($title);?>" title="<?php echo esc_attr($title);?>" class="img" src="<?php echo esc_url($bg_image);?>" />
						<div class="wd_banner_background_hover" style="background:<?php echo $bg_hover; ?>"></div>
					</div>
					<div style="bottom:<?php echo $position_text_bottom; ?>" class="wd_banner_background_text_wrapper <?php echo esc_attr($responsive) ?>">
						<img  alt="<?php echo esc_attr($title);?>" title="<?php echo esc_attr($title);?>" class="img" src="<?php echo esc_url($bg_text);?>" />
					</div>
					<?php if( absint($show_label) == 1 || strcmp($show_label,'yes') == 0 || strcmp($show_label,'Yes') == 0 ):?>
						<div class="shortcode_banner_label <?php echo esc_attr($label_style); ?>" ><?php echo esc_html($label_text)  ?></div>
					<?php endif;?>
				</div>	
			</a>
			</div>
					
			<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
	add_shortcode('banner','banner_shortcode_function');
?>