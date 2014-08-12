<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('aitmanufacturers')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('aitmanufacturers')->__('Disabled')
        );
    }
}
