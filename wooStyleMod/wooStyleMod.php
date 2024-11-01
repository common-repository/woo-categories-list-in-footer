<?php

/**
 * @package Woo Commerce Storefront Style Mod
 * @version 1.0
 */
/*
Plugin Name: Woo Commerce Storefront Style Mod
Description: This plugin mods the default style of Woo Commerce Storefront to look a bit more elegant.
Author: Eugene Trounev
Version: 1.0
Author URI: www.likalo.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wooStyleMod_path = plugin_dir_path(__FILE__);
$wooStyleMod_URL = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'.dirname( plugin_basename(__FILE__) );
$wooStyleMod_name = "woo Footer Products";
$wooStyleMod_alias = "wooStyleMod";
//--------------------------------

//Plugin part
if (!function_exists('wooStyleMod_assets')):
    function wooStyleMod_assets() {
            global $wooStyleMod_URL;
            wp_enqueue_style( 'RobotoRalewayFonts',  'https://fonts.googleapis.com/css?family=Roboto|Raleway' );
            wp_enqueue_style( 'YanoneFonts',  'https://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:700' );
            wp_enqueue_style( 'wooStyleMod_style',  $wooStyleMod_URL. '/assets/css/style.css' );
    }
endif;

if (!function_exists('wooStyleMod_storefront_homepage_after_on_sale_products')):
    function wooStyleMod_storefront_homepage_after_on_sale_products() {
        $specialsPage = get_page_by_path( 'specials' );
        if($specialsPage) {
            $link =  get_permalink( $specialsPage );
            if ($link){
                echo "<a class='section-footer' href='$link'>more...</a>";
            }
        }
    }
endif;

//Override theme function
if ( ! function_exists( 'storefront_on_sale_products' ) ) {
	/**
	 * Display On Sale Products
	 * Hooked into the `homepage` action in the homepage template
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_on_sale_products( $args ) {
		if ( is_woocommerce_activated() ) {
			$args = apply_filters( 'storefront_on_sale_products_args', array(
				'limit' 			=> 4,
				'columns' 			=> 4,
				'title'				=> __( 'On Sale', 'storefront' ),
				) );
			echo '<section class="storefront-product-section storefront-on-sale-products">';
				do_action( 'storefront_homepage_before_on_sale_products' );
				echo '<h2 class="section-title">' . wp_kses_post( $args['title'] ) . '</h2>';
				echo storefront_do_shortcode( 'sale_products',
					array(
						'per_page' 	=> intval( $args['limit'] ),
						'columns'	=> intval( $args['columns'] ),
						) );
				do_action( 'storefront_homepage_after_on_sale_products' );
				wooStyleMod_storefront_homepage_after_on_sale_products();
			echo '</section>';
		}
	}
}

add_action( 'wp_enqueue_scripts', 'wooStyleMod_assets', 50 );

?>