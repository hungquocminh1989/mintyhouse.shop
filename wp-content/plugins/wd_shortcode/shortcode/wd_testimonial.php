<?php
add_image_size('testimonial',115,115);
	if(!function_exists('testimonial')){
		function testimonial($atts,$content){
			extract(shortcode_atts(array(
				'style'				=> '1'
				,'slug'				=> ''
				,'id'				=> 0
				,'limit'			=> 5
				,'slider'			=> 0
				,'category'			=> 0
			),$atts));
			
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "testimonials-by-woothemes/woothemes-testimonials.php", $_actived ) ) {
				return;
			}
			
			global $post;
			$count = 0;
			if( absint($id) > 0 ){
				$_testimonial = woothemes_get_testimonials( array('id' => $id,'limit' => 1, 'size' => 100 ));
			}elseif( strlen(trim($slug)) > 0 ){
				$_testimonial = get_page_by_path($slug, OBJECT, 'testimonial');
				if( !is_null($_testimonial) ){
					$_testimonial = woothemes_get_testimonials( array('id' => $_testimonial->ID,'limit' => 1, 'size' => 100 ));
				}else{
					return;
				}
			}else{
				// Load multi testimonial
				$_testimonial = woothemes_get_testimonials( array('limit' => $limit, 'size' => 100, 'category' => $category ));
				//invalid input params.
			}
			
			if( !is_array($_testimonial) || count($_testimonial) <= 0 ){
				return;
			}
			ob_start();
			global $post;
			$random_id = 'wd_testimonial_wrapper_'.rand(0,1000);
			?>
			<div class="wd_testimonial_wrapper style-<?php echo $style; ?> <?php echo ((int)$slider == 1)?'is_slider loading':''; ?>" id="<?php echo $random_id ?>">
				<div class="testimonial-inner">
				<?php 
				foreach( $_testimonial as $testimonial ){
					$post = $testimonial;
					setup_postdata( $post ); 
					$_twitter_username = get_post_meta($post->ID,THEME_SLUG.'username_twitter_testimonial',true);
					?>
					<div class="testimonial-item">
						<div class="avatar">
							<a href="<?php echo $testimonial->url; ?>"><?php the_post_thumbnail('woo_shortcode');?></a>
						</div>							
						<div class="detail">
							<div class="testimonial-content"><?php echo do_shortcode(get_the_content());?></div>
							<a class="title" href="<?php echo $testimonial->url; ?>"><?php the_title();?></a> 
							<!--<span class="line">-</span> 
							<span class="job"> <?php //echo get_post_meta($post->ID,'_byline',true);?></span>-->
							<span class="twitter"><a href="http://twitter.com/<?php echo $_twitter_username; ?>" target="_blank" title="<?php _e('Follow us', 'wpdance'); ?>" ><?php _e('VIEW TWEET', 'wpdance'); ?></a></span>					
						</div>
					</div>
					<?php } ?>
				</div>
				<?php if( (int)$slider == 1 ): ?>
				<div class="slider_control">
					<a title="prev" class="prev" href="#">&lt;</a>
					<a title="next" class="next" href="#">&gt;</a>
				</div>
				<?php endif; ?>
			</div>
			<?php if( (int)$slider == 1 ): ?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					var $_this = jQuery('#<?php echo $random_id; ?>');
					var owl = $_this.find('.testimonial-inner').owlCarousel({
							loop : true
							,items : 1
							,nav : false
							,dots : false
							,navSpeed : 1000
							,slideBy: 1
							,rtl:jQuery('body').hasClass('rtl')
							,navRewind: false
							,autoplay: true
							,autoplayTimeout: 5000
							,autoplayHoverPause: true
							,autoplaySpeed: false
							,autoHeight: true
							,mouseDrag: true
							,touchDrag: true
							,responsiveBaseElement: $_this
							,responsiveRefreshRate: 1000
							,onInitialized: function(){
								$_this.removeClass('loading').addClass('loaded');
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
			<?php endif; ?>
			<?php
			$output = ob_get_contents();
			ob_end_clean();
			rewind_posts();
			wp_reset_query();
			return $output;
		}
	}
	add_shortcode('testimonial','testimonial');
?>