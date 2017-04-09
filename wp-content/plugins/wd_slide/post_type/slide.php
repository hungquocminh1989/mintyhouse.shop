<?php
if(!function_exists ('wd_register_slide')){
	function wd_register_slide(){
				register_post_type('slide', array(
			'labels' => array(
					'name' => _x('Slide Items', 'post type general name','wpdance'),
					'singular_name' => _x('Slide Item', 'post type singular name','wpdance'),
					'add_new' => _x('Add New', 'Slide','wpdance'),
					'add_new_item' => __('Add New Slide Item','wpdance'),
					'edit_item' => __('Edit Slide Item','wpdance'),
					'new_item' => __('New Slide Item','wpdance'),
					'view_item' => __('View Slide Item','wpdance'),
					'search_items' => __('Search Slide Items','wpdance'),
					'not_found' =>  __('No Slide item found','wpdance'),
					'not_found_in_trash' => __('No Slide items found in Trash','wpdance'),
					'parent_item_colon' => '',
					'menu_name' => __('Slide','wpdance'),
			),
			'singular_label' => __('Slide','wpdance'),
			'public' => false,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'capability_type' => 'page',
			'hierarchical' => false,
			'supports'  =>  array(
					  'title','custom-fields','thumbnail'
					),
			'has_archive' => false,
			'rewrite' =>  array('slug'  =>  'slide', 'with_front' =>  false),
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => false,
		));	
		
	}
}
add_action('init','wd_register_slide');
?>