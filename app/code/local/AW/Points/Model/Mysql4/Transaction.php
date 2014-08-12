<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Points
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */class AW_Points_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('points/transaction', 'id');
    }

    public function loadByOrderIncrementId($transaction, $orderIncrementId) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('transaction_orderspend'))
                ->where('order_increment_id = ?', $orderIncrementId);
        $data = $this->_getReadAdapter()->fetchRow($select);
        if (isset($data['transaction_id'])) {
            $transaction->load($data['transaction_id'])->addData($data);
        }
        return $this;
    }

    public function saveSpendOrderInfo($transaction, $order) {
        $orderCustomer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $orderWebsite = $order->getStore()->getWebsite();
        $moneyForPointsBase = Mage::getModel('points/api')->changePointsToMoney($transaction->getBalanceChange(), $orderCustomer, $orderWebsite);
        $moneyForPoints = $order->getBaseCurrency()->convert($moneyForPointsBase, $order->getOrderCurrencyCode());

        $data = array(
            'transaction_id' => $transaction->getId(),
            'order_increment_id' => $order->getIncrementId(),
            'points_to_money' => $moneyForPoints,
            'base_points_to_money' => $moneyForPointsBase
        );
        $this->_getWriteAdapter()->insert($this->getTable('transaction_orderspend'), $data);
        return $this;
    }

}
