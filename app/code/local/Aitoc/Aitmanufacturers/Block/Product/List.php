<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
class Aitoc_Aitmanufacturers_Block_Product_List extends Mage_Catalog_Block_Product_List //Mage_Catalog_Block_Product_Abstract
{
    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;
    protected $_manufacturer;
    
    public function __construct(){
        $manufacturers = Mage::registry('aitmanufacturers_manufacturers');
        if (isset($manufacturers[$this->getRequest()->getParam('id')])){
            $this->_manufacturer = $manufacturers[$this->getRequest()->getParam('id')];
        }
        else {
            $this->_manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($this->getRequest()->getParam('id'))
                ->getManufacturerId();
            $manufacturers[$this->getRequest()->getParam('id')] = $this->_manufacturer;
            Mage::register('aitmanufacturers_manufacturers', $manufacturers);
        }
    }
    
    public function getToolbarBlock()
    {
    	$block = parent::getToolbarBlock();
    	
    	$sortAttribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product','aitmanufacturers_sort');
        
	    $block->removeOrderFromAvailableOrders('position');
            
        $newOrders = $block->getAvailableOrders();
        $newOrders = array_reverse($newOrders, true);
        $newOrders['aitmanufacturers_sort'] = Mage::helper('catalog')->__($sortAttribute->getFrontend()->getLabel());
        $newOrders = array_reverse($newOrders, true);
        
       $block->setAvailableOrders($newOrders);

		if($block->getDefaultOrder()=='position') {
			$block->setDefaultOrder('aitmanufacturers_sort');
		}
    	
    	return $block;
    }
    
	/**
     * Retrieve loaded product collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getResourceModel('catalog/product_collection');
            $attributes = Mage::getSingleton('catalog/config')
                ->getProductAttributes();
            $collection->addAttributeToSelect($attributes)
                ->addAttributeToSelect('sort')
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter()
                ->joinAttribute('sort', 'catalog_product/aitmanufacturers_sort', 'entity_id', null, 'left')
                ;
            $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($this->_manufacturer, Mage::app()->getStore()->getId());
            //$collection->addAttributeToFilter(Mage::helper('aitmanufacturers')->getAttributeCode(), array('eq' => $this->_manufacturer), 'left');
            $collection->addIdFilter($productIds);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
    
    protected function _toHtml()
    {
        if ($this->_getProductCollection()->count()){
            return parent::_toHtml();
        }
        return '';
    }
}
