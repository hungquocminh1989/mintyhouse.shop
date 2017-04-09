<?php

	if(!function_exists('banner_description_shortcode')){
		function banner_description_shortcode($atts,$content){
			extract(shortcode_atts(array(
				'link_url'						=> "#" 
				,'image' 						=> ""
				,'description'					=> "your description"
				,'title'						=> "your title"
				
			),$atts));
			ob_start();
			?>
			
			<div class="banner_description_shortcode">
				<div class="banner_description_wrapper">
					<div class="banner_description_image">
						<a title="<?php echo $title; ?>" href="<?php echo $link_url;?>">
							<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" />
							<div class="thumbnail-effect"></div>
						</a>
					</div>
					<div class="banner_description_content">
						<h3><a title="<?php echo $title; ?>" href="<?php echo $link_url;?>"><?php echo $title; ?></a></h3>
						<div class="banner_description">
							<?php echo $description; ?>
						</div>
					</div>					
				</div>				
			</div>	
			<?php
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}
	}
	add_shortcode('banner_description','banner_description_shortcode');
?>