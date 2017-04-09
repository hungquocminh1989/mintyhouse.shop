<?php
/*
Plugin Name: WD ShortCode
Plugin URI: http://www.wpdance.com/
Description: ShortCode From WPDance Team
Author: Wpdance
Version: 1.2.1
Author URI: http://www.wpdance.com/
*/
class WD_Shortcode
{
	protected $arrShortcodes = array();
	public function __construct(){
		$this->constant();
		//$this->init_script();
		add_action('wp_enqueue_scripts',array($this,'init_script'));
		add_action( 'init', array($this,'wd_add_shortcode_button' ));
		add_action( 'wp_footer', array($this,'wd_define_ajax_uri' ));
		$this->initArrShortcodes();
		$this->initShortcodes();
		
		add_filter( 'no_texturize_shortcodes', array($this, 'no_texturize_shortcode') );
		
		add_action('admin_head', array($this, 'add_google_tracking'), 999);
	}
	public function wd_add_shortcode_button() {
		//if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
		//if ( get_user_option('rich_editing') == 'true') :
			add_filter('mce_external_plugins', array($this,'wd_add_shortcode_tinymce_plugin'));
			add_filter('mce_buttons', array($this,'wd_register_shortcode_button'));
		//endif;
	}
	function wd_define_ajax_uri(){
	?>
		<script type="text/javascript">
			var _sc_ajax_uri = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
	<?php
	}
	public function wd_add_shortcode_tinymce_plugin($plugin_array) {
		global $woocommerce,$wp_version;
		$plugin_array['Wd_shortcodes'] = SC_JS.'/wd_editor_plugin.js';
		return $plugin_array;
	}
	public function wd_register_shortcode_button($buttons) {
		array_push($buttons, "|", "wd_shortcodes_button");
		return $buttons;
	}
	protected function initArrShortcodes(){
		$this->arrShortcodes = array('banner','banner_description','accordion','code','custom_query','embbed_video','image_video','faq'
		,'lightbox','list_post','listing','quote','sidebar','google_map','style_box','symbol','table','tabs','child_categories'
		,'recent_post','recent_post_sticky','typography','column_article','bt_buttons','portfolio','bt_accordion','bt_labels'
		,'bt_badges','bt_multitab','bt_tooltips','bt_carousel','wd_features','wd_testimonial','woo-shortcode','pricing_table','milestone');
	}
	
	protected function initShortcodes(){
		foreach($this->arrShortcodes as $shortcode){
			//echo SC_ShortCode."{$shortcode}.php <br/>";
			if(file_exists(SC_ShortCode."/{$shortcode}.php")){
				require_once SC_ShortCode."/{$shortcode}.php";
			}	
		}
	}
	
	function no_texturize_shortcode( $list ){
		$list[] = 'tabs';
		$list[] = 'accordions';
		return $list;
	}
	
	public function init_script(){
		wp_enqueue_script('jquery');
		
		wp_register_style( 'shortcode', SC_CSS.'/shortcode.css');
		wp_enqueue_style('shortcode');
		wp_register_script( 'wd_shortcode', SC_JS.'/wd_shortcode.js',false,false,true);
		wp_enqueue_script('wd_shortcode');
		
		wp_register_style( 'bootstrap-theme.css', SC_CSS.'/bootstrap-theme.css');
		wp_enqueue_style('bootstrap-theme.css');	
		
		wp_register_style( 'bootstrap', SC_CSS.'/bootstrap.css');
		wp_enqueue_style('bootstrap');		
		
		wp_register_script( 'bootstrap', SC_JS.'/bootstrap.js');
		wp_enqueue_script('bootstrap');
		
		wp_register_style( 'font-awesome', SC_CSS.'/font-awesome.css');
		wp_enqueue_style('font-awesome');
		
		/* SLIDER CAROUSEL */
		wp_register_style( 'owl.carousel', SC_CSS.'/owl.carousel.css');
		wp_enqueue_style('owl.carousel');
		
		wp_register_script( 'jquery.owlCarousel', SC_JS.'/owl.carousel.min.js',false,false,true);
		wp_enqueue_script('jquery.owlCarousel');
		
		wp_register_script( 'jquery.imagesloaded', SC_JS.'/jquery.imagesloaded.min.js',false,false,true);
		
		wp_enqueue_script('jquery.imagesloaded');
		
		/* END SLIDER CAROUSEL */
	}
	
	protected function constant(){
		//define('DS',DIRECTORY_SEPARATOR);	
		define('SC_BASE'	,  	plugins_url( '', __FILE__ ));
		define('SC_ShortCode'	, 	plugin_dir_path( __FILE__ ) . '/shortcode'	);
		define('SC_JS'		, 	SC_BASE . '/js'			);
		define('SC_CSS'		, 	SC_BASE . '/css'		);
		define('SC_IMAGE'	, 	SC_BASE . '/images'		);
	}

	function add_google_tracking(){
		?>
		<script>

		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){

		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),

		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)

		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');



		  ga('create', 'UA-55571446-5', 'auto');

		  ga('require', 'displayfeatures');

		  ga('send', 'pageview');



		</script>
		<?php
	}
	
		
}	

$_wd_shortcode = new WD_Shortcode; // Start an instance of the plugin class
?>