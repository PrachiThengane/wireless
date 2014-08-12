<?php

class AW_Points_Block_Customer_Reward_Coupon extends Mage_Core_Block_Template {
    const SUBSCRIPTION_ACTION = "couponActivation";

    protected function _construct() {
        parent::_construct();

        $magentoVersionTag = AW_Points_Helper_Data::MAGENTO_VERSION_14;

        if (Mage::helper('points')->magentoLess14()) {
            $magentoVersionTag = AW_Points_Helper_Data::MAGENTO_VERSION_13;
        }

        $this->setTemplate("aw_points/customer/" . $magentoVersionTag . "/reward/coupon.phtml");
    }

    public function getIsSubscribed() {
        $summary = Mage::getModel('points/summary')
                ->loadByCustomer(
                Mage::getSingleton('customer/session')
                ->getCustomer()
        );
        return (bool) (int) $summary->getBalanceUpdateNotification();
    }

    public function getAction() {
        return Mage::getUrl('*/*/'.self::SUBSCRIPTION_ACTION);
    }

}

?>
