<?php
if(!function_exists ('slide_function')){
	function slide_function($atts){
		extract(shortcode_atts(array(
			'id'    		 => -1

		),$atts));
		if ( !post_type_exists( 'slide' ) ) {
			return;
		}
		return show_fredsel_slider($id);
	}
}
add_shortcode('wd_slider','slide_function');
	
if(!function_exists ('show_fredsel_slider')){
	function show_fredsel_slider($post_id){
		if( (int)$post_id > 0 ){
			$slider_datas = get_post_meta($post_id,'wd_slider_list',true);
			$slider_datas = unserialize($slider_datas);
			
			$sliders_config = get_post_meta($post_id,'wd_slider_config',true);
			$sliders_config = unserialize($sliders_config);

			$_custom_size = 'wd_slide';

							
			if( is_array($slider_datas) && count($slider_datas) > 0 ){
				$_random_id = "fredsel_" . $post_id . "_" . rand();
				
				ob_start();
				
				?>
				<div class="featured_product_slider_wrapper shortcode_slider" id="<?php echo $_random_id;?>">
					<div class="fredsel_slider_wrapper_inner loading">
						<ul>
							<?php
								foreach( $slider_datas as $_slider ){
							?>	
								<li>
									<a href="<?php echo $_slider['url'];?>" title="<?php echo $_slider['slide_title'];?>">
										<?php echo wp_get_attachment_image( $_slider['thumb_id'], $_custom_size , false, array('title' => $_slider['title'], 'alt' => $_slider['title']) ); ?>
									</a>
								</li>
							<?php
								}
							?>						
						</ul>
						<?php if( $sliders_config['show_nav'] == 1): ?>
						<div class="slider_control">
							<a id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						
						jQuery('#<?php echo $_random_id?> > .fredsel_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .fredsel_slider_wrapper_inner');
						var scroll_per_page 		= <?php echo $sliders_config['scroll_per_page']; ?>;
						scroll_per_page 			= ( scroll_per_page == 1)?'page':1;
						var mouse_drag 				= <?php echo $sliders_config['mouse_drag']; ?> == 1;
						var touch_drag 				= <?php echo $sliders_config['touch_drag']; ?> == 1;
						var auto_play 				= <?php echo $sliders_config['auto_play']; ?> == 1;
						var auto_play_speed		 	= <?php echo $sliders_config['auto_play_speed']; ?>;
						var auto_play_timeout 		= <?php echo $sliders_config['auto_play_timeout']; ?>;
						var auto_play_hover_pause 	= <?php echo $sliders_config['auto_play_hover_pause']; ?>==1;
						var margin 					= <?php echo $sliders_config['margin']; ?>;
						
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						var slide_speed = <?php echo (wp_is_mobile())?200:800; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						
						var owl_data = {
							loop : true
							,margin : margin
							,nav : false
							,dots : false
							,navSpeed : slide_speed
							,slideBy: scroll_per_page
							,rtl:jQuery('body').hasClass('rtl')
							,navRewind: false
							,autoplay: auto_play
							,autoplayTimeout: auto_play_timeout
							,autoplayHoverPause: auto_play_hover_pause
							,autoplaySpeed: auto_play_speed
							,mouseDrag: mouse_drag
							,touchDrag: touch_drag
							,responsiveBaseElement: $_this
							,responsiveRefreshRate: responsive_refresh_rate
							,responsive:{
								0:{
									items : 1
								},
								220:{
									items : 2
								},
								440:{
									items : 3
								},
								660:{
									items : 4
								},
								880:{
									items : 5
								},
								1100:{
									items : 6
								},
								1320:{
									items : 7
								}
							}
							,onInitialized: function(){
								$_this.addClass('loaded').removeClass('loading');
							}
						};
						<?php if( isset($sliders_config['responsive_option']['break_point'],$sliders_config['responsive_option']['item']) ): ?>
							owl_data.responsive = {};
							<?php foreach($sliders_config['responsive_option']['break_point'] as $key => $break): ?>
								owl_data.responsive[<?php echo $break; ?>] = {items: <?php echo $sliders_config['responsive_option']['item'][$key]; ?>};
							<?php endforeach; ?>
						<?php endif; ?>
						
						var owl = $_this.find('ul').owlCarousel(owl_data);
						$_this.on('click', '.next', function(e){
							e.preventDefault();
							owl.trigger('next.owl.carousel');
						});

						$_this.on('click', '.prev', function(e){
							e.preventDefault();
							owl.trigger('prev.owl.carousel');
						});
						
						});
					});
					//]]>
				</script>
				<?php	
				return ob_get_clean();
			}
		}		
	}
}

?>