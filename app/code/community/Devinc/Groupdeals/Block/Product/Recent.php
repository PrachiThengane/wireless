<?php
class Devinc_Groupdeals_Block_Product_Recent extends Mage_Core_Block_Template
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
				->addAttributeToFilter('groupdeal_status', 4)
				->setOrder('entity_id', 'DESC')
				->load();
				
        return $productCollection;
    }

    public function getProductUrl($_product)
    {			
		return $_product->getProductUrl().'?city='.urlencode($this->getCity());
    }	
	
    public function getGroupdeals($_product)
    {
		$_groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', array('eq'=>$_product->getId()))->getFirstItem();
        return $_groupdeals;
    }
	
	public function getSalesNumber($_groupdeals)
    {
		
		$sold_qty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($_groupdeals);	
		
        return $sold_qty;
    }	
	
	public function getCity() {		
		return Mage::getSingleton('core/session')->getCity();
	}
}