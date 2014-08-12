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
 */class AW_Points_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('points/rule', 'rule_id');
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        
		// Mage::log($object->getWebsiteIds());
		
		if(is_array($object->getWebsiteIds())){
			$websiteids = implode(",", $object->getWebsiteIds());
			$object->setWebsiteIds($websiteids);
		}
		
		if(is_array($object->getCustomerGroupIds())){
			$group_ids = implode(",", $object->getCustomerGroupIds());
			$object->setCustomerGroupIds($group_ids);
		}
		
		
		
        if (!$object->getFromDate()) {
            $date = Mage::app()->getLocale()->date();
            $date->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
            $object->setFromDate($date);
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
       

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        
        parent::_beforeSave($object);
    }


}
