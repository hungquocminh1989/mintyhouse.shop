<?php 
/*
Plugin Name: WD Custom bbPress
Plugin URI: http://www.wpdance.com/
Description: Register a taxonomy for bbPress plugin
Author: Wpdance
Version: 1.0
Author URI: http://www.wpdance.com/
*/

class WD_Custom_bbPress{
	function __construct(){
		if( class_exists('bbPress') ){
			add_action('init', array($this, 'register_taxonomy'));
		}
	}
	
	function register_taxonomy(){
		$labels = array(
			'name'              => __( 'Categories', 'wpdance' ),
			'singular_name'     => __( 'Category', 'wpdance' ),
			'search_items'      => __( 'Search Categories', 'wpdance' ),
			'all_items'         => __( 'All Categories', 'wpdance' ),
			'parent_item'       => __( 'Parent Category', 'wpdance' ),
			'parent_item_colon' => __( 'Parent Category:', 'wpdance' ),
			'edit_item'         => __( 'Edit Category', 'wpdance' ),
			'update_item'       => __( 'Update Category', 'wpdance' ),
			'add_new_item'      => __( 'Add New Category', 'wpdance' ),
			'new_item_name'     => __( 'New Category Name', 'wpdance' ),
			'menu_name'         => __( 'Categories', 'wpdance' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'forum_cat' ),
		);

		register_taxonomy( 'forum_cat', 'forum', $args );
	}
}
new WD_Custom_bbPress();
?>