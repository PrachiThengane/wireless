<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher //Mage_Adminhtml_Block_Template
{
    protected $_storeIds;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitmanufacturers/store_switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName($this->__('All Store Views'));
    }
}
