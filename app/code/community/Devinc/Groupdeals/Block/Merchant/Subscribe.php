<?php
class Devinc_Groupdeals_Block_Merchant_Subscribe extends Mage_Core_Block_Template
{
    public function getPostActionUrl()
    {
        return $this->helper('groupdeals')->getFormAction();
    }
    
}
