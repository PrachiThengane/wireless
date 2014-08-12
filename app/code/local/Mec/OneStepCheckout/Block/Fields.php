<?php
class Mec_OneStepCheckout_Block_Fields extends Mec_OneStepCheckout_Block_Checkout
{
    public function _construct(){
        $this->setSubTemplate(true);
        parent::_construct();
    }
}
