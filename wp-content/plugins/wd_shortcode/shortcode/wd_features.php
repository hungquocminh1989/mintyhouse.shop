<?php 
	if(!function_exists('features_function')){
		function features_function($atts,$content){
			extract(shortcode_atts(array(
				'slug'				=>		''
				,'id'				=>		0
				,'style'			=>		'style-1'
				,'title'			=>		'yes'
				,'thumbnail'		=>		'no'
				,'excerpt'			=>		'yes'
				,'content'			=>		'no'
				,'class_icon_font'	=>		''
				,'show_icon_font'	=>		'yes'
				
				
			),$atts));
			
			$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
			if ( !in_array( "features-by-woothemes/woothemes-features.php", $_actived ) ) {
				return;
			}
			
			if( absint($id) > 0 ){
				$_feature = woothemes_get_features( array('id' => $id,'size' => 'feature-thumbnail' ));
			}elseif( strlen(trim($slug)) > 0 ){
				$_feature = get_page_by_path($slug, OBJECT, 'feature');
				if( !is_null($_feature) ){
					$_feature = woothemes_get_features( array('id' => $_feature->ID,'size' => 'feature-thumbnail' ));
				}else{
					return;
				}
			}else{
				return;
				//invalid input params.
			}
			
			//nothing found
			if( !is_array($_feature) || count($_feature) <= 0 ){
				return;
			}else{
				global $post;
				$_feature = $_feature[0];
				$post = $_feature;
				setup_postdata( $post ); 
			}
			
			//handle features
			
			ob_start();
			?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('shortcode')?>>
					<?php if((strcmp(trim($style),"style-1") == 0) || (strcmp(trim($style),"Style-1") == 0) || (strcmp(trim($style),"STYLE-1") == 0) ) :?>
						<div class="feature_content_wrapper <?php if( strcmp(trim($show_icon_font),'yes') == 0 ) echo "has_icon"; else echo "no_icon"; ?> style-1">	
							<?php 
							if( strcmp(trim($show_icon_font),'yes') == 0 ) :?>
							<a class="wd-feature-icon" href="<?php echo esc_url($_feature->url);?>"><div class="feature_icon fa <?php echo $class_icon_font ?>"></div></a>
							<?php
							elseif( strcmp(trim($show_icon_font),'no') == 0 ) :?>
							<a href="<?php echo esc_url($_feature->url);?>">
								<div class="feature_thumbnail_image">
									<?php 
										if( has_post_thumbnail() ) : 
											the_post_thumbnail( 'woo_feature', array( 'alt' => esc_attr(get_the_title()), 'title' => esc_attr(get_the_title()) ) );
										endif;
									?>
									<div class="thumbnail-effect"></div>
								</div>
							</a>
							<?php endif;?>
							<?php
							if( strcmp(trim($title),'yes') == 0 ) :?>
								<h3 class="feature_title heading_title">
									<a href="<?php echo esc_url($_feature->url);?>"><?php the_title(); ?></a>
								</h3>
							<?php endif;?>
							
							<?php if( strcmp(trim($excerpt),'yes') == 0 ) :?>
								<div class="feature_excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif;?>
							
							<?php if( strcmp(trim($content),'yes') == 0 ) :?>
								<div class="feature_content ">
									<?php the_content(); ?>
								</div>
							<?php endif;?>
						</div>
					<?php elseif((strcmp(trim($style),"style-2")== 0) || (strcmp(trim($style),"Style-2")== 0) || (strcmp(trim($style),"STYLE-2")== 0) || (strcmp(trim($style),"style-3")== 0) || (strcmp(trim($style),"Style-3")== 0) || (strcmp(trim($style),"STYLE-3")== 0) ) :?>
						<div class="feature_content_wrapper <?php if( strcmp(trim($show_icon_font),'yes') == 0 ) echo "has_icon ";?> <?php echo ((strcmp(trim($style),"style-2")== 0) || (strcmp(trim($style),"Style-2")== 0) || (strcmp(trim($style),"STYLE-2")== 0) )?'style-2 ':'' ?><?php echo ((strcmp(trim($style),"style-3")== 0) || (strcmp(trim($style),"Style-3")== 0) || (strcmp(trim($style),"STYLE-3")== 0))?'style-3 ':'' ?>">	
							<?php
							if( strcmp(trim($show_icon_font),'yes') == 0 ) :?>
							<a class="wd-feature-icon" href="<?php echo esc_url($_feature->url);?>"><div class="feature_icon fa <?php echo $class_icon_font ?>"></div></a>
							<?php
							endif;
							if( strcmp(trim($title),'yes') == 0 ) :?>
								<h3 class="feature_title heading_title">
									<a href="<?php echo esc_url($_feature->url);?>"><?php the_title(); ?></a>
								</h3>
							<?php endif;?>
							
							<?php if( strcmp(trim($excerpt),'yes') == 0 ) :?>
								<div class="feature_excerpt">
									<?php the_excerpt(); ?>
								</div>
							<?php endif;?>
							
							<?php if( strcmp(trim($content),'yes') == 0 ) :?>
								<div class="feature_content ">
									<?php the_content(); ?>
								</div>
							<?php endif;?>
						</div>
					<?php endif; ?>
				</div>

			<?php
			$output = ob_get_contents();
			ob_end_clean();
			rewind_posts();
			wp_reset_query();
			return $output;
		}
	}
	add_shortcode('feature','features_function');
?>