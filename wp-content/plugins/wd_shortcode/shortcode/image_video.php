<?php 
if(!function_exists ('ew_img_video')){
	function ew_img_video($atts){
		extract(shortcode_atts(array(
			'src_thumb'		=> 	'',
			'src_zoom_img' 	=> 	'',
			'link_video'	=>	'',
			'width_thumb' 	=> 	'190',
			'height_thumb' 	=> 	'103',
			'type'			=>	'',
			'use_lightbox'	=>	'true',
			'custom_link'	=>	'#',
			'class'			=>	'',
			'title'			=>	''
		),$atts));
		$width_div = $width_thumb + 8;
		$height_div = $height_thumb + 8;
		$left_fancy = floor(($width_div - 30)/2);
		$top_fancy = floor(($height_div - 30)/2);
		$result = "<div class='image-style {$class}' style='width:{$width_div}px;height:{$height_div}px'>";
		
		if($type == 'video'){
			if($link_video){
				if(strstr($link_video,'youtube.com') || strstr($link_video,'youtu.be')){
					 $class_fancy = ' youtube';
					 $big_video_url = 'http://www.youtube.com/watch?v='.  wp_parse_youtube_link($link_video);
				}
				else{
					$class_fancy = 'vimeo';
					$big_video_url = $link_video;
				}
				$result .= "<a class='thumbnail' href='".$custom_link."' style='width:{$width_thumb}px;height:{$height_thumb}px'>".get_thumbnail_video($link_video,$width_thumb,$height_thumb)."</a>";
				if($use_lightbox == 'true')
					$result .= "<div class='fancybox_container' style='display:none;' id='img-video-".rand(0,1000)."{$width_thumb}{$height_thumb}'>
							<a title='{$title}' class='fancybox_control {$class_fancy}' href='{$big_video_url}' style='left:{$left_fancy}px;top:{$top_fancy}px'>Lightbox</a>
						</div>";
			}
		}
		else {
			if($src_thumb)
				$result .= "<a href='{$custom_link}' class='thumbnail' style='width:{$width_thumb}px;height:{$height_thumb}px'><img width='{$width_thumb}' height='{$height_thumb}' src='{$src_thumb}'/></a>";
			if($src_zoom_img && $use_lightbox == 'true')	
				$result .= "<div class='fancybox_container' style='display:none;' id='img-video-".rand(0,1000)."{$width_thumb}{$height_thumb}'>
						<a title='{$title}' class='fancybox_control' href='{$src_zoom_img}' style='left:{$left_fancy}px;top:{$top_fancy}px'>Lightbox</a>
					</div>";
		}
		
		$result .= "</div>";
		
		return $result;
	}
}
add_shortcode('ew_img_video','ew_img_video');

if( !function_exists('wd_background_video_shortcode') ){
	function wd_background_video_shortcode($atts, $content = null){
		extract(shortcode_atts(array(
				'video_url'		=>	''
				,'volume'			=> 0 /* 0 -> 1*/
				,'height'			=> '480px'
				,'bg_opacity'		=> "0.35"
				,'bg_color'			=> 'black'
				,'auto_play'		=> 0
				,'loop'				=> 1
				,'margin_top'		=> '-100px'
				,'not_support_txt'	=>	'Your browser does not support the video tag.'
			),$atts));
			
			if( strlen(trim($video_url)) == 0 ){
				return;
			}
			
			$bg_opacity = is_numeric($bg_opacity)? $bg_opacity: 0.35;
			
			$video_type = 'mp4';
			$parse_url = parse_url($video_url);
			if( isset($parse_url['path']) && strlen($parse_url['path']) > 4 ){
				$video_name = explode('.', $parse_url['path']);
				if( is_array($video_name) && count($video_name) > 0 ){
					$video_type = $video_name[count($video_name)-1];
				}
			}
			
			$rand_id = "wd_background_video_" . rand(0, 1000);
			
			$style = '';
			$style .= '#'.$rand_id.'{height:'.$height.';}';
			$style .= '#'.$rand_id.' .top_content{background-color:'.(($bg_color == 'black')? 'rgba(0,0,0,'.$bg_opacity.')' : 'rgba(255,255,255,'.$bg_opacity.')').';}';
			$style .= '#'.$rand_id.' video{margin-top:'.$margin_top.';}';
			ob_start();
			?>
			<div class="wd_background_video" id="<?php echo $rand_id;?>">
				
				<div class="top_content">
					<div class="cover_button <?php echo ($auto_play)?'playing':'paused'; ?>"></div>
					<div class="container"><?php echo do_shortcode($content);?></div>
				</div>
				<?php if( in_array($video_type, array('mp4', 'ogg', 'webm', 'ogv')) ): ?>
				<video height="auto" <?php echo ($auto_play)? 'autoplay':'' ;?> <?php echo ($loop)? 'loop':'' ;?>>
					<source src="<?php echo esc_url($video_url);?>" type="video/<?php echo ($video_type == 'ogv')?'ogg':$video_type; ?>">
					<?php echo esc_attr($not_support_txt);?>
				</video>
				<?php endif;?>
			</div>
			
			<style type="text/css">
				<?php echo $style; ?>
			</style>
			
			<script type="text/javascript">
				
				jQuery(document).ready(function() {
					"use strict";
					var rand_id = '<?php echo $rand_id; ?>';
					var video = jQuery('#'+rand_id).find('video');
					var video_dom = video.get(0);
					video.prop("volume", <?php echo $volume; ?>);
					
					var bg_color = jQuery('#'+rand_id +' .top_content').css('background-color');
					
					<?php if( $auto_play ): ?>
						jQuery('#'+rand_id+' .cover_button').addClass('playing');
						jQuery('#'+rand_id+' .top_content').css({'background-color':'transparent'});
					<?php endif; ?>
					
					jQuery('#'+rand_id).bind('click', function(){
						if( video_dom.paused ) {
							jQuery(this).find('.cover_button').removeClass('paused').addClass('playing');
							jQuery(this).find('.top_content').css({'background-color':'transparent'});
							video_dom.play();
						}
						else{
							jQuery(this).find('.cover_button').removeClass('playing').addClass('paused');
							jQuery(this).find('.top_content').css({'background-color':bg_color});
							video_dom.pause();
						}
					});

				});
			</script>
			<?php
			return ob_get_clean();
	}
}
add_shortcode('background_video', 'wd_background_video_shortcode');
?>