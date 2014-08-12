<?php
class Devinc_Groupdeals_Block_Product_List extends Mage_Core_Block_Template
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
				->addAttributeToFilter('groupdeal_status', 1)
				->setOrder('entity_id', 'DESC')
				->load();
				
        return $productCollection;
    }

    public function getProductUrl($_product)
    {			
		return $_product->getProductUrl().'?city='.urlencode($this->getCity());
    }	
	
	public function getMerchantDescription($_merchant, $store_id, $limit) 
	{
		$merchant_description = Mage::getModel('groupdeals/groupdeals')->getDecodeString($_merchant->getDescription(),$store_id);
		
		return strlen($merchant_description) > $limit ? substr($merchant_description, 0, $limit - 3) . '...' : $merchant_description; 
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