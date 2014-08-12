<?php
/**
 * Facebook connect template block
 * 
 * @author     Ivan Weiler <ivan.weiler@gmail.com>
 */
class Devinc_Groupdeals_Block_Facebook_Template extends Mage_Core_Block_Template
{
	
	public function isSecure()
	{
		return Mage::app()->getStore()->isCurrentlySecure();
	}
	
	public function getConnectUrl()
	{
		return $this->getUrl('groupdeals/customer_account/connect', array('_secure'=>true));
	}
	
	public function getChannelUrl()
	{
		return $this->getUrl('groupdeals/channel', array('_secure'=>$this->isSecure(),'locale'=>$this->getLocale()));
	}	
	
	public function getRequiredPermissions()
	{
		return json_encode('email');
	}
	
	public function isEnabled()
	{
		return Mage::getSingleton('groupdeals/facebook_config')->isEnabled();
	}
	
	public function getApiKey()
	{
		return Mage::getSingleton('groupdeals/facebook_config')->getApiKey();
	}
	
	public function getLocale()
	{
		return Mage::getSingleton('groupdeals/facebook_config')->getLocale();
	}
	
    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
	
}