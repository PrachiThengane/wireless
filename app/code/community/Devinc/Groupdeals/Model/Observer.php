<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout observer model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Groupdeals_Model_Observer
{		
    /* public function hookToControllerActionPreDispatch($observer)
    {
		$actionName = $observer->getEvent()->getControllerAction()->getFullActionName();
		
        if ($actionName == 'checkout_onepage_saveBilling' || $actionName == 'checkout_onepage_saveShipping' || $actionName == 'checkout_onepage_savePayment' || $actionName == 'checkout_onepage_shippingMethod' || $actionName == 'checkout_onepage_review' || $actionName == 'checkout_multishipping_addresses' || $actionName == 'checkout_multishipping_shipping' || $actionName == 'checkout_multishipping_billing' || $actionName == 'checkout_multishipping_overview') {
            Mage::dispatchEvent("groupdeals_observer_cart_refresh", array('request' => $observer->getControllerAction()->getRequest()));
        }
    }  */
	 
    public function hookToControllerActionPostDispatch($observer)
    {
		$actionName = $observer->getEvent()->getControllerAction()->getFullActionName();
		
        if ($actionName == 'adminhtml_process_massReindex' || $actionName == 'adminhtml_process_reindexProcess' || $actionName == 'adminhtml_catalog_category_save' || $actionName == 'adminhtml_catalog_product_save') {
            Mage::dispatchEvent("refresh_indexes", array('request' => $observer->getControllerAction()->getRequest()));
        }
        
        if ($actionName == 'adminhtml_sales_order_invoice_save') {
            Mage::dispatchEvent("invoice_save_after", array('request' => $observer->getControllerAction()->getRequest()));
        }
    }	
	
	//Adminhtml Events
	public function merchantVerification($observer)
    {
		$ccdModel = new Mage_Core_Model_Config();
		
		if(Mage::getModel('groupdeals/merchants')->isMerchant()) {
			$ccdModel->saveConfig('groupdeals/is_merchant', 1, 'default', 0);
		} else {
			$ccdModel->saveConfig('groupdeals/is_merchant', 0, 'default', 0);
		}
	}	
	
	public function deleteDeal($observer)
    {
		$product_id = $observer->getEvent()->getProduct()->getId();		
		$groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $product_id)->getFirstItem();		
		
		$model = Mage::getModel('groupdeals/groupdeals');		 
		$model->setId($groupdeals->getId())
			->delete();
		
		return $this;
	}
	
	public function couponStatusUpdate($observer)
    {
        $request = $observer->getEvent()->getRequest()->getParams();
        $order = Mage::getModel('sales/order')->load($request['order_id']);
        
        if ($order->hasInvoices()) {
			$items = $order->getItemsCollection();
					
			foreach ($items as $item) {		
				$couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId());
				if (count($couponsCollection)>0) {		
					foreach ($couponsCollection as $coupon) {
						$coupon->setStatus('pending')->save();
					}
				}			
			}
		}
    }
	
	public function deleteCoupon($observer)
    {
        $order = $observer->getEvent()->getOrder();
		$items = $order->getItemsCollection();
		    	
		foreach ($items as $item) {		
		    $couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId());
		    if (count($couponsCollection)>0) {		
		    	foreach ($couponsCollection as $coupon) {
		    		$coupon->delete();
		    	}
		    }			
		}
    }
	
	public function refreshIndexes()
    {
        $stores = Mage::app()->getStores();		
		foreach ($stores as $_eachStoreId => $val) 
		{
			$_storeId = Mage::app()->getStore($_eachStoreId)->getId();
			
			$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection();
			
			foreach ($groupdeals_collection as $groupdeals) {
				$productId = $groupdeals->getProductId();
				$product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($productId);
				
				// Category url refresh
				if (count($product->getCategoryIds())>0) {
					foreach ($product->getCategoryIds() as $categoryId) {
						$categoryUrlRewriteId = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/'.$productId.'/category/'.$categoryId)->getFirstItem()->getId();
						
						if ($categoryUrlRewriteId!='') {
							Mage::getModel('core/url_rewrite')->load($categoryUrlRewriteId)->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdeals->getId())->save();
						}	
					}
				}
				// Category url refresh END
				
				$productUrlRewriteId = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/'.$productId)->getFirstItem()->getId();
				if ($productUrlRewriteId!='') {
					Mage::getModel('core/url_rewrite')->load($productUrlRewriteId)->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdeals->getId())->save();
				}
			}			
		}	
		
        return $this;	
    }
	
	public function setGroupdealsRedirect($observer)
    {    		
		$store = $observer->getEvent()->getStore();		
		$website = $observer->getEvent()->getWebsite();		
		$scope = 'default';
		$store_id = 0;
		$scope_id = 0;
		if ($store && $store!='') {
			$scope = 'stores';
			$scope_id = Mage::getModel('core/store')->getCollection()->addFieldToFilter('code', $store)->getFirstItem()->getId();
			$store_id = $scope_id;
		} elseif ($website && $website!='') {
			$scope = 'websites';
			$scope_id = Mage::getModel('core/website')->getCollection()->addFieldToFilter('code', $website)->getFirstItem()->getId();
			$store_id = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website)->getFirstItem()->getId();
		}
		if (Mage::getStoreConfig('groupdeals/configuration/homepage_deals', $store_id)!='') {
			$path = 'groupdeals/product/redirect';
		} else {
			$path = 'cms';
		}
		
		$ccdModel = new Mage_Core_Model_Config();
		$ccdModel->saveConfig('web/default/front', $path, $scope, $scope_id);	
		
        return;	
    }
	 
	//Frontend Events
	
	public function reviewCart($observer) {
	
		if (Mage::getStoreConfig('groupdeals/configuration/enabled')) {	
			$quote = $observer->getEvent()->getCart()->getQuote();				

			$quote_items = $quote->getAllItems();
			if (count($quote_items)>0) {			
				$qtys = array();
				
				foreach ($quote_items as $quote) {
					$groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id',$quote->getProductId())->getFirstItem();
					if ($groupdeals->getId()!='') {	
						$_product = Mage::getModel('catalog/product')->load($groupdeals->getProductId());
						if (!isset($qtys[$groupdeals->getProductId()])) {
							$qtys[$groupdeals->getProductId()] = 0;
						}
						$max_qty = $groupdeals->getMaximumQty();
						$qtys[$groupdeals->getProductId()] = $qtys[$groupdeals->getProductId()]+$quote->getQty();
						$product_name = $_product->getName();
						
						//retrieve previous orders qty
						$customer_session = Mage::getSingleton('customer/session');
						$prev_orders_qty = 0;
						if ($customer_session->isLoggedIn()) {
							$customer = Mage::getModel('customer/customer')->load($customer_session->getCustomerId());
							$sales_collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('nin' => array('holded','closed','canceled','fraud')))->addFieldToFilter('customer_email',$customer->getEmail());
							$start_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeFrom());
							$end_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeTo());
							if (count($sales_collection)>0) {
								foreach($sales_collection as $sale) {
									$sale_item_collection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('created_at', array("from" =>	$start_date_time, "to" =>  $end_date_time, "datetime" => true))->addFieldToFilter('product_id', $groupdeals->getProductId())->addFieldToFilter('order_id', $sale->getId());	
									if (count($sale_item_collection)>0) {
										foreach($sale_item_collection as $item) {							
											$prev_orders_qty = $prev_orders_qty+$item->getQtyOrdered();
										}
									}
								}
							}
						}
						
						if ($max_qty<($qtys[$groupdeals->getProductId()]+$prev_orders_qty)) {
							Mage::getSingleton('checkout/session')->getQuote()->setHasError(true);
						//	Mage::getSingleton('checkout/session')->addError('The maximum order qty available for the "'.$product_name.'" DEAL is '.$max_qty.'. Please take into account your previous purchases as well.');
						}							
					}     
				}				
			}				
		}
	}
	
	public function quoteMergeGift($observer)
    {
		$quote = $observer->getEvent()->getSource();
		$customerQuote = $observer->getEvent()->getQuote();
		
        $customerQuote->setGroupdealsCouponFrom($quote->getGroupdealsCouponFrom());
        $customerQuote->setGroupdealsCouponTo($quote->getGroupdealsCouponTo());
        $customerQuote->setGroupdealsCouponToEmail($quote->getGroupdealsCouponToEmail());
        $customerQuote->setGroupdealsCouponMessage($quote->getGroupdealsCouponMessage());
        
        return $this;
	}	
	
	public function reviewCheckout($observer) {
		if (Mage::getStoreConfig('groupdeals/configuration/enabled')) {	
			$order = $observer->getEvent()->getOrder();
        	$helper = Mage::helper('checkout');				

			$order_items = $order->getAllItems();
			if (count($order_items)>0) {			
				$qtys = array();
				
				foreach ($order_items as $item) {
					$groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id',$item->getProductId())->getFirstItem();
					if ($groupdeals->getId()!='') {	
						$_product = Mage::getModel('catalog/product')->load($groupdeals->getProductId());
						if (!isset($qtys[$groupdeals->getProductId()])) {
							$qtys[$groupdeals->getProductId()] = 0;
						}
						$max_qty = $groupdeals->getMaximumQty();
						$qtys[$groupdeals->getProductId()] = $qtys[$groupdeals->getProductId()]+$item->getQtyOrdered();
						$product_name = $_product->getName();
						
						//retrieve previous orders qty
						$prev_orders_qty = 0;
						$customer_email = $order->getCustomerEmail();
						
						$sales_collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('nin' => array('holded','closed','canceled','fraud')))->addFieldToFilter('customer_email',$customer_email);
						$start_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeFrom());
						$end_date_time = Mage::getModel('groupdeals/groupdeals')->convertDateToUtc($groupdeals->getDatetimeTo());
						if (count($sales_collection)>0) {
						    foreach($sales_collection as $sale) {
						    	$sale_item_collection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('created_at', array("from" =>	$start_date_time, "to" =>  $end_date_time, "datetime" => true))->addFieldToFilter('product_id', $groupdeals->getProductId())->addFieldToFilter('order_id', $sale->getId());	
						    	if (count($sale_item_collection)>0) {
						    		foreach($sale_item_collection as $item) {							
						    			$prev_orders_qty = $prev_orders_qty+$item->getQtyOrdered();
						    		}
						    	}
						    }
						}
						
						if ($max_qty<($qtys[$groupdeals->getProductId()]+$prev_orders_qty)) {
							//Mage::throwException($helper->__('The maximum order qty available for the "'.$product_name.'" DEAL is '.$max_qty.'. Please take into account your previous purchases as well.'));
						}					
					}     
				}				
			}				
		}
	}
	
	public function updateGroupdeal($observer)
    {
		$order = $observer->getEvent()->getOrder();
		$items = $order->getItemsCollection();
		$customer_email = $order->getCustomerEmail();
		if ($order->getStatus()=='complete') {
			$coupon_status = 'pending';
		} else {
		    $coupon_status = 'processing';
		}
				
		foreach ($items as $item) {		
			$groupdeal = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', $item->getProductId())->getFirstItem();	
			$city = $groupdeal->getCity();
			if ($groupdeal->getId()!='') {
				$sold_qty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($groupdeal);
				$sold_qty = $sold_qty+$item->getQtyOrdered();		
				Mage::getModel('groupdeals/groupdeals')->load($groupdeal->getId())->setSoldQty($sold_qty)->save();						
				
				if ($item->getIsVirtual()) {
					for ($i=0; $i<$item->getQtyOrdered(); $i++) {
						$random = rand(10e16, 10e20);
					    $coupon_code = strtoupper(base_convert($random, 10, 36));
					    $coupon = Mage::getModel('groupdeals/coupons')
					    			->setGroupdealsId($groupdeal->getId())
					    			->setOrderItemId($item->getId())
					    			->setCouponCode($coupon_code)
					    			->setRedeem('not_used')
					    			->setStatus($coupon_status)
					    			->save();
					}
				}
			}	

			$store_id = Mage::app()->getStore()->getStoreId();
			
			$subscribersCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('store_id', $store_id)->addFieldToFilter('email', $customer_email)->addFieldToFilter('city', $city);				
			if (count($subscribersCollection)==0 && $customer_email!='' && $city!='') {		
				$subscriber_data['email'] = $customer_email;
				$subscriber_data['city'] = $city;
				$subscriber_data['store_id'] = $store_id;
				
				$model = Mage::getModel('groupdeals/subscribers');							
				$model->setData($subscriber_data);			
				$model->save();		
			}			
		}
		Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
		
		return $this;
	}
}
