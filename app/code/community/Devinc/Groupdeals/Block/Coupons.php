<?php
class Devinc_Groupdeals_Block_Coupons extends Mage_Core_Block_Template
{	
    public function getItemsCollection() {
   		$customer = Mage::getSingleton('customer/session');
		$itemsCollection = array();
    	$orderCollection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('customer_id', array('in'=> array($customer->getId())))->setOrder('entity_id', 'DESC');	
		if (count($orderCollection)>0) {
			foreach($orderCollection as $order){
				$orderItemsCollection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $order->getId());
				if (count($orderItemsCollection)>0) {
					foreach($orderItemsCollection as $item){
						$groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $item->getProductId())->getFirstItem();
						if($groupdeals){
							$product = Mage::getModel('catalog/product')->load($groupdeals->getProductId());
							if($product->getTypeId()=='virtual'){
								$itemsCollection[] = $item;
							}
						}
					}
				}
			}
		}	
		
		return $itemsCollection;
    }
    
    public function getCoupon($coupon_id) {
    	$coupon = Mage::getModel('groupdeals/coupons')->load($coupon_id);
		$item = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
		$groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $item->getProductId())->getFirstItem();	
		$order = Mage::getModel('sales/order')->load($item->getOrderId());
		$storeId = $order->getStoreId();
		$customer_name = $order->getBillingAddress()->getName();
		
		$html = Mage::getModel('groupdeals/coupons')->getCouponHtml($groupdeals, $coupon, $item, $customer_name, $storeId);
		
		return $html;
    }
    
    public function getValue($item) {
       	$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
       	
		return $currency_symbol.number_format($item->getPrice(),2);
    }
}