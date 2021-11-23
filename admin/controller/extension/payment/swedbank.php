<?php

class ControllerExtensionPaymentSwedbank extends Controller {

    private $error = array();

    public function index() {

        $this->language->load('extension/payment/swedbank');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->request->post['payment_swedbank_lt_card_status'] = isset($this->request->post['swedbank_lt_card_status']) ? $this->request->post['swedbank_lt_card_status'] : 0 ;
            $this->request->post['swedbank_test_url'] = 'https://accreditation.datacash.com/Transaction/acq_a';
            $this->request->post['swedbank_live_url'] = 'https://mars.transaction.datacash.com/Transaction';
            $this->model_setting_setting->editSetting('swedbank', $this->request->post);

            $this->model_setting_setting->editSetting('payment_swedbank_ee_card', ['payment_swedbank_ee_card_status'=> isset($this->request->post['swedbank_ee_card_status']) ? $this->request->post['swedbank_ee_card_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_ee_swedbank', ['payment_swedbank_ee_swedbank_status'=>isset($this->request->post['swedbank_ee_swedbank_status']) ? $this->request->post['swedbank_ee_swedbank_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lt_card', ['payment_swedbank_lt_card_status'=>isset($this->request->post['swedbank_lt_card_status']) ? $this->request->post['swedbank_lt_card_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lt_dnb', ['payment_swedbank_lt_dnb_status'=>isset($this->request->post['swedbank_lt_dnb_status']) ? $this->request->post['swedbank_lt_dnb_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lt_seb', ['payment_swedbank_lt_seb_status'=>isset($this->request->post['swedbank_lt_seb_status']) ? $this->request->post['swedbank_lt_seb_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lt_swedbank', ['payment_swedbank_lt_swedbank_status'=>isset($this->request->post['swedbank_lt_swedbank_status']) ? $this->request->post['swedbank_lt_swedbank_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lv_card', ['payment_swedbank_lv_card_status'=>isset($this->request->post['swedbank_lv_card_status']) ? $this->request->post['swedbank_lv_card_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lv_citadele', ['payment_swedbank_lv_citadele_status'=>isset($this->request->post['swedbank_lv_citadele_status']) ? $this->request->post['swedbank_lv_citadele_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lv_seb', ['payment_swedbank_lv_seb_status'=>isset($this->request->post['swedbank_lv_seb_status']) ? $this->request->post['swedbank_lv_seb_status'] : 0 ]);
            $this->model_setting_setting->editSetting('payment_swedbank_lv_swedbank', ['payment_swedbank_lv_swedbank_status'=>isset($this->request->post['swedbank_lv_swedbank_status']) ? $this->request->post['swedbank_lv_swedbank_status'] : 0 ]);


            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));

        }
/*echo '<pre>';
        print_r($this->config); die('</pre>');*/


        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_payment'] = $this->language->get('text_payment');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_swedbank'] = $this->language->get('text_swedbank');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_sort_order'] = $this->language->get('text_sort_order');
        $data['text_lithuania'] = $this->language->get('text_lithuania');
        $data['text_latvia'] = $this->language->get('text_latvia');
        $data['text_estonia'] = $this->language->get('text_estonia');
        $data['text_general'] = $this->language->get('text_general');
        $data['text_order_status_a_p'] = $this->language->get('text_order_status_a_p');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['text_enable_plugin'] = $this->language->get('text_enable_plugin');

        //Order credentials
        $data['text_order_cre'] = $this->language->get('text_order_cre');
        $data['text_if_dont_have_cre'] = $this->language->get('text_if_dont_have_cre');
        $data['text_request_for_ecom'] = $this->language->get('text_request_for_ecom');
        $data['text_country'] = $this->language->get('text_country');
        $data['text_company_name'] = $this->language->get('text_company_name');
        $data['text_company_code'] = $this->language->get('text_company_code');
        $data['text_your_name'] = $this->language->get('text_your_name');
        $data['text_phone'] = $this->language->get('text_phone');
        $data['text_email'] = $this->language->get('text_email');
        $data['text_company_webstore'] = $this->language->get('text_company_webstore');
        $data['text_intrest_service'] = $this->language->get('text_intrest_service');
        $data['text_swedbank_payment_portal'] = $this->language->get('text_swedbank_payment_portal');
        $data['text_swedbank_banklink'] = $this->language->get('text_swedbank_banklink');
        $data['text_all_fields_req'] = $this->language->get('text_all_fields_req');
        $data['text_answers_to_question'] = $this->language->get('text_answers_to_question');
        $data['text_send_request'] = $this->language->get('text_send_request');
        $data['text_thank_you_contacting'] = $this->language->get('text_thank_you_contacting');
        $data['text_anwers_raised_final'] = $this->language->get('text_anwers_raised_final');


        $data['text_notification_url'] = $this->language->get('text_notification_url');
        $data['text_please_provide_url'] = $this->language->get('text_please_provide_url');
        $data['text_cronjob'] = $this->language->get('text_cronjob');
        $data['text_nocron_url'] = $this->language->get('text_nocron_url');
        $data['text_enable_disable'] = $this->language->get('text_enable_disable');
        $data['text_to_use_plugin'] = $this->language->get('text_to_use_plugin');
        $data['text_debug'] = $this->language->get('text_debug');
        $data['text_storing_transaction'] = $this->language->get('text_storing_transaction');
        $data['text_test_mode'] = $this->language->get('text_test_mode');

        //LT
        $data['text_card_paymnet_lt'] = $this->language->get('text_card_paymnet_lt');
        $data['text_swedbank_banklink_lt'] = $this->language->get('text_swedbank_banklink_lt');
        $data['text_seb_banklink_lt'] = $this->language->get('text_seb_banklink_lt');
        $data['text_dnb_banklink_lt'] = $this->language->get('text_dnb_banklink_lt');
        $data['text_nordea_banklink_lt'] = $this->language->get('text_nordea_banklink_lt');
        $data['text_danske_banklink_lt'] = $this->language->get('text_danske_banklink_lt');
        $data['text_paypal_lt'] = $this->language->get('text_paypal_lt');
        $data['text_banklink_language_lt'] = $this->language->get('text_banklink_language_lt');
        $data['text_test_vtid_lt'] = $this->language->get('text_test_vtid_lt');
        $data['text_test_password_lt'] = $this->language->get('text_test_password_lt');
        $data['text_test_page_set_id_lt'] = $this->language->get('text_test_page_set_id_lt');
        $data['text_vtid_lt'] = $this->language->get('text_vtid_lt');
        $data['text_password_lt'] = $this->language->get('text_password_lt');
        $data['text_page_set_id_lt'] = $this->language->get('text_page_set_id_lt');
        $data['text_geo_zone_lt'] = $this->language->get('text_geo_zone_lt');


        //LV
        $data['text_card_paymnent_lv'] = $this->language->get('text_card_paymnent_lv');
        $data['text_swedbank_banklink_lv'] = $this->language->get('text_swedbank_banklink_lv');
        $data['text_seb_banklink_lv'] = $this->language->get('text_seb_banklink_lv');
        $data['text_citadele_banklink_lv'] = $this->language->get('text_citadele_banklink_lv');
        $data['text_paypal_lv'] = $this->language->get('text_paypal_lv');
        $data['text_banklink_language_lv'] = $this->language->get('text_banklink_language_lv');
        $data['text_test_vtid_lv'] = $this->language->get('text_test_vtid_lv');
        $data['text_test_password_lv'] = $this->language->get('text_test_password_lv');
        $data['text_test_page_set_id_lv'] = $this->language->get('text_test_page_set_id_lv');
        $data['text_vtid_lv'] = $this->language->get('text_vtid_lv');
        $data['text_password_lv'] = $this->language->get('text_password_lv');
        $data['text_page_set_id_lv'] = $this->language->get('text_page_set_id_lv');
        $data['text_geo_zone_lv'] = $this->language->get('text_geo_zone_lv');

        //EE
        $data['text_card_payment_ee'] = $this->language->get('text_card_payment_ee');
        $data['text_swedbank_banklink_ee'] = $this->language->get('text_swedbank_banklink_ee');
        $data['text_seb_banklink_ee'] = $this->language->get('text_seb_banklink_ee');
        $data['text_nordea_banklink_ee'] = $this->language->get('text_nordea_banklink_ee');
        $data['text_paypal_ee'] = $this->language->get('text_paypal_ee');
        $data['text_banklink_language_ee'] = $this->language->get('text_banklink_language_ee');
        $data['text_test_vtid_ee'] = $this->language->get('text_test_vtid_ee');
        $data['text_test_password_ee'] = $this->language->get('text_test_password_ee');
        $data['text_test_page_set_id_ee'] = $this->language->get('text_test_page_set_id_ee');
        $data['text_vtid_ee'] = $this->language->get('text_vtid_ee');
        $data['text_password_ee'] = $this->language->get('text_password_ee');
        $data['text_page_set_id_ee'] = $this->language->get('text_page_set_id_ee');
        $data['text_geo_zone_ee'] = $this->language->get('text_geo_zone_ee');


        //errors
        $data['error_warning'] = $this->error['warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_project'] = $this->error['project'] = isset($this->error['project']) ? $this->error['project'] : '';
        $data['error_sign'] = $this->error['sign'] = isset($this->error['sign']) ? $this->error['sign'] : '';

        $data['error_hosted_page_id'] = $this->error['hosted_page_id'] = isset($this->error['hosted_page_id']) ? $this->error['hosted_page_id'] : '';
        $data['error_permission'] = $this->error['permission'] = isset($this->error['permission']) ? $this->error['permission'] : '';
        $data['error_refresh'] = $this->error['refresh'] = isset($this->error['refresh']) ? $this->error['refresh'] : '';

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
            ],
            [
                'text' => $this->language->get('text_payment'),
                'href' => $this->url->link('marketplace/extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL')
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('marketplace/extension/payment/swedbank', 'user_token=' . $this->session->data['user_token'], 'SSL')
            ]
        ];
//route=marketplace/extension
        $data['action'] = $this->url->link('extension/payment/swedbank', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);


        //Inputs
        $data['swedbank_status'] = isset($this->request->post['swedbank_status']) ? $this->request->post['swedbank_status'] : $_POST ? 0 : $this->config->get('swedbank_status');
        $data['swedbank_debuging_lt'] = isset($this->request->post['swedbank_debuging_lt']) ? $this->request->post['swedbank_debuging_lt'] : $this->config->get('swedbank_debuging_lt');
        $data['swedbank_lt_card_status'] = isset($this->request->post['swedbank_lt_card_status']) ? $this->request->post['swedbank_lt_card_status'] : $_POST ? 0 : $this->config->get('swedbank_lt_card_status');
        $data['swedbank_lt_swedbank_status'] = isset($this->request->post['swedbank_lt_swedbank_status']) ? $this->request->post['swedbank_lt_swedbank_status'] : $_POST ? 0 : $this->config->get('swedbank_lt_swedbank_status');
        $data['swedbank_lt_seb_status'] = isset($this->request->post['swedbank_lt_seb_status']) ? $this->request->post['swedbank_lt_seb_status'] : $_POST ? 0 : $this->config->get('swedbank_lt_seb_status');
        $data['swedbank_lt_dnb_status'] = isset($this->request->post['swedbank_lt_dnb_status']) ? $this->request->post['swedbank_lt_dnb_status'] : $_POST ? 0 : $this->config->get('swedbank_lt_dnb_status');
        $data['swedbank_testmode_lt'] = isset($this->request->post['swedbank_testmode_lt']) ? $this->request->post['swedbank_testmode_lt'] : $this->config->get('swedbank_testmode_lt');
        $data['swedbank_banklinklng_lt'] = isset($this->request->post['swedbank_banklinklng_lt']) ? $this->request->post['swedbank_banklinklng_lt'] : $this->config->get('swedbank_banklinklng_lt');
        $data['swedbank_testvtid_lt'] = isset($this->request->post['swedbank_testvtid_lt']) ? $this->request->post['swedbank_testvtid_lt'] : $this->config->get('swedbank_testvtid_lt');
        $data['swedbank_testpass_lt'] = isset($this->request->post['swedbank_testpass_lt']) ? $this->request->post['swedbank_testpass_lt'] : $this->config->get('swedbank_testpass_lt');
        $data['swedbank_vtid_lt'] = isset($this->request->post['swedbank_vtid_lt']) ? $this->request->post['swedbank_vtid_lt'] : $this->config->get('swedbank_vtid_lt');
        $data['swedbank_pass_lt'] = isset($this->request->post['swedbank_pass_lt']) ? $this->request->post['swedbank_pass_lt'] : $this->config->get('swedbank_pass_lt');
        $data['swedbank_lv_card_status'] = isset($this->request->post['swedbank_lv_card_status']) ? $this->request->post['swedbank_lv_card_status'] : $_POST ? 0 : $this->config->get('swedbank_lv_card_status');
        $data['swedbank_lv_swedbank_status'] = isset($this->request->post['swedbank_lv_swedbank_status']) ? $this->request->post['swedbank_lv_swedbank_status'] : $_POST ? 0 : $this->config->get('swedbank_lv_swedbank_status');
        $data['swedbank_lv_seb_status'] = isset($this->request->post['swedbank_lv_seb_status']) ? $this->request->post['swedbank_lv_seb_status'] : $_POST ? 0 : $this->config->get('swedbank_lv_seb_status');
        $data['swedbank_testmode_lv'] = isset($this->request->post['swedbank_testmode_lv']) ? $this->request->post['swedbank_testmode_lv'] : $this->config->get('swedbank_testmode_lv');
        $data['swedbank_banklinklng_lv'] = isset($this->request->post['swedbank_banklinklng_lv']) ? $this->request->post['swedbank_banklinklng_lv'] : $this->config->get('swedbank_banklinklng_lv');
        $data['swedbank_testvtid_lv'] = isset($this->request->post['swedbank_testvtid_lv']) ? $this->request->post['swedbank_testvtid_lv'] : $this->config->get('swedbank_testvtid_lv');
        $data['swedbank_testpass_lv'] = isset($this->request->post['swedbank_testpass_lv']) ? $this->request->post['swedbank_testpass_lv'] : $this->config->get('swedbank_testpass_lv');
        $data['swedbank_vtid_lv'] = isset($this->request->post['swedbank_vtid_lv']) ? $this->request->post['swedbank_vtid_lv'] : $this->config->get('swedbank_vtid_lv');
        $data['swedbank_pass_lv'] = isset($this->request->post['swedbank_pass_lv']) ? $this->request->post['swedbank_pass_lv'] : $this->config->get('swedbank_pass_lv');
        $data['swedbank_ee_card_status'] = isset($this->request->post['swedbank_ee_card_status']) ? $this->request->post['swedbank_ee_card_status'] : $_POST ? 0 : $this->config->get('swedbank_ee_card_status');
        $data['swedbank_ee_swedbank_status'] = isset($this->request->post['swedbank_ee_swedbank_status']) ? $this->request->post['swedbank_ee_swedbank_status'] : $_POST ? 0 : $this->config->get('swedbank_ee_swedbank_status');
        $data['swedbank_testmode_ee'] = isset($this->request->post['swedbank_testmode_ee']) ? $this->request->post['swedbank_testmode_ee'] : $this->config->get('swedbank_testmode_ee');
        $data['swedbank_banklinklng_ee'] = isset($this->request->post['swedbank_banklinklng_ee']) ? $this->request->post['swedbank_banklinklng_ee'] : $this->config->get('swedbank_banklinklng_ee');
        $data['swedbank_testvtid_ee'] = isset($this->request->post['swedbank_testvtid_ee']) ? $this->request->post['swedbank_testvtid_ee'] : $this->config->get('swedbank_testvtid_ee');
        $data['swedbank_testpass_ee'] = isset($this->request->post['swedbank_testpass_ee']) ? $this->request->post['swedbank_testpass_ee'] : $this->config->get('swedbank_testpass_ee');
        $data['swedbank_vtid_ee'] = isset($this->request->post['swedbank_vtid_ee']) ? $this->request->post['swedbank_vtid_ee'] : $this->config->get('swedbank_vtid_ee');
        $data['swedbank_pass_ee'] = isset($this->request->post['swedbank_pass_ee']) ? $this->request->post['swedbank_pass_ee'] : $this->config->get('swedbank_pass_ee');
        $data['swedbank_sort_order'] = isset($this->request->post['swedbank_sort_order']) ? $this->request->post['swedbank_sort_order'] : $this->config->get('swedbank_sort_order');
        $data['swedbank_geo_zone_id_lt'] = isset($this->request->post['swedbank_geo_zone_id_lt']) ? $this->request->post['swedbank_geo_zone_id_lt'] : $this->config->get('swedbank_geo_zone_id_lt');
        $data['swedbank_geo_zone_id_lv'] = isset($this->request->post['swedbank_geo_zone_id_lv']) ? $this->request->post['swedbank_geo_zone_id_lv'] : $this->config->get('swedbank_geo_zone_id_lv');
        $data['swedbank_geo_zone_id_ee'] = isset($this->request->post['swedbank_geo_zone_id_ee']) ? $this->request->post['swedbank_geo_zone_id_ee'] : $this->config->get('swedbank_geo_zone_id_ee');
        $data['swedbank_order_status_id'] = isset($this->request->post['swedbank_order_status_id']) ? $this->request->post['swedbank_order_status_id'] : $this->config->get('swedbank_order_status_id');
        //$data[''] = isset($this->request->post['']) ? $this->request->post[''] : $this->config->get('');

        $data['swedbank_enable_plugin_lt'] = isset($this->request->post['swedbank_enable_plugin_lt']) ? $this->request->post['swedbank_enable_plugin_lt'] : $_POST ? 0 : $this->config->get('swedbank_enable_plugin_lt');
        $data['swedbank_enable_plugin_lv'] = isset($this->request->post['swedbank_enable_plugin_lv']) ? $this->request->post['swedbank_enable_plugin_lv'] : $_POST ? 0 : $this->config->get('swedbank_enable_plugin_lv');
        $data['swedbank_enable_plugin_ee'] = isset($this->request->post['swedbank_enable_plugin_ee']) ? $this->request->post['swedbank_enable_plugin_ee'] : $_POST ? 0 : $this->config->get('swedbank_enable_plugin_ee');


        $data['callback'] = HTTP_CATALOG . 'index.php?route=marketplace/extension/payment/swedbank/callback';

        $data['http'] = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        $data['hostSer'] = $_SERVER['HTTP_HOST'];

        //@TODO
        $this->load->model('localisation/language');
        //echo '<pre>';
        $lang = $this->model_localisation_language->getLanguages();
        $data['lang_code_list'] = [];


        foreach ($lang as $key => $value) {
            if ((int) $value['status'] === 1) {
                $data['lang_code_list'][] = explode('-', $value['code'])[0];
            }
        }


        $data['language_total'] = count($data['lang_code_list']);

        //<***@TODO

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        //loading default swedbank template
        $this->template = 'extension/payment/swedbanktpl';

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view($this->template, $data));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/swedbank')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $this->load->model('localisation/language');
        //echo '<pre>';
        $lang = $this->model_localisation_language->getLanguages();
        $lang_code_list = [];


        foreach ($lang as $key => $value) {
            if ((int) $value['status'] === 1) {
                $lang_code_list[] = explode('-', $value['code'])[0];
            }
        }


        return !$this->error;
    }

    public function install() {
        $this->load->model('extension/payment/swedbank');
        $this->model_extension_payment_swedbank->install();
    }

    public function uninstall() {
        $this->load->model('extension/payment/swedbank');
        $this->model_extension_payment_swedbank->uninstall();
    }

    public function order() {

        if ($this->config->get('swedbank_status')) {

            $this->load->model('marketplace/extension/payment/swedbank');

            $swedbank_order = $this->model_extension_swedbank->getOrder($this->request->get['order_id']);

            $this->load->language('extension/payment/swedbank');

            $swedbank_order['total_released'] = $this->model_extension_swedbank->getTotalReleased($swedbank_order['swedbank_order_id']);

            $swedbank_order['total_formatted'] = $this->currency->format($swedbank_order['total'], $swedbank_order['currency_code'], true);
            $swedbank_order['total_released_formatted'] = $this->currency->format($swedbank_order['total_released'], $swedbank_order['currency_code'], true);

            $data['swedbank_order'] = $swedbank_order;

            $data['text_payment_info'] = $this->language->get('text_payment_info');
            $data['text_order_ref'] = $this->language->get('text_order_ref');
            $data['text_order_total'] = $this->language->get('text_order_total');
            $data['text_total_released'] = $this->language->get('text_total_released');
            $data['text_transactions'] = $this->language->get('text_transactions');
            $data['text_column_date_added'] = $this->language->get('text_column_date_added');
            $data['text_column_type'] = $this->language->get('text_column_type');
            $data['text_column_amount'] = $this->language->get('text_column_amount');


            $data['order_id'] = $this->request->get['order_id'];
            $data['user_token'] = $this->request->get['user_token'];

            return $this->load->view('extension/payment/swedbank_order', $data);
        }
    }

}
