<?php
class Devinc_Groupdeals_Block_Subscribe extends Mage_Core_Block_Template
{	
	public function getCity() {		
		$city = Mage::getSingleton('core/session')->getCity();
		$groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $city);
		
		if (count($groupdealsCollection)!=0) {
			return $city;
		} else {
			return;
		}
	}
	
	public function getFormActionUrl() {
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).'groupdeals/product/subscribe/';
	}

}