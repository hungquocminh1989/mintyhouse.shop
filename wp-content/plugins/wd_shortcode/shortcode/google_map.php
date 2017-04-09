<?php 
function google_map_function($atts) {
	extract(shortcode_atts(array(
		'address' 		=> 	"Ho Chi Minh"
		,'title'		=>	"Head Office"
        ,'height'		=> 	360
		,'zoom'			=> 	16
		,'map_type'			=> 	"TERRAIN"
		,'map_color' 	=> 	'#00ffee'
		,'water_color'	=> '#00ffee'
		,'road_color'	=> '#00ffee'

	), $atts));
	
	$height = absint($height) > 0 ? absint($height) : 360;
	
	ob_start();
	?> 
    <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>-->
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
    var map,geocoder;
	geocoder = new google.maps.Geocoder();
	
    function initialize() {
		var _lat_lgn_array;
        var styles = {
            'wpdance':  [{
				"featureType": "administrative",
				"stylers": [
				  { "visibility": "on" }
				]}	
			<?php if( strlen($map_color) > 0 ): ?>	
			,{	"stylers": [
					{ "visibility": "on" }
					,{ "hue": "<?php echo $map_color;?>" }
					,{ "saturation": -50 }
			]}		
			<?php endif; ?>
			,{ featureType: "water", stylers: [ { hue: "<?php echo $water_color;?>"} ] }
			,{ featureType: "road", stylers: [ { hue: "<?php echo $road_color;?>" } ] }		  

			]
		};
        
		_lat_lgn_array = codeAddress('<?php echo $address;?>');
        var myLatlng = new google.maps.LatLng('21.033333', '105.85');
        var myOptions = {
            zoom: <?php echo $zoom;?>
			,zoomControlOptions: {
			  style: google.maps.ZoomControlStyle.LARGE
			}			
            ,center: myLatlng
            ,mapTypeId: google.maps.MapTypeId.<?php echo strtoupper($map_type);?>
            ,disableDefaultUI: false
			,mapTypeId: 'wpdance'
            ,draggable: true
            ,zoomControl: true
			,panControl: true
			,mapTypeControl: true
			,scaleControl: false
			,streetViewControl: true
			,overviewMapControl: true
            ,scrollwheel: false
            ,disableDoubleClickZoom: false
        }
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        var styledMapType = new google.maps.StyledMapType(styles['wpdance'], {name: 'wpdance'});
        map.mapTypes.set('wpdance', styledMapType);
        
        var marker = new google.maps.Marker({
            position: myLatlng, 
            map: map,
            title:"<?php echo $title;?>"
        });   
    }
 
	function codeAddress(address) {
		_ret_array = new Array();
		geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				_ret_array =  new Array(results[0].geometry.location.lat(),results[0].geometry.location.lng());
				map.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: map
					,title:"<?php echo $title;?>"
					,position: results[0].geometry.location
				});
				
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
		return _ret_array;
	} 
	
	jQuery(document).ready(function() {
	    google.maps.event.addDomListener(window, 'load', initialize);
		google.maps.event.addDomListener(window, 'resize', initialize);
	});	
	

    
    </script>
    
    <div id="map_container">
        <div id="map_canvas" style="height:<?php echo $height ?>px;"></div>
        <div id="map_overlay_top"></div>
        <div id="map_overlay_bottom"></div>
    </div>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

add_shortcode('google_map','google_map_function');
?>