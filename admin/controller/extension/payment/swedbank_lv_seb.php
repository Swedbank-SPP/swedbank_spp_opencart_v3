<?php

class ControllerExtensionPaymentSwedbankLvSeb extends Controller {

    public function index() {

        $this->language->load('extension/payment/swedbank');

        $this->response->redirect($this->url->link('extension/payment/swedbank', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        die;
    }

    public function install() {
        $this->load->model('extension/payment/swedbank');
        $this->model_extension_payment_swedbank->install();
    }

}
