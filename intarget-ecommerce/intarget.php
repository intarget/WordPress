<?php
/*
Plugin Name: inTarget eCommerce
Plugin URI: https://intarget.ru/
Description: inTarget — система аналитики для интернет-магазина, с возможностью отслеживать продажи и анализировать конверсии в реальном времени.
Version: 1.0.4.
Author: inTarget Team
Author URI: https://intarget.ru/
*/

// Creating the widget

include 'intarget_options.php';

add_action('wp_enqueue_scripts', 'intarget_scripts_method');

add_filter('plugin_action_links', 'intarget_plugin_action_links', 10, 2);

add_action('wc_ajax_add_to_cart', 'intarget_ajax_add_to_cart');
add_action('woocommerce_restore_cart_item', 'intarget_ajax_add_to_cart');

function intarget_ajax_add_to_cart() {
    setcookie('intarget_add', true, time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

add_action('woocommerce_cart_item_removed', 'intarget_del_from_cart');

function intarget_del_from_cart() {
    setcookie('intarget_del', 'true', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
}

function intarget_plugin_action_links($actions, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $actions;
    $settings_link = '<a href="options-general.php?page=intarget_settings">Настройки</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('plugin_row_meta', 'intarget_plugin_description_links', 10, 4);

function intarget_plugin_description_links($meta, $plugin_file) {
    if (false === strpos($plugin_file, basename(__FILE__))) return $meta;
    $meta[] = '<a href="options-general.php?page=intarget_settings">Настройки</a>';
    return $meta;
}

add_filter('wc_add_to_cart_message', 'intarget_add_filter', 10, 4);

function intarget_add_filter($product_id) {
    add_action('wp_enqueue_scripts', 'intarget_scripts_add');
    return $product_id;
}

$options = get_option('intarget_option_name');

if (is_admin()) {
    $options = get_option('intarget_option_name');

    if (is_bool($options)) {
        intarget_set_default_code();
    }

    $reg_domain = 'https://intarget.ru';
    $url = get_site_url();

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_REQUEST['intarget_option_name']))) {
        $options = $_REQUEST['intarget_option_name'];
        if (($options['intarget_email'] !== '') && ($options['intarget_api_key'] !== '') && ($options['intarget_project_id'] == '')) {
            $reg_ans = regbyApi($reg_domain, $options['intarget_email'], $options['intarget_api_key'], $url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))) {
                    $intarget_options = get_option('intarget_option_name');
                    $intarget_options['intarget_project_id'] = $reg_ans->payload->projectId;
                    $intarget_options['intarget_reg_error'] = '';
                    $intarget_options['intarget_email'] = $options['intarget_email'];
                    $intarget_options['intarget_api_key'] = $options['intarget_api_key'];
                    update_option('intarget_option_name', $intarget_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                } elseif ($reg_ans->status = 'error') {
                    $intarget_options = get_option('intarget_option_name');
                    $intarget_options['intarget_reg_error'] = $reg_ans->code;
                    $intarget_options['intarget_project_id'] = '';
                    $intarget_options['intarget_email'] = $options['intarget_email'];
                    $intarget_options['intarget_api_key'] = $options['intarget_api_key'];
                    update_option('intarget_option_name', $intarget_options);
                    header("Location: " . get_site_url() . $_REQUEST['_wp_http_referer']);
                    die();
                }
            }
        }
    } else {
        $options = get_option('intarget_option_name');
        $my_settings_page = new intargetSettingsPage();
    }
}

function intarget_script_cookie() {
    if (isset($_COOKIE['intarget_add'])) {
        add_action('wp_enqueue_scripts', 'intarget_scripts_add');
        setcookie('intarget_add', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }

    if (isset($_COOKIE['intarget_del'])) {
        add_action('wp_enqueue_scripts', 'intarget_scripts_del');
        setcookie('intarget_del', '', time() + 3600 * 24 * 100, COOKIEPATH, COOKIE_DOMAIN, false);
    }
};

add_action('init', 'intarget_script_cookie');