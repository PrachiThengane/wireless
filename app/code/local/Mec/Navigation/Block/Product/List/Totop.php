<?php

class Mec_Navigation_Block_Product_List_Totop extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        if ($this->showToTopButton()){
        	$this->setTemplate('mec/navigation/catalog/product/list/back_to_top.phtml');
        }                               
    } 
	 
	public function showToTopButton(){
		return Mage::getStoreConfig('mec_navigation/general/back_to_top');
	}
	                    
}
