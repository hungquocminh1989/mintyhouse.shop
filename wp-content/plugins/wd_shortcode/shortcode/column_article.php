<?php 
function ew_shortcode_column($atts, $content = null, $code) {
	return '<div class="'.$code.'">' . do_shortcode(trim($content)) . '</div>';
}
function ew_shortcode_column_last($atts, $content = null, $code) {
	return '<div class="'.str_replace('_last','',$code).' last">' . do_shortcode(trim($content)) . '</div><div class="clearboth"></div>';
}

function ew_shortcode_column_nest($atts, $content = null, $code) {
	return '<div class="'.str_replace('_nest','',$code).'">' . do_shortcode(trim($content)) . '</div>';
}
function ew_shortcode_column_nest_last($atts, $content = null, $code) {
	return '<div class="'.str_replace('_nest_last','',$code).' last">' . do_shortcode(trim($content)) . '</div><div class="clearboth"></div>';
}

add_shortcode('one_half', 'ew_shortcode_column');
add_shortcode('one_third', 'ew_shortcode_column');
add_shortcode('one_fourth', 'ew_shortcode_column');
add_shortcode('one_fifth', 'ew_shortcode_column');
add_shortcode('one_sixth', 'ew_shortcode_column');

add_shortcode('two_third', 'ew_shortcode_column');
add_shortcode('three_fourth', 'ew_shortcode_column');
add_shortcode('two_fifth', 'ew_shortcode_column');
add_shortcode('three_fifth', 'ew_shortcode_column');
add_shortcode('four_fifth', 'ew_shortcode_column');
add_shortcode('five_sixth', 'ew_shortcode_column');

add_shortcode('one_half_last', 'ew_shortcode_column_last');
add_shortcode('one_third_last', 'ew_shortcode_column_last');
add_shortcode('one_fourth_last', 'ew_shortcode_column_last');
add_shortcode('one_fifth_last', 'ew_shortcode_column_last');
add_shortcode('one_sixth_last', 'ew_shortcode_column_last');

add_shortcode('two_third_last', 'ew_shortcode_column_last');
add_shortcode('three_fourth_last', 'ew_shortcode_column_last');
add_shortcode('two_fifth_last', 'ew_shortcode_column_last');
add_shortcode('three_fifth_last', 'ew_shortcode_column_last');
add_shortcode('four_fifth_last', 'ew_shortcode_column_last');
add_shortcode('five_sixth_last', 'ew_shortcode_column_last');

add_shortcode('one_half_nest', 'ew_shortcode_column_nest');
add_shortcode('one_third_nest', 'ew_shortcode_column_nest');
add_shortcode('one_fourth_nest', 'ew_shortcode_column_nest');
add_shortcode('one_fifth_nest', 'ew_shortcode_column_nest');
add_shortcode('one_sixth_nest', 'ew_shortcode_column_nest');

add_shortcode('two_third_nest', 'ew_shortcode_column_nest');
add_shortcode('three_fourth_nest', 'ew_shortcode_column_nest');
add_shortcode('two_fifth_nest', 'ew_shortcode_column_nest');
add_shortcode('three_fifth_nest', 'ew_shortcode_column_nest');
add_shortcode('four_fifth_nest', 'ew_shortcode_column_nest');
add_shortcode('five_sixth_nest', 'ew_shortcode_column_nest');

add_shortcode('one_half_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('one_third_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('one_fourth_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('one_fifth_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('one_sixth_nest_last', 'ew_shortcode_column_nest_last');

add_shortcode('two_third_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('three_fourth_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('two_fifth_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('three_fifth_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('four_fifth_nest_last', 'ew_shortcode_column_nest_last');
add_shortcode('five_sixth_nest_last', 'ew_shortcode_column_nest_last');
?>