<?php
class Devinc_Groupdeals_Block_Product_List_Universaldeals extends Mage_Core_Block_Template
{
	public function getItems()
    {
		if (Mage::getSingleton('core/session')->getCity()!='Universal') {
			$_product = Mage::registry('product');
			if ($_product) {
				$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', 'Universal')->addFieldToFilter('product_id', array('neq'=>$_product->getId()))->setOrder('groupdeals_id', 'DESC');
			} else {
				$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', 'Universal')->setOrder('groupdeals_id', 'DESC');
			}

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
		
		return;
    }
	
	public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
	
	public function getCity() {		
		return 'Universal';
	}
}