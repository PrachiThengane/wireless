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
 * @package    AW_FBIntegrator
 * @version    2.1.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_FBIntegrator_Model_Customer extends Varien_Object {

    public function registerCustomer($fb_id, $data) {

        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer->setData($data);
        $customer->getGroupId();
        $password = uniqid();
        $customer->setPassword($password);
        $customer->setConfirmation($password);
        $customer->save();

        if ($customer->getConfirmation() && $customer->isConfirmationRequired()) {
            $customer->setConfirmation(null);
            $customer->save();
        }

        return $customer;
    }

}
