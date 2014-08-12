<?php

class Devinc_Groupdeals_Model_Product_Type extends Varien_Object
{
    const TYPE_VIRTUAL		= 'virtual';
    const TYPE_SIMPLE		= 'simple';
    const TYPE_CONFIGURABLE	= 'configurable';

    static public function getOptionArray()
    {
        return array(
            self::TYPE_VIRTUAL   	   => Mage::helper('groupdeals')->__('Virtual Product (Coupon)'),
            self::TYPE_SIMPLE    	   => Mage::helper('groupdeals')->__('Simple Product'),
            self::TYPE_CONFIGURABLE    => Mage::helper('groupdeals')->__('Configurable Product'),
        );
    }
}