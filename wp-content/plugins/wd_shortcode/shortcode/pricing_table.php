<?php

// **********************************************************************// 
// ! Register New Element: Pricing Table
// **********************************************************************//
if (!function_exists('wd_ptable_shortcode')) {
	function wd_ptable_shortcode($atts, $content = null) {
        $args = array(
            "title"         => "",
            "price"         => "0",
            "currency"      => "$",
            "price_period"  => "/mo",
            "link"          => "",
            "target"        => "",
            "button_text"   => "Buy Now",
            "active"        => ""
        );
	        
		extract(shortcode_atts($args, $atts));
	        
	    $html = ""; 
	        
        if($target == ""){
                $target = "_self";
        }
        
        if($active == "yes"){
            $html .= "<div class='wd_price_table active_price'>";
        } else {
            $html .= "<div class='wd_price_table'>";
        }
		$html .= "<div class='price_table_inner'>";
        $html .= "<ul>";
		$html .= "<li class='cell table_title'><h4>".$title."</h4></li>";
        $html .= "<li class='prices'>";
        $html .= "<div class='price_in_table'>";
        $html .= "<sup class='value'>".$currency."</sup>";
        $html .= "<span class='pricing'>".$price."</span>";
        $html .= "<span class='period'>".$price_period."</span>";
        $html .= "</div>";
        $html .= "</li>"; //close price li wrapper
	    
	    $html .= "<li class='content'>". $content. "</li>"; //append pricing table content 
	    
	    $html .="<li class='price_button'>";
	    $html .= "<a class='button' href='$link' target='$target'>".$button_text."</a>";
	    $html .= "</li>"; //close button li wrapper
	    
	    $html .= "</ul>";
	    $html .= "</div>"; //close price_table_inner
	    $html .="</div>"; //close price_table
	    
	    return $html;
	}
}
add_shortcode('wd_ptable', 'wd_ptable_shortcode');

?>