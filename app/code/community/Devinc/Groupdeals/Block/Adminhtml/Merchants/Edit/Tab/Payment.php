<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit_Tab_Payment extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);	  
	  
      $fieldset = $form->addFieldset('merchants_payment', array('legend'=>Mage::helper('groupdeals')->__('Payment Information')));
           	 
	  $field = $fieldset->addField('paypal_email', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('PayPal Email'),
            'name'      => 'paypal_email',
			'style'     => 'width:594px;',
      ));	
	  	  
	  $field = $fieldset->addField('authorize_info', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Authorize.net Information'),
            'name'      => 'authorize_info',
			'style'     => 'width:594px;',
      ));	
	  	  
	  $field = $fieldset->addField('bank_info', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Bank Information'),
            'name'      => 'bank_info',
			'style'     => 'width:594px;',
      ));	
	  	  
	  $field = $fieldset->addField('other', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Other Information'),
            'name'      => 'other',
			'style'     => 'width:594px;',
      ));	
		
      if ( Mage::getSingleton('adminhtml/session')->getGroupdealsData() ) {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGroupdealsData());
          Mage::getSingleton('adminhtml/session')->setGroupdealsData(null);
      } elseif ( Mage::registry('groupdeals_data') ) {
          $form->setValues(Mage::registry('groupdeals_data')->getData());
      }
      return parent::_prepareForm();
  }
}