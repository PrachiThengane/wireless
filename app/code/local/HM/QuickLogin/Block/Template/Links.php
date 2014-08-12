<?php
class HM_QuickLogin_Block_Template_Links extends Mage_Page_Block_Template_Links
{
	protected function _construct()
    {
        if(Mage::getStoreConfig('hm_quicklogin/general/enable')==false ){
    		$this->setTemplate('page/template/links.phtml');
        }else{
        	$this->setTemplate('quicklogin/links.phtml');
        }
    }
}