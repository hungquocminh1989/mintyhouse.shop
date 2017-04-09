<?php
//add_image_size('portfolio',115,115);
	if(!function_exists('wd_portfolio_slider_function')){
		function wd_portfolio_slider_function($atts){
			extract( shortcode_atts( array(
				'title' 			=> ''
				,'desc' 			=> ''
				,'portfolio_cats'	=>''
				,'per_page' 		=> 5
				,'show_nav' 		=> 1
			),$atts));
			
			if( !post_type_exists('portfolio') ) {	
				return;
			}
			
			$args = array(
					'post_type'	=> 'portfolio'
					,'posts_per_page'	=> $per_page
					);
			if( $portfolio_cats != "" ){
				$args['tax_query'] = array(
									array(
										'taxonomy' => 'wd-portfolio-category'
										,'terms' => explode(',', esc_attr($portfolio_cats))
										,'field' => 'slug'
										,'operator' => 'IN'
									)
				);
			}
			
			$query = new WP_Query($args);
				
			global $post;
			ob_start();
			if( $query->have_posts() ): 
				$rand_id = 'portfolio_slider_shortcode_'.rand(0, 1000);
				?>
				<div class="portfolio_slider_shortcode wd_shortcode" id="<?php echo $rand_id; ?>">
					<?php if( strlen($title) > 0 || strlen($desc) > 0 ): ?>
					<div class="portfolio_slider_title wd_shortcode_title">
						<?php if( strlen($title) > 0 ): ?>
						<h2 class="heading-title box-heading">
							<?php echo esc_html($title); ?>
						</h2>
						<?php endif; ?>
						<?php if( strlen($desc) > 0 ): ?>
						<p class="desc-wrapper">
							<?php echo esc_html($desc); ?>
						</p>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<div class="portfolio-content-wrapper loading">
						<div class="portfolio-items">
							<?php 
							while( $query->have_posts() ) : $query->the_post();
								$post_title = esc_html(get_the_title($post->ID));
								$post_url 	= esc_url(get_permalink($post->ID));
								$thumb		= get_post_thumbnail_id($post->ID);
								$thumburl	= wp_get_attachment_image_src($thumb,'portfolio_image');
							?>
							<div class="portfolio-item">
								<a class="image" href="<?php echo $post_url; ?>">
									<?php if($thumburl[0]) { ?>
										<img alt="<?php echo $post_title; ?>" title="<?php echo $post_title; ?>" class="opacity_0" src="<?php echo  esc_url($thumburl[0]);?>"/>																
										<?php } else { ?>
										<img alt="<?php echo $post_title; ?>" title="<?php echo $post_title; ?>" class="opacity_0" src="<?php echo get_template_directory_uri(); ?>/images/no-gallery-830x494.gif"/>
										<?php } ?>
									<div  class="thumbnail-effect"></div>
								</a>
								<div class="detail">
										<h3 class="heading-title">
											<a href="<?php echo $post_url; ?>">
											<?php echo $post_title; ?>
											</a>
										</h3>
										<span class="date-time"><i class="fa fa-calendar-o"></i><?php echo get_the_time(get_option('date_format')); ?></span>
								</div>
							</div>
							<?php endwhile; ?>
						</div>
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" class="prev" href="#">&lt;</a>
							<a title="next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
					</div>
				</div>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						"use strict";
						var $_this = jQuery('#<?php echo $rand_id; ?> .portfolio-content-wrapper');
						var owl = $_this.find('.portfolio-items').owlCarousel({
								loop : true
								,nav : false
								,dots : false
								,navSpeed : 1000
								,slideBy: 1
								,rtl: jQuery('body').hasClass('rtl')
								,margin:20
								,navRewind: false
								,autoplay: false
								,autoplayTimeout: 5000
								,autoplayHoverPause: false
								,autoplaySpeed: false
								,mouseDrag: true
								,touchDrag: true
								,responsiveBaseElement: $_this
								,responsiveRefreshRate: 1000
								,responsive:{
									0:{
										items : 1
									},
									400:{
										items : 2
									},
									800:{
										items : 3
									},
									1200:{
										items : 4
									},
									1600:{
										items : 5
									}
								}
								,onInitialized: function(){
									$_this.addClass('loaded').removeClass('loading');
								}
							});
							$_this.on('click', '.next', function(e){
								e.preventDefault();
								owl.trigger('next.owl.carousel');
							});

							$_this.on('click', '.prev', function(e){
								e.preventDefault();
								owl.trigger('prev.owl.carousel');
							});
					});
				</script>
				<?php
				wp_reset_query();
			endif;
			
			return ob_get_clean();
		}
	}
	add_shortcode('wd_portfolio_slider','wd_portfolio_slider_function');
?>
