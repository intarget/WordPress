<?php




function intarget_admin_page()
{


    $options = get_option('intarget_option_name');

    ?>
    <script type="text/javascript">
        <?php include('main.js'); ?>
    </script>
    <style type="text/css">
        <?php include('uptolike_style.css')?>
    </style>

    <div class="wrap">


        <div id="wrapper">

            <form id="settings_form" method="post" action="options.php">
                <H1>Плагин inTarget eCommerce</H1>


                <?php

                echo_before_text();

                $intarget_settings_page = new intargetSettingsPage();
                $intarget_settings_page->page_init();
                settings_fields('intarget_option_group');
                do_settings_sections('intarget_settings');
                ?>

                <input type="submit" name="submit_btn" value="Cохранить изменения">

            </form>

        </div>
    </div>
<?php
}

intarget_admin_page();
