<?php 
if(!function_exists ('recent_blogs_functions')){
	function recent_blogs_functions($atts,$content = false){
		extract(shortcode_atts(array(
			'category'		=>	''
			,'columns'		=> 4
			,'number_posts'	=> 4
			,'layout'		=> 'vertical'
			,'title'		=> 'yes'
			,'thumbnail'	=> 'yes'
			,'meta'			=> 'yes'
			,'excerpt'		=> 'yes'
			,'tag'			=> 'yes'
			,'sharing'		=> 'yes'
			,'excerpt_words'=> 20
		),$atts));

		wp_reset_query();	

		$args = array(
				'post_type' 			=> 'post'
				,'ignore_sticky_posts' 	=> 1
				,'showposts' 			=> $number_posts
		);	
		if( strlen($category) > 0 ){
			$args = array(
				'post_type' 			=> 'post'
				,'ignore_sticky_posts' 	=> 1
				,'showposts' 			=> $number_posts
				,'category_name' 		=> $category
			);	
		}		
		$title = strcmp('yes',$title) == 0 ? 1 : 0;
		$thumbnail = strcmp('yes',$thumbnail) == 0 ? 1 : 0;
		$meta = strcmp('yes',$meta) == 0 ? 1 : 0;
		$excerpt = strcmp('yes',$excerpt) == 0 ? 1 : 0;
		$tag = strcmp('yes',$tag) == 0 ? 1 : 0;
		$sharing = strcmp('yes',$sharing) == 0 ? 1 : 0;
		
		$span_class = "col-sm-".(24/$columns);
		
		$num_count = count(query_posts($args));	
		if( have_posts() ) :
			$id_widget = 'recent-blogs-shortcode'.rand(0,1000);
			ob_start();
			echo '<ul id="'. $id_widget .'" class="shortcode-recent-blogs columns-'.$columns.' layout_'.$layout.'">';
			$i = 0;
			while(have_posts()) {
				the_post();
				global $post;
				?>
				<?php if($layout=="horizontal"): ?>
					<li class="item <?php echo $span_class ?><?php if( $i == 0 || $i % $columns == 0 ) echo ' first';?><?php if( $i == $num_count-1 || $i % $columns == $columns-1 ) echo ' last';?>">
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
								
								<span class="comments-count">
									<span class="number"><?php $comments_count = wp_count_comments($post->ID); if($comments_count->approved < 10 && $comments_count->approved > 0) echo '0'; echo $comments_count->approved;?></span>
									<?php _e('comment(s)','wpdance'); ?>
								</span>
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
				
				<?php elseif($layout=="vertical"):?>
				
					<li class="item <?php echo $span_class ?><?php if( $i == 0 || $i % $columns == 0 ) echo ' first';?><?php if( $i == $num_count-1 || $i % $columns == $columns-1 ) echo ' last';?>">								
							
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
								
								<span class="comments-count">
									<span class="number"><?php $comments_count = wp_count_comments($post->ID); if($comments_count->approved < 10 && $comments_count->approved > 0) echo '0'; echo $comments_count->approved;?></span>
									<?php _e('comment(s)','wpdance'); ?>
								</span>
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
					
					</li>

					
				<?php endif;?>
		<?php
			$i++;
			}
			echo '</ul>';
			$ret_html = ob_get_contents();
			ob_end_clean();
			//ob_end_flush();
		endif;
		wp_reset_query();
		return $ret_html;
	}
} 
add_shortcode('recent_blogs','recent_blogs_functions');

if( !function_exists('wd_recent_blogs_video_functions') ){
	function wd_recent_blogs_video_functions($atts){
		extract(shortcode_atts(array(
			'right_columns'		=> 2
			,'number_posts'		=> 5
		),$atts));
		wp_reset_query();
		global $wpdb;
		$sql = "select ID from {$wpdb->prefix}posts, {$wpdb->prefix}postmeta where meta_key='_video_url' and meta_value<>'' and ID=post_id and post_status='publish' order by post_date desc limit {$number_posts};";
		$ids = $wpdb->get_results( $sql, OBJECT );
		
		if( !is_array($ids) || count($ids) == 0 ){
			return;
		}
		
		ob_start();
		$count = 0;
		$right_item_class = "col-sm-".(24/$right_columns);
		global $post;
		?>
		<div class="wd-recent-blogs-video-wrapper">
			<?php foreach( $ids as $id ): $post = get_post($id->ID); setup_postdata($post); ?>
				<?php if( $count == 0 ): ?>
				<div class="left-wrapper">
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
				<?php if( $count > 0 ): ?>
					<?php if( $count == 1 ): ?>
					<div class="right-wrapper">
						<ul>
					<?php endif; ?>
					<?php $last_first = ($count%$right_columns == 1)?'first':(($count%$right_columns == 0)?'last':''); ?>
							<li class="<?php echo $right_item_class.' '.$last_first; ?>">
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
							</li>
					<?php if( $count == count($ids) - 1 ): ?>	
						</ul>
					</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php $count++; endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
add_shortcode('recent_blogs_video', 'wd_recent_blogs_video_functions');

?>