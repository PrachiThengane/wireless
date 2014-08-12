<?php

class Mec_OneStepCheckout_Block_Valid extends Mage_Core_Block_Template
{

	public function getMeccode()
    {
        return Mage::getSingleton('onestepcheckout/type_standard');
    }


    public function _toHtml()
    {
	  //Mage::log($this->getMeccode()->getTest());
      return $this->getMeccode()->getHtml();
		
    }
}

