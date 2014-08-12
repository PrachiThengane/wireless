<?php

class Mec_Navigation_Model_Mysql4_Attribute_Store extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('mec_navigation/attribute_store', 'id');
    }
        
}
