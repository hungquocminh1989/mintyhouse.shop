<?php 
if(!function_exists ('custom_query')){
	function custom_query($atts)
	{
		extract(shortcode_atts(array(
				'query_string'=>'',
		),$atts));
		$result = '';
		global $post;
		$query = new wp_query($query_string);
		if($query->have_posts()) {
			while($query->have_posts()) { $query->the_post();
				$meta=get_post_meta($post->ID,'thumbnail',true);
				$result.="<div class='post-item'>
							<a href=".get_permalink()."><img width='100' height='100' src=".$meta." /></a>
							<h1><a href=".get_permalink().">".get_the_title()."</a></h1>
							<p class='metadata'><span class='post'>Posted by:</span>".get_the_author()."<span class='in-cat'>In:</span>".get_the_category_list(', ').
							"<span class='on-date'>On:</span>".get_comment_time()."<a href=".get_comments_link().">".get_comments_number('1 comment','2 comment','% comment')."</a></p>
							<p>"
														.get_the_content().
							"</p>
							<p><a class='more-link floatright' href=".get_permalink().">Read More</a></p>
						</div>";
			}
		}
		return $result;
	}
}
add_shortcode('custom_query','custom_query');
?>