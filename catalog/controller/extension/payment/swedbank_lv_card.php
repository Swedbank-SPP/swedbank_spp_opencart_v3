<?php

class ControllerExtensionPaymentSwedbankLvCard extends Controller {

    public function index() {
        $this->load->language('extension/payment/swedbank');

        $data['text_loading'] = $this->language->get('text_loading');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['form_submit'] = $this->url->link('extension/payment/swedbank/send&pmethod=lv_card', '', true);

        return $this->load->view('extension/payment/swedbank', $data);
    }

}
