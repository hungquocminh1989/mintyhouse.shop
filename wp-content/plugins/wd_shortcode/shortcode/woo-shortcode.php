<?php
/**
 * @package WordPress
 * @subpackage Oswad Market
 * @since WD_Responsive
 */
 
	
	
	if( !function_exists('wd_get_sub_categories') ){
		function wd_get_sub_categories($category_id,$show_all=true){
			$args = array(
			   'taxonomy'     => 'product_cat'
			   ,'orderby'      => 'name'
			   ,'order'        => 'asc'
			   ,'hierarchical' => 0
			   ,'title_li'     => ''
			   ,'hide_empty'   => 0
			);
			if($show_all){
				$args['child_of'] = $category_id;
			}
			else{
				$args['parent'] = $category_id;
			}
			return get_categories( $args );
		}
	}
	
	/* individual product */
	if( !function_exists('wd_individual_product_function') ){
		function wd_individual_product_function($atts, $content = null){
			extract(shortcode_atts(array(
					'id'				=> ''
					,'sku'				=> ''
					,'desc_from'		=> 'content'
					,'show_title'		=> 1
					,'show_desc'		=> 1
					,'show_sku'			=> 0
					,'show_availability'=> 0
					,'show_price'		=> 1
					,'show_add_to_cart'	=> 1
				), $atts));
				
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			
			if( $id == '' && $sku == '' ){
				return;
			}
			
			wp_reset_query();
			
			$args = array(
				'post_type' => 'product'
				,'posts_per_page' => 1
				,'no_found_rows' => 1
				,'post_status' => 'publish'
				,'meta_query' => array(
					array(
						'key' => '_visibility'
						,'value' => array('catalog', 'visible')
						,'compare' => 'IN'
					)
				)
			);
			
			if( $sku != '' ){
				$args['meta_query'][] = array(
					'key' 		=> '_sku'
					,'value' 	=> $sku
					,'compare' 	=> '='
				);
			}
			else{
				$args['p'] = $id;
			}
			
			ob_start();
			
			$product = new WP_Query($args);
			if( $product->have_posts() ):
			?>
			<div class="wd_individual_product_wrapper">
				<?php while( $product->have_posts() ): $product->the_post(); ?>
				<div class="image">
					<a href="<?php the_permalink(); ?>">
					<?php 
					if( has_post_thumbnail() ){
						the_post_thumbnail('shop_single', array('alt'=>''));
					}
					?>
					</a>
				</div>
				<div class="summary">
					<?php if( $show_title ): ?>
						<h3 class="heading-title product-title"><a href="<?php echo get_permalink(); ?>"><?php the_title();?></a></h3>
					<?php endif; ?>
					
					<?php 
					if( $show_availability && function_exists('wd_template_single_availability') ){
						wd_template_single_availability();
					}
					
					if( $show_sku && function_exists('wd_template_single_sku') ){
						wd_template_single_sku();
					}
					?>
					
					<?php if( $show_desc ): ?>
					<div class="description">
						<?php
						if( $desc_from == 'content' ){
							the_content();
						}
						else{
							the_excerpt(); 
						}
						?>
					</div>
					<?php endif; ?>
					
					<?php if( $show_price ): ?>
					<div class="price_wrapper">
						<?php woocommerce_template_loop_price(); ?>
					</div>
					<?php endif; ?>
					
					<?php if( $show_add_to_cart ): ?>
					<div class="add_to_cart_wrapper">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>
					<?php endif; ?>
				</div>
				<?php endwhile; ?>
			</div>
			<?php
			endif;
			wp_reset_query();
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';
		}
	}
	add_shortcode('individual_product', 'wd_individual_product_function');
	
	/* Custom Product */
	if(!function_exists('wd_custom_product_function')){
		function wd_custom_product_function($atts,$content){
			extract(shortcode_atts(array(
				'style' 			=> 1 /* 1, 2, big */
				,'id' 				=> 0
				,'sku' 				=> ''
				,'title' 			=> ''
				,'show_add_to_cart' => 1
				,'show_short_desc' 	=> 1
				,'show_sku' 		=> 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1
				,'show_categories'	=> 0
			),$atts));
			
			if (empty($atts)) return;
				
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
		
			wp_reset_query(); 
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => 1,
				'no_found_rows' => 1,
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);

			if(isset($atts['sku'])){
				$args['meta_query'][] = array(
					'key' => '_sku',
					'value' => $atts['sku'],
					'compare' => '='
				);
			}

			if(isset($atts['id'])){
				$args['p'] = $atts['id'];
			}

			ob_start();	
			$products = new WP_Query( $args );
			if ( $products->have_posts() ) : 
				if(strlen(trim($title)) >0)
					echo "<div class='wp_title_shortcode_products'><h3 class='heading-title'>{$title}</h3></div>";
			?>	
				<div class="custom-product-shortcode <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>">
				<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
						
						<?php		
						//start product-content.Copy from core code
							
						global $product, $woocommerce_loop;
						$old_loop = $woocommerce_loop;
						// Store loop count we're currently on
						if ( empty( $woocommerce_loop['loop'] ) )
							$woocommerce_loop['loop'] = 0;

						// Store column count for displaying the grid
						if ( empty( $woocommerce_loop['columns'] ) )
							$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

						// Ensure visibility
						if ( ! $product->is_visible() )
							return;

						// Increase loop count
						$woocommerce_loop['loop']++;

						// Extra post classes
						$classes = array();
						if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
							$classes[] = 'first';
						if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
							$classes[] = 'last';
							
						?>
						<li <?php post_class( $classes ); ?>>

							<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
							<div class="product_item_wrapper">
								<div class="product_thumbnail_wrapper">
									
									
								
									<a href="<?php the_permalink(); ?>">

										<?php
											/**
											 * woocommerce_before_shop_loop_item_title hook
											 *
											 * @hooked woocommerce_show_product_loop_sale_flash - 10
											 * @hooked woocommerce_template_loop_product_thumbnail - 10
											 */
											if( $style != 'big' ){
												do_action( 'woocommerce_before_shop_loop_item_title' );
											}
											else{
												echo woocommerce_get_product_thumbnail('shop_single');
											}
										?>

										<?php
											/**
											 * woocommerce_after_shop_loop_item_title hook
											 *
											 * @hooked woocommerce_template_loop_price - 10
											 */
											do_action( 'woocommerce_after_shop_loop_item_title' );
										?>

									</a>
								
								</div>
								
								<?php //do_action( 'woocommerce_after_shop_loop_item' ); ?>
								
								<div class="product-meta-wrapper">
									<?php 
										if( (int)$show_categories )
											get_product_categories();
									?>
									<h3 class="heading-title product-title"><a href="<?php echo get_permalink(); ?>"><?php the_title();?></a></h3>
									<?php
										if( (int)$show_short_desc && function_exists('wd_template_loop_excerpt') ){
											wd_template_loop_excerpt();
										}
										
										if((int)$show_sku)
											add_sku_to_product_list();
										woocommerce_template_loop_price();
										
										if( function_exists('wd_open_div_list_add_to_cart'))
											wd_open_div_list_add_to_cart();
										if((int)$show_add_to_cart){
											if( function_exists('wd_list_template_loop_add_to_cart'))
												wd_list_template_loop_add_to_cart();
										}
										if( function_exists('wd_add_wishlist_button_to_product_list'))
											wd_add_wishlist_button_to_product_list();
										if( function_exists('wd_add_compare_button_to_product_list'))
											wd_add_compare_button_to_product_list();
										if((int)$show_rating)
											woocommerce_template_loop_rating();
										if( function_exists('wd_close_div_list_add_to_cart'))
											wd_close_div_list_add_to_cart();
										
									?>
								</div>
							</div>
							
						</li>
						
						<?php //end of copy ?>
						
					<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>
				</div>
			<?php endif;
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					"use strict";
					
					jQuery('.custom-product-shortcode .products').addClass('no_quickshop');
					jQuery('.custom-product-shortcode .products .em_quickshop_handler').remove();
				});
			</script>
			<?php
			$woocommerce_loop = $old_loop;
			wp_reset_postdata();
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			//add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';
		}
	}
	add_shortcode('custom_product','wd_custom_product_function');
	
	
	
	if(!function_exists('wd_custom_products_function')){
		function wd_custom_products_function($atts,$content){
			extract(shortcode_atts(array(
				'style' 			=> 1
				,'ids' 				=> 0
				,'skus' 			=> ''
				,'show_sku' 		=> 1
				,'show_rating' 		=> 1
				,'show_title' 		=> 1
				,'show_price' 		=> 1
				,'show_short_desc' 	=> 1
				,'show_label' 		=> 1
				,'show_label_title' => 0
				,'show_categories' 	=> 0
				,'show_add_to_cart' => 1
			),$atts));
			
			if (empty($atts)) return;
			
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			
			global $woocommerce_loop;
			wp_reset_query(); 
			
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			extract(shortcode_atts(array(
				'columns' 	=> '4',
				'orderby'   => 'title',
				'order'     => 'asc'
				), $atts));

			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'orderby' => $orderby,
				'order' => $order,
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
						'key' 		=> '_visibility',
						'value' 	=> array('catalog', 'visible'),
						'compare' 	=> 'IN'
					)
				)
			);

			if( isset($atts['skus']) ){
				$skus = explode(',', $atts['skus']);
				$skus = array_map('trim', $skus);
				$args['meta_query'][] = array(
					'key' 		=> '_sku',
					'value' 	=> $skus,
					'compare' 	=> 'IN'
				);
			}

			if(isset($atts['ids']) && trim($atts['ids'])!=""){
				$ids = explode(',', $atts['ids']);
				$ids = array_map('trim', $ids);
				$args['post__in'] = $ids;
			}

			ob_start();
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$products = new WP_Query( $args );
			
			$woocommerce_loop['columns'] = $columns;
			if ( $products->have_posts() ) : ?>
				<div class="custom-products-shortcode <?php echo 'style-'.$style; ?> <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>">
				<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

				<?php woocommerce_product_loop_end(); ?>
				</div>
			<?php endif;
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					//jQuery('.custom-products-shortcode .products').addClass('no_quickshop');
				});
			</script>
			<?php 
			wp_reset_postdata();
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			add_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );
			add_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );	
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';			
			
		}
	}	
	
	
	add_shortcode('custom_products','wd_custom_products_function');

	
	/*
	*	columns : 3 or 4
	*	style : 1 or 2 or 3
	*	per_page : 4 to 12
	*	title : ""
	*	desc : ""
	*	show nav thumb : 1
	* 	show thumb : 1
	*	show title : 1
	* 	show sku : 1
	*	show price
	*	show label
	* 	item slide : 1
	*/
	

	
	if(!function_exists('wd_featured_product_slider_function')){
		function wd_featured_product_slider_function($atts,$content){
			wp_reset_query(); 
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'product_cats' 	=> ''
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'show_nav' 		=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1	
				,'show_label_title' => 0	
				,'show_categories'	=> 0	
				,'show_add_to_cart' => 1
			),$atts));
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
		
			global $woocommerce_loop;
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );	
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					),
					array(
						'key' => '_featured',
						'value' => 'yes'
					)
				)
			);
			
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0 && $product_cats !== 'all-product-cats' ){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}

			ob_start();
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$products = new WP_Query( $args );

			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_slider_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
						
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('.slideshow-wrapper #<?php echo $_random_id; ?> ul.products').addClass('no_quickshop');
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);
						
						});
					});
				//]]>	
				</script>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('featured_product_slider','wd_featured_product_slider_function');
	
	
	/* featured product no slider*/
		if(!function_exists('wd_featured_product_function')){
		function wd_featured_product_function($atts,$content){
			wp_reset_query(); 
			extract(shortcode_atts(array(
				'columns' 				=> 4
				,'style' 				=> 1
				,'per_page' 			=> 8
				,'product_cats'			=> ''
				,'title' 				=> ''
				,'icon_title_class'		=> ''
				,'desc' 				=> ''
				,'show_image' 			=> 1
				,'show_title' 			=> 1
				,'show_sku' 			=> 1
				,'show_price' 			=> 1
				,'show_short_desc'  	=> 1
				,'show_rating' 			=> 1
				,'show_label' 			=> 1	
				,'show_label_title' 	=> 0	
				,'show_categories'		=> 1	
				,'show_short_content' 	=> 1
				,'show_add_to_cart' 	=> 1
				,'show_load_more' 		=> 0
			),$atts));
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop;
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
				
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );					
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					),
					array(
						'key' => '_featured',
						'value' => 'yes'
					)
				)
			);
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}

			
			ob_start();
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$products = new WP_Query( $args );

			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_wrapper_'.rand(); ?>
				<div class="featured_product_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
					
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						
					</div>
					<?php if( (int)$show_load_more && $products->max_num_pages > 1 ){
							wd_product_shortcode_show_load_more_button('featured',$_random_id,$atts);
						} ?>
				</div>
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action ( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action ( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action ( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						"use strict";
						
						var random_id = "<?php echo $_random_id; ?>";
						var columns = "<?php echo $columns; ?>";
						var li_class = "col-sm-6";
						if(columns!=""){
							li_class = "col-sm-"+(24/parseInt(columns));
						}
						jQuery("#"+random_id).find("ul.products li").removeClass("col-sm-24 col-sm-12 col-sm-8 col-sm-6 col-sm-4 col-sm-3");
						jQuery("#"+random_id).find("ul.products li").addClass(li_class);
						
					});
					
				</script>
			<?php
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('featured_product','wd_featured_product_function');
	/* featured product no slider*/
	

	if(!function_exists('wd_featured_by_category_function')){
		function wd_featured_by_category_function($cat_slug = '',$per_page = 1){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			wp_reset_query(); 
			$args = array(
				'post_type'	=> 'product'
				,'post_status' => 'publish'
				,'ignore_sticky_posts'	=> 1
				,'posts_per_page' => $per_page
				,'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
					,array(
						'key' => '_featured',
						'value' => 'yes'
					)
				)
				,'tax_query' 			=> array(
					array(
						'taxonomy' 		=> 'product_cat',
						'terms' 		=> array( esc_attr($cat_slug) ),
						'field' 		=> 'slug',
						'operator' 		=> 'IN'
					)
				)
			);
			wp_reset_query(); 
			$products = new WP_Query( $args );
			if( $products->have_posts() ){
				global $post;
				$products->the_post();
				$product = get_product( $post->ID );
				return $product;
			}
			return NULL;
		}
	}
			
	
	function wd_order_by_rating_post_clauses( $args ) {
		global $wpdb;

		$args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";

		$args['where'] .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";

		$args['join'] .= "
			LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
			LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
		";

		$args['orderby'] = "average_rating DESC, $wpdb->posts.post_date DESC";

		$args['groupby'] = "$wpdb->posts.ID";

		return $args;
	}	
	

	/*
	*	columns : 3 or 4
	*	style : 1 or 2 or 3
	*	per_page : 4 to 12
	*	title : ""
	*	desc : ""
	*	product_tag : tag of prods
	*	show nav thumb : 1
	* 	show thumb : 1
	*	show title : 1
	* 	show sku : 1
	*	show price
	*	show label
	* 	item slide : 1
	*/
	

	
	if(!function_exists('wd_popular_product_slider_function')){
		function wd_popular_product_slider_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'product_tag' 		=> ''
				,'product_cats' 	=> ''
				,'show_nav' 		=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1
				,'show_label_title' => 0
                ,'show_categories' 	=> 0		
				,'show_add_to_cart' => 1				
			),$atts));
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );						
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );				
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
			wp_reset_query(); 
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'orderby' => 'date',
				'order' => 'desc',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}
			
			if( strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
				$args = array_merge($args, array('product_tag' => $product_tag));
			}
			
			ob_start();

	  	add_filter( 'posts_clauses', 'wd_order_by_rating_post_clauses' );

		$products = new WP_Query( $args );

		remove_filter( 'posts_clauses', 'wd_order_by_rating_post_clauses' );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_slider_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
						
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);
						
						});
					});
				//]]>	
				</script>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns ;
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('popular_product_slider','wd_popular_product_slider_function');

	
	/*
	*	columns : 3 or 4
	*	style : 1 or 2 or 3
	*	per_page : 4 to 12
	*	title : ""
	*	desc : ""
	*	product_tag : tag of prods
	*	show_image : 1
	* 	show thumb : 1
	*	show_title : 1
	* 	show_sku : 0
	*	show_price: 1
	*	show_rating: 1
	* 	show_label : 1
	* 	show_categories : 0
	* 	show_add_to_cart : 1
	*/
	

	
	if(!function_exists('wd_popular_product_function')){
		function wd_popular_product_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'product_cats'		=> ''
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'product_tag' 		=> ''
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1
				,'show_label_title' => 0
                ,'show_categories' 	=> 0		
				,'show_add_to_cart' => 1	
				,'show_load_more' 	=> 0
			),$atts));
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );						
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );				
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
			wp_reset_query(); 
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'orderby' => 'date',
				'order' => 'desc',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);
			
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}
		
			
			if( strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
				$args = array_merge($args, array('product_tag' => $product_tag));
			}
			
			ob_start();

	  	add_filter( 'posts_clauses', 'wd_order_by_rating_post_clauses' );

		$products = new WP_Query( $args );

		remove_filter( 'posts_clauses', 'wd_order_by_rating_post_clauses' );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_wrapper_'.rand(); ?>
				<div class="featured_product_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						<?php if( (int)$show_load_more && $products->max_num_pages > 1 ){
							wd_product_shortcode_show_load_more_button('popular',$_random_id,$atts);
						} ?>
					</div>
				</div>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns ;
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('popular_product','wd_popular_product_function');
	
	/* Recent product no slider */
	if(!function_exists('wd_recent_product_function')){
		function wd_recent_product_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'product_tag' 		=> ''
				,'product_cats' 	=> ''
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1
				,'show_label_title' => 0
                ,'show_categories' 	=> 0
				,'show_add_to_cart' => 1
				,'show_load_more' 	=> 0
			),$atts));
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
				
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );					
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );				
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'orderby' => 'date',
				'order' => 'desc',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);
		
			wp_reset_query(); 
			
			if( strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
				$args = array_merge($args, array('product_tag' => $product_tag));
			}
			$product_cats = trim($product_cats);
			if( strlen($product_cats) > 0 ){
				$arr_query_tax = array(
									'tax_query' => array(
													array(
														'taxonomy' => 'product_cat'
														,'terms' => array_map('trim',explode(',',$product_cats))
														,'field' =>'slug'
														,'operator' => 'IN'
														)
													)
									);
				$args = array_merge($args, $arr_query_tax);
			}
		
			ob_start();

			$products = new WP_Query( $args );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$woocommerce_loop['columns'] = $columns;
			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_wrapper_'.rand(); ?>
				<div class="featured_product_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						<?php if( (int)$show_load_more && $products->max_num_pages > 1 ){
							wd_product_shortcode_show_load_more_button('recent',$_random_id,$atts);
						} ?>
					</div>
				</div>
			<?php endif;

			wp_reset_postdata();

			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );	
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns ;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('recent_product','wd_recent_product_function');


	/*
	*	columns : 3 or 4
	*	style : 1 or 2 or 3
	*	per_page : 4 to 12
	*	title : ""
	*	desc : ""
	*	product_tag : tag of prods
	*	show nav thumb : 1
	* 	show thumb : 1
	*	show title : 1
	* 	show sku : 1
	*	show price
	*	show label
	* 	item slide : 1
	*/
	

	
	if(!function_exists('wd_recent_product_slider_function')){
		function wd_recent_product_slider_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'product_tag' 		=> ''
				,'product_cats' 	=> ''
				,'show_nav' 		=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1
				,'show_label_title' => 0
                ,'show_categories' 	=> 0
				,'show_add_to_cart' => 1
			),$atts));
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
				
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );					
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );				
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'orderby' => 'date',
				'order' => 'desc',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);
		
			wp_reset_query(); 
			
			if( strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
				$args = array_merge($args, array('product_tag' => $product_tag));
			}
			$product_cats = trim($product_cats);
			if( strlen($product_cats) > 0 ){
				$arr_query_tax = array(
									'tax_query' => array(
													array(
														'taxonomy' => 'product_cat'
														,'terms' => array_map('trim',explode(',',$product_cats))
														,'field' =>'slug'
														,'operator' => 'IN'
														)
													)
									);
				$args = array_merge($args, $arr_query_tax);
			}
			
			ob_start();

			$products = new WP_Query( $args );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$woocommerce_loop['columns'] = $columns;
			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_slider_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
						
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);
						
						});
					});
				//]]>	
				</script>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );	
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns ;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('recent_product_slider','wd_recent_product_slider_function');




		/*
	*	columns : 3 or 4
	*	style : 1 or 2 or 3
	*	per_page : 4 to 12
	*	title : ""
	*	desc : ""
	*	product_tag : tag of prods
	*	show nav thumb : 1
	* 	show thumb : 1
	*	show title : 1
	* 	show sku : 1
	*	show price
	*	show label
	* 	item slide : 1
	*/
	

	
	if(!function_exists('wd_best_selling_product_slider_function')){
		function wd_best_selling_product_slider_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'product_cats'		=> ''
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'show_nav' 		=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1		
				,'show_label_title' => 0		
				,'show_categories' 	=> 0		
				,'show_add_to_cart' => 1				
			),$atts));
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );				
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'order' => 'desc',		
				'meta_key' 		 => 'total_sales',
				'orderby' 		 => 'meta_value_num',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}
		
			wp_reset_query(); 
			
			/*if( strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
				$args = array_merge($args, array('product_tag' => $product_tag));
			}*/
			
			ob_start();
			
			$products = new WP_Query( $args );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];	
			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_slider_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
						
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);
						
						});
					});
				//]]>	
				</script>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );	
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('best_selling_product_slider','wd_best_selling_product_slider_function');
	
	if(!function_exists('wd_best_selling_product_function')){
		function wd_best_selling_product_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'product_cats'		=> ''
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1		
				,'show_label_title'	=> 0		
				,'show_categories' 	=> 0		
				,'show_add_to_cart' => 1
				,'show_load_more' 	=> 0
			),$atts));
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );				
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'order' => 'desc',		
				'meta_key' 		 => 'total_sales',
				'orderby' 		 => 'meta_value_num',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
			);
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}
		
			wp_reset_query(); 
			
			
			ob_start();
			
			$products = new WP_Query( $args );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];	
			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_wrapper_'.rand(); ?>
				<div class="featured_product_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						<?php if( (int)$show_load_more && $products->max_num_pages > 1 ){
							wd_product_shortcode_show_load_more_button('best_selling',$_random_id,$atts);
						} ?>
					</div>
				</div>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );	
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('best_selling_product','wd_best_selling_product_function');
	
	
	/* No use */
	if(!function_exists('wd_best_selling_product_by_category_slider_function')){
		function wd_best_selling_product_by_category_slider_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop, $woocommerce;
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'desc' 			=> ''
				,'product_tag' 		=> ''
				,'show_nav' 		=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1		
				,'show_categories' 	=> 0
				,'cat_slug'			=> ''			
			),$atts));
			
			if($cat_slug=='' && has_term( $cat_slug, 'product_cat', 'product' )){
				echo 'cxc';
				return;
			}
			
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );					
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'order' => 'desc',		
				'meta_key' 		 => 'total_sales',
				'orderby' 		 => 'meta_value_num',				
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					)
				)
				,'tax_query' 			=> array(
					array(
						'taxonomy' 		=> 'product_cat',
						'terms' 		=> array( esc_attr($cat_slug) ),
						'field' 		=> 'slug',
						'operator' 		=> 'IN'
					)
				)
			);
		
			wp_reset_query(); 
			
			if( strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
				$args = array_merge($args, array('product_tag' => $product_tag));
			}
			
			ob_start();

			$products = new WP_Query( $args );
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];	
			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_slider_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0)
								echo "<div class='wp_title_shortcode_products'><h3 class='heading-title slider-title'>{$title}</h3></div>";
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
						
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);
						
						});
					});
				//]]>	
				</script>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );			
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('best_selling_product_by_category_slider','wd_best_selling_product_by_category_slider_function');
	
	
	if(!function_exists('wd_custom_categories_function')){
		function wd_custom_categories_function($atts,$content){
			extract(shortcode_atts(array(
				'number' => 0
				,'columns' 			=> 4
				,'parent' 			=> ''
				,'ids'	 			=> ''
				,'hide_empty'		=> '0'
				,'show_nav' 		=> 1
				,'show_item_title'	=> 0
				,'title'			=> ''
				,'icon_title_class'	=> ''
				,'desc'				=> ''
			),$atts));
			
			if (empty($atts)) return;
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop;
			$args = array(
				'orderby'     => 'name'
				,'order'      => 'ASC'
				,'hide_empty' => $hide_empty
				,'include'    => explode(',',$ids)
				,'pad_counts' => true
				,'child_of'   => ''
				,'parent'     => $parent
			);
			
			$product_categories = get_terms( 'product_cat', $args );
			foreach ( $product_categories as $key => $category ) {
				if ( $category->count == 0 ) {
					unset( $product_categories[ $key ] );
				}
			}
			if( $number != 0 && $number !=""){
				$product_categories = array_slice( $product_categories, 0, $number );
			}
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$woocommerce_loop['columns'] = $columns;
			
			ob_start();
			$woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';
			?>
			
			<?php $_random_id = 'featured_categories_slider_wrapper_'.rand(); ?>
				<div class="featured_categories_slider_wrapper" id="<?php echo $_random_id; ?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title slider-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading">
						<?php //echo do_shortcode('[product_categories number="'.$number.'"]'); 
							if ( $product_categories ) {
								woocommerce_product_loop_start();
								foreach ( $product_categories as $category ) {
									wc_get_template( 'content-product_cat.php', array(
										'category' => $category
									) );
								}
								woocommerce_product_loop_end();
							}
							woocommerce_reset_loop();
						?>
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					var _columns = <?php echo $columns; ?>;
					var _random_id = jQuery('#<?php echo $_random_id; ?>');
					
					/* Config show_item_title option */
					var _show_item_title = <?php echo $show_item_title; ?>;
					if(!_show_item_title){
						_random_id.find("ul.products li.product h3").remove();
					}
					
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
							,responsive:{
									0:{
										items : 1
									},
									150:{
										items : 2
									},
									300:{
										items : 3
									},
									450:{
										items : 4
									},
									600:{
										items : 5
									},
									750:{
										items : 6
									},
									900:{
										items : <?php echo $columns;?>
									}
								}
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);

						});
					});
				//]]>	
				</script>
			<?php
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';			
			
		}
	}	
	
	
	add_shortcode('product_categories_slider','wd_custom_categories_function');
	
	if(!function_exists('wd_custom_categories_2_function')){
		function wd_custom_categories_2_function($atts,$content){
			extract(shortcode_atts(array(
				'number' 			=> 0
				,'columns' 			=> 4
				,'parent' 			=> ''
				,'ids'	 			=> ''
				,'hide_empty'		=> '0'
				,'title'			=> ''
				,'icon_title_class'	=> ''
				,'desc'				=> ''
			),$atts));
			
			if (empty($atts)) return;
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop;
			$args = array(
				'orderby'     => 'name'
				,'order'      => 'ASC'
				,'hide_empty' => $hide_empty
				,'include'    => explode(',',$ids)
				,'pad_counts' => true
				,'child_of'   => ''
				,'parent'     => $parent
				,'number'	  => $number
			);
			
			$product_categories = get_terms( 'product_cat', $args );
			
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$woocommerce_loop['columns'] = $columns;
			
			ob_start();
			?>
			
			<?php $_random_id = 'product_categories_2_wrapper_'.rand(); ?>
				<div class="product_categories_2_wrapper" id="<?php echo $_random_id; ?>">
					<div class="product_categories_2_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title slider-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="product_categories_2_inner">
						<?php 
							if ( $product_categories ) {
								woocommerce_product_loop_start();
								foreach ( $product_categories as $category ) {
									wc_get_template( 'content-product_cat_2.php', array(
										'category' => $category
									) );
								}
								woocommerce_product_loop_end();
							}
							woocommerce_reset_loop();
						?>
					</div>
				</div>
			<?php
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';			
			
		}
	}	
	
	add_shortcode('product_categories_2','wd_custom_categories_2_function');
	
	
	if(!function_exists('wd_sale_product_slider_function')){
		function wd_sale_product_slider_function($atts,$content){
			wp_reset_query(); 
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'product_cats'		=> ''
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'show_nav' 		=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1	
				,'show_label_title' => 0	
				,'show_categories'	=> 0	
				,'show_add_to_cart' => 1
			),$atts));
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop;
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );						
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					),
					array(
						'key' => '_sale_price',
						'value' =>  0,
						'compare'   => '>',
						'type'      => 'NUMERIC'
					)
				)
			);
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}

			ob_start();
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$products = new WP_Query( $args );

			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_slider_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_slider_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_slider_wrapper_inner loading <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						
						<?php if($show_nav):?>
						<div class="slider_control">
							<a title="prev" id="<?php echo $_random_id;?>_prev" class="prev" href="#">&lt;</a>
							<a title="next" id="<?php echo $_random_id;?>_next" class="next" href="#">&gt;</a>
						</div>
						<?php endif;?>
						
					</div>
				</div>
				<?php global $smof_data; ?>
				<script type='text/javascript'>
				//<![CDATA[
					jQuery(document).ready(function() {
						"use strict";
						// Using custom configuration
						jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner').imagesLoaded(function(){
						var $_this = jQuery('#<?php echo $_random_id?> > .featured_product_slider_wrapper_inner');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_mobile'])?(int)$smof_data['wd_shop_slider_slide_speed_mobile']:200; ?>;
						<?php } else { ?>
							var slide_speed = <?php echo isset($smof_data['wd_shop_slider_slide_speed_pc'])?(int)$smof_data['wd_shop_slider_slide_speed_pc']:800; ?>;
						<?php } ?>
						var scroll_per_page = <?php echo isset($smof_data['wd_shop_slider_scroll_per_page'])?$smof_data['wd_shop_slider_scroll_per_page']:0; ?>;
						scroll_per_page = ( scroll_per_page == 1 )?'page':1;
						var infinity_loop = <?php echo isset($smof_data['wd_shop_slider_infinity_loop'])?$smof_data['wd_shop_slider_infinity_loop']:1; ?>;
						infinity_loop = ( infinity_loop == 1 );
						var rewind_nav = <?php echo isset($smof_data['wd_shop_slider_rewind_nav'])?$smof_data['wd_shop_slider_rewind_nav']:1; ?>;
						rewind_nav = ( rewind_nav == 1 );
						var auto_play = <?php echo isset($smof_data['wd_shop_slider_auto_play'])?$smof_data['wd_shop_slider_auto_play']:0; ?>;
						auto_play = ( auto_play == 1 );
						var stop_on_hover = <?php echo isset($smof_data['wd_shop_slider_stop_on_hover'])?$smof_data['wd_shop_slider_stop_on_hover']:0; ?>;
						stop_on_hover = ( stop_on_hover == 1 );
						var mouse_drag = <?php echo isset($smof_data['wd_shop_slider_mouse_drag'])?$smof_data['wd_shop_slider_mouse_drag']:0; ?>;
						mouse_drag = ( mouse_drag == 1 );
						var touch_drag = <?php echo isset($smof_data['wd_shop_slider_touch_drag'])?$smof_data['wd_shop_slider_touch_drag']:1; ?>;
						touch_drag = ( touch_drag == 1 );
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						
						var options = {
							loop : infinity_loop
							,navSpeed : slide_speed
							,slideBy : scroll_per_page
							,navRewind : rewind_nav
							,autoplay : auto_play
							,autoplayHoverPause : stop_on_hover
							,mouseDrag : mouse_drag
							,touchDrag : touch_drag
							,responsiveRefreshRate : responsive_refresh_rate
						};
						$_this.wd_shortcode_generate_product_slider(options,<?php echo $columns; ?>);
						
						});
					});
				//]]>	
				</script>
				
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('sale_product_slider','wd_sale_product_slider_function');
	
	if(!function_exists('wd_sale_product_function')){
		function wd_sale_product_function($atts,$content){
			wp_reset_query(); 
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'product_cats'		=> ''
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1	
				,'show_label_title' => 0	
				,'show_categories'	=> 0	
				,'show_add_to_cart' => 1
				,'show_load_more' 	=> 0
			),$atts));
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce_loop;
			if(!(int)$show_image)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );						
			if(!(int)$show_label)
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			
			$args = array(
				'post_type'	=> 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> 1,
				'posts_per_page' => $per_page,
				'meta_query' => array(
					array(
						'key' => '_visibility',
						'value' => array('catalog', 'visible'),
						'compare' => 'IN'
					),
					array(
						'key' => '_sale_price',
						'value' =>  0,
						'compare'   => '>',
						'type'      => 'NUMERIC'
					)
				)
			);
			$product_cats = str_replace(' ','',$product_cats);
			if( strlen($product_cats) > 0){
				$args['tax_query'] = array(
										array(
											'taxonomy' => 'product_cat',
											'terms' => explode(',',$product_cats),
											'field' => 'slug',
											'include_children' => false
										)
									);
			}

			ob_start();
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$products = new WP_Query( $args );

			$woocommerce_loop['columns'] = $columns;

			if ( $products->have_posts() ) : ?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_wrapper_meta"> 
						<?php
							if(strlen(trim($title)) >0){
								$has_icon = ($icon_title_class != '')?'has_icon':'';
								$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
								?>
								<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3></div>
								<?php
							}
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">
						
						<?php woocommerce_product_loop_start(); ?>

							<?php while ( $products->have_posts() ) : $products->the_post(); ?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						<?php if( (int)$show_load_more && $products->max_num_pages > 1 ){
							wd_product_shortcode_show_load_more_button('sale',$_random_id,$atts);
						} ?>
					</div>
				</div>
			<?php endif;

			wp_reset_postdata();

			
			
			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
			
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('sale_product','wd_sale_product_function');
	
	
	
	if(!function_exists('wd_product_filter_by_sub_category_function')){
		function wd_product_filter_by_sub_category_function($atts,$content){
			wp_reset_query(); 
			extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'icon_title_class'	=> ''
				,'desc' 			=> ''
				,'product_cats' 	=> ''
				,'show_nav'			=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1	
				,'show_label_title' => 0	
				,'show_categories'	=> 0	
				,'show_add_to_cart' => 1
			),$atts));
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			
			$product_cats = trim($product_cats);
			if( strlen($product_cats) == 0)
				return;
				
			$_sub_prod_cat = array();
			
			$_prod_cats = array_map('trim',explode(',',$product_cats));
			foreach( $_prod_cats as $cat ){
				$prod_cat = get_term_by('slug', esc_attr($cat), 'product_cat');
				$sub_prod_cat = wd_get_sub_categories($prod_cat->term_id);
				$_sub_prod_cat = array_merge($_sub_prod_cat,$sub_prod_cat);
			}

			if( count($_sub_prod_cat) == 0 )
				return;
			
			ob_start();
			?>
				<?php $_random_id = 'featured_product_slider_wrapper_'.rand(); ?>
				<div class="featured_product_wrapper <?php echo 'style-'.$style; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id;?>">
					<div class="featured_product_wrapper_meta"> 
						<?php
							$has_icon = ($icon_title_class != '')?'has_icon':'';
							$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
							?>
							<div class='wp_title_shortcode_products <?php echo $has_icon; ?>'><h3 class='heading-title'><?php echo $icon_html; ?><?php echo esc_html($title); ?></h3>
								<div class="wd_list_categories loading">
									<ul>
									<?php foreach($_sub_prod_cat as $key => $sub_cat){ ?>
										<li>
											<a class="link_cat <?php echo ($key==0)?'current':''; ?>" href="javascript:void(0)" data-slug="<?php echo $sub_cat->slug; ?>"><?php echo esc_html($sub_cat->name); ?></a>
										</li>
									<?php } ?>
									</ul>
									<?php if( $show_nav ): ?>
									<div class="slider_control">
										<a href="#" class="prev">&lt;</a>
										<a href="#" class="next">&gt;</a>
									</div>
									<?php endif; ?>
								</div>
							<?php
							echo "</div>";
							if(strlen(trim($desc)) >0)	
								echo "<p class='desc-wrapper'>{$desc}</p>";
						?>
					</div>
					<?php 
						echo wd_product_filter_by_sub_category_load_content($atts, $_sub_prod_cat[0]->slug);
					?>
				</div>
				
				<script type="text/javascript">
					jQuery(document).ready(function($){
						"use strict";
						
						var _random_id = jQuery('#<?php echo $_random_id; ?>');
						var _shortcode_data_<?php echo $_random_id ?> = [];
						
						var current_slug = _random_id.find('.wd_list_categories ul li a.link_cat.current').attr('data-slug');
						/*_shortcode_data_<?php echo $_random_id ?>[current_slug] = _random_id.find('.featured_product_wrapper_inner').html();*/
						
						_random_id.find('.wd_list_categories ul li a.link_cat').live('click', function(){
							if( jQuery(this).hasClass('current') || _random_id.find('.featured_product_wrapper_inner').hasClass('loading') )
								return;
							_random_id.find('.wd_list_categories ul li a.link_cat').removeClass('current');
							jQuery(this).addClass('current');
							
							var sub_cat_slug = jQuery(this).attr('data-slug');
							if( _shortcode_data_<?php echo $_random_id ?>[sub_cat_slug] ){
								_random_id.find('.featured_product_wrapper_inner').fadeOut(300, function(){
									_random_id.find('.featured_product_wrapper_inner').remove();
									_random_id.append(_shortcode_data_<?php echo $_random_id ?>[sub_cat_slug]);
									if( typeof wd_qs_prettyPhoto == 'function' ){
										wd_qs_prettyPhoto();
									}
								});
								return;
							}
							
							_random_id.find('.featured_product_wrapper_inner').addClass('loading');
							
							var data = {
								action : 'wd_product_filter_by_sub_category_load_content'
								,atts : <?php echo json_encode($atts); ?>
								,sub_cat_slug : sub_cat_slug
							};
						
							jQuery.ajax({
								type : "POST",
								timeout : 30000,
								url : _ajax_uri,
								data : data,
								error: function(xhr,err){
									_random_id.find('.featured_product_wrapper_inner').removeClass('loading');
								},
								success: function(response) {
									_random_id.find('.featured_product_wrapper_inner').remove();
									_random_id.append(response);
									_shortcode_data_<?php echo $_random_id ?>[sub_cat_slug] = response;
									if( typeof wd_qs_prettyPhoto == 'function' ){
										wd_qs_prettyPhoto();
									}
								}
							});
									
						});
						
						//Add slider for list category
						var $_this = jQuery('#<?php echo $_random_id?> .wd_list_categories');
						<?php if( wp_is_mobile() ){ ?>
							var slide_speed = 200;
						<?php } else { ?>
							var slide_speed = 800;
						<?php } ?>
						var responsive_refresh_rate = <?php echo (wp_is_mobile())?400:200; ?>;
						if( navigator.platform === 'iPod' ){
							slide_speed = 0;
							responsive_refresh_rate = 1000;
						}
						var owl = $_this.find('ul').owlCarousel({
								loop : true
								,nav : false
								,dots : false
								,navSpeed : slide_speed
								,slideBy: 1
								,rtl:jQuery('body').hasClass('rtl')
								,margin:10
								,navRewind: false
								,autoplay: false
								,autoplayTimeout: 5000
								,autoplayHoverPause: false
								,autoplaySpeed: false
								,mouseDrag: true
								,touchDrag: true
								,responsiveBaseElement: $_this
								,responsiveRefreshRate: responsive_refresh_rate
								,responsive:{
									0:{
										items : 1
									},
									200:{
										items : 2
									},
									300:{
										items : 3
									},
									390:{
										items : 4
									},
									500:{
										items : 5
									},
									720:{
										items : 6
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

			wp_reset_postdata();
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
			
		}
	}		
	add_shortcode('product_filter_by_sub_category','wd_product_filter_by_sub_category_function');
	
	/* to do */
	add_action("wp_ajax_wd_product_filter_by_sub_category_load_content", "wd_product_filter_by_sub_category_load_content");
	add_action("wp_ajax_nopriv_wd_product_filter_by_sub_category_load_content", "wd_product_filter_by_sub_category_load_content");
	function wd_product_filter_by_sub_category_load_content($atts = array(), $sub_cat_slug = ''){
		if( isset($_POST['atts']) ){
			$atts = $_POST['atts'];
		}
		if( isset($_POST['sub_cat_slug']) ){
			$sub_cat_slug = $_POST['sub_cat_slug'];
		}
		
		extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'desc' 			=> ''
				,'product_cats' 	=> ''
				,'show_nav'			=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1	
				,'show_label_title' => 0	
				,'show_categories'	=> 0	
				,'show_add_to_cart' => 1
			),$atts));
			
		if(!(int)$show_image)
			remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
		if(!(int)$show_categories)
			remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
		if(!(int)$show_title)
			remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
		if(!(int)$show_sku)
			remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
		if(!(int)$show_price)
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
		if(!(int)$show_short_desc)
			remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
		if(!(int)$show_rating)
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );						
		if(!(int)$show_label)
			remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
		if(!(int)$show_add_to_cart)
			remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
		
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' => $per_page,
			'order' => 'date',
			'orderby' => 'desc',
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				),
			),
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'terms' => array( esc_attr($sub_cat_slug) ),
					'field' => 'slug',
					'include_children' => false
				)
			)
		);
		global $woocommerce_loop;
		$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
		$woocommerce_loop['columns'] = $columns;
		$products = new WP_Query( $args );
		ob_start();
		if ( $products->have_posts() ){
			?>
			<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">

				<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>
				<?php woocommerce_product_loop_end(); ?>
				
			</div>
				
			<?php
		}
		$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
		
		//add all the hook removed
		add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
		add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
		add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
		add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
		add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
		
		add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
		
		add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
		add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
		//end
		
		$html = ob_get_clean();
		if( is_ajax() ){
			die($html);
		}
		else{
			return $html;
		}
	}
	
	
	
	/********* Custom Product Category *******/
	if(!function_exists('wd_custom_products_category_function')){
		function wd_custom_products_category_function($atts,$content){
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
				return;
			}
			global $woocommerce, $woocommerce_loop;
			if ( empty( $atts ) ) return;
			extract( shortcode_atts( array(
				'per_page' 			=> 10
				,'columns'			=> 3
				,'title'			=> ''
				,'icon_title_class'	=> ''
				,'product_cats'		=> ''
				,'show_thumbnail' 	=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 1
				,'show_price'		=> 1
				,'show_short_desc' 	=> 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1
				,'show_label_title' => 0
				,'show_categories' 	=> 1
				,'show_add_to_cart' => 1
				), $atts ) );
			if( absint($columns) == 0 )
				$columns = 1;
			if(!(int)$show_categories)
				remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
			if(!(int)$show_title)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
			if(!(int)$show_sku)
				remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
				
			if(!(int)$show_price)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			if(!(int)$show_short_desc)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			if(!(int)$show_rating)
				remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );		
				remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
			if(!(int)$show_add_to_cart)
				remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
		
			if ( ! $product_cats ) return;
			wp_reset_query(); 
			$ar_prod_cat = explode(',',str_replace(' ','',$product_cats));
			// Default ordering args
			$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
			$_random_id = 'custom_category_shortcode_'.rand(0,1000);
			$_is_mobile = defined('WD_IS_MOBILE')?WD_IS_MOBILE:wp_is_mobile();
			ob_start();
			?>
			<div class="wd_custom_category_shortcode <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?> <?php echo ((int)$show_rating)?'has_rating':''; ?>" id="<?php echo $_random_id; ?>">
				<?php
				$has_icon = ($icon_title_class != '')?'has_icon':'';
				$icon_html = ($icon_title_class != '')?'<i class="fa '.$icon_title_class.'"></i>':'';
				?>
				<div class="wp_title_shortcode_products <?php echo $has_icon; ?>">
					<h3 class="heading-title"><?php echo $icon_html; ?><?php echo $title; ?></h3>
					<div class="wd_list_categories">
						<ul>
						<?php foreach($ar_prod_cat as $key => $prod_cat){ 
							$_prod_cat = get_term_by('slug', esc_attr($prod_cat), 'product_cat');
							if( !(isset($_prod_cat) && is_object($_prod_cat)) ){
								unset($ar_prod_cat[$key]);
								continue;
							}
						?>
							<li>
								<a class="link_cat <?php echo ($key==0)?'current':''; ?>" href="javascript:void(0)" data-slug="<?php echo $_prod_cat->slug; ?>"><?php echo esc_html($_prod_cat->name); ?></a>
							</li>
						<?php } ?>
						</ul>
					</div>
				</div>
			<?php
			
			foreach( $ar_prod_cat as $key=>$prod_cat ){
				$args = array(
					'post_type'				=> 'product'
					,'post_status' 			=> 'publish'
					,'ignore_sticky_posts'	=> 1
					,'orderby' 				=> 'meta_value_num'
					,'order' 				=> 'desc'
					,'meta_key' 			=> 'total_sales'
					,'posts_per_page' 		=> $per_page
					,'meta_query' 			=> array(
						array(
							'key' 			=> '_visibility'
							,'value' 		=> array('catalog', 'visible')
							,'compare' 		=> 'IN'
						)
					)
					,'tax_query' 			=> array(
						array(
							'taxonomy' 			=> 'product_cat'
							,'terms' 			=> array( esc_attr($prod_cat) )
							,'field' 			=> 'slug'
							,'operator' 		=> 'IN'
							,'include_children' => true
						)
					)
					
				);

				$products = new WP_Query( $args );
				$woocommerce_loop['columns'] = $columns;
				$_count = 0;
				$_post_count = isset($products->post_count)?$products->post_count:$per_page;
				$is_first_cat = true;
				
				$gallery_id = 'product-gallery-'.$prod_cat.'-'.rand(0,100);
				if ( $products->have_posts() ) : ?>

					<ul class="products no_quickshop" data-slug="<?php echo $prod_cat; ?>" style="<?php echo ($key != 0)?'display: none;':''; ?>" >

						<?php while ( $products->have_posts() ) : ?>

						<?php
							global $product, $post;
							$products->the_post();
							
							if( $is_first_cat ){ $is_first_cat = false;
							?>
							<li class="left-wrapper">
								<div class="wd-custom-category-left-wrapper">
									<ul>
										<li <?php post_class(); ?>>
											<div class="product_item_wrapper">
												<div class="product_thumbnail_wrapper">
											<?php 
												$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
												if( $_is_mobile ){
													$image_link  	= get_permalink( $post->ID );
												}
												else{
													$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
												}
												
												$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
													'title' => $image_title
													) );
												$product = get_product($post->ID);
												$attachment_count   = count( $product->get_gallery_attachment_ids() );

												if ( $attachment_count > 0 && !$_is_mobile ) {
													$gallery = 'prettyPhoto['.$gallery_id.']';
												} else {
													$gallery = '';
												}
												if( (int)$show_label ){
													$product_label = '<span class="product_label best_label"><span class="text">'.__('Best','wpdance').'</span>1</span>';
												}
												else{
													$product_label = '';
												}
												echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="" title="%s"  data-rel="' . $gallery . '">%s%s</a>', $image_link, $image_title,$product_label, $image ), $post->ID );
											?>
												</div>
												<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
											</div>
										</li>
									</ul>
									<input type="hidden" value="<?php echo $gallery_id; ?>" class="gallery_id" />
									<?php
									if( (int)$show_thumbnail ){
										echo wd_custom_products_category_get_thumbnail($gallery_id);
									}
									?>
								</div>
							</li>
							<?php
							}
							else{
								if( $_count == 0){
									echo "<li>
											<div class='wd-custom-category-right-wrapper'>
												<ul>";
								}
								$_count++;
								
								$_columns = absint($woocommerce_loop['columns']);
								$_sub_class = "col-sm-".(24/($_columns));
								if ( empty( $woocommerce_loop['loop'] ) )
									$woocommerce_loop['loop'] = 0;
								$woocommerce_loop['loop']++;

								$classes = array();
								if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $_columns || 1 == $_columns )
									$classes[] = 'first';
								if ( 0 == $woocommerce_loop['loop'] % $_columns )
									$classes[] = 'last';

								$classes[] = $_sub_class ;
								?>
								<li <?php post_class( $classes ); ?>>
									<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
									<div class="product_item_wrapper">
										<div class="product_thumbnail_wrapper">
											<?php
											$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
											if( $_is_mobile ){
												$image_link  	= get_permalink( $post->ID );
											}
											else{
												$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
											}
											$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
												'title' => $image_title
												) );
											if( (int)$show_label ){
												$product_label = '<span class="product_label">'.($_count+1).'</span>';
											}
											else{
												$product_label = '';
											}
											echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" class="" title="%s">%s%s</a>', $image_link, $image_title,$product_label, $image ), $post->ID );
											?>
										</div>
										<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
									</div>
								</li>
								<?php
								
								if( $_count >= $_post_count - 1 ){
									echo "</ul>
											</div>
												</li>";
								}
							}
						?>
							
						<?php endwhile; // end of the loop. ?>
					</ul>
				<?php endif;
			}
			?>
			</div>
			<script type="text/javascript">
				var _column = <?php echo $columns; ?>;
				var _class_column = 'col-sm-'+(24/parseInt(_column));
				var _ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
				var _wd_is_loading_thumbnail = 0;
				var _wd_custom_product_category_thumb = {};
				var _show_thumbnail = <?php echo $show_thumbnail; ?> == 1;
				jQuery(document).ready(function(){
					"use strict";
					var _random_id = jQuery('#<?php echo $_random_id; ?>');
					//Remove empty category
					_random_id.find('.wd_list_categories ul li a.link_cat').each(function(index,element){
						var _data_slug = jQuery(this).attr('data-slug');
						if( _random_id.find('ul.products[data-slug='+_data_slug+'] li').length == 0 )
							jQuery(element).parents('li').remove();
					});
					
					//Show product for first category
					var _data_slug = _random_id.find('.wd_list_categories ul li a.link_cat:first').attr('data-slug');
					if( typeof _data_slug != "undefined" ){
						_random_id.find('.wd_list_categories ul li a.link_cat:first').addClass('current');
						_random_id.find('ul.products[data-slug='+_data_slug+']').show().addClass('current');
					}
					
					_random_id.find('.wd_list_categories ul li a.link_cat').bind('click', function(){
						if( jQuery(this).hasClass('current') )
							return;
						_random_id.find('.wd_list_categories ul li a.link_cat').removeClass('current');
						jQuery(this).addClass('current');
						var _data_slug = jQuery(this).attr('data-slug');
						_random_id.find('ul.products.current').fadeOut(300, function(){
							_random_id.find('ul.products[data-slug='+_data_slug+']').fadeIn(250).addClass('current');
						}).removeClass('current');
					});
					
					/* Add prettyPhoto  */
					var ul_current = _random_id.find('ul.products.current');
					wd_custom_product_shortcode_register_prettyphoto(ul_current);
					
					//Change product
					<?php if( !$_is_mobile ): ?>
					_random_id.find('.wd-custom-category-right-wrapper ul li').wd_custom_product_shortcode_change_product( _random_id );
					<?php endif; ?>
					  
				});
				jQuery.fn.wd_custom_product_shortcode_change_product = function( _random_id ){
					jQuery(this).find('.product_thumbnail_wrapper a').bind('click',function(event){
						event.preventDefault();
						var ul_left = _random_id.find('ul.current .wd-custom-category-left-wrapper ul');
						var ul_right = _random_id.find('ul.current .wd-custom-category-right-wrapper ul');
						// Product on right content
						var li_product = jQuery(this).parents('li.product');
						var index = li_product.index();
						li_product = li_product.detach();
						//Add rel attribute
						var a_rel_left = ul_left.find('li a').attr('data-rel');
						li_product.find('.product_thumbnail_wrapper a').attr('data-rel',a_rel_left);
						//Update first last class, rel attr and event
						var first_last = ((li_product.hasClass('first'))?'first':((li_product.hasClass('last'))?'last':''))+' '+_class_column;
						li_product.removeClass('first last');
						var li_left = ul_left.find('li').clone(false);
						ul_left.find('li').remove();
						li_left.addClass(first_last).find('a').removeAttr('data-rel');
						li_left.wd_custom_product_shortcode_change_product( _random_id );
						//append product
						ul_left.append(li_product);
						if( ul_right.find('li').eq(index).length > 0 ){
							ul_right.find('li').eq(index).before(li_left);
						}
						else{
							ul_right.append(li_left);
						}
						
						// Load thumbnail
						if( _show_thumbnail ){
							var post_id = _random_id.find('ul.current .wd-custom-category-left-wrapper .hidden_product_id').val();
							var gallery_id = _random_id.find('ul.current .wd-custom-category-left-wrapper .gallery_id').val();
							var ul_current = jQuery(this).parents('ul.current');
							wd_custom_product_shortcode_load_thumbnail(ul_current,post_id,gallery_id);
						}
					});
				}
				function wd_custom_product_shortcode_register_prettyphoto(ul_current){
					ul_current.find('.wd-custom-category-left-wrapper a[data-rel^=prettyPhoto]').prettyPhoto({
						hook: 'data-rel',
						social_tools: false,
						theme: 'pp_woocommerce',
						horizontal_padding: 40,
						opacity: 0.9,
						deeplinking: false
					});
				}
				function wd_custom_product_shortcode_load_thumbnail(ul_current,post_id,gallery_id){
					if( _wd_custom_product_category_thumb[post_id] && _wd_custom_product_category_thumb[post_id].gallery_id == gallery_id ){
						ul_current.find('.wd-custom-category-left-wrapper .product_thumbnails').remove();
						ul_current.find('.wd-custom-category-left-wrapper').append(_wd_custom_product_category_thumb[post_id].html);
						wd_custom_product_shortcode_register_prettyphoto(ul_current);
					}
					else{
						_wd_is_loading_thumbnail++;
						ul_current.find('.wd-custom-category-left-wrapper .product_thumbnails').addClass('loading');
						jQuery.ajax({
							type : "POST",
							timeout : 10000,
							url : _ajax_url,
							data : {action: "wd_custom_products_category_get_thumbnail", post_id : post_id, gallery_id: gallery_id},
							error: function(xhr,err){
								_wd_is_loading_thumbnail--;
							},
							success: function(response) {
							   ul_current.find('.wd-custom-category-left-wrapper .product_thumbnails').remove();
							   ul_current.find('.wd-custom-category-left-wrapper').append(response);
							   _wd_custom_product_category_thumb[post_id] = {'gallery_id':gallery_id, 'html':response};
							   wd_custom_product_shortcode_register_prettyphoto(ul_current);
							   _wd_is_loading_thumbnail--;
							   if( _wd_is_loading_thumbnail > 0 )
									ul_current.find('.wd-custom-category-left-wrapper .product_thumbnails').addClass('loading');
							}
						});
					}					
				}
			</script>
			<?php
			wp_reset_postdata();

			//add all the hook removed
			add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
			add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
			add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
			add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
			add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
			
			add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );			
			add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );	
			add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
			//end			
			$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns ;
			return '<div class="woocommerce">' . ob_get_clean() . '</div>';		
		
		}
	}	
	add_shortcode('custom_products_category','wd_custom_products_category_function');
	
	add_action('wp_ajax_nopriv_wd_custom_products_category_get_thumbnail','wd_custom_products_category_get_thumbnail');
	add_action('wp_ajax_wd_custom_products_category_get_thumbnail','wd_custom_products_category_get_thumbnail');
	if( !function_exists('wd_custom_products_category_get_thumbnail') ){
		function wd_custom_products_category_get_thumbnail($gallery_id="product-gallery"){
			global $post, $product;
			if( isset($_POST['post_id']) ){
				$post_id = $_POST['post_id'];
				$post = get_post($post_id);
				$product = get_product($post_id);
			}
			
			if( isset($_POST['gallery_id']) )
				$gallery_id = $_POST['gallery_id'];
			if( !(is_object($post) && is_object($product)) )
				return '';
			
			ob_start();
			$_is_mobile = defined('WD_IS_MOBILE')?WD_IS_MOBILE:wp_is_mobile();
			$attachment_ids = $product->get_gallery_attachment_ids();
			$attachment_ids = array_slice($attachment_ids,0,4);
			if ( $attachment_ids ) {
			?>
			<div class="product_thumbnails">
			<?php
				foreach ( $attachment_ids as $key => $attachment_id ) {
					$image_class = "";
					if( $_is_mobile ){
						$image_link = get_permalink( $post->ID );
					}
					else{
						$image_link = wp_get_attachment_url( $attachment_id );
					}
					
					if ( !$_is_mobile ) {
						$gallery = 'prettyPhoto['.$gallery_id.']';
					} else {
						$gallery = '';
					}
					
					if ( !$image_link )
						continue;
					$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
					$image_title = esc_attr( get_the_title( $attachment_id ) );
					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="col-sm-6"><a href="%s" class="%s" title="%s"  data-rel="'.$gallery.'">%s</a></div>', $image_link, $image_class, $image_title,$image ), $attachment_id, $post->ID, $image_class );
				} ?> 
			</div>
			<?php
			}
			$thumbnail_html = ob_get_clean();
			if( is_ajax() )
				die($thumbnail_html);
			return $thumbnail_html;
			
		}
	}
	
	/**** Load more function ****/
	
	if(!function_exists('wd_product_shortcode_show_load_more_button')){
		function wd_product_shortcode_show_load_more_button($product_type='', $_random_id, $atts){
		?>
		<div class="wd_button_loadmore_wrapper">
			<input class="btn_load_more" type="button" value="<?php _e('Load more','wpdance'); ?>" data-paged="2" >
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				"use strict";
				
				var atts = <?php echo json_encode($atts); ?>;
				atts.product_type = '<?php echo $product_type; ?>';
				atts.paged = jQuery('#<?php echo $_random_id; ?> .btn_load_more').attr('data-paged');
				jQuery('#<?php echo $_random_id; ?> .btn_load_more').bind('click',function(){
					if( !jQuery(this).hasClass("loading") ){
						jQuery('#<?php echo $_random_id; ?>').wd_product_shortcode_load_more(atts);
					}
				});
			});
		</script>
		<?php
		}
	}
	
	if(!function_exists('wd_product_shortcode_load_more')){
		function wd_product_shortcode_load_more(){
			if( isset($_POST, $_POST['atts']) ){
				
				wp_reset_query(); 
				extract($_POST['atts']);
				global $woocommerce_loop;
				if( isset($show_image) && !(int)$show_image )
					remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
				
				remove_action( 'woocommerce_after_shop_loop_item', 'add_short_content',5 );
				if( isset($show_categories) && !(int)$show_categories )
					remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
				if( isset($show_title) && !(int)$show_title )
					remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
				if( isset($show_sku) && !(int)$show_sku )
					remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
					
				if( isset($show_price) && !(int)$show_price )
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
				if( isset($show_rating) && !(int)$show_rating )
					remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 1 );					
				if( isset($show_label) && !(int)$show_label )
					remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
				if( isset($show_add_to_cart) && !(int)$show_add_to_cart )
					remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				
				if( !isset($per_page) )
					$per_page = 4;
				$args = array(
					'post_type'	=> 'product',
					'post_status' => 'publish',
					'ignore_sticky_posts'	=> 1,
					'posts_per_page' => $per_page,
					'paged' => $paged,
					'meta_query' => array(
						array(
							'key' => '_visibility',
							'value' => array('catalog', 'visible'),
							'compare' => 'IN'
						)
					)
				);
				if( isset($product_type) ){
					switch($product_type){
						case 'sale':
							$args['meta_query'][] = array(
								'key' 			=> '_sale_price'
								,'value' 		=>  0
								,'compare'   	=> '>'
								,'type'      	=> 'NUMERIC'
							);
							break;
						case 'featured':
							$args['meta_query'][] = array(
								'key' 			=> '_featured'
								,'value' 		=> 'yes'
							);
							break;
						case 'recent':
							$args['orderby'] = 'date';
							$args['order'] = 'desc';
							break;
						case 'best_selling':
							$args['order'] = 'desc';
							$args['meta_key'] = 'total_sales';
							$args['orderby'] = 'meta_value_num';
							break;
					}
				}
				
				if( isset($product_cats) && strlen(trim($product_cats)) > 0){
					$args['tax_query'] = array(
											array(
												'taxonomy' 		=> 'product_cat',
												'terms' 		=> explode(',', esc_attr($product_cats) ),
												'field' 		=> 'slug',
												'operator' 		=> 'IN'
											)
										);
				}
				
				if( isset($product_tag) && strlen($product_tag) > 0 && strcmp('all-product-tags',$product_tag) != 0 ){
					$args = array_merge($args, array('product_tag' => $product_tag));
				}

				//ob_start();
				$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
				if( isset($product_type) && $product_type=='popular' )
					add_filter( 'posts_clauses', 'wd_order_by_rating_post_clauses' );
				$products = new WP_Query( $args );
				if( isset($product_type) && $product_type=='popular' )
					remove_filter( 'posts_clauses', 'wd_order_by_rating_post_clauses' );
				if( isset( $columns ) )
					$woocommerce_loop['columns'] = $columns;
				
				if ( $products->have_posts() ): ?>

						<?php while ( $products->have_posts() ) : $products->the_post(); ?>

							<?php wc_get_template_part( 'content', 'product' ); ?>

						<?php endwhile; ?>
					
				<?php endif;
				if($products->max_num_pages == $paged || !$products->have_posts()){
					?>
						<span class="hidden wd_flag_end_page" ></span>
					<?php
				}
				wp_reset_postdata();
				
				add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
				add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
				add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
				add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
				add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
				add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 1 );			
				
				add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
				
				add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
				add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
				$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
				echo ob_get_clean();
				die();
			}
			else{
				echo "";
				die();
			}
			
		}
	}
	
	add_action("wp_ajax_wd_product_shortcode_load_more", "wd_product_shortcode_load_more");
	add_action("wp_ajax_nopriv_wd_product_shortcode_load_more", "wd_product_shortcode_load_more");
	
	function wd_product_tab_by_category_shortcode($atts){
		$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
		if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
			return;
		}
		global $woocommerce_loop, $woocommerce;
		extract(shortcode_atts(array(
			'columns' 			=> 4
			,'per_page' 		=> 8
			,'product_cats' 	=> ''
			,'view_all_text'	=> 'view all'
			,'show_image' 		=> 1
			,'show_title' 		=> 1
			,'show_sku' 		=> 0
			,'show_price' 		=> 1
			,'show_short_desc'  => 1
			,'show_rating' 		=> 1
			,'show_label' 		=> 1
			,'show_label_title' => 0
			,'show_categories' 	=> 0		
			,'show_add_to_cart' => 1				
		),$atts));
		
		wp_reset_query(); 
		
		$product_cats = str_replace(' ','',$product_cats);
		
		if( strlen($product_cats) > 0 ){
			$product_cats = explode(',',$product_cats);
		}
		if( !is_array($product_cats) || count($product_cats) == 0 ){
			return;
		}
		else{
			foreach( $product_cats as $key => $product_cat ){
				if( !term_exists($product_cat, 'product_cat') ){
					unset($product_cats[$key]);
				}
			}
			$product_cats = array_values($product_cats);
			if( count($product_cats) == 0 ){
				return;
			}
		}

		ob_start();
		$_random_id = 'wd_product_tab_by_category_shortcode_'.rand(0, 1000);
		?>
		<div class="wd_product_tab_by_category_shortcode" id="<?php echo $_random_id; ?>">
			<div class="wd_list_categories">
				<ul>
					<?php 
					foreach( $product_cats as $key => $product_cat ): 
					$term = get_term_by('slug', $product_cat, 'product_cat');
					?>
					<li>
						<a class="link_cat <?php echo ($key == 0)?'current':''; ?>" data-slug="<?php echo $term->slug; ?>"><?php echo esc_html($term->name) ?></a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php
			echo wd_product_tab_by_category_load_content($atts, $product_cats[0]);
		?>
			<div class="view_all">
				<?php 
				foreach( $product_cats as $key => $product_cat ): 
				$term = get_term_by('slug', $product_cat, 'product_cat');
				$style = '';
				if( $key != 0 ){
					$style = 'style="display: none"';
				}
				?>
					<a href="<?php echo get_term_link($term, 'product_cat'); ?>" data-slug="<?php echo $term->slug; ?>" <?php echo $style; ?>><?php echo esc_html($view_all_text); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		
		<script type="text/javascript">
			jQuery(document).ready(function($){
			
				var _random_id = jQuery('#<?php echo $_random_id; ?>');
				var _shortcode_data_<?php echo $_random_id ?> = [];
				
				_random_id.find('.wd_list_categories ul li a.link_cat').live('click', function(){
					if( jQuery(this).hasClass('current') || _random_id.find('.featured_product_wrapper_inner').hasClass('loading') )
						return;
					_random_id.find('.wd_list_categories ul li a.link_cat').removeClass('current');
					jQuery(this).addClass('current');
					
					var cat_slug = jQuery(this).attr('data-slug');
					
					_random_id.find('.view_all a').hide();
					_random_id.find('.view_all a[data-slug="'+cat_slug+'"]').show();
					
					if( _shortcode_data_<?php echo $_random_id ?>[cat_slug] ){
						_random_id.find('.featured_product_wrapper_inner').fadeOut(300, function(){
							_random_id.find('.featured_product_wrapper_inner').remove();
							_random_id.find('.view_all').before(_shortcode_data_<?php echo $_random_id ?>[cat_slug]);
							if( typeof wd_qs_prettyPhoto == 'function' ){
								wd_qs_prettyPhoto();
							}
						});
						return;
					}
					
					_random_id.find('.featured_product_wrapper_inner').addClass('loading');
					
					var data = {
						action : 'wd_product_tab_by_category_load_content'
						,atts : <?php echo json_encode($atts); ?>
						,cat_slug : cat_slug
					};
				
					jQuery.ajax({
						type : "POST",
						timeout : 30000,
						url : _ajax_uri,
						data : data,
						error: function(xhr,err){
							_random_id.find('.featured_product_wrapper_inner').removeClass('loading');
						},
						success: function(response) {
							_random_id.find('.featured_product_wrapper_inner').remove();
							_random_id.find('.view_all').before(response);
							_shortcode_data_<?php echo $_random_id ?>[cat_slug] = response;
							if( typeof wd_qs_prettyPhoto == 'function' ){
								wd_qs_prettyPhoto();
							}
						}
					});
							
				});
			});
		</script>
		<?php
		
		return '<div class="woocommerce">'. ob_get_clean() .'</div>';
	}
	add_shortcode('product_tab_by_category', 'wd_product_tab_by_category_shortcode');
	
	add_action("wp_ajax_wd_product_tab_by_category_load_content", "wd_product_tab_by_category_load_content");
	add_action("wp_ajax_nopriv_wd_product_tab_by_category_load_content", "wd_product_tab_by_category_load_content");
	function wd_product_tab_by_category_load_content($atts = array(), $cat_slug = ''){
		if( isset($_POST['atts']) ){
			$atts = $_POST['atts'];
		}
		if( isset($_POST['cat_slug']) ){
			$cat_slug = $_POST['cat_slug'];
		}
		
		extract(shortcode_atts(array(
				'columns' 			=> 4
				,'style' 			=> 1
				,'per_page' 		=> 8
				,'title' 			=> ''
				,'desc' 			=> ''
				,'product_cats' 	=> ''
				,'show_nav'			=> 1
				,'show_image' 		=> 1
				,'show_title' 		=> 1
				,'show_sku' 		=> 0
				,'show_price' 		=> 1
				,'show_short_desc'  => 1
				,'show_rating' 		=> 1
				,'show_label' 		=> 1	
				,'show_label_title' => 0	
				,'show_categories'	=> 0	
				,'show_add_to_cart' => 1
			),$atts));		
		
		if(!(int)$show_image)
			remove_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );
		
		add_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_open_div_left', 1 );
		if(!(int)$show_categories)
			remove_action( 'woocommerce_after_shop_loop_item', 'get_product_categories', 2 );
		
		if(!(int)$show_title)
			remove_action( 'woocommerce_after_shop_loop_item', 'add_product_title', 3 );
		
		if(!(int)$show_sku)
			remove_action( 'woocommerce_after_shop_loop_item', 'add_sku_to_product_list', 5 );
		
		remove_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
		if((int)$show_short_desc)
			add_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_loop_excerpt', 8 );
		
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );						
		if((int)$show_rating)
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 9 );
		add_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_close_div', 10 );
		
		if(!(int)$show_label)
			remove_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );		
		
		add_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_open_div_right', 9996 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
		if((int)$show_price)
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 9997 );
			
		if(!(int)$show_add_to_cart)
			remove_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
		add_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_close_div', 10004 );
		
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' => $per_page,
			'order' => 'date',
			'orderby' => 'desc',
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				),
			),
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'terms' => array( esc_attr($cat_slug) ),
					'field' => 'slug',
					'include_children' => false
				)
			)
		);
		global $woocommerce_loop;
		$_old_woocommerce_loop_columns = $woocommerce_loop['columns'];
		$woocommerce_loop['columns'] = $columns;
		$products = new WP_Query( $args );
		ob_start();
		if ( $products->have_posts() ){
			?>
			<div class="featured_product_wrapper_inner <?php echo (!(int)$show_label_title)?'wd_hide_label_title':''; ?>">

				<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>
				<?php woocommerce_product_loop_end(); ?>
				
			</div>
				
			<?php
		}
		$woocommerce_loop['columns'] = $_old_woocommerce_loop_columns;
		
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 9997 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 9 );
		remove_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_loop_excerpt', 8 );
		remove_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_open_div_left', 1 );
		remove_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_close_div', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_open_div_right', 9996 );
		remove_action( 'woocommerce_after_shop_loop_item', 'wd_product_tab_by_category_close_div', 10004 );
		
		//add all the hook removed
		add_action ('woocommerce_after_shop_loop_item','open_div_style',1);
		add_action ('woocommerce_after_shop_loop_item','get_product_categories',2);
		add_action ('woocommerce_after_shop_loop_item','add_product_title',3);
		add_action ('woocommerce_after_shop_loop_item','add_sku_to_product_list',5);
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 4 );
		add_action( 'woocommerce_after_shop_loop_item', 'wd_template_loop_excerpt', 8 );
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 10002 );			
		
		add_action( 'woocommerce_before_shop_loop_item_title', 'add_label_to_product_list', 5 );	
		
		add_action( 'woocommerce_before_shop_loop_item_title', 'wd_template_loop_product_thumbnail', 10 );		
		add_action( 'woocommerce_after_shop_loop_item', 'wd_list_template_loop_add_to_cart',9999 );
		//end
		
		$html = ob_get_clean();
		if( is_ajax() ){
			die($html);
		}
		else{
			return $html;
		}
	}
	
	function wd_product_tab_by_category_open_div_left(){
		echo '<div class="product-meta-left">';
	}
	function wd_product_tab_by_category_open_div_right(){
		echo '<div class="product-meta-right">';
	}
	function wd_product_tab_by_category_close_div(){
		echo '</div>';
	}
	function wd_product_tab_by_category_loop_excerpt(){
		?>
		<div class="loop-short-description">
			<?php 
			if( function_exists('the_excerpt_max_words') ){
				echo the_excerpt_max_words(30,'',false)." ...";
			}
			else{
				echo get_the_excerpt();
			}
			?>
		</div>
		<?php
	}
	
?>