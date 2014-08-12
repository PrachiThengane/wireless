<?php
class Devinc_Groupdeals_CouponsController extends Mage_Core_Controller_Front_Action
{ 
	public function indexAction() {
        $this->loadLayout();   
        
		$loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Coupons'));
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('groupdeals/coupons/index/');
        }
        
		$this->renderLayout();
	}

	public function viewAction() {
        $this->loadLayout();   
        
        $coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id')); 
		$item = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
		$order = Mage::getModel('sales/order')->load($item->getOrderId());
   		$customer = Mage::getSingleton('customer/session');
	
		$loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        if (!$coupon || $coupon->getStatus()!='complete' || $customer->getId()!=$order->getCustomerId()) {
            $this->_redirect('*/*');
            return;
        }
		$this->getLayout()->getBlock('head')->setTitle($this->__('Coupon - %s', $coupon->getCouponCode()));
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('groupdeals/coupons/index/');
        }

		$this->renderLayout();
	}	
	
	public function printAction()
    {
        $this->loadLayout('print');
        
        $coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id')); 
		$item = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
		$order = Mage::getModel('sales/order')->load($item->getOrderId());
   		$customer = Mage::getSingleton('customer/session');
	
		$loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        if (!$coupon || $coupon->getStatus()!='complete' || $customer->getId()!=$order->getCustomerId()) {
            $this->_redirect('*/*');
            return;
        }
		$this->getLayout()->getBlock('head')->setTitle($this->__('Print Coupon - %s', $coupon->getCouponCode()));
        
        $this->renderLayout();
    }	

	public function redeemViewAction() {
        $this->loadLayout();      
		$this->renderLayout();
	}	
	
	public function redeemAction()
    {
		$post = $this->getRequest()->getPost();
        if ($post)  {
            try {
				$coupon = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('coupon_code', trim($post['coupon_code']))->getFirstItem();
                
				if ($coupon->getId()>0) {
					if ($coupon->getRedeem()=='not_used') {
						$coupon->setRedeem('used')->save();
						Mage::getSingleton('core/session')->addSuccess(Mage::helper('groupdeals')->__('The "%s" Coupon has been redeemed.', $coupon->getCouponCode()));
					} else {
						Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('The "%s" Coupon has already been used.', $coupon->getCouponCode()));
					}
				} else {
					Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('The Coupon doesn\'t exist in our database.'));
				}
				
                $this->_redirect('*/*/redeemview');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/redeemview');
                return;
            }

        } else {
            $this->_redirect('*/*/redeemview');
        }
    }
	
	public function saveGiftAction()
    {
        $post = $this->getRequest()->getPost();
        if ($post)  {
            try {
            	$quote = Mage::getSingleton('checkout/session')->getQuote();
    			if ($quote) {
            		$quote->setGroupdealsCouponFrom($post['coupon_from']);
            		$quote->setGroupdealsCouponTo($post['coupon_to']);
            		$quote->setGroupdealsCouponToEmail($post['coupon_to_email']);
            		$quote->setGroupdealsCouponMessage($post['coupon_message']);
            		$quote->collectTotals()->save();
				} else {
					Mage::getSingleton('checkout/session')->addError('Unable to submit your request. Please, try again later');
				}
				$this->getResponse()->setBody(
					$this->getLayout()->getBlockSingleton('groupdeals/popup')->setTemplate("groupdeals/popup/popup-gift.phtml")->toHtml()
				);
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addError('Unable to submit your request. Please, try again later');
                return;
            }

        }
    }	
	
	public function removeGiftAction()
    {
    	$quote = Mage::getSingleton('checkout/session')->getQuote();
    	if ($quote) {
    		$quote->setGroupdealsCouponFrom('');
    		$quote->setGroupdealsCouponTo('');
    		$quote->setGroupdealsCouponToEmail('');
    		$quote->setGroupdealsCouponMessage('');
    		$quote->collectTotals()->save();
    	}
    	
    }	
}