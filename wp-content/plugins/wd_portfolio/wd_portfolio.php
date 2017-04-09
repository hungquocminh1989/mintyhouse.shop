<?php
/*
  Plugin Name: WD Portfolio
  Plugin URI: http://www.wpdance.com
  Description: Portfolio From WPDance Team
  Version: 1.0.0
  Author: WD Team
  Author URI: http://www.wpdance.com
 */
 
require_once ( dirname(__FILE__) . '/templates' . "/template.php"); 
 
class WD_Portfolio {

	/******************************** PORTFOLIO POST TYPE INIT START ***********************************/
	
	public function wd_portfolio_list_categories() {
		$_categories = get_categories('taxonomy=wd-portfolio-category');
		foreach ($_categories as $_cat) {
			?>
			<li class="cat-item cat-item-<?php echo $_cat->term_id; ?>">
				<a title="View all posts filed under <?php echo $_cat->name; ?>" href="#<?php //echo get_term_link($_cat->slug, 'wd-portfolio-category'); ?>" data-filter=".<?php echo $_cat->slug; ?>"><?php echo $_cat->name; ?></a>
			</li>
			<?php
		}
	}

	public function wd_portfolio_get_item_classes($post_id = null) {
		if ($post_id === null)
			return;
		$_terms = wp_get_post_terms($post_id, 'wd-portfolio-category');
		foreach ($_terms as $_term) {
			echo " " . $_term->slug;
		}
	}

	public function wd_portfolio_get_attachment_src($attachment_id, $size_name = 'thumbnail') {

		global $_wp_additional_image_sizes;
		$size_name = trim($size_name);
		$meta = wp_get_attachment_metadata($attachment_id);

		if (empty($meta['sizes']) || empty($meta['sizes'][$size_name])) {

			// let's first see if this is a registered size
			if (isset($_wp_additional_image_sizes[$size_name])) {
				$height = (int) $_wp_additional_image_sizes[$size_name]['height'];
				$width = (int) $_wp_additional_image_sizes[$size_name]['width'];
				$crop = (bool) $_wp_additional_image_sizes[$size_name]['crop'];

				// if not, see if name is of form [width]x[height] and use that to crop
			} else if (preg_match('#^(\d+)x(\d+)$#', $size_name, $matches)) {
				$height = (int) $matches[2];
				$width = (int) $matches[1];
				$crop = true;
			}

			if (!empty($height) && !empty($width)) {
				$resized_path = $this->wd_portfolio_generate_attachment($attachment_id, $width, $height, $crop);
				$fullsize_url = wp_get_attachment_url($attachment_id);

				$file_name = basename($resized_path);
				$new_url = str_replace(basename($fullsize_url), $file_name, $fullsize_url);

				if (!empty($resized_path)) {
					$meta['sizes'][$size_name] = array(
						'file' => $file_name,
						'width' => $width,
						'height' => $height,
					);

					wp_update_attachment_metadata($attachment_id, $meta);
					return array(
						$new_url,
						$width,
						$height
					);
				}
			}
		}
		return wp_get_attachment_image_src($attachment_id, $size_name);
	}

	public function wd_portfolio_generate_attachment($attachment_id = 0, $width = 0, $height = 0, $crop = true) {
		$attachment_id = (int) $attachment_id;
		$width = (int) $width;
		$height = (int) $height;
		$crop = (bool) $crop;

		$original_path = get_attached_file($attachment_id);

		$resized_path = @image_resize($original_path, $width, $height, $crop);

		if (
				!is_wp_error($resized_path) &&
				!is_array($resized_path)
		) {
			return $resized_path;

			// perhaps this image already exists.  If so, return it.
		} else {
			$orig_info = pathinfo($original_path);
			$suffix = "{$width}x{$height}";
			$dir = $orig_info['dirname'];
			$ext = $orig_info['extension'];
			$name = basename($original_path, ".{$ext}");
			$destfilename = "{$dir}/{$name}-{$suffix}.{$ext}";
			if (file_exists($destfilename)) {
				return $destfilename;
			}
		}

		return '';
	}


	public function wd_portfolio_get_filetype($itemSrc) {
		if(preg_match('/youtube\.com\/watch/i', $itemSrc) || preg_match('/youtu\.be/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/vimeo\.com/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/\b.mov\b/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/\b.swf\b/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/\b.avi\b/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/\b.mpg\b/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/\b.mpeg\b/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else if(preg_match('/\b.mp4\b/i', $itemSrc)) {
			return 'wd-pretty-video';
		} else {
			return 'wd-pretty-image';
		}
	}


	public function wd_portfolio_section_options() {
		?>
		<div class="wd-portfolio-meta-section">
			<div class="form-wrap">
				<div class="form-field">
					<label for="wd_portfolio"><?php _e('Image/Video URL', 'wpdance_pf')?></label>
					<input type="text" id="wd_portfolio" name="wd_portfolio" value="<?php echo htmlspecialchars($this->wd_portfolio_get_meta('wd-portfolio')); ?>" style="width:70%;" />
					<a id="wd_portfolio_media_lib" href="javascript:void(0);" class="button" rel="wd_portfolio">URL from Media Library</a>
					<p><?php _e('Enter URL for the full-size image or video (youtube, vimeo, swf, quicktime) you want to display in the lightbox gallery. You can also choose Image URL from your Media gallery', 'wpdance_pf')?></p>
				</div>            
				<div class="form-field">
					<label for="wd_portfolio_url"><?php _e('Portfolio URL', 'wpdance_pf')?></label>
					<input type="text" name="wd_portfolio_url" value="<?php echo htmlspecialchars($this->wd_portfolio_get_meta('wd-portfolio-url')); ?>" />
					<p><?php _e('Enter URL to the live version of the project.', 'wpdance_pf')?></p>
				</div>   

				<?php
					require_once( WDP_TEMPLATE.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."slideshow.php" );
				?>
				
			</div>
			<input type="hidden" name="wd_portfolio_noncename" id="wd_portfolio_noncename" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
		</div>
		<?php
	}


	public function wd_portfolio_save_data($post_id, $post) {

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( isset($_POST['wd_portfolio_noncename']) && !wp_verify_nonce($_POST['wd_portfolio_noncename'], plugin_basename(__FILE__)))
			return $post->ID;

		if ($post->post_type == 'revision')
			return; //don't store custom data twice

		if (!current_user_can('edit_post', $post->ID))
			return $post->ID;

		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.
		
		//do save portfolio slider
		if( isset($_POST['_wd_slider']) && $_POST['_wd_slider'] == 1 ){
			$ret_str = '';
			$element_count = count($_POST['element_id']);
			$ret_arr = array();
			for( $i = 0 ; $i < $element_count ; $i++ ){	
				$temp_arr = array(
					'id' 				=> absint($_POST['element_id'][$i])
					,'image_url' 		=> wp_kses_data($_POST['element_image_url'][$i])
					,'thumb_id' 		=> absint($_POST['thumb_id'][$i])
					,'thumb_url' 		=> wp_kses_data($_POST['thumb_url'][$i])
					,'url' 				=> wp_kses_data($_POST['element_url'][$i])
					,'alt' 				=> wp_kses_data($_POST['element_alt'][$i])
					,'title'			=> wp_kses_data($_POST['element_title'][$i])
				
				);
				array_push( $ret_arr, $temp_arr );
			}
			
			$ret_str = serialize($ret_arr);
			update_post_meta($post_id,'_wd_slider',$ret_str);	
		}		
		if( isset($_POST) && isset($_POST['wd_portfolio']) && isset($_POST['wd_portfolio_url']) ){
			$mydata = array();
			$mydata['wd-portfolio'] = $_POST['wd_portfolio'];
			$mydata['wd-portfolio-url'] = $_POST['wd_portfolio_url'];

			// Add values of $mydata as custom fields
			foreach ($mydata as $key => $value) { //Let's cycle through the $mydata array!
				update_post_meta($post->ID, $key, $value);
				if (!$value)
					delete_post_meta($post->ID, $key); //delete if blank
			}
		}
	}	
	
	public function wd_portfolio_register() {

		$labels = array(
			'name' => __('Portfolio Items', 'wpdance_pf'),
			'singular_name' => __('Portfolio Item', 'wpdance_pf'),
			'add_new' => __('Add Portfolio Item', 'wpdance_pf'),
			'add_new_item' => __('Add New Portfolio Item', 'wpdance_pf'),
			'edit_item' => __('Edit Portfolio Item', 'wpdance_pf'),
			'new_item' => __('New Portfolio Item', 'wpdance_pf'),
			'view_item' => __('View Portfolio Item', 'wpdance_pf'),
			'search_items' => __('Search Portfolio Item', 'wpdance_pf'),
			'not_found' => __('No Portfolio Items found', 'wpdance_pf'),
			'not_found_in_trash' => __('No Portfolio Items found in Trash', 'wpdance_pf'),
			'parent_item_colon' => '',
			'menu_name' => __('Portfolio Items', 'wpdance_pf')
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'capability_type' => 'post',
			'hierarchical' => true,
			'rewrite' => array('slug' => 'portfolio'),
			'supports' => array(
				'title',
				'thumbnail',
				'editor',
				'excerpt',
			//'author',
			//'trackbacks',
			'custom-fields',
			//'comments', 
			//'revisions', 
			//'page-attributes'
			),
			'menu_position' => 23,
			'menu_icon' => WDP_IMAGE . '/icon.png',
			'taxonomies' => array('wd-portfolio')
		);

		register_post_type('portfolio', $args);

		$this->wd_portfolio_register_taxonomies();
	}	
	
	public function wd_portfolio_register_taxonomies() {
		register_taxonomy('wd-portfolio-category', 'portfolio', array('hierarchical' => true, 'label' => 'Portfolio Category', 'query_var' => true, 'rewrite' => array('slug' => 'portfolio-category')));
	}		
	
	/******************************** PORTFOLIO POST TYPE INIT END *************************************/


	public function __construct(){
		$this->constant();
		
		/****************************/
		// Register Portfolio post type
		add_action('init', array($this,'wd_portfolio_register') );
		add_theme_support('post-thumbnails', array('portfolio'));
		
		register_activation_hook(__FILE__, array($this,'wd_portfolio_activate') );
		register_deactivation_hook(__FILE__, array($this,'wd_portfolio_deactivate') );

	
		
		
		add_action('admin_enqueue_scripts',array($this,'init_admin_script'));
		
		add_action('admin_menu', array( $this,'wd_portfolio_create_section' ) );	
		
		add_filter('attribute_escape', array($this,'rename_second_menu_name') , 10, 2);
		
		add_action('save_post', array($this,'wd_portfolio_save_data') , 1, 2);
		
		add_action( 'template_redirect', array($this,'wd_portfolio_template_redirect') );
		
		
		
		$this->init_trigger();
		$this->init_handle();
		
		add_action( 'plugins_loaded', array($this, 'plugin_load_textdomain') );
	}
	
	function plugin_load_textdomain(){
		load_plugin_textdomain( 'wpdance_pf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	public function wd_portfolio_template_redirect(){
		global $wp_query,$post,$page_datas,$smof_data;
		if( $wp_query->is_page() || $wp_query->is_single() ){
			//if ( has_shortcode( $post->post_content, 'wd-portfolio' ) ) { 
				add_action('wp_enqueue_scripts',array($this,'init_script'));
			//}
		}
		
	}
	
	public function wd_portfolio_create_section() {
		add_meta_box('wd-portfolio-section-options', __('Options', 'wpdance_pf'), array($this,'wd_portfolio_section_options') , 'portfolio', 'normal', 'high');
	}


	
	public function wd_portfolio_deactivate() {
		flush_rewrite_rules();
	}

	public function wd_portfolio_activate() {
		$this->wd_portfolio_register();
		flush_rewrite_rules();
	}		
	
	public function rename_second_menu_name($safe_text, $text) {
		if (__('Portfolio Items', 'wpdance_pf') !== $text) {
			return $safe_text;
		}

		// We are on the main menu item now. The filter is not needed anymore.
		remove_filter('attribute_escape', array($this,'rename_second_menu_name') );

		return __('WD Portfolio', 'wpdance_pf');
	}
	
	public function wd_portfolio_get_meta($field) {
		global $post;
		$custom_field = get_post_meta($post->ID, $field, true);
		switch ($field) {
			case 'wd-portfolio':
				if (preg_match('/\.pdf/', $custom_field)) {
					$pdf_src = urlencode($custom_field);
					$custom_field = "http://docs.google.com/viewer?url=$pdf_src&embedded=true&iframe=true&width=100%&height=100%";
				}
				break;
			default :
				break;
		}
		return $custom_field;
	}




	public function wd_portfolio($atts = array()) {
	
		ob_start();
		$this->wd_portfolio_show($atts);
		$content = ob_get_clean();
		return $content;
	}

	public function wd_portfolio_show($atts = array()) {

		extract(shortcode_atts(array(
			'columns' 				=>  4
			,'portfolio_cats' 			=>  ''
			,'show_filter' 			=>  'yes'
			,'style' 				=>  'padding'
			,'show_title' 			=>  'yes'
			,'show_desc' 			=>  'yes'
			,'show_paging' 			=>  'yes'
			,'count' 				=>  '-1'
		),$atts));		
		show_wd_portfolio(  $columns,$portfolio_cats,$show_filter,$style,$show_title,$show_desc,$show_paging,$count );	
	}


	protected function init_trigger(){
		global $smof_data;
		$width = 550;
		$height = 550;
		if( isset($smof_data['wd_portfolio_thumb_width']) )
			$width = absint( $smof_data['wd_portfolio_thumb_width'] );
		if( isset($smof_data['wd_portfolio_thumb_height']) )
			$height = absint( $smof_data['wd_portfolio_thumb_height'] );
			
		add_image_size('portfolio_image',$width,$height,true); 
	}
	
	
	protected function init_handle(){
		add_shortcode('wd-portfolio', array( $this,'wd_portfolio') );
	}	
	
	public function init_admin_script() {
		if (function_exists('wp_enqueue_media')) {
			wp_register_script('admin_media_lib_35', WDP_JS . '/admin-media-lib-35.js', 'jquery', false,false);
			wp_enqueue_script('admin_media_lib_35');
		} else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_register_script('admin_media_lib', WDP_JS . '/admin-media-lib.js', 'jquery', false,false);
			wp_enqueue_script('admin_media_lib');
		}

	}	
	
	
	public function init_script(){
		wp_enqueue_script('jquery');
		wp_register_script( 'TweenMax', WDP_JS.'/TweenMax.min.js');
		wp_enqueue_script('TweenMax');		
		wp_register_script( 'jquery.prettyPhoto', WDP_JS.'/jquery.prettyPhoto.min.js',array('jquery','TweenMax'));
		wp_enqueue_script('jquery.prettyPhoto');	
		
		// wp_register_script( 'js.isotope', WDP_JS.'/jquery.isotope.min.js',false,false,true);
		// wp_enqueue_script('js.isotope');
		// wp_register_style( 'css.isotope', WDP_CSS.'/isotope.css');
		// wp_enqueue_style('css.isotope');	
		
		wp_register_script( 'wd.portfolio.js', WDP_JS.'/portfolio.js',false,false,true);			
		
		wp_register_style( 'css.prettyPhoto', WDP_CSS.'/prettyPhoto.css');
		wp_enqueue_style('css.prettyPhoto');	
		
	
		
		wp_register_script( 'jquery.quicksand', WDP_JS.'/jquery.quicksand.js');
		wp_enqueue_script('jquery.quicksand');	
		wp_register_script( 'wd.animation', WDP_JS.'/wd.animation.js',array('jquery','TweenMax'));
		wp_enqueue_script('wd.animation');
		wp_register_style( 'wd.portfolio', WDP_CSS.'/wd.portfolio.css');		
		wp_enqueue_style('wd.portfolio');
	}
	
	protected function constant(){
		//define('DS',DIRECTORY_SEPARATOR);	
		if( !defined('WDP_BASE') ){
			define('WDP_BASE'		,  	plugins_url( '', __FILE__ )		);
			define('WDP_JS'			, 	WDP_BASE . '/js'		);
			define('WDP_CSS'		, 	WDP_BASE . '/css'		);
			define('WDP_IMAGE'		, 	WDP_BASE . '/images'	);
			define('WDP_TEMPLATE' 	, 	dirname(__FILE__) . '/templates'	);
		}
	}	
	
}
 
$_wd_portfolio = new WD_Portfolio; // Start an instance of the plugin class 