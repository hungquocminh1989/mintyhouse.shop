<?php
if(!function_exists ('wd_array_atts')){
	function wd_array_atts($pairs, $atts) {
		$atts = (array)$atts;
		$out = array();
	   foreach($pairs as $name => $default) {
			if ( array_key_exists($name, $atts) ){
				if( strlen(trim($atts[$name])) > 0 ){
					$out[$name] = $atts[$name];
				}else{
					$out[$name] = $default;
				}
			}
			else{
				$out[$name] = $default;
			}	
		}
		return $out;
	}
}

global $post,$wd_custom_size;
wp_nonce_field( 'wd_slide_box', 'wd_slide_box_nonce' );
$sliders = get_post_meta($post->ID,'wd_slider_list',true);
$sliders = unserialize($sliders);

$sliders_config = get_post_meta($post->ID,'wd_slider_config',true);
$sliders_config = unserialize($sliders_config);
$sliders_config = wd_array_atts(array(
				'show_nav' 				=> 1
				,'scroll_per_page' 		=> 0
				,'mouse_drag' 			=> 1
				,'touch_drag' 			=> 1
				,'auto_play' 			=> 1
				,'auto_play_speed' 		=> 1000
				,'auto_play_timeout' 	=> 5000
				,'auto_play_hover_pause'=> 1
				,'margin'				=> 0
				,'responsive_option'	=> array(
										'break_point' => array(0, 200, 400, 600, 800, 1000, 1200, 1400)
										,'item' => array(1, 2, 3, 4, 5, 6, 7, 8)
										)
			),$sliders_config);

?>
<div class="show-shortcode">
	<input type="hidden" name="slide-id" id="slide-id" value="<?php echo $post->ID;?>">
	<p>
		<span id="carousel-shortcode" name="carousel-shortcode">
			Slider Shortcode : <?php echo "[wd_slider id=\"{$post->ID}\"]";?><br>
		</span>
	</p>
</div>
<br />
<div class="uploader">
	<input type="hidden" name="_sliders_slider" value="1"/>
	<a href="javascript:void(0)" class="button stag-metabox-table" name="_unique_name_button" id="_unique_name_button"/>Insert</a>
	<a href="javascript:void(0)" class="button clear-all-slides" name="clear-all-slides" id="clear-all-slides"/>Clear</a>
	<div class="sortable-wrapper">
		<ul id="sortable">
			<?php
			if( is_array($sliders) && count($sliders) > 0 ):
				foreach( $sliders as $single_slider ):
					$_image_url = wp_get_attachment_image_src( $single_slider['thumb_id'], 'full', false );
					$_image_url = $_image_url[0];
					$_thumb_url = wp_get_attachment_image_src( $single_slider['thumb_id'], 'thumbnail', false );
					$_thumb_url = $_thumb_url[0];
			?>
				<li>
					<div id="image-value<?php echo $single_slider['id'];?>" class="hidden lightbox-image">
						<img  class="lightbox-preview-img" src="<?php echo $_image_url;?>" alt="<?php echo $single_slider['alt'];?>" title="<?php echo $single_slider['title'];?>">
						<input type="hidden" value="<?php echo $single_slider['id'];?>" name="element_id[]" class="inline-element element_id">
						<input type="hidden" value="<?php echo $_image_url;?>" name="element_image_url[]" id="element_image_url" class="inline-element insert_url">
						<input type="hidden" value="<?php echo $single_slider['thumb_id'];?>" name="thumb_id[]" id="thumb_id" class="inline-element element_thumb_id">
						<input type="hidden" value="<?php echo $_thumb_url;?>" name="thumb_url[]" id="thumb_url" class="inline-element element_thumb">
						<p><span class="label">Slide Url</span><input type="text" value="<?php echo esc_url($single_slider['url']);?>" name="element_url[]" class="inline-element link_url"></p>
						<p><span class="label">Image Title</span><input type="text" value="<?php echo esc_html($single_slider['title']);?>" name="element_title[]" class="inline-element image_title "></p>
						<p><span class="label">Image Alt</span><input type="text" value="<?php echo esc_html($single_slider['alt']);?>" name="element_alt[]" class="inline-element image_alt"></p>
						<p><span class="label">Slide Title</span><input type="text" value="<?php echo esc_html($single_slider['slide_title']);?>" name="slide_title[]" class="inline-element slide_title"></p>
						<p><span class="label">Slide Contents</span><textarea name="slide_content[]" class="inline-element slide_content"><?php echo esc_textarea($single_slider['slide_content']);?></textarea></p>						
						<div class="btn fancy-button-wrapper">
							<a href="javascript:void(0)" class="button save-slide" name="save-slide"/>Save</a>
							<a href="javascript:void(0)" class="button save-slide" name="close-slide"/>Close</a>
						</div>
					</div>
					
					
					<p class="image-wrappper">
						<img  class="preview-img" src="<?php echo $_thumb_url;?>" alt="<?php echo $single_slider['alt'];?>" title="<?php echo esc_html($single_slider['title']);?>" width="120" height="120">
						<a href="#image-value<?php echo $single_slider['id'];?>" class="preview-img-edit">Edit</a>
						<a href="javascript:void(0)" class="preview-img-remove">Del</a>
					</p>
				</li>						
			<?php	
				endforeach;		
			endif;	
			?>

		</ul> 
	</div>
  
</div>
<br />
<div class="shortcode-config">
	<div class="line_wrapper">
		<label><?php _e('Show Navigation','wpdance'); ?></label>
		<select name="show_nav">
			<option value="1" <?php echo ($sliders_config['show_nav'])?'selected':''; ?>><?php _e('Yes','wpdance'); ?></option>
			<option value="0" <?php echo (!$sliders_config['show_nav'])?'selected':''; ?>><?php _e('No','wpdance'); ?></option>
		</select>
	</div>
	<div class="line_wrapper">
		<label><?php _e('Scroll Per Page','wpdance'); ?></label>
		<select name="scroll_per_page">
			<option value="1" <?php echo ($sliders_config['scroll_per_page'])?'selected':''; ?>><?php _e('Yes','wpdance'); ?></option>
			<option value="0" <?php echo (!$sliders_config['scroll_per_page'])?'selected':''; ?>><?php _e('No','wpdance'); ?></option>
		</select>
	</div>
	<div class="line_wrapper">
		<label><?php _e('Mouse Drag','wpdance'); ?></label>
		<select name="mouse_drag">
			<option value="1" <?php echo ($sliders_config['mouse_drag'])?'selected':''; ?>><?php _e('Yes','wpdance'); ?></option>
			<option value="0" <?php echo (!$sliders_config['mouse_drag'])?'selected':''; ?>><?php _e('No','wpdance'); ?></option>
		</select>
	</div>
	<div class="line_wrapper">
		<label><?php _e('Touch Drag','wpdance'); ?></label>
		<select name="touch_drag">
			<option value="1" <?php echo ($sliders_config['touch_drag'])?'selected':''; ?>><?php _e('Yes','wpdance'); ?></option>
			<option value="0" <?php echo (!$sliders_config['touch_drag'])?'selected':''; ?>><?php _e('No','wpdance'); ?></option>
		</select>
	</div>
	<div class="line_wrapper">
		<label><?php _e('Auto Play','wpdance'); ?></label>
		<select name="auto_play">
			<option value="1" <?php echo ($sliders_config['auto_play'])?'selected':''; ?>><?php _e('Yes','wpdance'); ?></option>
			<option value="0" <?php echo (!$sliders_config['auto_play'])?'selected':''; ?>><?php _e('No','wpdance'); ?></option>
		</select>
	</div>
	<div class="line_wrapper">
		<label><?php _e('Auto Play Speed','wpdance'); ?></label>
		<input name="auto_play_speed" type="number" min="1" step="1" value="<?php echo $sliders_config['auto_play_speed']; ?>" />
		ms
	</div>
	<div class="line_wrapper">
		<label><?php _e('Auto Play Timeout','wpdance'); ?></label>
		<input name="auto_play_timeout" type="number" min="1" step="1" value="<?php echo $sliders_config['auto_play_timeout']; ?>" />
		ms
	</div>
	<div class="line_wrapper">
		<label><?php _e('Auto Play Hover Pause','wpdance'); ?></label>
		<select name="auto_play_hover_pause">
			<option value="1" <?php echo ($sliders_config['auto_play_hover_pause'])?'selected':''; ?>><?php _e('Yes','wpdance'); ?></option>
			<option value="0" <?php echo (!$sliders_config['auto_play_hover_pause'])?'selected':''; ?>><?php _e('No','wpdance'); ?></option>
		</select>
	</div>
	<div class="line_wrapper">
		<label><?php _e('Item Margin Right','wpdance'); ?></label>
		<input name="margin" type="number" min="0" step="1" value="<?php echo $sliders_config['margin']; ?>" />
		px
	</div>
	<div class="responsive_option_wrapper">
		<label><?php _e('Responsive Option','wpdance'); ?></label>
		<div class="option_list">
			<ul>
				<?php foreach( $sliders_config['responsive_option']['break_point'] as $k => $break){ ?>
				<li>
					<label><?php _e('Breakpoint from','wpdance'); ?></label>
					<input name="responsive_option[break_point][]" type="number" min="0" step="1" value="<?php echo (int)$break; ?>" class="small-text" />
					<span>px</span>
					<label><?php _e('Items','wpdance'); ?></label>
					<input name="responsive_option[item][]" type="number" min="0" step="1" value="<?php echo (int)$sliders_config['responsive_option']['item'][$k]; ?>" class="small-text" />
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
	function sort_list_images(){
		jQuery( "#sortable" ).sortable();
	}
    jQuery(document).ready(function($){

		clear_button = $('#clear-all-slides');
		
		if( $('#sortable > li').length > 0 )
			clear_button.show();
		else
			clear_button.hide();
		
		clear_button.click(function(event){
			$('#sortable').html('');
			clear_button.hide();
		});
		
		count_id = '<?php echo rand(0,1000),time()?>';
		count_id = parseInt(count_id); 
	var ready_lightbox = false;
	fancy = $(".preview-img-edit").fancybox({
		'minWidth' : 450
		,'minHeight' : 450
		,beforeLoad : function(){
			if(	ready_lightbox ){
			}			
		}
		,beforeClose  : function(){
			ready_lightbox = false;
		}
	});

	$(".save-slide").live('click',function(){
		$('.fancybox-close').trigger('click');
	});	

	

	$( "#sortable" ).disableSelection();	
		sort_list_images();
		var _custom_media = true,_orig_send_attachment = wp.media.editor.send.attachment;
		$('.stag-metabox-table').click(function(e) {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			var button = $(this);
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment){
				console.log(attachment);
				//console.log(props);
				if( attachment.type == 'image' ){
					var thumb_id  = attachment.id;
					var thumb_url = '';
					if( typeof(attachment.sizes.thumbnail) !== 'undefined' ){
						thumb_url = attachment.sizes.thumbnail.url;
					}else{
						thumb_url = attachment.sizes[props.size].url;
					}
					//var insert_url = attachment.sizes[props.size].url;
					var insert_url = attachment.sizes['full'].url;
					var link_url = props.linkUrl;
					if( props.link == 'file' ){
						link_url = attachment.url;
					}
					if( props.link == 'post' ){
						link_url = attachment.link;
					}	
					if( props.link == 'none' ){
						link_url = '#';
					}					
					var image_title = attachment.title;
					var slide_description = attachment.description; 
					var image_alt = attachment.alt;		
					build_html = '';
					if ( _custom_media ) {
						count_id = count_id + 1;
						build_html += '<div id="image-value' + count_id + '" class="hidden lightbox-image">';
						build_html += '<img  class="lightbox-preview-img" src="' + insert_url + '" alt="' + image_alt + '" title="' + image_title + '">';
						build_html += '<input type="hidden" value="' + count_id + '" name="element_id[]" class="inline-element element_id">';
						build_html += '<input type="hidden" value="' + thumb_url + '" name="thumb_url[]" id="thumb_url" class="inline-element element_thumb">';
						build_html += '<input type="hidden" value="' + thumb_id + '" name="thumb_id[]" id="thumb_id" class="inline-element element_thumb_id">';
						build_html += '<input type="hidden" value="' + insert_url + '" id="element_image_url" name="element_image_url[]" class="inline-element insert_url">';
						build_html += '<p><span class="label">Slide Url</span><input type="text" value="' + link_url + '" name="element_url[]" class="inline-element link_url"></p>';
						build_html += '<p><span class="label">Image Title</span><input type="text" value="' + image_title + '" name="element_title[]" class="inline-element image_title "></p>';
						build_html += '<p><span class="label">Image Alt</span><input type="text" value="' + image_alt + '" name="element_alt[]" class="inline-element image_alt"></p>';
						build_html += '<p><span class="label">Slide Title</span><input type="text" value="' + image_title + '" name="slide_title[]" class="inline-element slide_title"></p>';
						build_html += '<p><span class="label">Slide Contents</span><textarea name="slide_content[]" class="inline-element slide_content">'+slide_description+'</textarea></p>';
						build_html += '<div class="btn fancy-button-wrapper"><a href="javascript:void(0)" class="button save-slide" name="save-slide">Save</a>';
						build_html += '<a href="javascript:void(0)" class="button save-slide" name="close-slide">Close</a></div>';
						build_html += '</div>';
						
						
						build_html += '<p class="image-wrappper">';
						build_html += '<img  class="preview-img" src="' + thumb_url + '" alt="' + image_alt + '" title="' + image_title + '" width="120" height="120">';
						build_html += '<a href="#image-value' + count_id + '" class="preview-img-edit">Edit</a>';
						build_html += '<a href="javascript:void(0)" class="preview-img-remove">Del</a>';
						build_html += '</p>';
						
						jQuery('<li class="ui-state-default"></li>').html(build_html).appendTo('#sortable');
						clear_button.show();

					} else {
						return _orig_send_attachment.apply( this, [props, attachment] );
					};
				}
			}
			wp.media.editor.open(button);
			sort_list_images();
			
			return false;
		});
		
		//bind editor upload image
		$('.add_media').on('click', function(){
			_custom_media = false;
		});
		
		//remove thumb function
		$('.image-wrappper > .preview-img-remove').live('click',function(){
			$(this).parent().parent().remove();
			if( $('#sortable > li').length > 0 )
				clear_button.show();
			else
				clear_button.hide();			
			sort_list_images();
		});
    });
//]]>	
</script>