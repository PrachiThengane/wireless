<?php
class Devinc_Groupdeals_Block_Product_Upcoming extends Mage_Core_Block_Template
{
	public function getLoadedProductCollection()
    {
		$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', Mage::getSingleton('core/session')->getCity())->setOrder('groupdeals_id', 'DESC');
		
		$groupdeals_product_id = array();
		  
		foreach ($groupdeals_collection as $groupdeals) {   	
			$groupdeals_product_id[] = $groupdeals->getProductId();     
		}	  
		  
		$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('name')
				->addAttributeToSelect('small_image')
				->addAttributeToSelect('price')
				->addAttributeToSelect('special_price')
				->addAttributeToSelect('status')
				->addAttributeToSelect('groupdeal_status')
				->addStoreFilter(Mage::app()->getStore()->getId())
				->addAttributeToFilter('entity_id', array('in' => $groupdeals_product_id))
				->addAttributeToFilter('groupdeal_status', 0)
				->setOrder('entity_id', 'DESC')
				->load();
				
        return $productCollection;
    }

    public function getProductUrl($_product)
    {			
		return $_product->getProductUrl().'?city='.urlencode($this->getCity());
    }	
	
	public function getCity() {		
		return Mage::getSingleton('core/session')->getCity();
	}
}