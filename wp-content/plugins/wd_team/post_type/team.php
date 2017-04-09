<?php
function register_team(){
			register_post_type('team', array(
		'labels' => array(
                'name' => _x('Team Members', 'post type general name','wpdance'),
                'singular_name' => _x('Team Members', 'post type singular name','wpdance'),
                'add_new' => _x('Add Member', 'Team','wpdance'),
                'add_new_item' => __('Add Member','wpdance'),
                'edit_item' => __('Edit Member','wpdance'),
                'new_item' => __('New Member','wpdance'),
                'view_item' => __('View Member','wpdance'),
                'search_items' => __('Search Member','wpdance'),
                'not_found' =>  __('No Member found','wpdance'),
                'not_found_in_trash' => __('No Member found in Trash','wpdance'),
                'parent_item_colon' => '',
                'menu_name' => __('Team Members','wpdance'),
		),
		'singular_label' => __('Team','wpdance'),
		'public' => false,
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'page',
		'hierarchical' => false,
		'supports'  =>  array(
                  'title','custom-fields','editor','thumbnail'
                ),
		'has_archive' => false,
		'rewrite' =>  array('slug'  =>  'team', 'with_front' =>  true),
		'query_var' => false,
		'can_export' => true,
		'show_in_nav_menus' => false,
	));	
}
add_action('init','register_team');
?>