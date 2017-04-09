<?php
/*
Plugin Name: WD Shop By Color
Plugin URI: http://www.wpdance.com/
Description: Shop By Color From WPDance
Author: Wpdance
Version: 1.1
Author URI: http://www.wpdance.com/
*/

class WD_Shopbycolor {

	
	public  $woo_ready;
	public  $color_ready;
	public  $tax_slug;

	public function __construct(){
	
		$this->constant();
		$this->init_trigger();
		
	}
	
	protected function init_trigger(){

		if( $this->woo_ready == false ){
			return ;
		}
		global $woocommerce,$_wd_msg;
		$_color = 'color';
		$attribute_name    = ( isset( $_color ) )    ? woocommerce_sanitize_taxonomy_name( stripslashes( (string) $_color ) ) : '';	
		$attribute_name  = wc_attribute_taxonomy_name( $attribute_name );
		$attribute_name_array = wc_get_attribute_taxonomy_names();
		//$taxonomy_exists = taxonomy_exists( wc_attribute_taxonomy_name( $attribute_name ) );
		$taxonomy_exists = in_array($attribute_name,$attribute_name_array);
		/**************** Check if attribute available ****************/
		$this->tax_slug = '';
		
		if( !$taxonomy_exists ){
			$this->color_ready = false;
			$_wd_msg = "<strong>Color attribute is not exist.</strong>.Go to Products => Attributes,create new attibute with slug <strong>color</strong>";
			add_action('admin_notices', array($this,'show_msg'));   
		}else{
			$this->color_ready = true;
		
			add_image_size('wd_pc_thumb',30,30,true);
		
			$_tax_slug =  $attribute_name ;
			$this->tax_slug = $_tax_slug;
			$this->init_script();
			$this->init_handle();			
		}
	}
	public function color_layered_nav_init(){
		if ( !is_active_widget( false, false, 'woocommerce_layered_nav', true ) && ! is_admin() ) {
				global $_chosen_attributes;

				$_chosen_attributes = array();

				$attribute_taxonomies = wc_get_attribute_taxonomies();
				if ( $attribute_taxonomies ) {
					foreach ( $attribute_taxonomies as $tax ) {
						if($tax->attribute_name == "color"){
							$attribute       = wc_sanitize_taxonomy_name( $tax->attribute_name );
							$taxonomy        = wc_attribute_taxonomy_name( $attribute );
							$name            = 'filter_' . $attribute;
							$query_type_name = 'query_type_' . $attribute;
							
							$taxonomy_exists = in_array($taxonomy,wc_get_attribute_taxonomy_names());
							
							if ( ! empty( $_GET[ $name ] ) && $taxonomy_exists ) {
							
								$_chosen_attributes[ $taxonomy ]['terms'] = explode( ',', $_GET[ $name ] );

								if ( empty( $_GET[ $query_type_name ] ) || ! in_array( strtolower( $_GET[ $query_type_name ] ), array( 'and', 'or' ) ) )
									$_chosen_attributes[ $taxonomy ]['query_type'] = apply_filters( 'woocommerce_layered_nav_default_query_type', 'and' );
								else
									$_chosen_attributes[ $taxonomy ]['query_type'] = strtolower( $_GET[ $query_type_name ] );

							}
						}
					}
				}
				$wc_query = new WC_Query();
				/*add_filter('loop_shop_post_in',array( $wc_query, 'layered_nav_query' ));*/
		}
	}
	
	protected function init_handle(){
		if( $this->color_ready && $this->woo_ready ){
			$_edit_hook_name = $this->tax_slug.'_edit_form_fields';
			$_add_hook_name = $this->tax_slug.'_add_form_fields';
			add_action( $_edit_hook_name, array($this,'wd_pc_edit_attribute'), 100000, 2 );
			add_action( $_add_hook_name, array($this,'wd_pc_add_attribute'), 100000 );
			add_action('wp_ajax_wd_pc_find_media_thumbnail', array( $this, 'find_media_thumbnail'));
			add_action('wp_ajax_nopriv_wd_pc_find_media_thumbnail', array( $this, 'find_media_thumbnail') );	

			add_action( 'created_term', array( $this, 'wd_pc_color_fields_save'), 10,3 );
			add_action( 'edit_term', array( $this, 'wd_pc_color_fields_save'), 10,3 );
			add_action( 'delete_term', array( $this, 'wd_pc_color_fields_remove'), 10,3 );		

			add_action( 'widgets_init', array($this,'load_color_nav_widget'));	

			$edit_title_color_column_hook = 'manage_edit-'. $this->tax_slug .'_columns';
			$edit_color_column_hook = 'manage_'. $this->tax_slug .'_custom_column';
			
			add_filter( $edit_title_color_column_hook, array($this,'wd_pc_color_color_columns') );
			add_filter( $edit_color_column_hook , array($this,'wd_pc_color_color_column'), 10, 3 );				
			add_action( 'init', array( $this, 'color_layered_nav_init' ) );
		}

	}	
	
	protected function init_script(){
		add_action( 'admin_enqueue_scripts', array($this,'wd_enqueue_color_picker') );
		

	}
	
	protected function constant(){
		//define('DS'			,	DIRECTORY_SEPARATOR			);	
		
		define('PC_BASE'	,  	plugins_url( '', __FILE__ )	);
		define('PC_JS'		, 	PC_BASE . '/js'				);
		define('PC_CSS'		, 	PC_BASE . '/css'			);
		define('PC_IMAGE'	, 	PC_BASE . '/images'			);

		/**************** Check if woocommerce actived ****************/
		$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
		if ( in_array( "woocommerce/woocommerce.php", $_actived ) ) {
			$this->woo_ready = true;
		}else{
			$this->woo_ready = false;
		}
		$this->color_ready = false;
		
	}	
	
	/******************* All Handle Function Start *******************/
	
	public function wd_pc_color_color_columns( $columns ) {
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['color'] = __( 'Color', 'wpdance' );

		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}



	public function wd_pc_color_color_column( $columns, $column, $id ) {
		global $woocommerce;

		if ( $column == 'color' ) {

			$datas = get_metadata( 'term', $id, "wd_pc_color_config", true );
			if( strlen($datas) > 0 ){
				$datas = unserialize($datas);	
			}else{
				$datas = array(
							'wd_pc_color_color' 				=> "#aaaaaa"
							,'wd_pc_color_image' 				=> 0
						);
		
			}

			$columns .= "<span style='background-color:{$datas['wd_pc_color_color']}'></span>";

		}

		return $columns;
	}


	
	public function load_color_nav_widget(){
		register_widget( 'WD_Widget_PC_Color_Nav' );
	}
	
	
	public function wd_pc_color_fields_save( $term_id, $tt_id, $taxonomy ){
		
		$_term_config = array();
		
		$_term_config["wd_pc_color_image"] = isset( $_POST['wd_pc_color_image'] ) ? absint( $_POST['wd_pc_color_image'] ) : 0 ;
		$_term_config["wd_pc_color_color"] = isset( $_POST['wd_pc_color_color'] ) ? esc_attr( $_POST['wd_pc_color_color'] ) : "#aaaaaa" ;

		
		$_term_config_str = serialize($_term_config);
		
		$result = update_metadata( 'term',$term_id,"wd_pc_color_config",$_term_config_str );

	}

	public function wd_pc_color_fields_remove( $term_id, $tt_id, $taxonomy ){
		delete_metadata( 'term',$term_id,"wd_pc_color_config" );
	}	
	
	
	public function wd_pc_edit_attribute( $term, $taxonomy ){
			
		$datas = get_metadata( 'term', $term->term_id, "wd_pc_color_config", true );
		if( strlen($datas) > 0 ){
			$datas = unserialize($datas);	
		}else{
			$datas = array(
						'wd_pc_color_color' 				=> "#aaaaaa"
						,'wd_pc_color_image' 				=> 0
					);
	
		}
		
		$_img =  PC_IMAGE.'/default.png';
		if( absint($datas['wd_pc_color_image']) > 0 ){
			$_img = wp_get_attachment_image_src( absint($datas['wd_pc_color_image']), 'wd_pc_thumb', true ); 
			$_img = $_img[0];
		}
	?>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label><?php _e( 'Color', 'wpdance' ); ?></label></th>
			<td>
				<input name="wd_pc_color_color" id="hex-color" class="wd_pc_colorpicker" data-default-color="<?php echo esc_attr($datas['wd_pc_color_color']);?>" type="text" value="<?php echo esc_attr($datas['wd_pc_color_color']);?>" size="40" aria-required="true">
				<span class="description">Use color picker to pick one color.</span>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Thumbnail Image', 'wpdance' ); ?></label></th>
			<td>
				<input name="wd_pc_color_image" type="hidden" class="wd_pc_custom_image" value="<?php echo absint($datas['wd_pc_color_image']);?>" />
				<img style="padding-bottom:5px;" src="<?php echo esc_url( $_img ) ;?>" class="wd_pc_preview_image" /><br />
				<input class="wd_pc_upload_image_button button" type="button"  size="40" value="Choose Image" />
				<small>&nbsp;<a href="#" class="wd_pc_clear_image_button">Remove Image</a></small>
				<br clear="all" />		
				<span class="description">Choose one thumbnail.</span>
			</td>
		</tr>
			
	<?php
	}		
	
	public function find_media_thumbnail(){
		$thumbnail_id = absint($_POST['img_id']);
		$img_arr =  wp_get_attachment_image_src( $thumbnail_id, 'wd_pc_thumb', true);
		echo $img_arr[0];
		die();
	}	
	
	public function show_msg(){
		global $_wd_msg;
	?>
	
		<div id="message" class="updated">
			<p><?php echo $_wd_msg;?></p>
		</div>
		
	<?php		
	}	
	
	public function wd_enqueue_color_picker(  ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_register_script( 'product.color.filter', PC_JS.'/product_color.js',array( 'wp-color-picker','jquery' ));
		wp_enqueue_script('product.color.filter');				
	}	
	
	public function wd_pc_add_attribute(){
	?>
	
	<div class="form-field form-required">
		<label for="display_type"><?php _e( 'Color', 'wpdance' ); ?></label>
		<input name="wd_pc_color_color" id="hex-color" class="wd_pc_colorpicker" type="text" value="#aaaaaa" size="40" aria-required="true">
		<p>Use color picker to pick one color.</p>
	</div>

	<div class="form-field">
		<label for="display_type"><?php _e( 'Thumbnail Image', 'wpdance' ); ?></label>
		<input name="wd_pc_color_image" type="hidden" class="wd_pc_custom_image" value="" />
		<img style="padding-bottom:5px;" src="" class="wd_pc_preview_image" /><br />
		<input class="wd_pc_upload_image_button button" type="button"  size="40" value="Choose Image" />
		<small>&nbsp;<a href="#" class="wd_pc_clear_image_button">Remove Image</a></small>
		<br clear="all" />		
		<p>Choose one thumbnail.</p>
	</div>


	
	<?php
	}
	
	/******************* All Handle Function End *******************/
	
}	
//add_action('admin_footer', 'add_pc_default_image', 10 );	
add_action('woocommerce_init','shopbycolor_load');


function add_pc_default_image(){

?>

	<script type="text/javascript">
		//<![CDATA[
			var _pc_default_img = '<?php echo PC_IMAGE.'/default.png'; ?>';
		//]]>	
	</script>
		
<?php	

}

function shopbycolor_load(){
	$_wd_shopbycolor = new WD_Shopbycolor; // Start an instance of the plugin class
}

class WD_Widget_PC_Color_Nav extends WP_Widget {

	var $wpdance_widget_cssclass;
	var $wpdance_widget_description;
	var $wpdance_widget_idbase;
	var $wpdance_widget_name;

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		/* Widget variable settings. */
		$this->wpdance_widget_cssclass 		= 'wpdance widget_wd_pc_color_nav';
		$this->wpdance_widget_description	= __( 'Shows a color attribute in a widget which lets you narrow down the list of products when viewing product categories.', 'wpdance' );
		$this->wpdance_widget_idbase 		= 'wpdance_color_nav';
		$this->wpdance_widget_name 			= __( 'WD - Color Layered Nav', 'wpdance' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpdance_widget_cssclass, 'description' => $this->wpdance_widget_description );

		/* Create the widget. */
		parent::__construct( 'wpdance_color_nav', $this->wpdance_widget_name, $widget_ops );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		ob_start();
		global $_chosen_attributes, $woocommerce, $_attributes_array;

		extract( $args );

		if ( ! is_post_type_archive( 'product' ) && ! is_tax( array_merge( array($_attributes_array), array( 'product_cat', 'product_tag' ) ) ) )
			return;
		//if ( ! is_post_type_archive( 'product' ) )
		//	return;
		$current_term 	= $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->term_id : '';
		$current_tax 	= $_attributes_array && is_tax( $_attributes_array ) ? get_queried_object()->taxonomy : '';
		$instance['attribute'] = 'color';

		$title 			= apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$taxonomy 		= wc_attribute_taxonomy_name($instance['attribute']);
		$query_type 	= isset( $instance['query_type'] ) ? $instance['query_type'] : 'and';

		if ( ! taxonomy_exists( $taxonomy ) )
			return;

		$terms 			= get_terms( $taxonomy, array( 'hide_empty' => '1' ) );
		$term_counts    = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
		if ( count( $terms ) > 0 ) {

			

			$found = false;

			echo $before_widget . $before_title . $title . $after_title;

			// Force found when option is selected - do not force found on taxonomy attributes
			if ( ! $_attributes_array || ! is_tax( $_attributes_array ) )
				if ( is_array( $_chosen_attributes ) && array_key_exists( $taxonomy, $_chosen_attributes ) )
					$found = true;

			if(1) {

				// List display
				echo "<ul>";

				foreach ( $terms as $term ) {

					// Get count based on current view - uses transients
					$transient_name = 'wc_ln_count_' . md5( sanitize_key( $taxonomy ) . sanitize_key( $term->term_id ) );

					if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {

						$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );

						set_transient( $transient_name, $_products_in_term );
					}

					$option_is_set = ( isset( $_chosen_attributes[ $taxonomy ] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) );
					
					// skip the term for the current archive
					if ( $current_term == $term->term_id )
						continue;
					
					// If this is an AND query, only show options with count > 0
					if ( $query_type == 'and' ) {

						$count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

						if ( $count > 0 && $current_term !== $term->term_id )
							$found = true;

						if ( $count == 0 && ! $option_is_set )
							continue;

					// If this is an OR query, show all options so search can be expanded
					} else {

						// skip the term for the current archive
						if ( $current_term == $term->term_id )
							continue;

						$count = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

						if ( $count > 0 )
							$found = true;

					}

					$arg = 'filter_' . sanitize_title( $instance['attribute'] );

					$current_filter = ( isset( $_GET[ $arg ] ) ) ? explode( ',', $_GET[ $arg ] ) : array();

					if ( ! is_array( $current_filter ) )
						$current_filter = array();

					$current_filter = array_map( 'esc_attr', $current_filter );

					if ( ! in_array( $term->term_id, $current_filter ) )
						$current_filter[] = $term->slug;

					// Base Link decided by current page
					if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
						$link = home_url();
					} elseif ( is_post_type_archive( 'product' ) || is_page( woocommerce_get_page_id('shop') ) ) {
						$link = get_post_type_archive_link( 'product' );
					} else {
						$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
					}

					// All current filters
					if ( $_chosen_attributes ) {
						foreach ( $_chosen_attributes as $name => $data ) {
							if ( $name !== $taxonomy ) {

								//exclude query arg for current term archive term
								while ( in_array( $current_term, $data['terms'] ) ) {
									$key = array_search( $current_term, $data );
									unset( $data['terms'][$key] );
								}

								if ( ! empty( $data['terms'] ) )
									$link = add_query_arg( sanitize_title( str_replace( 'pa_', 'filter_', $name ) ), implode(',', $data['terms']), $link );

								if ( $data['query_type'] == 'or' )
									$link = add_query_arg( sanitize_title( str_replace( 'pa_', 'query_type_', $name ) ), 'or', $link );
							}
						}
					}

					// Min/Max
					if ( isset( $_GET['min_price'] ) )
						$link = add_query_arg( 'min_price', $_GET['min_price'], $link );

					if ( isset( $_GET['max_price'] ) )
						$link = add_query_arg( 'max_price', $_GET['max_price'], $link );

					// Current Filter = this widget
					if ( isset( $_chosen_attributes[ $taxonomy ] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) ) {

						$class = 'class="chosen"';

						// Remove this term is $current_filter has more than 1 term filtered
						if ( sizeof( $current_filter ) > 1 ) {
							$current_filter_without_this = array_diff( $current_filter, array( $term->term_id ) );
							$link = add_query_arg( $arg, implode( ',', $current_filter_without_this ), $link );
						}

					} else {

						$class = '';
						$link = add_query_arg( $arg, implode( ',', $current_filter ), $link );

					}

					// Search Arg
					if ( get_search_query() )
						$link = add_query_arg( 's', get_search_query(), $link );

					// Post Type Arg
					if ( isset( $_GET['post_type'] ) )
						$link = add_query_arg( 'post_type', $_GET['post_type'], $link );

					// Query type Arg
					if ( $query_type == 'or' && ! ( sizeof( $current_filter ) == 1 && isset( $_chosen_attributes[ $taxonomy ]['terms'] ) && is_array( $_chosen_attributes[ $taxonomy ]['terms'] ) && in_array( $term->term_id, $_chosen_attributes[ $taxonomy ]['terms'] ) ) )
						$link = add_query_arg( 'query_type_' . sanitize_title( $instance['attribute'] ), 'or', $link );

					$datas = get_metadata( 'term', $term->term_id, "wd_pc_color_config", true );
					if( strlen($datas) > 0 ){
						$datas = unserialize($datas);	
					}else{
						$datas = array(
									'wd_pc_color_color' 				=> "#aaaaaa"
									,'wd_pc_color_image' 				=> 0
								);
				
					}					
						
					echo '<li ' . $class . '>';

					echo ( $count > 0 || $option_is_set ) ? '<a title="'. $term->name .'" href="' . esc_url( apply_filters( 'woocommerce_layered_nav_link', $link ) ) . '">' : '<span>';

					
					if( absint($datas['wd_pc_color_image']) > 0  ){
						echo $img_arr =  wp_get_attachment_image( absint($datas['wd_pc_color_image']), 'wd_pc_thumb', true,array('title'=>$term->name,'alt'=>$term->name) );
						
					}else{
						echo "<div style='width:10px; height:10px;background-color:{$datas['wd_pc_color_color']}'></div><span>{$term->name}</span>";
					}					

					echo ( $count > 0 || $option_is_set ) ? '</a>' : '</span>';
					
					echo ' <small class="count">(' . $count . ')</small>';
					
					echo '</li>';

				}

				echo "</ul>";

			} // End display type conditional

			echo $after_widget;

			if ( ! $found )
				ob_end_clean();
			else
				echo ob_get_clean();
		}
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		global $woocommerce;

		if ( empty( $new_instance['title'] ) )
			$new_instance['title'] = wc_attribute_label( $new_instance['attribute'] );

		$instance['title'] 			= strip_tags( stripslashes($new_instance['title'] ) );
		$instance['query_type'] 	= stripslashes( $new_instance['query_type'] );

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		global $woocommerce;

		if ( ! isset( $instance['query_type'] ) )
			$instance['query_type'] = 'and';

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpdance' ) ?></label>
		<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php if ( isset( $instance['title'] ) ) echo esc_attr( $instance['title'] ); ?>" /></p>


		<p><label for="<?php echo $this->get_field_id( 'query_type' ); ?>"><?php _e( 'Query Type:', 'wpdance' ) ?></label>
		<select id="<?php echo esc_attr( $this->get_field_id( 'query_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'query_type' ) ); ?>">
			<option value="and" <?php selected( $instance['query_type'], 'and' ); ?>><?php _e( 'AND', 'wpdance' ); ?></option>
			<option value="or" <?php selected( $instance['query_type'], 'or' ); ?>><?php _e( 'OR', 'wpdance' ); ?></option>
		</select></p>
		<?php
	}
	protected function get_filtered_term_product_counts( $term_ids, $taxonomy, $query_type ) {
		global $wpdb;

		$tax_query  = WC_Query::get_main_tax_query();
		$meta_query = WC_Query::get_main_meta_query();

		if ( 'or' === $query_type ) {
			foreach ( $tax_query as $key => $query ) {
				if ( $taxonomy === $query['taxonomy'] ) {
					unset( $tax_query[ $key ] );
				}
			}
		}

		$meta_query      = new WP_Meta_Query( $meta_query );
		$tax_query       = new WP_Tax_Query( $tax_query );
		$meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );

		// Generate query
		$query           = array();
		$query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
		$query['from']   = "FROM {$wpdb->posts}";
		$query['join']   = "
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
			INNER JOIN {$wpdb->terms} AS terms USING( term_id )
			" . $tax_query_sql['join'] . $meta_query_sql['join'];
		$query['where']   = "
			WHERE {$wpdb->posts}.post_type IN ( 'product' )
			AND {$wpdb->posts}.post_status = 'publish'
			" . $tax_query_sql['where'] . $meta_query_sql['where'] . "
			AND terms.term_id IN (" . implode( ',', array_map( 'absint', $term_ids ) ) . ")
		";
		$query['group_by'] = "GROUP BY terms.term_id";
		$query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
		$query             = implode( ' ', $query );
		$results           = $wpdb->get_results( $query );

		return wp_list_pluck( $results, 'term_count', 'term_count_id' );
	}
}

?>