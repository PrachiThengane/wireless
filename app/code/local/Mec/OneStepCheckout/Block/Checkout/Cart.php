<?php

class Mec_OneStepCheckout_Block_Checkout_Cart extends Mage_Checkout_Block_Cart
{

    public function getCheckoutUrl()
    {
        if (!$this->helper('onestepcheckout')->isRewriteCheckoutLinksEnabled()){
            return parent::getCheckoutUrl();
        }
        return $this->getUrl('onestepcheckout', array('_secure'=>true));
    }

}
