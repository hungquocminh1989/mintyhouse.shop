<?php 
if(!function_exists ('recent_blogs_sticky_functions')){
	function recent_blogs_sticky_functions($atts,$content = false){
		extract(shortcode_atts(array(
			'category'				=>	''
			,'columns_child'			=> 1
			,'number_posts'			=> 5
			,'layout_sticky'		=> 'vertical'
			,'title_sticky'			=> 1
			,'thumbnail_sticky'		=> 1
			,'meta_sticky'			=> 1
			,'excerpt_sticky'		=> 1
			,'tag_sticky'			=> 1
			,'sharing_sticky'		=> 1
			,'excerpt_words_sticky'	=> 40
			,'title'				=> 1
			,'thumbnail'			=> 1
			,'meta'					=> 1
			,'excerpt'				=> 1
			,'tag'					=> 1
			,'sharing'				=> 1
			,'excerpt_words'		=> 20
		),$atts));

		wp_reset_query();	

		$args = array(
				'post_type' 			=> 'post'
				,'ignore_sticky_posts' 	=> 1
				,'posts_per_page' 		=> $number_posts
		);	
		if( strlen($category) > 0 ){
			$args['category_name'] = $category;
		}		
		
		$span_class = "col-sm-".(24/$columns_child);
		
		$num_count = count(query_posts($args));	
		ob_start();
		if( have_posts() ) :
			$id_widget = 'recent-blogs-sticky-shortcode-'.rand(0, 1000);
			
			echo '<div id="'. $id_widget .'" class="shortcode-recent-blogs recent-blogs-sticky layout_horizontal columns-'.$columns_child.'" >';
			$i = 0;
				while(have_posts()) {
					the_post();
					global $post;
					?>
					<?php if($i == 0) :?>
					<div class="item <?php echo 'layout_'.$layout_sticky ?> ">
						<?php if($thumbnail_sticky): ?>
						<div class="image_wrapper">
							<div class="image">
								<a class="thumbnail" href="<?php the_permalink(); ?>">
									<?php 
										if( has_post_thumbnail() ){
											the_post_thumbnail('blog_shortcode',array('class' => 'thumbnail-effect-1'));
										}
									?>
									<div class="thumbnail-effect"></div>
								</a>								
							</div>
						</div>
						<?php endif; ?>
						
						<div class="blog_wrapper">
							<?php if($title_sticky) :?>
							<h1 class="heading-title"><a href="<?php echo get_permalink($post->ID); ?>" class="wp_title"  ><?php echo get_the_title($post->ID); ?></a></h1>
							<?php endif; ?>
							<?php if($meta_sticky): ?>
							<div class="info-detail">
								<span class="author">	
								<?php _e('','wpdance'); ?> 
								<?php the_author_posts_link(); ?> 
								</span>
								
								<span class="date-time"><?php the_time(get_option('date_format')); ?></span>
								
								<!--<span class="comments-count">
									<span class="number"><?php //$comments_count = wp_count_comments($post->ID); if($comments_count->approved < 10 && $comments_count->approved > 0) echo '0'; echo $comments_count->approved;?></span>
									<?php //_e('comment(s)','wpdance'); ?>
								</span>-->
							</div>
							<?php endif; ?>
							<?php if($excerpt_sticky): ?>
							<div class="excerpt"><?php the_excerpt_max_words($excerpt_words_sticky); ?>...</div>
							<?php endif; ?>
							<?php if(($tag_sticky) || ($sharing_sticky)): ?>
							<div class="bottom-share">
								<?php if($tag_sticky): ?>
								<div class="tag_blog">
									<?php the_tags(__('Tags ','wpdance')," "); ?>
								</div>
								<?php endif; ?>
								<?php if($sharing_sticky): ?>
								<?php if( function_exists('wd_template_social_sharing') ){ ?>
									<div class="sharing_blog">
									<?php wd_template_social_sharing(); ?>
									</div>
								<?php } ?>
								<?php endif; ?>
								
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php else: ?>
						<?php 
						if( $i == 1 ){
							echo '<ul class="blogs-sticky-child blog_wrapper_horizontal layout_'.$layout_sticky.'">';
						}
						?>
						<li class="item <?php echo $span_class; ?> <?php echo ($i % $columns_child == 1)?' first':($i % $columns_child == 0?' last':''); ?>">
						<?php if($thumbnail): ?>
						<div class="image_wrapper">
							<div class="image">
								<a class="thumbnail" href="<?php the_permalink(); ?>">
									<?php 
										if( has_post_thumbnail() ){
											the_post_thumbnail('blog_shortcode',array('class' => 'thumbnail-effect-1'));
										}
									?>
									<div class="thumbnail-effect"></div>
								</a>								
							</div>
						</div>
						<?php endif; ?>
						
						<div class="blog_wrapper_horizontal">
							<?php if($title) :?>
							<h1 class="heading-title"><a href="<?php echo get_permalink($post->ID); ?>" class="wp_title"  ><?php echo get_the_title($post->ID); ?></a></h1>
							<?php endif; ?>
							<?php if($meta): ?>
							<div class="info-detail">
								<span class="author">	
								<?php _e('','wpdance'); ?> 
								<?php the_author_posts_link(); ?> 
								</span>
								
								<span class="date-time"><?php the_time(get_option('date_format')); ?></span>
								
								<!--<span class="comments-count">
									<span class="number"><?php //$comments_count = wp_count_comments($post->ID); if($comments_count->approved < 10 && $comments_count->approved > 0) echo '0'; echo $comments_count->approved;?></span>
									<?php //_e('comment(s)','wpdance'); ?>
								</span>-->
							</div>
							<?php endif; ?>
							<?php if($excerpt): ?>
							<div class="excerpt"><?php the_excerpt_max_words($excerpt_words); ?>...</div>
							<?php endif; ?>
							<?php if(($tag) || ($sharing)): ?>
							<div class="bottom-share">
								<?php if($tag): ?>
								<div class="tag_blog">
									<?php the_tags(__('Tags ','wpdance')," "); ?>
								</div>
								<?php endif; ?>
								<?php if($sharing): ?>
								<?php if( function_exists('wd_template_social_sharing') ){ ?>
									<div class="sharing_blog">
									<?php wd_template_social_sharing(); ?>
									</div>
								<?php } ?>
								<?php endif; ?>
								
							</div>
							<?php endif; ?>
						</div>
						</li>
					<?php 
					if( $i == $num_count - 1){
						echo '</ul>';
					}
					endif;
					$i++;
				} ?>
				
			</div>		
			<?php
		endif;
		wp_reset_query();
		return ob_get_clean();
	}
} 
add_shortcode('recent_blogs_sticky','recent_blogs_sticky_functions');

?>