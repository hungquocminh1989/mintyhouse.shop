<?php
function wd_milestone_shortcode($atts, $content){
	extract( shortcode_atts(array(
					'number'			=>  '0'
					,'symbol'			=> ''
					,'symbol_position'	=> 'after'
					,'subject'			=> ''
					,'color'			=> '#ffffff'
					,'font_size'		=> '60px'
				), $atts)
			);
	if( !is_numeric($number) ){
		$number = 0;
	}
	
	static $wd_milestone_count = 1;
	$unique_class = 'wd_milestone_'.$wd_milestone_count;
	$wd_milestone_count++;
	
	$style = '';
	if( $color != '' ){
		$style .= '.'.$unique_class.'{color:'.$color.'}';
	}
	
	if( $font_size != '' ){
		$style .= '.'.$unique_class.' .number_wrapper{font-size:'.$font_size.'}';
	}
	
	ob_start();
	?>
	<div class="wd_milestone <?php echo $unique_class; ?>">
		<div class="number_wrapper">
		<?php if( $symbol_position == 'after' ): ?>
			<span class="number"><?php echo esc_html($number); ?></span>
			<span class="symbol"><?php echo esc_html($symbol); ?></span>
		<?php else: ?>
			<span class="symbol"><?php echo esc_html($symbol); ?></span>
			<span class="number"><?php echo esc_html($number); ?></span>
		<?php endif; ?>
		</div>
		<div class="subject">
			<?php echo esc_html($subject); ?>
		</div>
	</div>
	
	<style type="text/css">
		<?php echo $style; ?>
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode('milestone', 'wd_milestone_shortcode');
?>