<?php
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/
class Aitoc_Aitmanufacturers_Model_Observer
{
    public function rewrite_urls(){
        //Mage::getSingleton('adminhtml/session')->addNotice('observer ready');
        if(Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_update_stores')===true){
            Mage::register('aitmanufacturers_update_get_stores', true);
            Mage::register('aitmanufacturers_fillout_inprogress',true);
            Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()->save();
            Mage::getSingleton('adminhtml/session')->setData('aitmanufacturers_update_stores',false);
        }
    } 
}
