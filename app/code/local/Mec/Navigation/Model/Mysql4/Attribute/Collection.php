<?php

class Mec_Navigation_Model_Mysql4_Attribute_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mec_navigation/attribute');
    }      
}    
        
