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
 */class AW_Points_Model_Cron {

    /**
     *  Cron launches  the function once a day (see crontab settings in config.xml)
     */
    public function checkAndCleanExpiredTransactions() {

        $this
                ->_cleanExpiredTransactions()
                ->_sendWarningLetter();
    }

    /**
     * Cleans expired transactions. If transaction is expired, balance_change_spent becomes = balance_change
     */
    protected function _cleanExpiredTransactions() {
        // Remove old locks
        Mage::getModel('points/transaction')->getCollection()->addOldLockedFilter()->unlock();

        // Lock table for writing
        $expiredTransactions = Mage::getModel('points/transaction')->getCollection()
                ->addBalanceActiveFilter()
                ->addNotLockedFilter()
                ->addExpiredFilter();

        $expiredTransactions->getResource()->getReadConnection()->raw_query('LOCK TABLE ' . $expiredTransactions->getMainTable() . " as main_table READ");

        $ids = array();
        foreach ($expiredTransactions as $tr) {
            $ids[] = $tr->getId();
        }

        $expiredTransactions->getResource()->getReadConnection()->raw_query('UNLOCK TABLES');

        // Set lock
        $expiredTransactions->lock();

        foreach ($ids as $transactionId) {
            $transaction = Mage::getModel('points/transaction')->load($transactionId);
            $result = Mage::getModel('points/api')->addTransaction(
                    $transaction->getBalanceChangeSpent() - $transaction->getBalanceChange(), 'transaction_expired', $transaction->getCustomer(), $transaction, array('transaction_id' => $transaction->getId())
            );
        }


        return $this;
    }

    protected function _sendWarningLetter() {
        if (Mage::helper('points/config')->getIsEnabledNotifications()) {

            //Send points expire email before
            $sendBeforeDays = Mage::helper('points/config')->getDaysBeforePointExpiredToSendEmail();

            $transactionsToWarn = Mage::getModel('points/transaction')
                    ->getCollection()
                    ->addBalanceActiveFilter()
                    ->addExpiredAfterDaysFilter($sendBeforeDays);

            $_currentSummaryId = -1;
            foreach ($transactionsToWarn as $transaction) {

                $isEnabledExpirationNotification = (bool) (int) Mage::getModel('points/summary')
                                ->load($transaction
                                        ->getSummaryId()
                                )
                                ->getPointsExpirationNotification();

                // this condition avoid more than once email sending if collection $transactionsToWarn is sorted
                if ($_currentSummaryId != $transaction->getSummaryId() && $isEnabledExpirationNotification) {
                    $_currentSummaryId = $transaction->getSummaryId();

                    $pointsGoingToExpire = $this->_getPointsSummFor($_currentSummaryId, $transactionsToWarn);

                    $customer = $transaction->getCustomer();
                    if ($transaction->getStoreId()) {
                        $store = Mage::app()->getStore($transaction->getStoreId());
                    } else {
                        $store = $customer->getStore();
                    }

                    $mail = Mage::getModel('core/email_template');
                    $pointUnitName = Mage::helper('points/config')->getPointUnitName($store->getStoreId());

                    $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getStoreId()))
                            ->sendTransactional(
                                    Mage::helper('points/config')->getPointsExpireTemplate($store->getStoreId()), Mage::helper('points/config')->getNotificatioinSender($store->getStoreId()), $transaction->getCustomer()->getEmail(), null, array(
                                'store' => $store,
                                'customer' => $customer,
                                'pointsname' => $pointUnitName,
                                'pointstoexpire' => $pointsGoingToExpire,
                                'expirationdays' => $sendBeforeDays
                            ));

                    $transaction->setData('expiration_notification_sent', true)->save();

                    if (!$mail->getSentSuccess()) {
                        Mage::helper('awcore/logger')->log($this, Mage::helper('points')->__('Unable to send %s expiration email.', $pointUnitName), AW_Core_Model_Logger::LOG_SEVERITY_WARNING
                        );
                    }
                }
            }
            return $this;
        }
    }

    private function _getPointsSummFor($_currentSummaryId, $transactionsToWarn) {

        $sum = 0;

        foreach ($transactionsToWarn as $transaction) {
            if ($transaction->getSummaryId() == $_currentSummaryId) {
                $sum+=$transaction->getBalanceChange();
            }
        }
        return $sum;
    }

}
