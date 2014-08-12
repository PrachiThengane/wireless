<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * This extension is only for developers as a technology exchange
 * Based on EasyTestimonial_v1.5.8 by mage-world.com
 * Fixed the bug that when compilation has been enabled, the testimonial tab in the backend will be blank page.
 *****************************************************
 * @category   Cc
 * @package    Cc_Testimonial
 * @Author     Chimy
 */
?>
<?php

class Cc_Testimonial_Model_Status extends Varien_Object
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED    = 2;
    const STATUS_HIDDEN        = 3;

    public function addEnabledFilterToCollection($collection)
    {
        $collection->addEnableFilter(array('in'=>$this->getEnabledStatusIds()));
        return $this;
    }
    
    public function addCatFilterToCollection($collection, $cat)
    {
        $collection->addCatFilter($cat);
        return $this;
    }
    
    public function getEnabledStatusIds()
    {
        return array(self::STATUS_ENABLED);
    }
    
    public function getDisabledStatusIds()
    {
        return array(self::STATUS_DISABLED);
    }
    
    public function getHiddenStatusIds()
    {
        return array(self::STATUS_HIDDEN);
    }

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('testimonial')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('testimonial')->__('Disabled')
            //self::STATUS_HIDDEN     => Mage::helper('testimonial')->__('Hidden')
        );
    }
}
