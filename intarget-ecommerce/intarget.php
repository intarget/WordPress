<?php
/*
Plugin Name: inTarget eCommerce
Plugin URI: https://intarget.ru/
Description: inTarget — система аналитики для интернет-магазина, с возможностью отслеживать продажи и анализировать конверсии в реальном времени.
Version: 1.0.2
Author: inTarget Team
Author URI: https://intarget.ru/
*/

// Creating the widget

include 'intarget_options.php';

add_action('wp_enqueue_scripts', 'intarget_scripts_method');
//register_activation_hook(__FILE__, 'intarget_admin_actions');
//add_action('widgets_init', 'intarget_register_widgets');
//add_action('admin_menu', 'intarget_admin_actions');
add_filter('plugin_action_links', 'intarget_plugin_action_links', 10, 2);

function intarget_plugin_action_links($actions, $plugin_file)
{
    if (false === strpos($plugin_file, basename(__FILE__)))
        return $actions;
    $settings_link = '<a href="options-general.php?page=intarget_settings">Настройки</a>';
    array_unshift($actions, $settings_link);
    return $actions;
}

add_filter('plugin_row_meta', 'intarget_plugin_description_links', 10, 4);

function intarget_plugin_description_links($meta, $plugin_file)
{
    if (false === strpos($plugin_file, basename(__FILE__)))
        return $meta;
    $meta[] = '<a href="options-general.php?page=intarget_settings">Настройки</a>';
    return $meta;
}

$options = get_option('intarget_option_name');

if (is_admin()) {
    $options = get_option('intarget_option_name');

    if (is_bool($options)) {
        intarget_set_default_code();
    }

    $reg_domain = 'https://intarget.ru';//intarget-dev.lembrd.com
    $url = get_site_url();

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_REQUEST['intarget_option_name']))) {
        $options = $_REQUEST['intarget_option_name'];
        if (($options['intarget_email'] !== '') &&
            ($options['intarget_api_key'] !== '') &&
            ($options['intarget_project_id'] == '')
        ) {
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

