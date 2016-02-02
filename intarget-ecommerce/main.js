/* 15/10/15 15:58*/
jQuery(document).ready(function () {


    jQuery('.form-table th').css('width', '70px');
    jQuery('th').css('padding-bottom', '12px').css('padding-top', '12px');
    jQuery('.form-table td').css('padding', '5px 10px');
    jQuery('input[name=submit_btn]').css('margin-top', '8px').css('margin-bottom', '8px').css('-webkit-appearance', 'button');


    jQuery('#intarget_project_id').parent().parent().hide();
    jQuery('#intarget_reg_error').parent().parent().hide();

    jQuery('input[name=submit_btn]').attr('value', 'Авторизация');

    //jQuery('[name=submit_btn]').attr('disabled', 'disabled');
    //jQuery('#intarget_api_key, #intarget_email').keyup(function () {
    //
    //    var empty = false;
    //    jQuery('#intarget_api_key, #intarget_email').each(function () {
    //        if (jQuery(this).val() == '') {
    //            empty = true;
    //        }
    //    });
    //    if (empty) {
    //        jQuery('[name=submit_btn]').attr('disabled', 'disabled');
    //    } else {
    //        jQuery('[name=submit_btn]').removeAttr('disabled');
    //    }
    //});

    var app_key_selector = '#intarget_api_key';
    var images_path = '/wp-content/plugins/intarget-ecommerce/';
    var email_selector = '#intarget_email';

    var text_after = "<br><br>Введите email и ключ API из личного кабинета inTarget. <br>" +
        "Если вы еще не регистрировались в сервисе inTarget это можно сделать по ссылке <a href='http://intarget.ru'>inTarget.ru</a>";
    var support_text = "<br><br>Служба поддержки: <a href='mailto:plugins@intarget.ru'>plugins@intarget.ru</a><br>inTarget eCommerce v1.0.1";
    var success_text = "Поздравляем! Ваш сайт успешно привязан к аккаунту <a href='http://intarget.ru'>intarget.ru</a>. Войдите в личный кабинет для просмотра статистики."


    if ((!jQuery('#intarget_reg_error').val()) && (jQuery('#intarget_project_id').val())) {
        window.intarget_succes_reg = true;
    } else {
        window.intarget_reg_error = jQuery('#intarget_reg_error').val();
        window.intarget_succes_reg = false;
    }

    if ((jQuery(app_key_selector).val() !== '') && (jQuery(email_selector).val() !== '')) {
        if (window.intarget_succes_reg == true) {
            jQuery(app_key_selector).after('<img title="Введен правильный ключ!" class="intrg_ok" src="' + images_path + 'ok.png">');
            jQuery(email_selector).after('<img title="Введен правильный email!" class="intrg_ok" src="' + images_path + 'ok.png">');
            jQuery(app_key_selector).attr('disabled', 'disabled');
            jQuery(email_selector).attr('disabled', 'disabled');
            jQuery('[name=submit_btn]').after('<br>' + success_text + support_text);
            jQuery('[name=submit_btn]').hide();
        } else if (window.intarget_succes_reg == false) {
            jQuery('input[name=submit_btn]').after(text_after + support_text);
            if (window.intarget_reg_error == 403) {
                var error_text = 'Неверно введен email или ключ API.';
            } else if (window.intarget_reg_error == 500) {
                var error_text = 'Данный сайт уже используется на сайте <a href="http://intarget.ru">http://intarget.ru</a>';

            } else if (window.intarget_reg_error == 404) {
                var error_text = 'Данный email не зарегистрирован на сайте <a href="http://intarget.ru">http://intarget.ru</a>';
            }
            var intrg_btn_html = '<div style="width:100%;margin-top: 5px;">' +
                '<div style="padding-top: 7px;">' +
                '<span class="intrg_err" style="color:red;margin-left: 5px;"> <b>Ошибка!</b> ' + error_text +
                '</span>' +
                '</div>' +
                '</div>';
            jQuery('input[name=submit_btn]').after(intrg_btn_html);
            jQuery('input[name=submit_btn]').css('float', 'left');
        }
        else {
        //    jQuery('input#jform_params_email').parent().after('<button style="float:left;margin-top: 15px;" id="intrgt_auth_btn"> Авторизация </button>')
            jQuery('input[name=submit_btn]').after(text_after + support_text);
        }
    } else {
        //jQuery('input#jform_params_email').parent().after('<button style="float:left;margin-top: 15px;" id="intrgt_auth_btn"> Авторизация </button>')
        jQuery('input[name=submit_btn]').after(text_after + support_text);
    }

    jQuery(app_key_selector).parent().css('margin-left', '70px');
    jQuery(email_selector).parent().css('margin-left', '70px');
});

var text_after2 = "<p><b>inTarget</b> — сервис повышения продаж и аналитика посетителей сайта.</p>" +
    "<p>Оцените принципиально новый подход к просмотру статистики. Общайтесь со своей аудиторией, продавайте лучше, зарабатывайте больше. И все это бесплатно!</p>";
jQuery('.readmore').parent().hide();
jQuery('.info-labels').after(text_after2);





