<?php

/**
 * @package Woo Categories List in Footer
 * @version 1.0
 */
/*
Plugin Name: Woo Categories List in Footer
Description: This plugin adds a list of products by category to the footer of a page using the storefront theme.
Author: frametagmedia
Version: 1.0
Author URI: https://frametagmedia.com.au
*/

/* SETTINGS

Display Sitemap: true/false (def: true)
Sitemap Title: string (def: Sitemap)
Show Pages First: true/false (def: false)
Columns: [One, Two, Three, Four] (def: Four)

*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once("assets/lib/AdminSection.php");
require_once("assets/lib/PluginOptionsPage.php");
require_once("assets/php/customWalker.php");

$wooFooterProducts_path = plugin_dir_path(__FILE__);
$wooFooterProducts_URL = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'.dirname( plugin_basename(__FILE__) );
$wooFooterProducts_name = "woo Footer Products";
$wooFooterProducts_alias = "wooFooterProducts";
$wooFooterProducts_options = array(
    array(
		"type" => "section",
		"name" => "Plugin Settings",
		),
    array(
		"type" => "open",
		),
    array(
		"type" => "checkbox",
		"name" => "Enable sitemap",
		"desc" => "If enabled product sitemap will show right abowe footer widgets (if any).",
		"id" => $wooFooterProducts_alias."_enabled",
		"std" => 1
		),
    array(
		"type" => "text",
		"name" => "Sitemap title",
		"desc" => "Title to show in the beginning of sitemap listing.",
		"id" => $wooFooterProducts_alias."_title",
		"std" => ""
		),
    array(
		"type" => "close",
		),
	array(
		"type" => "section",
		"name" => "Plugin Presentation",
		),
    array(
		"type" => "checkbox",
		"name" => "List pages",
		"desc" => "Enable or disable showing of plages as a part of sitemap.",
		"id" => $wooFooterProducts_alias."_pages",
		"std" => 0
		),
    array(
		"type" => "text",
		"name" => "Pages title",
		"desc" => "Title to show in the beginning of pages listing.",
		"id" => $wooFooterProducts_alias."_pagesTitle",
		"std" => ""
		),
    array(
		"type" => "select",
		"name" => "Number of columns",
		"desc" => "How many colums.",
		"id" => $wooFooterProducts_alias."_columns",
		"options" => array(
 array("name" => "One column", "value" => 1),
 array("name" => "Two columns", "value" => 2),
 array("name" => "Three columns", "value" => 3),
 array("name" => "Four columns", "value" => 4),
),
		"std" => 4
		),
    array(
		"type" => "close",
		),
);

//--------------------------------
//private:

$wooFooterProductsOptionsPage = new PluginOptionsPage(AdminSection::SETTINGS, $wooFooterProducts_name, $wooFooterProducts_alias, $wooFooterProducts_options);
register_activation_hook(__FILE__, array($wooFooterProductsOptionsPage, 'install'));
register_deactivation_hook(__FILE__, array($wooFooterProductsOptionsPage, 'uninstall'));
$wooFooterProductsOptionsPage->menu("Footer Products");

//Plugin part

if (!function_exists('wooFooterProducts_storefront_footer_get_products')):
function wooFooterProducts_storefront_footer_get_products() {
	global $wp_query, $post;
  	global $wooFooterProducts_name, $wooFooterProducts_alias, $wooFooterProducts_options;

	$list_args = array();
	$list_args['taxonomy'] = 'product_cat';
	$list_args['hide_empty'] = true ;
	$list_args['show_count'] = 0;

	// Menu Order
	//$list_args['menu_order'] = false;
	$list_args['menu_order'] = 'asc';
	$list_args['orderby']    = 'term_order';

	$list_args['depth']            = 1;
	$list_args['child_of']         = 0;
	$list_args['hierarchical']     = 0;
	$list_args['title_li']          = '';
	$list_args['pad_counts']        = 1;
	$list_args['show_option_none']  = __('No product categories exist.', 'woocommerce' );
	$list_args['current_category']  = false;
	$list_args['current_category_ancestors'] = array();

	$args = array(
	  'orderby' => 'term_order',
	  'parent' => 0,
		'taxonomy' => 'product_cat'
	  );
	$categories = get_categories( $args );
	$totalCats = count ($categories);

	$list_args['depth']            = 2;
	$list_args['hierarchical']     = 1;

	$items = get_categories( $list_args );
	$totalItems = count ($items) - $totalCats;

	$sitemap_title = get_option($wooFooterProducts_alias."_title");
	$showPagesFirst = get_option($wooFooterProducts_alias."_pages");
	$colimns = get_option($wooFooterProducts_alias."_columns");

	$maxPerColum = floor ( $totalItems / $colimns );

	$list_args['walker'] = new Footer_Product_Cat_List_Walker($maxPerColum, $totalCats, $colimns);

	?>
		<section class=""><h2 class="section-title"><?php echo $sitemap_title; ?></h2><div class="woocommerce columns-4">
			<ul class="product-categories columns">
	<?php
		if ($showPagesFirst) {
			$page_args = array();
			$page_args['post_type'] = 'page';
			$page_args['hide_empty'] = true ;
			$page_args['show_count'] = 0;
			// Menu Order
			$page_args['depth']            = 1;
			$page_args['child_of']         = 0;
			$page_args['hierarchical']     = 0;
			$page_args['title_li']          = '';

			$pages_title = get_option($wooFooterProducts_alias."_pagesTitle");

			echo "<li class='column'><strong>$pages_title</strong><ul>";
				if ( has_nav_menu( 'woo_prod_footer_nav' ) ) {
					wp_nav_menu(array(
						'theme_location'  => 'woo_prod_footer_nav',
						'container'       => false,
						'container_class' => false,
						'menu_class'      => 'pure-menu-list',
					));
				}else{
					wp_list_pages( $page_args );
				}
			echo "</ul></li>";
		}
		wp_list_categories( apply_filters( 'woocommerce_product_categories_widget_args', $list_args ) );
	?>
			</ul>
		</div></section>
	<?php
}
endif;

if (!function_exists('wooFooterProducts_storefront_footer_widgets')):
    function wooFooterProducts_storefront_footer_widgets() {

            global $wooFooterProducts_name, $wooFooterProducts_alias;
            $plugin_enabled = get_option($wooFooterProducts_alias."_enabled");

            if($plugin_enabled)
                    wooFooterProducts_storefront_footer_get_products();
    }
endif;

if (!function_exists('wooFooterProducts_assets')):
    function wooFooterProducts_assets() {
            global $wooFooterProducts_URL;
            wp_enqueue_style( 'wooFooterProducts_style',  $wooFooterProducts_URL. '/assets/css/footerProductsStyle.css' );
    }
endif;

remove_action('storefront_footer', 'storefront_footer_widgets', 10);
add_action( 'storefront_footer', 'wooFooterProducts_storefront_footer_widgets', 10  );
add_action( 'wp_enqueue_scripts', 'wooFooterProducts_assets' );

if ( function_exists( 'register_nav_menu' ) ) {
	register_nav_menus( array(
		'woo_prod_footer_nav'  => __( 'Woo Footer Products Navigation Menu' ),
	) );
}

?>
