<?php

class Devinc_Groupdeals_Model_Merchants extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/merchants');
    }
	
    public function isMerchant()
    {
        $user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$merchant = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('user_id', $userId)->getFirstItem();
		if ($merchant->getId()) {
			return $merchant->getId();
		} else {
			return false;
		}
    }
	
    public function getPermission($type)
    {
        $user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$permissions = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('user_id', $userId)->getFirstItem()->getPermissions();
		$allowed = Mage::getModel('groupdeals/groupdeals')->getDecodeString($permissions, $type);
		return $allowed;
    }
}