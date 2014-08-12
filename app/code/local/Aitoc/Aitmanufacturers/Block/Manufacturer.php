<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Manufacturer extends Mage_Core_Block_Template
{
    protected $_manufacturer = null;
    
    public function __construct()
    {
        if (!$this->_manufacturer)
            $this->_manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($this->getRequest()->getParam('id'));
            
        $processor = Mage::getModel('core/email_template_filter');
        $html = $processor->filter(nl2br($this->_manufacturer->getContent()));
        $this->_manufacturer->setContent($html);
    }

    protected function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
        $breadcrumbs->addCrumb('manufacturers', array('label'=>Mage::helper('aitmanufacturers')->__('All Brands'), 'title'=>Mage::helper('aitmanufacturers')->__('Go to All Brands List'), 'link'=>Mage::helper('aitmanufacturers')->getManufacturersUrl()));
        $breadcrumbs->addCrumb('manufacturer', array('label'=>$this->_manufacturer->getManufacturer(), 'title'=>$this->_manufacturer->getManufacturer()));

        if ($root = $this->getLayout()->getBlock('root')) {
            $template = (string)Mage::getConfig()->getNode('global/aitmanufacturers/layouts/'.$this->_manufacturer->getRootTemplate().'/template');
            $root->setTemplate($template);
            $root->addBodyClass('aitmanufacturers-'.$this->_manufacturer->getUrlKey());
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($this->_manufacturer->getTitle());
            if ($this->_manufacturer->getMetaKeywords())
                $head->setKeywords($this->_manufacturer->getMetaKeywords());
            if ($this->_manufacturer->getMetaDescription())
            $head->setDescription($this->_manufacturer->getMetaDescription());
        }
    }
    
    public function getManufacturer()
    {
        return $this->_manufacturer;
    }
}
