<?php

class intargetSettingsPage
{

    public $options;
    public $settings_page_name = 'intarget_settings';
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        $this->options = get_option('intarget_option_name');
    }


    public function add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            'intarget',
            'manage_options',
            $this->settings_page_name,
            array($this, 'create_admin_page')
        );
    }


    public function create_admin_page()
    {
        $this->options = get_option('intarget_option_name');

        if ((isset($this->options['intarget_email'])) && ('' !== $this->options['intarget_email'])) {
            $email = $this->options['intarget_email'];
        } else $email = get_option('admin_email');


        ?>
        <script type="text/javascript">
            <?php include('main.js'); ?>
        </script>
        <style type="text/css">
            <?php include('intarget_style.css')?>
        </style>
        <div id="intarget_site_url" style="display: none"><?php echo get_site_url();?></div>
        <div class="wrap">

            <div id="wrapper">
                <form id="settings_form" method="post" action="options.php">
                    <H1>Плагин inTarget eCommerce</H1>

                    <?php
                    echo_before_text();
                    settings_fields('intarget_option_group');
                    do_settings_sections('intarget_settings');
                    ?>

                    <input type="submit" name="submit_btn" value="Cохранить изменения">

                </form>
            </div>
        </div>
    <?php
    }

    public function page_init()
    {
        register_setting(
            'intarget_option_group',
            'intarget_option_name',
            array($this, 'sanitize')
        );

        add_settings_section(
            'setting_section_id',
            '', // Title
            array($this, 'print_section_info'),
            $this->settings_page_name
        );

        add_settings_field(
            'intarget_api_key',
            'Ключ API',
            array($this, 'intarget_api_key_callback'),
            $this->settings_page_name,
            'setting_section_id'
        );

        add_settings_field(
            'email',
            'Email',
            array($this, 'intarget_email_callback'),
            $this->settings_page_name,
            'setting_section_id'
        );


        add_settings_field(
            'intarget_reg_error',
            'intarget_reg_error',
            array($this, 'intarget_reg_error_callback'),
            $this->settings_page_name,
            'setting_section_id'
        );

        add_settings_field(
            'intarget_project_id',
            'intarget_project_id',
            array($this, 'intarget_project_id_callback'),
            $this->settings_page_name,
            'setting_section_id'
        );


    }


    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['intarget_email']))
            $new_input['intarget_email'] = $input['intarget_email'];

        if (isset($input['intarget_project_id']))
            $new_input['intarget_project_id'] = $input['intarget_project_id'];


        if (isset($input['intarget_api_key']))
            $new_input['intarget_api_key'] = $input['intarget_api_key'];

        if (isset($input['intarget_reg_error']))
            $new_input['intarget_reg_error'] = $input['intarget_reg_error'];



        return $new_input;
    }

    public function print_section_info()
    {

    }

    public function intarget_email_callback()
    {
        printf(
            '<input type="text" id="intarget_email" name="intarget_option_name[intarget_email]" value="%s" title="Введите в данном поле email, указанный при регистрации на сайте http://intarget.ru"/>',
            isset($this->options['intarget_email']) ? esc_attr($this->options['intarget_email']) : ''
        );
    }
    public function intarget_api_key_callback()
    {
        printf(
            '<input type="text" id="intarget_api_key" name="intarget_option_name[intarget_api_key]" value="%s" title="Введите в данном поле ключ API, полученный на сайте http://intarget.ru" />',
            isset($this->options['intarget_api_key']) ? esc_attr($this->options['intarget_api_key']) : ''
        );
    }
    public function intarget_reg_error_callback()
    {
        printf(
            '<input type="text" id="intarget_reg_error" name="intarget_option_name[intarget_reg_error]" value="%s" />',
            isset($this->options['intarget_reg_error']) ? esc_attr($this->options['intarget_reg_error']) : ''
        );
    }
    public function intarget_project_id_callback()
    {
        printf(
            '<input type="text" id="intarget_project_id" name="intarget_option_name[intarget_project_id]" value="%s" />',
            isset($this->options['intarget_project_id']) ? esc_attr($this->options['intarget_project_id']) : ''
        );
    }

}





function echo_before_text(){
    echo '
<div id="before_install" style="display:none;">
Плагин inTarget успешно установлен!

Для начала работы плагина необходимо ввести ключ API, полученный в личном кабинете на сайте <a href="http://intarget.ru">inTarget.ru</a>
</div>
<div class="wrap" id="after_install" style="display:none;">

<p><b>inTarget</b> — сервис повышения продаж и аналитика посетителей сайта.</p>
<p>Оцените принципиально новый подход к просмотру статистики. Общайтесь со своей аудиторией, продавайте лучше, зарабатывайте больше. И все это бесплатно!</p>

    </div>
</div>
<script type="text/javascript">
    window.onload = function ()
    {
        if (document.location.search == "?option=com_installer&view=install") {
            document.getElementById("before_install").style.display = "block"
        } else document.getElementById("after_install").style.display = "block"
    }
</script>
';
}


function regbyApi($regDomain,$email,$key,$url){
    $domain = $regDomain;
    if (($domain == '') OR ($email == '') OR ($key == '') OR ($url == '') ){
        return;
    }
    $ch = curl_init();
    $jsondata = json_encode(array(
        'email' => $email,
        'key' => $key,
        'url' => $url,
        'cms' => 'wordpress'));

    $options = array(CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept: application/json'),
        CURLOPT_URL => $domain."/api/registration.json",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $jsondata,
        CURLOPT_RETURNTRANSFER => true,
    );

    curl_setopt_array($ch, $options);
    $json_result = json_decode(curl_exec($ch));
    if (isset($json_result->status)) {
        if (($json_result->status == 'OK') && (isset($json_result->payload))){
            return $json_result;
        } elseif ($json_result->status = 'error') {
            return $json_result;
        }
    }
    curl_close ($ch);

}

function intarget_admin_actions()
{

    if ( current_user_can('manage_options') ) {
        if (function_exists('add_meta_box')) {

            add_menu_page("intarget", "intarget", "manage_options", "intarget", 'intarget_custom_menu_page',  plugins_url('intarget-ecommerce/logo-small.png'));
        }


    }
}

function intarget_custom_menu_page(){
    include_once( 'intarget-admin.php' );
}


class intargetWidget extends WP_Widget {

    function intargetWidget() {
        parent::__construct( false, 'Блок кнопок intarget' );
    }

    function widget( $args, $instance ) {
        echo get_intarget_code();
    }

    function update( $new_instance, $old_instance ) {
    }

    function form( $instance ) {
   }
}

function intarget_register_widgets() {
    register_widget( 'intargetWidget' );
}

function intarget_scripts_method() {

    $options = get_option('intarget_option_name');
    if    ($options['intarget_project_id'] !== '') {
        wp_register_script( 'intarget_handle', '/wp-content/plugins/intarget-ecommerce/js/intarget_main.js',array( 'jquery' ) );

        $datatoBePassed = array(
            'project_id'            => $options['intarget_project_id']
        );
        wp_localize_script( 'intarget_handle', 'intarget_vars', $datatoBePassed );

        wp_enqueue_script( 'intarget_handle');
    }


}



add_action( 'wp_enqueue_scripts', 'intarget_scripts_method' );



register_activation_hook(__FILE__,'intarget_admin_actions');
register_deactivation_hook(__FILE__,'intarget_admin_actions_remove');
add_action( 'widgets_init', 'intarget_register_widgets' );
add_action('admin_menu', 'intarget_admin_actions');

$options = get_option('intarget_option_name');

function intarget_set_default_code()
{
    $options = get_option('intarget_option_name');
    if (is_bool($options)) {
        $options = array();
        $options['intarget_email'] = '';
        $options['intarget_api_key'] = '';
        $options['intarget_project_id'] = '';
        $options['intarget_reg_error'] = '';

        update_option('intarget_option_name', $options);
    }

}


if (is_admin()) {
    $options = get_option('intarget_option_name');

    if (is_bool($options)) {
        intarget_set_default_code();
    }

    $reg_domain = 'https://intarget.ru';
    $url = get_site_url();

    if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_REQUEST['intarget_option_name'])) ) {
        $options = $_REQUEST['intarget_option_name'];
        if (($options['intarget_email'] !== '') &&
            ($options['intarget_api_key'] !== '') &&
            ($options['intarget_project_id'] == '')){
            $reg_ans = regbyApi($reg_domain,$options['intarget_email'],$options['intarget_api_key'],$url);
            if (is_object($reg_ans)) {
                if (($reg_ans->status == 'OK') && (isset($reg_ans->payload))){
                    $intarget_options = get_option('intarget_option_name');
                    $intarget_options['intarget_project_id'] = $reg_ans->payload->projectId;
                    $intarget_options['intarget_reg_error'] ='';
                    $intarget_options['intarget_email'] = $options['intarget_email'];
                    $intarget_options['intarget_api_key'] = $options['intarget_api_key'];
                    update_option('intarget_option_name', $intarget_options);
                    header("Location: ".get_site_url().$_REQUEST['_wp_http_referer']);
                    die();

                } elseif ($reg_ans->status = 'error') {
                    $intarget_options = get_option('intarget_option_name');
                    $intarget_options['intarget_reg_error'] = $reg_ans->code;
                    $intarget_options['intarget_project_id'] = '';
                    $intarget_options['intarget_email'] = $options['intarget_email'];
                    $intarget_options['intarget_api_key'] = $options['intarget_api_key'];
                    update_option('intarget_option_name', $intarget_options);
                    header("Location: ".get_site_url().$_REQUEST['_wp_http_referer']);
                    die();

                }
            }


        }
    } else {
        $options = get_option('intarget_option_name');

        $my_settings_page = new intargetSettingsPage();

    }



}