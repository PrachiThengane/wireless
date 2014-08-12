<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Manufacturers_Featured extends Mage_Core_Block_Template
{
    public function getItems()
    {
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter()
            ->addFeaturedFilter()
            ->addSortOrder();
        $collection->load();
        $items = $collection->getItems();
        foreach ($items as $i => $item)
        {
            $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($item->getManufacturerId(), Mage::app()->getStore()->getId());
            if ( empty($productIds) && Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_withproducts_only') )
            {
                unset($items[$i]);
            }
        }
        return $items;
    }
}
