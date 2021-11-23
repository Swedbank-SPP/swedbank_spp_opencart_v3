<?php

class ModelExtensionPaymentSwedbank extends Model {

    public function getMethod($address, $total) {

        return array();
    }

    public function addOrder($order_info, $order_code) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "swedbank_order` SET `order_id` = '" . (int) $order_info['order_id'] . "', `order_code` = '" . $this->db->escape($order_code) . "', `date_added` = now(), `date_modified` = now(), `currency_code` = '" . $this->db->escape($order_info['currency_code']) . "', `total` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false) . "'");

        return $this->db->getLastId();
    }

    public function getOrder($order_id) {
        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "swedbank_order` WHERE `order_id` = '" . (int) $order_id . "' LIMIT 1");

        if ($qry->num_rows) {
            $order = $qry->row;
            $order['transactions'] = $this->getTransactions($order['swedbank_order_id']);

            return $order;
        } else {
            return false;
        }
    }

    public function addTransaction($swedbank_order_id, $type, $order_info) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "swedbank_order_transaction` SET `swedbank_order_id` = '" . (int) $swedbank_order_id . "', `date_added` = now(), `type` = '" . $this->db->escape($type) . "', `amount` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false) . "'");
    }

    public function getTransactions($swedbank_order_id) {
        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "swedbank_order_transaction` WHERE `swedbank_order_id` = '" . (int) $swedbank_order_id . "'");

        if ($qry->num_rows) {
            return $qry->rows;
        } else {
            return false;
        }
    }

    public function getSwedbankOrder($swedbank_order_id) {
        $qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "swedbank_order WHERE order_code = " . (int) $swedbank_order_id);
        return $qry->row;
    }

    public function logger($data) {

        if ((int) $this->config->get('swedbank_debuging_lt') === 1) {
            $text = print_r($data, true);
            $text = preg_replace("/<password>(.*)<\/password>/","<password>********</password>", $text);
            file_put_contents(dirname(__FILE__) . '/../../../../system/storage/logs/swedbank.log', $text, FILE_APPEND | LOCK_EX);
        }
    }

    public function runcron() {


    }

    public function payHps($order_info, $lang) {

        $oD = $_GET['pmethod'];

        $param = $this->getLoginParam($oD);

        If(!$param){
            return false;
        }


        $vtid = $param[0];
        $psw = $param[1];
        $envUrl = $param[2];
        $test = $param[3];

        if($lang == 'et'){
            $lang = 'ee';
        }

        $lang_list = ['lt','en','dk','ee','lv','no','ru','se','fr','it','es','de','pl','nl','sk','hu'];

        if(array_search($lang, $lang_list) === false){
                $lang = 'en';
        }

        $merchantReferenceId = 'o' . rand(10, 99) . '_' . $order_info['order_id'];
        if(strlen($merchantReferenceId) < 6){
            $merchantReferenceId = 'o' . rand(10, 99) . '0_' . $order_info['order_id'];
        }
        $purchaseAmount = round($order_info['total'],2); // Euro and cents needs to be separated by dot.
        $date = date('Ymd H:i:s');

        $exURL = $order_info['store_url'] . 'index.php?route=extension/payment/swedbank/confirmpayment&amp;mref=' . $merchantReferenceId . '&amp;pmethod=' . $oD; // expire url
        $reURL = $order_info['store_url'] . 'index.php?route=extension/payment/swedbank/confirmpayment&amp;mref=' . $merchantReferenceId . '&amp;pmethod=' . $oD; // return url
        $errURL = $order_info['store_url'] . 'index.php?route=extension/payment/swedbank/confirmpayment&amp;mref=' . $merchantReferenceId . '&amp;pmethod=' . $oD; // error url

        $page_set_id = $test ? '329' : '4018';

        $shopUrl = $order_info['store_url'];

        $xml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2">
   <Authentication>
      <client>{$vtid}</client>
      <password>{$psw}</password>
   </Authentication>
   <Transaction>
      <TxnDetails>
      <Risk>
        <Action service="1">
        <MerchantConfiguration>
          <channel>W</channel>
         </MerchantConfiguration>
        <CustomerDetails>
          <OrderDetails>
            <BillingDetails>
              <state_province></state_province>
              <name>{$order_info['payment_firstname']} {$order_info['payment_lastname']}</name>
              <address_line1>{$order_info['payment_address_1']}</address_line1>
              <address_line2>{$order_info['payment_address_2']}</address_line2>
              <city>{$order_info['payment_city']}</city>
              <zip_code>{$order_info['payment_postcode']}</zip_code>
              <country>{$order_info['payment_iso_code_2']}</country>
            </BillingDetails>
          </OrderDetails>
          <PersonalDetails>
             <first_name>{$order_info['firstname']}</first_name>
             <surname>{$order_info['lastname']}</surname>
             <telephone>{$order_info['telephone']}</telephone>
          </PersonalDetails>
          <ShippingDetails>
            <title></title>
            <first_name>{$order_info['shipping_firstname']}</first_name>
            <surname>{$order_info['shipping_lastname']}</surname>
            <address_line1>{$order_info['shipping_address_1']}</address_line1>
            <address_line2>{$order_info['shipping_address_2']}</address_line2>
            <city>{$order_info['shipping_city']}</city>
            <country>{$order_info['shipping_iso_code_2']}</country>
            <zip_code>{$order_info['shipping_postcode']}</zip_code>
          </ShippingDetails>
          <PaymentDetails>
            <payment_method>CC</payment_method>
          </PaymentDetails>
          <RiskDetails>
            <email_address>{$order_info['email']}</email_address>
            <ip_address>{$order_info['ip']}</ip_address>
          </RiskDetails>
        </CustomerDetails>
      </Action>
     </Risk>
     <merchantreference>{$merchantReferenceId}</merchantreference>
     <ThreeDSecure>
        <purchase_datetime>{$date}</purchase_datetime>
        <merchant_url>{$shopUrl}</merchant_url>
        <purchase_desc>Invoice nr: {$merchantReferenceId}</purchase_desc>
        <verify>yes</verify>
     </ThreeDSecure>
     <capturemethod>ecomm</capturemethod>
     <amount currency="EUR">{$purchaseAmount}</amount>
   </TxnDetails>
   <HpsTxn>
     <method>setup_full</method>
     <page_set_id>{$page_set_id}</page_set_id>
     <return_url>{$reURL}</return_url>
     <expiry_url>{$exURL}</expiry_url>
     <error_url>{$errURL}</error_url>
     <DynamicData>
    <dyn_data_3></dyn_data_3>
    <dyn_data_4>{$order_info['store_url']}</dyn_data_4>
        <dyn_data_5>{$lang}</dyn_data_5>
    <dyn_data_6>visaelectron_maestro_visa_mastercard</dyn_data_6>
    <dyn_data_7>Data processed by Swedbank</dyn_data_7>
    <dyn_data_8></dyn_data_8>
    <dyn_data_9></dyn_data_9>
</DynamicData>
   </HpsTxn>
   <CardTxn>
      <method>auth</method>
   </CardTxn>
</Transaction>
</Request>

EOL;

        $this->logger($xml);

        $xml = $this->curOp($envUrl, $xml);

        $this->logger($xml);

        try {
            $object = new SimpleXMLElement($xml);
        } catch (Exception $exc) {
            $this->logger('Failed parse xml');
            return false;
        }

        if ((int) $object->status === 1) {

            $url = ((string) $object->HpsTxn->hps_url[0]) . '?HPS_SessionID=' . ((string) $object->HpsTxn->session_id[0]);
        } else
            return false;

        header('Location: ' . $url);

        die;
    }


    private function getLoginParam($oD){

        $serviceType = null;
        $paymentmethod = null;

        if ($oD === 'lt_card' || $oD === 'lt_dnb' || $oD === 'lt_seb' || $oD === 'lt_swedbank') {
            if ($oD === 'lt_swedbank') {
                $paymentmethod = 'SW';
                $serviceType = '<ServiceType>LIT_BANK</ServiceType>';
            } else if ($oD === 'lt_seb') {
                $paymentmethod = 'SE';
                $serviceType = '<ServiceType>SEB_LIT</ServiceType>';
            } else if ($oD === 'lt_dnb') {
                $paymentmethod = 'DN';
                $serviceType = '';
            }
            if ((int) $this->config->get('swedbank_testmode_lt') === 1) {
                $vtid = $this->config->get('swedbank_vtid_lt');
                $psw = $this->config->get('swedbank_pass_lt');
                $envUrl = $this->config->get('swedbank_live_url');
                $test = false;
            } else {
                $vtid = $this->config->get('swedbank_testvtid_lt');
                $psw = $this->config->get('swedbank_testpass_lt');
                $envUrl = $this->config->get('swedbank_test_url');
                $test = true;
            }
        } else if ($oD === 'lv_card' || $oD === 'lv_swedbank' || $oD === 'lv_seb') {
            if ($oD === 'lv_swedbank') {
                $paymentmethod = 'SW';
                $serviceType = '<ServiceType>LTV_BANK</ServiceType>';
            } else if ($oD === 'lv_seb') {
                $paymentmethod = 'SE';
                $serviceType = '<ServiceType>SEB_LTV</ServiceType>';
            }
            if ((int) $this->config->get('swedbank_testmode_lv') === 1) {
                $vtid = $this->config->get('swedbank_vtid_lv');
                $psw = $this->config->get('swedbank_pass_lv');
                $envUrl = $this->config->get('swedbank_live_url');
                $test = false;
            } else {
                $vtid = $this->config->get('swedbank_testvtid_lv');
                $psw = $this->config->get('swedbank_testpass_lv');
                $envUrl = $this->config->get('swedbank_test_url');
                $test = true;
            }
        } else if ($oD === 'ee_card') {
            $paymentmethod = 'SW';
            $serviceType = '<ServiceType>EST_BANK</ServiceType>';
            if ((int) $this->config->get('swedbank_testmode_ee') === 1) {
                $vtid = $this->config->get('swedbank_vtid_ee');
                $psw = $this->config->get('swedbank_pass_ee');
                $envUrl = $this->config->get('swedbank_live_url');
                $test = false;
            } else {
                $vtid = $this->config->get('swedbank_testvtid_ee');
                $psw = $this->config->get('swedbank_testpass_ee');
                $envUrl = $this->config->get('swedbank_test_url');
                $test = true;
            }

        } else {
            return false;
        }

        return [$vtid, $psw, $envUrl, $test, $serviceType, $paymentmethod];
    }

    private function curOp($envUrl, $xml) {


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $envUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.12) Gecko/2009070611 Firefox/3.0.12");


        //print_r($xml); die;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $respond = curl_exec($ch);
        curl_close($ch);
        return $respond;
    }


    public function payBankLink($order_info, $lang) {

        $oD = $_GET['pmethod'];

        $param = $this->getLoginParam($oD);

        If(!$param){
            return false;
        }


        $vtid = $param[0];
        $psw = $param[1];
        $envUrl = $param[2];
        $test = $param[3];
        $serviceType = $param[4];
        $paymentmethod = $param[5];

        $lang_list = ['lt','en','et','lv','ru'];

        if(array_search($lang, $lang_list) === false){
                $lang = 'en';
        }

        $merchantReferenceId = 'o' . rand(10, 99) . '_' . $order_info['order_id'];
        if(strlen($merchantReferenceId) < 6){
            $merchantReferenceId = 'o' . rand(10, 99) . '0_' . $order_info['order_id'];
        }
        $purchaseAmount = round($order_info['total'],2)*100; // Euro and cents needs to be separated by dot.
        $date = date('Ymd H:i:s');

        $return_url = $order_info['store_url'] . 'index.php?route=extension/payment/swedbank/confirmpayment&amp;mref=' . $merchantReferenceId . '&amp;pmethod=' . $oD;

        $xml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2">
   <Authentication>
      <client>{$vtid}</client>
      <password>{$psw}</password>
   </Authentication>
   <Transaction>
    <TxnDetails>
      <merchantreference>{$merchantReferenceId}</merchantreference>
    </TxnDetails>
    <HpsTxn>
      <page_set_id>1</page_set_id>
      <method>setup_full</method>
    </HpsTxn>
    <APMTxns>
      <APMTxn>
        <method>purchase</method>
        <payment_method>{$paymentmethod}</payment_method>
        <AlternativePayment version="2">
          <TransactionDetails>
            <Description>Invoice nr: {$merchantReferenceId}</Description>
            <SuccessURL>{$return_url}</SuccessURL>
            <FailureURL>{$return_url}</FailureURL>
            <Language>{$lang}</Language>
            <PersonalDetails>
                <Email>{$order_info['email']}</Email>
            </PersonalDetails>
            <BillingDetails>
              <AmountDetails>
                <Amount>{$purchaseAmount}</Amount>
                <Exponent>2</Exponent>
                <CurrencyCode>978</CurrencyCode>
              </AmountDetails>
            </BillingDetails>
          </TransactionDetails>
          <MethodDetails>
            {$serviceType}
          </MethodDetails>
        </AlternativePayment>
      </APMTxn>
    </APMTxns>
  </Transaction>
</Request>

EOL;

        $this->logger($xml);

        $xml = $this->curOp($envUrl, $xml);

        $this->logger($xml);

        try {
            $object = new SimpleXMLElement($xml);
        } catch (Exception $exc) {
            $this->logger('Failed parse xml');
            return false;
        }

        if ((int) $object->status === 1) {

            $url = ((string) $object->HpsTxn->hps_url[0]) . '?HPS_SessionID=' . ((string) $object->HpsTxn->session_id[0]);
        } else
            return false;

        header('Location: ' . $url);

        die;

    }

    public function checkPaiment($oD, $orderId) {



        $param = $this->getLoginParam($oD);

        If(!$param){
            return false;
        }


        $vtid = $param[0];
        $psw = $param[1];
        $envUrl = $param[2];
        $test = $param[3];

        $xml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2">
   <Authentication>
      <client>{$vtid}</client>
      <password>{$psw}</password>
   </Authentication>
   <Transaction>
    <HistoricTxn>
        <method>query</method>
        <reference type="merchant">{$orderId}</reference>
    </HistoricTxn>
  </Transaction>
</Request>

EOL;

        $this->logger($xml);

        $xml = $this->curOp($envUrl, $xml);

        $this->logger($xml);

        try {
            $object = new SimpleXMLElement($xml);
        } catch (Exception $exc) {
            $this->obSw->settings['debuging'] === 'yes' ? $this->log->logData('Failed parse xml') : null;
            return false;
        }

        if ((int) $object->status === 1) {

            return ((string) $object->QueryTxnResult->status[0]);
        } else
            return false;

    }


}
