<?php
/**
 * Plugin Name: Shopwice Sales & Order Notifier
 * Plugin URI: https://shopwice.com
 * Description: Displays live sales notifications and order status updates (Processing, Pending, On Hold, Completed).
 * Version: 1.2
 * Author: Shopwice
 * Author URI: https://shopwice.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// AJAX function to get recent sales & order notifications
function shopwice_get_recent_sales_and_orders() {
    if (!function_exists('wc_get_orders')) {
        return;
    }

    $args = array(
        'limit'    => 5, // Show last 5 orders
        'orderby'  => 'date',
        'order'    => 'DESC',
        'status'   => array('processing', 'pending', 'on-hold', 'completed')
    );

    $orders = wc_get_orders($args);
    $notifications = [];

    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $billing_address = $order->get_billing_city() ?: 'an unknown location'; // Get city or default text
        $items = $order->get_items();
        if (empty($items)) continue;
        
        $first_item = reset($items);
        $product_id = $first_item->get_product_id();
        $product_title = get_the_title($product_id);
        $product_image = get_the_post_thumbnail_url($product_id, 'thumbnail') ?: wc_placeholder_img_src();
        $product_url = get_permalink($product_id); // Get product page URL

        $notifications[] = array(
            'type'    => 'order',
            'message' => "🛒 Someone from $billing_address purchased <strong>$product_title</strong>",
            'image'   => $product_image,
            'url'     => $product_url // Include product page URL
        );
    }

    wp_send_json(array_slice($notifications, 0, 5)); // Limit to 5 notifications
}

add_action('wp_ajax_nopriv_shopwice_get_recent_sales_and_orders', 'shopwice_get_recent_sales_and_orders');
add_action('wp_ajax_shopwice_get_recent_sales_and_orders', 'shopwice_get_recent_sales_and_orders');
