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
 */class AW_Points_Model_Rule extends Mage_Rule_Model_Rule {

    public function _construct() {
        Mage_Rule_Model_Rule::_construct();
        $this->_init('points/rule');
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance() {
        return Mage::getModel('points/rule_condition_combine');
    }

    public function getResourceCollection() {
        return Mage::getResourceModel('points/rule_collection');
    }

    public function checkRule($quote) {
        if (!$this->getIsActive())
            return false;
        $this->afterLoad();
        return $this->validate($quote);
    }

    public function loadPost(array $rule) {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions']))
            $this->getConditions()->loadArray($arr['conditions'][1]);
        return $this;
    }

    public function toString($format='') {
        $helper = Mage::helper('points');
        $str = $helper->__('Name: %s', $this->getName()) . "\n"
                . $helper->__('Start at: %s', $this->getStartAt()) . "\n"
                . $helper->__('Expire at: %s', $this->getExpireAt()) . "\n"
                . $helper->__('Description: %s', $this->getDescription()) . "\n\n"
                . $this->getConditions()->toStringRecursive() . "\n\n";
        return $str;
    }

}
