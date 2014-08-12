<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Manufacturers_Brief extends Mage_Core_Block_Template
{
    public function getItems()
    {
        if (Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_from_category_only') && !is_null(Mage::registry('current_category')))
        {
            return $this->getCategoryItems();
        }
        
        if (!Mage::helper('aitmanufacturers')->getBriefNum())
        {
            return array();
        }
        
        if (Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_withproducts_only'))
        {
            $items = array();
            $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter()
                ->addSortOrder();
            $this->_totalNum = $collection->count();
            $collection->clear();
            $collection->load();
            
            foreach ($collection as $item)
            {
                $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($item->getManufacturerId(), Mage::app()->getStore()->getId());
                if (!empty($productIds))
                {
                    $items[] = $item;
                }
                if (count($items) >= Mage::helper('aitmanufacturers')->getBriefNum())
                {
                    return $items;
                }
            }
            
            return $items;
        } else 
        {
            $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter()
                ->addSortOrder();
            $this->_totalNum = $collection->count();
            $collection->clear();
            $collection->addLimit(Mage::helper('aitmanufacturers')->getBriefNum());
            $collection->load();
            return $collection->getItems();
        }
    }
    
    public function getCategoryItems()
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addCategoryFilter(Mage::registry('current_category'))->load();
        $productIds = array();
        foreach ($productCollection as $product)
        {
            $productIds[] = $product->getId();
        }
        $manufacturerIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getManufacturersByProducts($productIds, Mage::app()->getStore()->getId());
        if (empty($manufacturerIds))
        {
            return array();
        }
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
                                                                         ->addStoreFilter(Mage::app()->getStore()->getId())
                                                                         ->addStatusFilter()
                                                                         ->addSortOrder()
                                                                         ->addFieldToFilter('main_table.manufacturer_id', array('in' => $manufacturerIds));
        $this->_totalNum = $collection->count();
        $collection->clear();
        $collection->addLimit(Mage::helper('aitmanufacturers')->getBriefNum());
        $collection->load();
        return $collection->getItems();
    }
    
    public function getTotal()
    {
        return $this->_totalNum;
    }
}
