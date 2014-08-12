<?php

class AW_Points_Model_Mysql4_Coupon_Transaction extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('points/coupon_transaction', 'id');
    }

    public function LoadByCouponIdCustomerId($couponTransaction, $couponId, $customerId) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('points/coupon_transaction'))
                ->where('coupon_id=?', $couponId)
                ->where('customer_id=?', $customerId)
                ->limit(1);
        if ($data = $this->_getReadAdapter()->fetchRow($select)) {
            $couponTransaction->addData($data);
        }
        return $this;
    }

}
