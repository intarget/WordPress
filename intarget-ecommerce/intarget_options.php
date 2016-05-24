<?php

class intargetSettingsPage {
    public $options;
    public $settings_page_name = 'intarget_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        $this->options = get_option('intarget_option_name');
    }

    public function add_plugin_page() {
        add_options_page('Settings Admin', 'inTarget', 'manage_options', $this->settings_page_name, array($this, 'create_admin_page'));
    }

    public function create_admin_page() {
        $this->options = get_option('intarget_option_name');

        if ((isset($this->options['intarget_email'])) && ('' !== $this->options['intarget_email'])) {
            $email = $this->options['intarget_email'];
        } else $email = get_option('admin_email');

        ?>
        <script type="text/javascript">
            <?php include('main.js'); ?>
        </script>
        <div id="intarget_site_url" style="display: none"><?php echo get_site_url(); ?></div>
        <div class="wrap">
            <div id="wrapper">
                <form id="settings_form" method="post"
                      action="<?php echo $_SERVER['REQUEST_URI'] ?>">
                    <h1>Плагин inTarget eCommerce</h1>
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

    public function page_init() {
        register_setting('intarget_option_group', 'intarget_option_name', array($this, 'sanitize'));

        add_settings_section('setting_section_id', '', // Title
            array($this, 'print_section_info'), $this->settings_page_name);

        add_settings_field('email', 'Email', array($this, 'intarget_email_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('intarget_api_key', 'Ключ API', array($this, 'intarget_api_key_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('intarget_reg_error', 'intarget_reg_error', array($this, 'intarget_reg_error_callback'), $this->settings_page_name, 'setting_section_id');

        add_settings_field('intarget_project_id', 'intarget_project_id', array($this, 'intarget_project_id_callback'), $this->settings_page_name, 'setting_section_id');
    }

    public function sanitize($input) {
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

    public function print_section_info() {
    }

    public function intarget_email_callback() {
        printf('<input type="text" id="intarget_email" name="intarget_option_name[intarget_email]" value="%s" title="Введите в данном поле email, указанный при регистрации на сайте https://intarget.ru"/>', isset($this->options['intarget_email']) ? esc_attr($this->options['intarget_email']) : '');
    }

    public function intarget_api_key_callback() {
        printf('<input type="text" id="intarget_api_key" name="intarget_option_name[intarget_api_key]" value="%s" title="Введите в данном поле Ключ API, полученный на сайте https://intarget.ru" />', isset($this->options['intarget_api_key']) ? esc_attr($this->options['intarget_api_key']) : '');
    }

    public function intarget_reg_error_callback() {
        printf('<input type="text" id="intarget_reg_error" name="intarget_option_name[intarget_reg_error]" value="%s" />', isset($this->options['intarget_reg_error']) ? esc_attr($this->options['intarget_reg_error']) : '');
    }

    public function intarget_project_id_callback() {
        printf('<input type="text" id="intarget_project_id" name="intarget_option_name[intarget_project_id]" value="%s" />', isset($this->options['intarget_project_id']) ? esc_attr($this->options['intarget_project_id']) : '');
    }
}

function echo_before_text() {
    echo '
<div id="before_install" style="display:none;">
Плагин inTarget успешно установлен!

Для начала работы плагина необходимо ввести Ключ API, полученный в личном кабинете на сайте <a href="https://intarget.ru">inTarget.ru</a>
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

function regbyApi($regDomain, $email, $key, $url) {
    $domain = $regDomain;
    if (($domain == '') OR ($email == '') OR ($key == '') OR ($url == '')) {
        return;
    }
    $ch = curl_init();
    $jsondata = json_encode(array('email' => $email, 'key' => $key, 'url' => $url, 'cms' => 'wordpress'));

    $options = array(CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept: application/json'), CURLOPT_URL => $domain . "/api/registration.json", CURLOPT_POST => 1, CURLOPT_POSTFIELDS => $jsondata, CURLOPT_RETURNTRANSFER => true,);

    curl_setopt_array($ch, $options);
    $json_result = json_decode(curl_exec($ch));
    curl_close($ch);
    if (isset($json_result->status)) {
        if (($json_result->status == 'OK') && (isset($json_result->payload))) {
        } elseif ($json_result->status = 'error') {
        }
    }
    return $json_result;
}

function intarget_scripts_method() {
    $options = get_option('intarget_option_name');
    if ($options['intarget_project_id'] !== '') {
        wp_register_script('intarget_handle', '/wp-content/plugins/intarget-ecommerce/js/main.js', array('jquery'));

        $datatoBePassed = array('project_id' => $options['intarget_project_id']);
        wp_localize_script('intarget_handle', 'intarget_vars', $datatoBePassed);

        wp_enqueue_script('intarget_handle');
    }
}

function intarget_scripts_add() {
    $options = get_option('intarget_option_name');
    if ($options['intarget_project_id'] !== '') {
        wp_register_script('intarget_add', '/wp-content/plugins/intarget-ecommerce/js/add.js', array('jquery'));
        wp_enqueue_script('intarget_add');
    }
}

function intarget_set_default_code() {
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
