<?php
/*
  Plugin Name: WD Slide
  Plugin URI: http://www.wpdance.com
  Description: Slide From WPDance Team
  Version: 1.0.0
  Author: WD Team
  Author URI: http://www.wpdance.com
 */
class WD_Slide {

	public function __construct(){
		$this->constant();
		
		/****************************/
		// Register Slide post type
		//add_action('init', array($this,'wd_slide_register') );
		$this->wd_slide_register();
		add_theme_support('post-thumbnails', array('slide'));
		
		register_activation_hook(__FILE__, array($this,'wd_slide_activate') );
		register_deactivation_hook(__FILE__, array($this,'wd_slide_deactivate') );

		add_action('admin_enqueue_scripts',array($this,'init_admin_script'));
		
		add_action('admin_menu', array( $this,'wd_slide_create_section' ) );	
		add_action('admin_menu', array( $this,'wd_slide_page_setting' ) );	
		
		add_filter('attribute_escape', array($this,'rename_second_menu_name') , 10, 2);
		
		add_action('save_post', array($this,'wd_slide_save_data') , 1, 2);
		
		add_action( 'template_redirect', array($this,'wd_slide_template_redirect') );
		
		add_action ( 'init', array($this,'register_image_size') );
		
		$this->init_trigger();
		$this->init_handle();
	}
	
	/******************************** Slide POST TYPE INIT START ***********************************/

	public function wd_slide_save_data($post_id, $post) {

		if ( ! isset( $_POST['wd_slide_box_nonce'] ) )
				return $post_id;
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if (!wp_verify_nonce($_POST['wd_slide_box_nonce'],'wd_slide_box'))
			return $post->ID;

		if ($post->post_type == 'revision')
			return; //don't store custom data twice

		if (!current_user_can('edit_post', $post->ID))
			return $post->ID;

		// OK, we're authenticated: we need to find and save the data
		// Sanitize the user input.
		if( isset($_POST['_sliders_slider']) && $_POST['_sliders_slider'] == 1 ){
			$ret_str = '';
			$element_count = count($_POST['element_id']);
			$ret_arr = array();
			for( $i = 0 ; $i < $element_count ; $i++ ){	
				$temp_arr = array(
					'id' 				=> $_POST['element_id'][$i]
					,'thumb_id' 		=> $_POST['thumb_id'][$i]
					,'url' 				=> $_POST['element_url'][$i]
					,'alt' 				=> $_POST['element_alt'][$i]
					,'title' 			=> $_POST['element_title'][$i]
					,'slide_title' 		=> $_POST['slide_title'][$i]
					,'slide_content' 	=> $_POST['slide_content'][$i]					
				
				);
				array_push( $ret_arr, $temp_arr );
			}
			
			$ret_str = serialize($ret_arr);
			update_post_meta($post_id,'wd_slider_list',$ret_str);	
			
			$slider_config = array(
				'show_nav' 				=> $_POST['show_nav']
				,'scroll_per_page' 		=> $_POST['scroll_per_page']
				,'mouse_drag' 			=> $_POST['mouse_drag']
				,'touch_drag' 			=> $_POST['touch_drag']
				,'auto_play' 			=> $_POST['auto_play']
				,'auto_play_speed' 		=> $_POST['auto_play_speed']
				,'auto_play_timeout' 	=> $_POST['auto_play_timeout']
				,'auto_play_hover_pause'=> $_POST['auto_play_hover_pause']
				,'margin'				=> $_POST['margin']
			);
			
			$slider_config['responsive_option']['break_point'] = $_POST['responsive_option']['break_point'];
			$slider_config['responsive_option']['item'] = $_POST['responsive_option']['item'];
			
			$slider_config = serialize ($slider_config);
			update_post_meta($post_id,'wd_slider_config',$slider_config);
			if(!$slider_config)
				delete_post_meta($post_id,'wd_slider_config');
			
		
		}
	}	
	
	public function wd_slide_register() {
		 require_once WDS_TYPES."/slide.php";
	}	
	
	
	/******************************** Slide POST TYPE INIT END *************************************/
	
	public function wd_slide_template_redirect(){
		global $wp_query,$post,$page_datas,$smof_data;
		if( $wp_query->is_page() || $wp_query->is_single() ){
			if ( has_shortcode( $post->post_content, 'slideshow' ) ||  has_shortcode( $post->post_content, 'slider' )) { 
				add_action('wp_enqueue_scripts',array($this,'init_script'));
			}
		}
		
	}
	
	public function wd_slide_create_section() {
		if(post_type_exists('slide')) {
			add_meta_box("wp_cp_custom_carousels", "Insert Slider", array($this,"showcarousel"), "slide", "normal", "high");
		}
	}
	function wd_slide_page_setting(){
		add_submenu_page('edit.php?post_type=slide',__('Slider Setting','wpdance'),__('Settings','wpdance'),'manage_options','wd-slide-setting',array($this,'wd_slide_page_setting_content'));
	}
	function wd_slide_page_setting_content(){
		$options_default = array(
							'width' => 180
							,'height' => 188
							,'crop' => 1
						);
		$options = $new_options = get_option ('wd_slide_setting',$options_default);
		if(isset($_POST['wd_slide_save_setting'])){
			$new_options['width'] = $_POST['width'];
			$new_options['height'] = $_POST['height'];
			$new_options['crop'] = $_POST['crop'];
			if( $options != $new_options ){
				$options = $new_options;
				update_option('wd_slide_setting',$new_options);
			}
		}
		?>	
			<h2><?php _e('Slider Settings','wpdance'); ?></h2>
			<p class="description"><?php _e('You must regenerate thumbnails after changing','wpdance'); ?></p>
			<div id="wd_slider_page_setting_wrapper">
				<form method="post">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><label><?php _e('Image width','wpdance'); ?></label></th>
								<td>
									<input type="number" min="1" step="1" name="width" value="<?php echo $options['width']; ?>" />
									<p class="description"><?php _e('Input image width to show (In pixel)','wpdance'); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label><?php _e('Image height','wpdance'); ?></label></th>
								<td>
									<input type="number" min="1" step="1" name="height" value="<?php echo $options['height']; ?>" />
									<p class="description"><?php _e('Input image height to show (In pixel)','wpdance'); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label><?php _e('Crop','wpdance'); ?></label></th>
								<td>
									<select name="crop">
										<option value="1" <?php echo ($options['crop']==1)?'selected':''; ?>><?php _e("Yes",'wpdance'); ?></option>
										<option value="0" <?php echo ($options['crop']==0)?'selected':''; ?>><?php _e("No",'wpdance'); ?></option>
									</select>
									<p class="description"><?php _e('Select Yes to crop image when uploaded','wpdance'); ?></p>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="submit" name="wd_slide_save_setting" value="<?php _e('Save changes','wpdance'); ?>" class="button button-primary" />
				</form>
			</div>
		<?php
	}
	function register_image_size(){
		$options_default = array(
							'width' => 180
							,'height' => 188
							,'crop' => 1
						);
		$options = $new_options = get_option ('wd_slide_setting',$options_default);
		add_image_size('wd_slide',absint($options['width']),absint($options['height']),$options['crop'] == 1 );
	}

	public function showcarousel(){
		require_once WDS_INCLUDES.'/carousel.php';
	}
	
	public function wd_Slide_deactivate() {
		flush_rewrite_rules();
	}

	public function wd_Slide_activate() {
		$this->wd_slide_register();
		flush_rewrite_rules();
	}		
	
	public function rename_second_menu_name($safe_text, $text) {
		if (__('Slide Items', 'WD_slide_context') !== $text) {
			return $safe_text;
		}

		// We are on the main menu item now. The filter is not needed anymore.
		remove_filter('attribute_escape', array($this,'rename_second_menu_name') );

		return __('WD Slide', 'wd_slide_context');
	}
		
	protected function init_trigger(){
	
	}
	protected function init_handle(){
		//add_shortcode('wd-slide', array( $this,'wd_Slide') );
		require_once WDS_TEMPLATE . "/slide.php";
	}	
	
	public function init_admin_script() {
		if (function_exists('wp_enqueue_media')) {
			wp_register_script('admin_media_lib_35', WDS_JS . '/admin-media-lib-35.js', 'jquery', false,false);
			wp_enqueue_script('admin_media_lib_35');
		} else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_register_script('admin_media_lib', WDS_JS . '/admin-media-lib.js', 'jquery', false,false);
			wp_enqueue_script('admin_media_lib');
		}
		/// Start Fancy Box
		wp_register_style( 'fancybox_css', WDS_CSS.'/jquery.fancybox.css');
		wp_enqueue_style('fancybox_css');
		
		wp_register_script( 'fancybox_js', WDS_JS.'/jquery.fancybox.pack.js',false,false,true);
		wp_enqueue_script('fancybox_js');	
	}	
	
	
	public function init_script(){
		wp_enqueue_script('jquery');
		
		wp_register_style( 'bootstrap', WDS_CSS.'/bootstrap.css');
		wp_enqueue_style('bootstrap');
		
		wp_register_style( 'bootstrap-theme', WDS_CSS.'/bootstrap-theme.css');
		wp_enqueue_style('bootstrap-theme');
		
		wp_register_script( 'bootstrap', WDS_JS.'/bootstrap.js');
		wp_enqueue_script('bootstrap');
		
		wp_register_style( 'owl.carousel', WDS_CSS.'/owl.carousel.css');
		wp_enqueue_style('owl.carousel');
		
		wp_register_script( 'jquery.owlCarousel', WDS_JS.'/owl.carousel.min.js',false,false,true);
		wp_enqueue_script('jquery.owlCarousel');
				
		wp_register_style( 'wd.slide', WDS_CSS.'/wd_slide.css');		
		wp_enqueue_style('wd.slide');
	}
	
	protected function constant(){
		//define('DS',DIRECTORY_SEPARATOR);	
		define('WDS_BASE'		,  	plugins_url( '', __FILE__ )		);
		define('WDS_JS'			, 	WDS_BASE . '/js'		);
		define('WDS_CSS'		, 	WDS_BASE . '/css'		);
		define('WDS_IMAGE'		, 	WDS_BASE . '/images'	);
		define('WDS_TEMPLATE' 	, 	dirname(__FILE__) . '/templates'	);
		define('WDS_TYPES'	, 	plugin_dir_path( __FILE__ ) . 'post_type'		);
		define('WDS_INCLUDES'	, 	plugin_dir_path( __FILE__ ) . 'includes'		);
	}	
	
}
 
$_wd_Slide = new WD_Slide; // Start an instance of the plugin class 