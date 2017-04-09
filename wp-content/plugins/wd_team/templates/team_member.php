<?php
if(!function_exists('wd_team_member_shortcode_function')){
	function wd_team_member_shortcode_function($atts = array(),$content) {
		extract(shortcode_atts(array(
			'style'  	=> '1'
			,'id'		=>''
			,'slug' 	=> ''
			,'width' 	=> '350'
			,'height' 	=> '350'
		), $atts));
		
		if( absint($id) > 0 ){
			$query = new WP_Query( array( 'post_type' => 'team', 'post__in' => array($id )) );
		}elseif( strlen(trim($slug)) > 0 ){
			$_post = get_page_by_path($slug, OBJECT, 'team');
			if( !is_null($_post) ){
				$query = new WP_Query( array( 'post_type' => 'team', 'post__in' => array($_post->ID )) );
			} else {
				return;
			}
		} else {
			return;
		}
		global $post;
		$count=0;
			if($query->have_posts()) : 
				while($query->have_posts()) : $query->the_post();
					$name 			= esc_html(get_the_title($post->ID));
					$role 			= esc_html(get_post_meta($post->ID,'wd_member_role',true));
					$email			= esc_html(get_post_meta($post->ID,'wd_member_email',true));
					$phone			= esc_html(get_post_meta($post->ID,'wd_member_phone',true));
					$link			= esc_url(get_post_meta($post->ID,'wd_member_link',true));
					$facebook_link	= esc_url(get_post_meta($post->ID,'wd_member_facebook_link',true));
					$twitter_link	= esc_url(get_post_meta($post->ID,'wd_member_twitter_link',true));
					$rss_link		= esc_url(get_post_meta($post->ID,'wd_member_rss_link',true));
					$google_link	= esc_url(get_post_meta($post->ID,'wd_member_google_link',true));
					$linkedlin_link	= esc_url(get_post_meta($post->ID,'wd_member_linkedlin_link',true));
					$dribble_link	= esc_url(get_post_meta($post->ID,'wd_member_dribble_link',true));
					$vimeo_link		= esc_url(get_post_meta($post->ID,'wd_member_vimeo_link',true));
					$content 		= substr(wp_strip_all_tags($post->post_content),0, 300).'...';	
					if($link == '') { $link = '#'; }		
			$_social = '';
			if($facebook_link){
				$_social .= '<a href="'.$facebook_link.'"><i class="fa fa-facebook"></i></a>';
			}
			if($twitter_link){
				$_social .= '<a href="'.$twitter_link.'"><i class="fa fa-twitter"></i></a>';
			}
			if($google_link){
				$_social .= '<a href="'.$google_link.'"><i class="fa fa-google-plus"></i></a>';
			}
			if($rss_link){
				$_social .= '<a href="'.$rss_link.'"><i class="fa fa-rss"></i></a>';
			}
			if($linkedlin_link){
				$_social .= '<a href="'.$linkedlin_link.'"><i class="fa fa-linkedin"></i></a>';
			}
			if($dribble_link){
				$_social .= '<a href="'.$dribble_link.'"><i class="fa fa-dribbble"></i></a>';
			}
			if($vimeo_link){
				$_social .= '<a href="'.$vimeo_link.'"><i class="fa fa-vimeo-square"></i></a>';
			}
			ob_start();
			?>
			<div class="wd_meet_team <?php echo 'style-'.$style; ?>">
				<a class="image" title="<?php echo $name; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php echo $name; ?>" href="<?php echo $link; ?>"><?php the_post_thumbnail('wd_team_thumb'); ?><div class="thumbnail-effect"></div> </a>
				<div class="description"><?php echo $content; ?></div>
				<div class="info">
					<div class="name-role">
						<a class="name" href="<?php echo $link; ?>"><?php echo $name; ?></a>
						-
						<span class="role"><?php echo $role; ?></span>
					</div>
					
					<?php if( $email ): ?>
						<span class="email"><?php echo $email; ?></span>
					<?php endif; ?>
					<?php if( $phone ): ?>
						<span class="phone"><?php echo $phone; ?></span>
					<?php endif; ?>
					<div class="social"><?php echo $_social; ?></div>
				</div>
			</div>
			<?php
				endwhile;
			endif;
			$output = ob_get_contents();
			ob_end_clean();
			wp_reset_query();
		return $output;
	}
}
add_shortcode('team_member','wd_team_member_shortcode_function');
?>