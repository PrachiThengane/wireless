<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);	  
	  
      $fieldset = $form->addFieldset('merchants_general', array('legend'=>Mage::helper('groupdeals')->__('General')));           	 
	  
      $storeId = $this->getRequest()->getParam('store', 0);
	  $name = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getName(), $storeId);
	  $description = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getDescription(), $storeId);
	  $website = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getWebsite(), $storeId);
	  $email = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getEmail(), $storeId);
	  $facebook = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getFacebook(), $storeId);
	  $twitter = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getTwitter(), $storeId);
	  $phone = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getPhone(), $storeId);
	  $mobile = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getMobile(), $storeId);
	  $business_hours = Mage::getModel('groupdeals/groupdeals')->getDecodeString(Mage::registry('groupdeals_data')->getBusinessHours(), $storeId);
	  
	  Mage::registry('groupdeals_data')->setName($name);
	  Mage::registry('groupdeals_data')->setDescription($description);	  
	  Mage::registry('groupdeals_data')->setWebsite($website);	  
	  Mage::registry('groupdeals_data')->setEmail($email);	  
	  Mage::registry('groupdeals_data')->setFacebook($facebook);	  
	  Mage::registry('groupdeals_data')->setTwitter($twitter);	  
	  Mage::registry('groupdeals_data')->setPhone($phone);	  
	  Mage::registry('groupdeals_data')->setMobile($mobile);	  
	  Mage::registry('groupdeals_data')->setBusinessHours($business_hours);	  
	  
	  $field = $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Name'),
            'name'      => 'name',
            'class'     => 'required-entry',
			'style'     => 'width:594px;',
            'required'  => true,
      ));	  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  
	  $fieldset->addField('merchant_logo', 'image', array(
          'name'      => 'merchant_logo',
          'label'     => Mage::helper('groupdeals')->__('Logo'),
          'class'     => '',
          'required'  => false,
      ));
	  
	  $field = $fieldset->addField('description', 'editor', array(
            'label'     => Mage::helper('groupdeals')->__('Description'),
            'name'      => 'description',
            'class'     => 'required-entry',
			'wysiwyg'   => true,
			'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			'theme'     => 'simple',
			'style'     => 'width:594px; height:250px;',
            'required'  => true,
      ));	 	  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  	  
	  $field = $fieldset->addField('website', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Website'),
            'name'      => 'website',
			'style'     => 'width:594px;',
      ));	
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  	  
	  $field = $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Email'),
            'name'      => 'email',
            'class'     => 'required-entry',
			'style'     => 'width:594px;',
            'required'  => true,
            'note'      => 'A list will be sent to the merchants email address, once the deal is over, with all his customers coupons',
      ));		  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  
	  $field = $fieldset->addField('facebook', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Facebook link'),
            'name'      => 'facebook',
			'style'     => 'width:594px;',
      ));		  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  
	  $field = $fieldset->addField('twitter', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Twitter link'),
            'name'      => 'twitter',
			'style'     => 'width:594px;',
      ));		  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  	  
	  $field = $fieldset->addField('phone', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Phone'),
            'name'      => 'phone',
			'style'     => 'width:594px;',
      ));		  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  	  
	  $field = $fieldset->addField('mobile', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Mobile'),
            'name'      => 'mobile',
			'style'     => 'width:594px;',
      ));		  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
	  	  
	  $field = $fieldset->addField('business_hours', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Business Hours'),
            'name'      => 'business_hours',
			'style'     => 'width:594px;',
      ));	
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
		
		
	  if(!Mage::getModel('groupdeals/merchants')->isMerchant()){		
		  $fieldset->addField('status', 'select', array(
			  'label'     => Mage::helper('groupdeals')->__('Status'),
			  'name'      => 'status',
			  'class'     => 'required-entry validate-select',
			  'required'  => true,
			  'values'    => array(
				  array(
					  'value'     => 1,
					  'label'     => Mage::helper('groupdeals')->__('Enabled'),
				  ),

				  array(
					  'value'     => 2,
					  'label'     => Mage::helper('groupdeals')->__('Disabled'),
				  ),
				  
				  array(
					  'value'     => 3,
					  'label'     => Mage::helper('groupdeals')->__('Pending Approval'),
				  ),
			  ),
		  ));
	  }
     
      if ( Mage::getSingleton('adminhtml/session')->getGroupdealsData() ) {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGroupdealsData());
          Mage::getSingleton('adminhtml/session')->setGroupdealsData(null);
      } elseif ( Mage::registry('groupdeals_data') ) {
          $form->setValues(Mage::registry('groupdeals_data')->getData());
      }
      return parent::_prepareForm();
  }
}