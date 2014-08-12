<?php

class Mec_Navigation_Model_Mysql4_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_isPkAutoIncrement = false;
	
    public function _construct()
    {
        $this->_init('mec_navigation/attribute', 'attribute_id');
    }       
}
