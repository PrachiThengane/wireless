<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Preview extends Mage_Adminhtml_Block_Widget_Form
{  
  const CSV_SEPARATOR = ',';

  public function __construct()
  {
      parent::__construct();
      $this->setTemplate('groupdeals/preview.phtml');
  }
  
  public function getCouponHtml()
  {
    $groupdeals = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('product_id', array('eq' => $this->getRequest()->getParam('id')))->getFirstItem();
	$storeId = $this->getRequest()->getParam('store', 0);
	if ($this->getRequest()->getParam('coupon_id', 0)!=0) {
		$coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id'));
		$order_item = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
		$order = Mage::getModel('sales/order')->load($order_item->getOrderId());
		$customer_name = $order->getBillingAddress()->getName();
		$html = Mage::getModel('groupdeals/coupons')->getCouponHtml($groupdeals, $coupon, $order_item, $customer_name, $storeId);
	} else {
		$html = Mage::getModel('groupdeals/coupons')->getCouponHtml($groupdeals, null, null, 'JOHN DOE', $storeId);
	}
	
	return $html;
  }
}