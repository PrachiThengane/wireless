<?php

class Devinc_Groupdeals_UnsubscribeController extends Mage_Core_Controller_Front_Action
{ 
	public function indexAction() {
		$this->loadLayout()->renderLayout();
	}	
	
	public function customerAction() {
		$this->loadLayout()->renderLayout();
		$subscriber_id = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('store_id', $this->getRequest()->getParam('store'))->addFieldToFilter('email', $this->getRequest()->getParam('email'))->addFieldToFilter('city', $this->getRequest()->getParam('city'))->getFirstItem()->getSubscriberId();
		
		if ($subscriber_id!='') {
			$model = Mage::getModel('groupdeals/subscribers');
					 
			$model->setId($subscriber_id)
				->delete();
		}
					 
		$this->_redirect('groupdeals/unsubscribe/index', array('city' => $this->getRequest()->getParam('city')));
	}	
	
}