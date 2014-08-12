<?php
class Devinc_Groupdeals_Block_Product_List_Sidedeals extends Mage_Core_Block_Template
{
	public function getItems()
    {
		$_product = Mage::registry('product');
		if (!isset($_product)) {
		   $_product_id = 0;
		} else {
		   $_product_id = $_product->getId();
		}	
		  
		$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', Mage::getSingleton('core/session')->getCity())->addFieldToFilter('product_id', array('neq'=>$_product_id))->setOrder('groupdeals_id', 'DESC');
		
		$groupdeals_product_id = array();
		  
		foreach ($groupdeals_collection as $groupdeals) { 
			$groupdeals_product_id[] = $groupdeals->getProductId();     
		}	  
		  
		$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('name')
				->addAttributeToSelect('thumbnail')
				->addAttributeToSelect('status')
				->addAttributeToSelect('price')
				->addAttributeToSelect('special_price')
				->addAttributeToSelect('groupdeal_status')
				->addStoreFilter(Mage::app()->getStore()->getId())
				->addAttributeToFilter('entity_id', array('in' => $groupdeals_product_id))
				->addAttributeToFilter('groupdeal_status', 1)
				->setOrder('entity_id', 'DESC')
				->load();
				
        return $productCollection;
    }
	
	public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
	
	public function getCity() {		
		return Mage::getSingleton('core/session')->getCity();
	}
}