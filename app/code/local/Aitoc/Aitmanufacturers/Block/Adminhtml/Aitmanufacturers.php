<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers extends Mage_Adminhtml_Block_Template//Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    //$this->_controller = 'adminhtml_aitmanufacturers';
    //$this->_blockGroup = 'aitmanufacturers';
    //$this->_headerText = Mage::helper('aitmanufacturers')->__('Brands Pages Manager');
    //$this->_addButtonLabel = Mage::helper('aitmanufacturers')->__('Add Brand Page');

    //$this->_addButton('fillout', array(
    //        'label'     => Mage::helper('aitmanufacturers')->__('Fill Out Brands Pages'),
    //        'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/fillOut') .'\')',
    //        'class'     => '',
    //    ));
    parent::__construct();
  }
  
    protected function getStoreId()
    {
        return Mage::registry('store_id');
    }
  
    protected function _prepareLayout()
    {
        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitmanufacturers')->__('Add Brand Page'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/new', array('store' => $this->getStoreId()))."')",
                    'class'   => 'add'
                    ))
        );
        
        $this->setChild('fillout_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('aitmanufacturers')->__('Fill Out Brands Pages'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/fillOut', array('store' => $this->getStoreId()))."')",
                    'class'   => ''
                    ))
        );
        $this->setChild('grid', $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_grid', 'aitmanufacturers.grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    public function getFillOutButtonHtml()
    {
        return $this->getChildHtml('fillout_button');
    }
    
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
               return false;
        }
        return true;
    }
}
