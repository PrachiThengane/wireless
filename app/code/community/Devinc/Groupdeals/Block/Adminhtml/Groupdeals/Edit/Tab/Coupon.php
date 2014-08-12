<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Coupon extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);	  
	  
      $fieldset = $form->addFieldset('groupdeals_coupon', array('legend'=>Mage::helper('groupdeals')->__('Coupon')));
          	
	  if (substr(Mage::app()->getLocale()->getLocaleCode(),0,2)!='en') {
		  $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
				Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
		  );
	  } else {		
		  $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
				Mage_Core_Model_Locale::FORMAT_TYPE_LONG
		  );
	  }
	  
	  $fieldset->addField('coupon_expiration_date', 'date', array(
          'name'      => 'coupon_expiration_date',
          'label'     => Mage::helper('groupdeals')->__('Expiration Date'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),
          'class'     => '',
          'required'  => false,
          'format'    => $dateFormatIso
      ));
	  
      $fieldset->addField('coupon_price', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Prices'),
          'name'      => 'coupon_price',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      ));
	  
      $fieldset->addField('coupon_barcode', 'image', array(
          'name'      => 'coupon_barcode',
          'label'     => Mage::helper('groupdeals')->__('Barcode'),
          'class'     => '',
          'required'  => false,
      ));
	  
      $fieldset->addField('coupon_merchant_address', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Merchant Address'),
          'name'      => 'coupon_merchant_address',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      )); 
	  
      $fieldset->addField('coupon_merchant_contact', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Merchant Contact Info'),
          'name'      => 'coupon_merchant_contact',
          'class'     => 'validate-select',
          'note'     => 'This includes information such as phone/mobile number, email and website.',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      )); 
	  
	  $fieldset->addField('coupon_fine_print', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Fine Print'),
          'name'      => 'coupon_fine_print',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      )); 
	  
	  $fieldset->addField('coupon_highlights', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Highlights'),
          'name'      => 'coupon_highlights',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      )); 

	  $fieldset->addField('coupon_merchant_logo', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Merchant Logo'),
          'name'      => 'coupon_merchant_logo',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
		  
      )); 
	  
	  $fieldset->addField('coupon_merchant_description', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Merchant Description'),
          'name'      => 'coupon_merchant_description',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      )); 
	  
	  $fieldset->addField('coupon_business_hours', 'select', array(
          'label'     => Mage::helper('groupdeals')->__('Display Business Hours'),
          'name'      => 'coupon_business_hours',
          'class'     => 'validate-select',
          'required'  => true,
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('groupdeals')->__('Yes'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('groupdeals')->__('No'),
              ),
          ),
      )); 
	  
	 /*
 $fieldset->addField('coupon_additional_info', 'textarea', array(
          'name'      => 'coupon_additional_info',
          'label'     => Mage::helper('groupdeals')->__('Additional Info'),
          'class'     => '',
          'required'  => false,
      ));	
*/
	  
	  $field = $fieldset->addField('coupon_additional_info', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Additional Info'),
            'name'      => 'coupon_additional_info',
			'theme'     => 'simple',
			'style'     => 'width:594px; height:250px;',
            'class'     => '',
            'required'  => false,
      ));
      
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_additional'));
	  
      if ( Mage::getSingleton('adminhtml/session')->getGroupdealsData() ) {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGroupdealsData());
          Mage::getSingleton('adminhtml/session')->setGroupdealsData(null);
      } elseif ( Mage::registry('groupdeals_data') ) {
          $form->setValues(Mage::registry('groupdeals_data')->getData());
      }
      return parent::_prepareForm();
  }
}