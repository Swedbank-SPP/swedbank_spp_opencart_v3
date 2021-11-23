<?php

class ControllerExtensionPaymentSwedbank extends Controller {

    public function index() {

        return '';
    }

    public function send() {

        $this->load->language('extension/payment/swedbank');
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/swedbank');

        $this->load->model('localisation/language');
        //echo '<pre>';
        $lang = $this->model_localisation_language->getLanguages();
        $lang_code_list = [];


        foreach ($lang as $key => $value) {
            if ((int) $value['status'] === 1) {
                $lang_code_list[] = explode('-', $value['code'])[0];
            }
        }

        $lang = $this->language->get('code');
        $er = true;
        if ($_GET['pmethod'] === 'lt_card' || $_GET['pmethod'] === 'lv_card' || $_GET['pmethod'] === 'ee_card') {
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $er = $this->model_extension_payment_swedbank->payHps($order_info, $lang);
        } else if ($_GET['pmethod'] === 'lt_swedbank' || $_GET['pmethod'] === 'lt_seb' || $_GET['pmethod'] === 'lt_dnb' || $_GET['pmethod'] === 'lt_nordea' || $_GET['pmethod'] === 'lt_danske' || $_GET['pmethod'] === 'lv_swedbank' || $_GET['pmethod'] === 'lv_seb' || $_GET['pmethod'] === 'lv_citadele' || $_GET['pmethod'] === 'ee_swedbank' || $_GET['pmethod'] === 'ee_seb' || $_GET['pmethod'] === 'ee_nordea') {
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $er = $this->model_extension_payment_swedbank->payBankLink($order_info, $lang);
        } else {
            return false;
        }
        if(!$er){
            $this->session->data['error'] = $this->language->get('error_process_order');
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

    }

    public function confirmpayment($oId = null, $ignore = false) {
        $this->load->language('extension/payment/swedbank');
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/swedbank');

        try {

            $orderId = isset($oId) ? $oId : $this->request->get['mref'];
            //$pmethod = $this->request->get['pmethod'];

            $oId = explode('_', $orderId)[1];

            $order_info = $this->model_checkout_order->getOrder($oId);

            $pmethod = str_replace('swedbank_', '', $order_info['payment_code']);

            $result = $this->model_extension_payment_swedbank->checkPaiment($pmethod, $orderId);

            //$this->logger($order_info);
            //$this->model_checkout_order->

            $order_info = $this->paymentTypeText($order_info, $pmethod);

            //$this->logger($order_info);

            if (empty($this->model_extension_payment_swedbank->getOrder($order_info['order_id']))) {
                $this->model_extension_payment_swedbank->addOrder($order_info, '');
            }

            $transactions = $this->model_extension_payment_swedbank->getTransactions($oId);

            $this->fixDescription($order_info['payment_method'], $order_info['order_id']);

            if ((int)$result === 1) {
                //payment is OK  - final status
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('swedbank_order_status_id'));
                $this->session->data['success'] = $this->language->get('text_payment_success');

                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                //$this->logger('Redirekting');
                $this->response->redirect($this->url->link('checkout/success', '', true));
            } else if ((int)$result === 2051) {
                //PENDING
                //payment in progress
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], 1);
                $this->session->data['success'] = $this->language->get('text_payment_success_vaiting');
                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                $this->response->redirect($this->url->link('checkout/waiting', '', true));
            } else if ((int)$result === 2052) {
                //ERROR
                //paiment failed - final status
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], 10);
                $this->session->data['error'] = $this->language->get('error_process_order');
                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            } else if ((int)$result === 2053) {
                //REDIRECT
                //payment in progress
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], 1);
                $this->session->data['success'] = $this->language->get('text_payment_success_vaiting');
                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                $this->response->redirect($this->url->link('checkout/waiting', '', true));
            } else if ((int)$result === 2054) {
                //CANCELED
                //paiment canceled  - final status
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], 10);
                $this->session->data['error'] = $this->language->get('error_process_order');
                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            } else if ((int)$result === 2066) {
                //REQUIRES INVESTIGTION  - final status, needs manually update status
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], 10);
                $this->session->data['error'] = $this->language->get('error_process_order');
                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            } else {
                //paiment failed  - final status
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], 10);
                $this->session->data['error'] = $this->language->get('error_process_order');
                if (empty($transactions)) {
                    $this->model_extension_payment_swedbank->addTransaction($oId, 'payment', $order_info);
                }
                $this->response->redirect($this->url->link('checkout/checkout', '', true));

            }

        } catch (Exception $e){
            $this->logger($e);
        }



        if($ignore){
            return false;
        }


    }

    public function bankinformme() {
        $this->load->language('extension/payment/swedbank');
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/swedbank');

        //$statusId = $this->config->get('swedbank_order_status_id');

        $xml = trim(file_get_contents('php://input'));

        $this->logger("Notifiction:");
        $this->logger($xml);

        try {
            $object = new SimpleXMLElement($xml);
        } catch (Exception $exc) {
            $this->logger('ERROR: Failed to parse notification');
            $this->logger($exc);
            die('<Response>OK</Response>');
        }

        if (isset($object) && isset($object->Event) && isset($object->Event->Purchase)) {

            $oId = $object->Event->Purchase[0]->attributes()['TransactionId'];

            $this->confirmpayment($oId, true);
            //-----

        }

        die('<Response>OK</Response>');
    }

    private function logger($data) {

        if ((int) $this->config->get('swedbank_debuging_lt') === 1) {
            $text = print_r($data, true);
            $text = preg_replace("/<password>(.*)<\/password>/","<password>********</password>", $text);
            file_put_contents(dirname(__FILE__) . '/../../../../system/storage/logs/swedbank.log', $text, FILE_APPEND | LOCK_EX);
        }
    }

    public function runcron() {
        $this->load->language('extension/payment/swedbank');
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/swedbank');

        $this->model_extension_payment_swedbank->runcron();
    }

    private function paymentTypeText(&$order_info, $pmethod = null) {
        switch (isset($pmethod) ? $pmethod : $this->request->get['pmethod']) {
            case 'lt_card':
            case 'lv_card':
            case 'ee_card':
                $order_info['payment_method'] = 'Card payment';
                break;
            case 'lt_swedbank':
            case 'lv_swedbank':
            case 'ee_swedbank':
                $order_info['payment_method'] = 'Banklink - Swedbank';
                break;
            case 'lt_seb':
            case 'lv_seb':
                $order_info['payment_method'] = 'Banklink - SEB';
                break;
            case 'lt_dnb':
                $order_info['payment_method'] = 'Banklink - DNB';
                break;

            default:
                $order_info['payment_method'] = 'Banklink';
                break;
        }
        return $order_info;
    }

    private function fixDescription($p_t, $oi) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_method = '" . $this->db->escape($p_t) . "' WHERE order_id = " . $this->db->escape($oi));
    }

}
