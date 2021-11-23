<?php


class ModelExtensionPaymentSwedbank extends Model {

    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "swedbank_order` (
			  `swedbank_order_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `order_id` INT(11) NOT NULL,
			  `order_code` VARCHAR(50),
			  `date_added` DATETIME NOT NULL,
			  `date_modified` DATETIME NOT NULL,
			  `refund_status` INT(1) DEFAULT NULL,
			  `currency_code` CHAR(3) NOT NULL,
			  `total` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`swedbank_order_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "swedbank_order_transaction` (
			  `swedbank_order_transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `swedbank_order_id` INT(11) NOT NULL,
			  `date_added` DATETIME NOT NULL,
			  `type` ENUM('payment', 'refund') DEFAULT NULL,
			  `amount` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`swedbank_order_transaction_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "swedbank_order`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "swedbank_order_transaction`;");
    }

    public function getOrder($order_id) {

        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "swedbank_order` WHERE `order_id` = '" . (int) $order_id . "' LIMIT 1");

        if ($qry->num_rows) {
            $order = $qry->row;
            $order['transactions'] = $this->getTransactions($order['swedbank_order_id'], $qry->row['currency_code']);

            return $order;
        } else {
            return false;
        }
    }

    public function getSwedbankOrderID($order_id) {

        $qry = $this->db->query("SELECT swedbank_order_id FROM `" . DB_PREFIX . "swedbank_order` WHERE `order_code` = '" . $this->db->escape($order_id) . "' LIMIT 1");
        $this->logger("SELECT swedbank_order_id FROM `" . DB_PREFIX . "swedbank_order` WHERE `order_code` = '" . $this->db->escape($order_id) . "' LIMIT 1");
        if ($qry->num_rows) {
            $order = $qry->row;

            return $order['swedbank_order_id'];
        } else {
            return false;
        }
    }

    private function getTransactions($swedbank_order_id, $currency_code) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "swedbank_order_transaction` WHERE `swedbank_order_id` = '" . (int) $swedbank_order_id . "'");

        $transactions = array();
        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $row['amount'] = $this->currency->format($row['amount'], $currency_code, true);
                $transactions[] = $row;
            }
            return $transactions;
        } else {
            return false;
        }
    }

    public function addTransaction($swedbank_order_id, $type, $total) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "swedbank_order_transaction` SET `swedbank_order_id` = '" . (int) $swedbank_order_id . "', `date_added` = now(), `type` = '" . $this->db->escape($type) . "', `amount` = '" . (double) $total . "'");
    }

    public function getTotalReleased($swedbank_order_id) {
        $query = $this->db->query("SELECT SUM(`amount`) AS `total` FROM `" . DB_PREFIX . "swedbank_order_transaction` WHERE `swedbank_order_id` = '" . (int) $swedbank_order_id . "' AND (`type` = 'payment' OR `type` = 'refund')");

        return (double) $query->row['total'];
    }

    public function logger($message) {
        if ((int) $this->config->get('swedbank_selectTets') === 1) {
            if ($this->config->get('swedbank_test_debug')) {
                $log = new Log('swedbank_debug_test.log');
                $log->write($message);
            }
        } else {
            if ($this->config->get('swedbank_test_debug_live')) {
                $log = new Log('swedbank_debug_live.log');
                $log->write($message);
            }
        }
    }

}


