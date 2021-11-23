<?php

class ModelExtensionPaymentSwedbankEeSwedbank extends Model {

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/swedbank');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('swedbank_geo_zone_id_ee') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('swedbank_total') > 0 && $this->config->get('swedbank_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('swedbank_geo_zone_id_ee')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = [
                'code' => 'swedbank_ee_swedbank',
                'title' => $this->language->get('text_ee_title_swedbank'),
                'terms' => '',
                'sort_order' => $this->config->get('swedbank_sort_order').'.32'
            ];
        }

        return $method_data;
    }

}

