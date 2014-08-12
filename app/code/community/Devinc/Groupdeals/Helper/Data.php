<?php

class Devinc_Groupdeals_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getFormAction()
    {
        return $this->_getUrl('groupdeals/merchant/post');
    }
    
	/**
 	* @author     Ivan Weiler <ivan.weiler@gmail.com>
 	*/
	public function getConnectUrl()
	{
		return $this->_getUrl('groupdeals/customer_account/connect', array('_secure'=>true));
	}
	
	public function isFacebookCustomer($customer)
	{
		if($customer->getFacebookUid()) {
			return true;
		}
		return false;
	}
}