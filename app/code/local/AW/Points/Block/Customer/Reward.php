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
 */class AW_Points_Block_Customer_Reward extends Mage_Core_Block_Template {




  /**
     * Get back url in account dashboard
     *
     * @return string
     */
    public function getBackUrl() {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

     protected function  _toHtml(){

         $magentoVersionTag = AW_Points_Helper_Data::MAGENTO_VERSION_14;

        if (Mage::helper('points')->magentoLess14())  $magentoVersionTag =AW_Points_Helper_Data::MAGENTO_VERSION_13;

        $this->setTemplate("aw_points/customer/".$magentoVersionTag."/reward.phtml");

        $html = parent::_toHtml();
        return $html;
    }

}
?>