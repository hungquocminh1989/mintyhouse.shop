<?php 
///// Embbed swf,flv file, ... /////
if(!function_exists ('ew_embbed_video')){
	function ew_embbed_video($atts,$content){
		extract(shortcode_atts(array(
			'custom_class'	=>	'',
			'src'			=> 	'',
			'width'			=>	'940',
			'height'		=>	'558'
		),$atts));
		$class = 'embbed_file';
		if($custom_class)
			$class .= " {$custom_class}";
		$id = 'swfobject-'.rand(0,1000);
		
		$result = "<div class='{$class}'><div id='{$id}'></div>";
		if(strlen($src) > 4){
			$ext = substr($src,strlen($src) - 3,3);
			if($ext == 'swf')
				$result .= "<script language='JavaScript'>swfobject.embedSWF('{$src}', '{$id}', '{$width}', '{$height}', '9.0.0', null, '',{wmode:'transparent'});</script>";
			elseif($ext == 'flv' || $ext == 'f4v')
				$result .= '<script language="JavaScript">swfobject.embedSWF("'.get_template_directory_uri().'/flash/videoWP.swf", "'.$id.'", "'.$width.'", "'.$height.'", "9.0.0", null, { 
				link:"'.$src.'",_width:"'.$width.'",_height:"'.$height.'",id_object:"'.$id.'"},{wmode:"transparent",allowfullscreen:"true",scale:"noscale",salign:"lt"});</script>';
		$result .= '</div>';
		}	
		return $result;
	}
}
add_shortcode('ew_embbed_video','ew_embbed_video');
?>