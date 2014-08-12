<?php
class Mec_OneStepCheckout_Helper_Url extends Mage_Checkout_Helper_Url
{
    /**
     * Retrieve checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        if (!Mage::helper('onestepcheckout')->isRewriteCheckoutLinksEnabled()){
            return parent::getCheckoutUrl();
        }
        return $this->_getUrl('onestepcheckout', array('_secure'=>true));
    }
}
