<?php
/**
 * Checkout login form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_user_logged_in()  || ! $checkout->enable_signup ) return;

$info_message  = apply_filters( 'woocommerce_checkout_login_message', __( 'Returning customer?', 'wpdance' ) );
$info_message .= ' <a href="#" class="showlogin">' . __( 'Click here to login', 'wpdance' ) . '</a>';
//wc_print_notice( $info_message, 'notice' );
?>

<p class="woocommerce-info"><a href="#" class="showlogin"><?php _e( 'Click here to login', 'wpdance' ); ?></a></p>

<?php
	woocommerce_login_form(
		array(
			'message'  => __( 'If you have shopped with us before, please enter your details in the boxes below.If you are a new customer please proceed to the Billing &amp; Shipping section.', 'wpdance' ),
			'redirect' => get_permalink( wc_get_page_id( 'checkout') ),
			'hidden'   => false
		)
	);
?>