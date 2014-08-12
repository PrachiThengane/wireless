<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitmanufacturers_Block_Product_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    /**
     * Remove order from available orders if exists
     *
     * @param string $order
     * @param Mage_Catalog_Block_Product_List_Toolbar
     */
    public function removeOrderFromAvailableOrders($order)
    {
        if (isset($this->_availableOrder[$order])) {
            unset($this->_availableOrder[$order]);
        }
        return $this;
    }
    
    public function _construct()
    { 
        $this->setDefaultDirection('asc');
        $this->setDefaultOrder('position');
        parent::_construct();
    }  
}
