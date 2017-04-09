<?php 

if(!function_exists ('hightlight_text')){
	function hightlight_text($atts,$content){
		extract(shortcode_atts(array(
			'background'=>'000'
		),$atts));
		return "<span style='background-color:{$background};padding-left:2px'>{$content}</span>";
	}
}
add_shortcode('hightlight_text','hightlight_text');

if(!function_exists ('add_line')){
	function add_line($atts)
	{
		extract(shortcode_atts(array(
						'height_line'	=>	'1'
						,'color'		=>	'black'
						,'class'		=>	''
		),$atts));
		return "<p class='add-line {$class}' style='width:100%;height:{$height_line}px;background-color:{$color};text-indent:-9999px'>wpdance</p>";

	}
}
add_shortcode('add_line','add_line');



if(!function_exists ('icon_function')){
	function icon_function($atts)
	{
		extract(shortcode_atts(array(
						'icon'				=> ''
						,'color'			=> ''
						,'font_size' 		=> ''
						,'extra_class'	=> ''
		),$atts));
		$inline_style = '';
		if( $color != '' ){
			$inline_style .= 'color:'.$color.';';
		}
		
		if( $font_size != '' ){
			$inline_style .= 'font-size:'.$font_size.';';
		}
		
		$inline_style = 'style="'.$inline_style.'"';
		
		if( strlen($icon)>0 )
			return "<i class=\"fa {$icon} {$extra_class}\" ".$inline_style."></i>";
		return '';

	}
}

add_shortcode('icon','icon_function');

if(!function_exists ('hide_phone_function')){
	function hide_phone_function($atts,$content){
		return "<div class='hidden-phone'>".do_shortcode($content)."</div>";
	}
}

add_shortcode('hidden_phone','hide_phone_function');


if(!function_exists ('dropcap_function')){
	function dropcap_function($atts,$content)
	{
		extract(shortcode_atts(array(
						'color'			=>	''
						,'font_size'	=> '60px'
		),$atts));
		static $wd_dropcap_counter = 1;
		
		$class_count = 'dropcap_'.$wd_dropcap_counter;
		$style = '';
		if( strlen($color) > 0 ){
			$style .= '.'.$class_count.' p:first-child:first-letter{color:'.$color.'}';
		}
		if( strlen($font_size) > 0 ){
			$style .= '.'.$class_count.' p:first-child:first-letter{font-size:'.$font_size.';}';
		}
		ob_start();
		$wd_dropcap_counter++;
		?>
			<div class="dropcap <?php echo $class_count; ?>"><p><?php echo do_shortcode($content); ?></p></div>
			<style type="text/css">
				<?php echo $style; ?>
			</style>
		<?php
		
		return ob_get_clean();
	}
}
add_shortcode('dropcap','dropcap_function');
?>