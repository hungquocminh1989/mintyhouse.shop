<?php
if( !function_exists('wd_child_categories_shortcode_function')){
	function wd_child_categories_shortcode_function($atts){
		extract(shortcode_atts(array(
			'category'				=> ''
			,'desc'					=> ''
			,'bg_color' 			=> 'transparent'
			,'bg_image'  			=> ''
			,'text_color'			=> '#fff'
			,'style'				=> 1
			,'limit'				=> 5
			,'taxonomy'				=> 'product_cat'
			,'button_text'			=> 'View All Categories'
		),$atts));
		
		if($category ==  '')
			return '';
		if( is_numeric($category) )
			$parent_cat = get_term_by('id',$category,$taxonomy);
		else
			$parent_cat = get_term_by('slug',$category,$taxonomy);
		
		if( !is_object($parent_cat) )
			return;
			
		$args = array(
					'child_of' 			=> $parent_cat->term_id
					,'number'  			=> $limit
					,'hide_empty'  		=> true
					,'hierarchical'  	=> false
				);
		$child_categories = get_terms($taxonomy,$args);
		
		ob_start();
		$inline_style = '';
		if( $bg_color != '')
			$inline_style .= 'background-color: '.$bg_color.';';
		if( $bg_image != ''){
			$inline_style .= 'background-image: url('.$bg_image.');';
			$inline_style .= 'background-repeat: no-repeat;';
			$inline_style .= 'background-position: center;';
		}
		$inline_style = 'style="'.$inline_style.'"';
		
		$inline_style_text = '';
		if( $text_color !='' ){
			$inline_style_text .= 'color: '.$text_color.';';
		}
		if( $inline_style_text != '' )
			$inline_style_text = 'style="'.$inline_style_text.'"';
		
		if( $desc == '' ){
			if( function_exists('string_limit_words') )
				$desc = string_limit_words($parent_cat->description, 15);
			else
				$desc = $parent_cat->description;
		}
		$parent_cat_link = get_term_link($parent_cat);
		
		$btn_data_style_hover = '';
		if( $bg_color != '' && $bg_color != 'transparent' && $text_color != '' ){
			$btn_data_style_hover .= 'data-hover="background-color:'.$text_color.';color:'.$bg_color.'"';
		}
		
		
		?>
		<div class="wd_child_categories_shortcode <?php echo 'style-'.$style; ?>" <?php echo $inline_style; ?>>
			<div class="parent_cat">
				<h3 class="title"><a href="<?php echo $parent_cat_link; ?>" <?php echo $inline_style_text; ?> ><?php echo $parent_cat->name; ?></a></h3>
				<span class="desc" <?php echo $inline_style_text; ?>><?php echo $desc; ?></span>
			</div>
			<div class="child_categories">
				<?php if( count( $child_categories )> 0 ): ?>
					<ul>
					<?php foreach( $child_categories as $child_cat ): ?>
						<li><a href="<?php echo get_term_link($child_cat); ?>" <?php echo $inline_style_text; ?> ><?php echo $child_cat->name; ?></a></li>
					<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="cat_button">
				<a href="<?php echo $parent_cat_link; ?>" <?php echo $inline_style_text; ?> <?php echo $btn_data_style_hover; ?> ><?php echo $button_text; ?></a>
			</div>
		</div>
		
		<?php
		return ob_get_clean();
	}
}

add_shortcode('child_categories','wd_child_categories_shortcode_function');

?>