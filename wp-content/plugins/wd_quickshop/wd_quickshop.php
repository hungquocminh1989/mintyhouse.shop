<?php
/*
Plugin Name: WD QuickShop
Plugin URI: http://www.wpdance.com/
Description: QuickShop From WPDance Team
Author: Wpdance
Version: 1.0.1
Author URI: http://www.wpdance.com/
*/

$_actived = apply_filters( 'active_plugins', get_option( 'active_plugins' )  );
if ( !in_array( "woocommerce/woocommerce.php", $_actived ) ) {
	return;
}

class WD_Quickshop 
{

	/** @var int The product (post) ID. */
	public $id;

	public function __construct(){
		$this->constant();
		add_action('wp_enqueue_scripts',array($this,'init_script'), 100 );
		//$this->init_script();
		$this->init_trigger();
		$this->init_handle();
		
		add_action( 'plugins_loaded', array($this, 'qs_plugin_load_textdomain') );
	}
	
	function qs_plugin_load_textdomain(){
		load_plugin_textdomain( 'wpdance', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	public function add_quickshop_button(){
	
		global $smof_data;
		$btn_label = __("QUICKSHOP","wpdance");
		$btn_img = "";
		if( isset($smof_data) && isset( $smof_data['wd_qs_button_label'] ) && strlen( trim($smof_data['wd_qs_button_label']) ) > 0 ){
			$btn_label = esc_attr($smof_data['wd_qs_button_label']);
		}
		if( isset($smof_data) && isset( $smof_data['wd_qs_button_imgage'] ) && strlen( trim($smof_data['wd_qs_button_imgage']) ) > 0 ){
			$btn_img = "<img class='em_quickshop_handler_img' src='" .esc_url($smof_data['wd_qs_button_imgage']) . "' title='{$btn_label}' alt=''>";
		}	
	
?>

		<a class="visible-desktop em_quickshop_handler" href="#" style="position:absolute;z-index:999;top:50px;left:-100px">
			<span class="qs_inner1">
				<span class="qs_inner2 <?php echo $qs_class = strlen(trim($btn_img)) > 0 ? "qs_img_btn" : "qs_text_btn" ;?>"> 
					<?php 
						if( strlen(trim($btn_img)) > 0 ){
							echo $btn_img;
						}else{
							echo $btn_label;
						}
					?>
				</span>
			</span>
		</a>		
<?php	
	}
	
	function add_quickshop_inline_script(){
	?>
		<script type="text/javascript">
			var _qs_ajax_uri = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
	<?php
	}	
	
	public function quickshop_init_product_id(){
		global $post, $product, $woocommerce;
		echo "<input type='hidden' value='{$product->id}' class='hidden_product_id product_hidden_{$product->id}'>";
	}
	
	public function wd_qs_template_single_sku(){
		global $product, $post;
		echo "<p class='wd_product_sku'>SKU: <span class=\"product_sku\">" . esc_attr($product->get_sku()) . "</span></p>";
	}	
	
	protected function init_trigger(){
		add_action('woocommerce_after_shop_loop_item', array( $this, 'quickshop_init_product_id'), 100000000000 );		
		add_action('woocommerce_after_shop_loop_item_title', array($this,'add_quickshop_button'), 99999999 );
		
		/** Build wd_quickshop_single_product_summary **/
		add_action( 'wd_quickshop_single_product_title', 'woocommerce_template_single_title', 1 );
		add_action( 'wd_quickshop_single_product_summary', array($this,'wd_quickshop_product_availability'), 6 );
		add_action( 'wd_quickshop_single_product_summary', array($this,'wd_qs_template_single_sku'), 7 );
		add_action( 'wd_quickshop_single_product_summary', 'wd_template_single_review', 8 );
		add_action( 'wd_qs_before_product_image', 'woocommerce_show_product_sale_flash', 10 );
		
		
		add_action( 'wd_quickshop_single_product_summary', 'woocommerce_template_single_excerpt', 10 );
		//add_action( 'wd_quickshop_single_product_summary', 'woocommerce_template_single_price', 20 );
		//add_action( 'wd_quickshop_single_product_summary', 'woocommerce_template_single_meta', 40 );
		add_action( 'wd_quickshop_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 ); 

		add_action( 'wp_footer', array($this,'add_quickshop_inline_script'), 9999);
		
 		
		
	}
	
	public function update_qs_add_to_cart_url(  $cart_url ){
		$ref_url = wp_get_referer();
		$ref_url = esc_url( remove_query_arg( array('added-to-cart','add-to-cart') , $ref_url ) );
		$ref_url = esc_url( add_query_arg( array( 'add-to-cart' => $this->id ),$ref_url ) );
		return $ref_url;
	}
	

	public function wd_quickshop_product_availability(){
		global $product;
		$_product_stock = get_product_availability($product);
		?>	
			<p class="availability stock <?php echo esc_attr($_product_stock['class']);?>"><?php _e('Availability','wpdance');?>: <span><?php echo esc_attr($_product_stock['availability']);?></span></p>	
		<?php
	}	
	
	
	public function load_product_content_callback(){
		global $smof_data;
		if( isset($smof_data['wd_qs_product_title']) && $smof_data['wd_qs_product_title'] == 0 )
			remove_action( 'wd_quickshop_single_product_title', 'woocommerce_template_single_title', 1 );
		if( isset($smof_data['wd_qs_product_label']) && $smof_data['wd_qs_product_label'] == 0 )
			remove_action( 'wd_qs_before_product_image', 'woocommerce_show_product_sale_flash', 10 );
		if( isset($smof_data['wd_qs_product_availability']) && $smof_data['wd_qs_product_availability'] == 0 )
			remove_action( 'wd_quickshop_single_product_summary', array($this,'wd_quickshop_product_availability'), 6 );
		if( isset($smof_data['wd_qs_product_sku']) && $smof_data['wd_qs_product_sku'] == 0 )
			remove_action( 'wd_quickshop_single_product_summary', array($this,'wd_qs_template_single_sku'), 7 );
		if( isset($smof_data['wd_qs_product_rating']) && $smof_data['wd_qs_product_rating'] == 0 )
			remove_action( 'wd_quickshop_single_product_summary', 'wd_template_single_review', 8 );
		if( isset($smof_data['wd_qs_product_short_description']) && $smof_data['wd_qs_product_short_description'] == 0 )
			remove_action( 'wd_quickshop_single_product_summary', 'woocommerce_template_single_excerpt', 10 );
		if( (isset($smof_data['wd_qs_product_add_to_cart']) && $smof_data['wd_qs_product_add_to_cart'] == 0) 
			|| (isset($smof_data['wd_enable_catalog_mod']) && $smof_data['wd_enable_catalog_mod'] == 1) ){
			remove_action( 'wd_quickshop_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
			
			
		$prod_id = absint($_GET['product_id']);
		
		$this->id = $prod_id;
		
		global $post, $product, $woocommerce;
		$post = get_post( $prod_id );
		$product = get_product( $prod_id );

		if( $prod_id <= 0 ){
			die('Invalid Products');
		}
		if( !isset($post->post_type) || strcmp($post->post_type,'product') != 0 ){
			die('Invalid Products');
		}
		
		add_filter('woocommerce_add_to_cart_url', array($this, 'update_qs_add_to_cart_url'),10);
		
		$product_type = $product->product_type;
		
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 1000 );
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_template_single_sharing', 50 );
		$_wrapper_class = "wd_quickshop product type-{$product_type}";
		ob_start();	
?>		
	
		<div itemscope itemtype="http://schema.org/Product" id="product-<?php echo get_the_ID();?>" <?php post_class( apply_filters('single_product_wrapper_class',$_wrapper_class  ) ); ?>>
				
				<div class="images">
				
				<?php do_action( 'wd_qs_before_product_image' ); ?>	
				
				<?php			
					if ( has_post_thumbnail() ) :
					
						$image_title 		= esc_attr( $product->get_title() );
						$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ),array( 'alt' => $image_title, 'title' => $image_title, 'srcset' => ' ' ) );
						$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
						$attachment_count   = count( $product->get_gallery_attachment_ids() );					
					
						echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image cloud-zoom wd_qs_main_image zoom" title="%s"  id=\'qs-zoom\' rel="position:\'inside\',showTitle:1,titleOpacity:0.5,lensOpacity:0.5,fixWidth:362,fixThumbWidth:72,fixThumbHeight:72,adjustX: 0, adjustY:0">%s</a>', $image_link, $image_title, $image ), $post->ID );
	
					else :
						echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" class="attachment-shop_single wp-post-image wd_qs_main_image zoom" />';
					endif;
					
					$attachment_ids = $product->get_gallery_attachment_ids();
					
					if ( $attachment_ids ) {
						?>
						
						<div class="thumbnails list_carousel" id="wd_quickshop_wrapper">
					
							<div class="qs-thumbnails">
							
								<?php
								
									if(has_post_thumbnail()) {
										array_unshift($attachment_ids, get_post_thumbnail_id($post->ID));
									}
							
									$loop = 0;
									$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
							
									foreach ( $attachment_ids as $attachment_id ) {
										
										$wrapClasses = array('quickshop-thumb-'.$columns.'col', 'wd_quickshop_thumb','pop_cloud_zoom cloud-zoom-gallery');
							
										$classes = array('attachment-shop_thumbnail');
							
										if ( $loop == 0 || $loop % $columns == 0 )
											$wrapClasses[] = 'first';
											
										if( $loop == 0 ) {
											$wrapClasses[] = 'firstThumb';
										}
							
										if ( ( $loop + 1 ) % $columns == 0 )
											$wrapClasses[] = 'last';
										
										$image_class = esc_attr( implode( ' ', $classes ) );
										
										$lrgImg = wp_get_attachment_image_src($attachment_id, 'shop_single');
										$lrgImg_full = wp_get_attachment_image_src($attachment_id, 'full');
																			
										echo '<div><a href="'.$lrgImg_full[0].'"rel="useZoom: \'qs-zoom\', smallImage: \''. $lrgImg[0] .'\'"  class="'.esc_attr( implode( ' ', $wrapClasses ) ).'">'.wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), false, array('class' => $image_class) ).'</a></div>';
							
										$loop++;
									}
							
								?>
								</div>
								<div class="slider_control">
									<a id="qs_thumbnails_prev" title="<?php _e('Previous','wpdance');?>" class="prev" href="#">&lt;</a>
									<a id="qs_thumbnails_next" title="<?php _e('Next','wpdance');?>" class="next" href="#">&gt;</a>
								</div>	
						</div>
						<?php
					}
				?>
				
					<div class="details_view">
						<a href="<?php echo the_permalink();?>" title="<?php _e('View Details','wpdance');?>" ><?php _e('View Details','wpdance');?></a>
					</div>
					
				</div>
		
				<div class="summary entry-summary">
					<?php do_action('wd_quickshop_single_product_title'); ?>
					<?php do_action( 'wd_quickshop_single_product_summary' ) ?>
		
				</div><!-- .summary -->
			
			</div><!-- #product-<?php echo get_the_ID();?> -->	
			
<?php
		
		remove_filter( 'woocommerce_add_to_cart_url', array($this, 'update_qs_add_to_cart_url') );

		$_ret_html = ob_get_contents();
		ob_end_clean();
		wp_reset_query();
		die($_ret_html);
	}
	
	protected function init_handle(){
		add_action('wp_ajax_load_product_content', array( $this, 'load_product_content_callback') );
		add_action('wp_ajax_nopriv_load_product_content', array( $this, 'load_product_content_callback') );		
	}	
	
	public function init_script(){
		wp_enqueue_script('jquery');
		wp_register_script( 'TweenMax', QS_JS.'/TweenMax.min.js');
		wp_enqueue_script('TweenMax');		
		
		wp_register_script( 'jquery.prettyPhoto', QS_JS.'/jquery.prettyPhoto.min.js',array('jquery','TweenMax'),false,true );
		wp_enqueue_script('jquery.prettyPhoto');	
		
		wp_register_script( 'cart-variation', QS_JS.'/add-to-cart-variation.min.js',false,false,true);
		wp_enqueue_script('cart-variation');	
		
		wp_register_script( 'quickshop-js', QS_JS.'/quickshop.js',false,false,true);
		wp_enqueue_script('quickshop-js');			
		
		wp_register_style( 'css.prettyPhoto', QS_CSS.'/prettyPhoto.css');
		wp_enqueue_style('css.prettyPhoto');	
		
		wp_register_style( 'owl.carousel', QS_CSS.'/owl.carousel.css');
		wp_enqueue_style('owl.carousel');
		
		wp_register_script( 'jquery.cloud-zoom', QS_JS.'/cloud-zoom.1.0.2.js',false,false,true );
		wp_enqueue_script('jquery.cloud-zoom');		
		wp_register_style( 'cloud-zoom-css', QS_CSS.'/cloud-zoom.css');
		wp_enqueue_style('cloud-zoom-css');					

		wp_register_script( 'jquery.owlCarousel', QS_JS.'/owl.carousel.min.js',false,false,true);
		wp_enqueue_script('jquery.owlCarousel');
		
		if( is_product() ){
			wp_dequeue_script( 'jquery.prettyPhoto' );
			//wp_dequeue_script( 'prettyPhoto-init' );
			//wp_dequeue_style( 'woocommerce_prettyPhoto_css' );			
		}
	
		
		
	}
	
	protected function constant(){
		//define('DS',DIRECTORY_SEPARATOR);	
		define('QS_BASE'	,  	plugins_url( '', __FILE__ )			);
		define('QS_JS'		, 	QS_BASE . '/js'			);
		define('QS_CSS'		, 	QS_BASE . '/css'		);
		define('QS_IMAGE'	, 	QS_BASE . '/images'		);
	}	
}	

$_wd_quickshop = new WD_Quickshop; // Start an instance of the plugin class
?>