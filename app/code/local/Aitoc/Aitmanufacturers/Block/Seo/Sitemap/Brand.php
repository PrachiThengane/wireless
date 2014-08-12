<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitmanufacturers_Block_Seo_Sitemap_Brand extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize products collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
    	$helper = Mage::helper('aitmanufacturers');		
		$storeId = Mage::app()->getStore()->getId();

		$this->setDisplayCollection();

		$collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();				
		$subQuery = new Zend_Db_Select($collection->getConnection());
		$subQuery
			->distinct()
			->from(array('main_table' => 'aitmanufacturers'), 'manufacturer_id')
			->joinInner(array('store_default_value' => 'eav_attribute_option_value'), 'store_default_value.option_id=main_table.manufacturer_id AND store_default_value.store_id=0', null)
			->joinLeft(array('store_value' => 'eav_attribute_option_value'), "store_value.option_id=main_table.manufacturer_id AND store_value.store_id = '" . $storeId  . "'", null)
			->where('main_table.status = 1');
			
			if (Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_withproducts_only'))
			{
				$subQuery->joinInner(array('PRD' => Mage::getResourceModel('catalogindex/attribute')->getMainTable()), 'PRD.value = main_table.manufacturer_id', null);
			}	
				
		$collection->getSelect()->reset();
		$collection->getSelect()->from(array('EEE' => $subQuery), 'COUNT(*)');
		$collection->getSelect()->assemble();	
		
		$this->setCollection($collection);
		return $this;
    }
    
    public function getDisplayCollection()
    {
    	$storeId = Mage::app()->getStore()->getId();
		
		$collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();		
		$collection->addStoreFilter($storeId)->addStatusFilter()->addSortOrder();    	
            
		if (Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_withproducts_only'))
		{
			$collection->getSelect()->joinInner(
				array('PRD' => Mage::getResourceModel('catalogindex/attribute')->getMainTable()),
					'PRD.value = main_table.manufacturer_id'
            	)->group('main_table.manufacturer_id');
		}
		
		return $collection;
    }
    
    public function getItemUrl($item)
    {
    	$item->getUrl();
    }    
}
