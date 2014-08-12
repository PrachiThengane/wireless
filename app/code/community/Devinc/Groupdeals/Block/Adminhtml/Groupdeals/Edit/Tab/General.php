<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);	  
	  
      $fieldset = $form->addFieldset('address_fieldset', array('legend'=>Mage::helper('groupdeals')->__('Information')));
           
	  $field = $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Name'),
            'name'      => 'product[name]',
			'style'     => 'width:594px;',
            'class'     => 'required-entry',
            'required'  => true,
      ));	  
	  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_name'));
	 				  
      $storeId = $this->getRequest()->getParam('store', 0);
	  $merchants = array();            
	  $merchants[] = array(
				'label' => '--- No Merchant ---',
				'value' => 0
	  );   
	  if ($merchant_id = Mage::getModel('groupdeals/merchants')->isMerchant()) {		
		  $merchant = Mage::getModel('groupdeals/merchants')->load($merchant_id);
		  $merchants[] = array(
			  'label' => Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->getName(), $storeId),
			  'value' => $merchant->getId()
		  );
	  } else {
		  $merchant_collection = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('status', array('neq' => 3));
			
		  foreach ($merchant_collection as $merchant) {
				$merchants[] = array(
					'label' => Mage::getModel('groupdeals/groupdeals')->getDecodeString($merchant->name, $storeId),
					'value' => $merchant->merchants_id
				);
		  }		

		  sort($merchants);	  
	  }

	  $fieldset->addField('merchant_id', 'select', array(
            'label'     => Mage::helper('groupdeals')->__('Merchant'),
            'name'      => 'merchant_id',
            'class'     => 'validate-select required-entry',
            'required'  => true,
            'values'    => $merchants,
      ));	  
	  
	  $country_collection = Mage::getModel('directory/country_api')->items();
	
      $countries = array();            
	  $countries[] = array(
                'label' => '--- Universal Deal ---',
                'value' => ''
      );   
			
      foreach ($country_collection as $country) {
            $countries[] = array(
                'label' => $country['name'],
                'value' => $country['country_id']
            );
      }		

	  sort($countries);	  

	  $fieldset->addField('country_id', 'select', array(
            'label'     => Mage::helper('groupdeals')->__('Country'),
            'name'      => 'country_id',
			'onchange'  => 'regionReload(this.value)',
            'required'  => false,
            'values'    => $countries,
      ));	  	  
	  
	  $data = Mage::registry('groupdeals_data')->getData();
	  if (isset($data['country_id']) && $data['country_id']!='') {
		  $regionCollection = Mage::getModel('directory/region_api')->items($data['country_id']);
		  if (count($regionCollection)>0) {
			  $regionCollection = Mage::getModel('directory/region_api')->items($data['country_id']);
		
			  $regions = array();            
			  $regions[] = array(
					'label' => '',
					'value' => ''
			  );   
				
			  foreach ($regionCollection as $region) {				  
				  $regionModel = Mage::getModel('directory/region')->load($region['region_id']);
				  $region_name = $regionModel->getName();
				  $regions[] = array(
					'label' => $region_name,
					'value' => $region_name
				  );
			  }		

			  sort($regions);	  

			  $field = $fieldset->addField('region', 'select', array(
				  'label'     => Mage::helper('groupdeals')->__('State/Province'),
				  'name'      => 'region',
				  'class'     => 'required-entry',
				  'required'  => true,
				  'values'    => $regions,
			  ));		
			  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_region'));
		  } else {
			  $field = $fieldset->addField('region', 'text', array(
					'label'     => Mage::helper('groupdeals')->__('State/Province'),
					'name'      => 'region',
					'required'  => true,
				    'class'     => 'required-entry',
			  ));	
			  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_region'));
		  }
	  } else {
		  $field = $fieldset->addField('region', 'text', array(
				'label'     => Mage::helper('groupdeals')->__('State/Province'),
				'name'      => 'region',
				'class'     => 'required-entry',
				'required'  => true,
		  ));	
		  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_region'));
	  }
	  
	  $fieldset->addField('city', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('City'),
            'name'      => 'city',
            'class'     => 'required-entry',
            'required'  => true,
      ));	
      	  
 	  //setting the date format type for the date fields
 	  if (substr(Mage::app()->getLocale()->getLocaleCode(),0,2)!='en') {
		  $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
				Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
		  );
	  } else {		
		  $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
				Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
		  );
	  }	
	  
	  $fieldset->addField('datetime_from', 'date', array(
          'name'      => 'datetime_from',
          'time'      => true,
          'label'     => Mage::helper('groupdeals')->__('Date/Time From'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),
          'class'     => 'required-entry',
          'required'  => true,
          'format'    => $dateFormatIso,
          'style'	  => 'width:162px !important',	
      ));
	
      $fieldset->addField('datetime_to', 'date', array(
          'name'      => 'datetime_to',
          'time'      => true,
          'label'     => Mage::helper('groupdeals')->__('Date/Time To'),
          'image'     => $this->getSkinUrl('images/grid-cal.gif'),
          'class'     => 'required-entry',
          'required'  => true,
          'format'    => $dateFormatIso,
          'style'	  => 'width:162px !important',	
      ));		
	  
	  $field = $fieldset->addField('groupdeal_fineprint', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Fine Print'),
            'name'      => 'product[groupdeal_fineprint]',
			//'wysiwyg'   => true,
			//'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			'theme'     => 'simple',
			'style'     => 'width:594px; height:250px;',
            'class'     => 'required-entry',
            'required'  => true,
      ));	 
	  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_fineprint'));
	  
	  $field = $fieldset->addField('groupdeal_highlights', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Highlights'),
            'name'      => 'product[groupdeal_highlights]',
			//'wysiwyg'   => true,
			//'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
			'theme'     => 'simple',
			'style'     => 'width:594px; height:250px;',
            'class'     => 'required-entry', 
            'required'  => true,
      ));	
	  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_highlights'));
	 			
	  
	  $type = $this->getRequest()->getParam('type');
	  $product_type = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'))->getTypeId();
      if ($type=='simple' || $product_type=='simple') {      
		  $field = $fieldset->addField('weight', 'text', array(
    	        'label'     => Mage::helper('groupdeals')->__('Weight'),
        	    'name'      => 'product[weight]',
				'style'     => 'width:594px;',
	            'class'     => 'required-entry',
    	        'required'  => true,
	      ));	
      }  
      
      if ($type=='virtual' || $product_type=='virtual') {
      	$field = $fieldset->addField('target_met_email', 'select', array(
	  		  'label'     => Mage::helper('groupdeals')->__('Send Coupons to Invoiced Orders after the Target has been met'),
	  		  'name'      => 'target_met_email',
	  		  'class'     => 'validate-select',
	  		  'required'  => true,
	  		  'values'    => array(
	  			  array(
	  				  'value'     => 0,
	  				  'label'     => Mage::helper('groupdeals')->__('No'),
	  			  ),
	  		      array(
	  				  'value'     => 1,
	  				  'label'     => Mage::helper('groupdeals')->__('Yes'),
	  			  ),
	  		  ),
	  	));	  
      }
      
	  if (Mage::getModel('groupdeals/merchants')->getPermission('approve')==0 || Mage::getModel('groupdeals/merchants')->getPermission('approve')==null) {			  
		  $field = $fieldset->addField('status', 'select', array(
			  'label'     => Mage::helper('groupdeals')->__('Status'),
			  'name'      => 'product[status]',
			  'class'     => 'validate-select',
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
			  ),
		  ));	  
	  } else {		
          $fieldset->addField('status', 'hidden',
              array(
                  'name'  => 'product[status]',
              )
          );
	  }
	  
	  $fieldset->addField('groupdeal_status', 'hidden',
		  array(
			  'name'  => 'product[groupdeal_status]',
		  )
	  );
	  $fieldset->addField('groupdeal_type', 'hidden',
		  array(
			  'name'  => 'product[groupdeal_type]',
		  )
	  );
	  
/*
      $fieldset->addField('type_id', 'hidden',
          array(
              'name'  => 'product[type_id]',
          )
      );
*/
	  
	  $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_status'));
	  
     
      if ( Mage::getSingleton('adminhtml/session')->getGroupdealsData() ) {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGroupdealsData());
          Mage::getSingleton('adminhtml/session')->setGroupdealsData(null);
      } elseif ( Mage::registry('groupdeals_data') ) {
		  $data = array_merge(Mage::registry('groupdeals_data')->getData(), Mage::registry('product')->getData());			
		  if (Mage::getModel('groupdeals/merchants')->isMerchant() && Mage::getModel('groupdeals/merchants')->getPermission('approve')==1) {
			  $data['status'] = 2;
			  $data['groupdeal_status'] = 5;
			  $data['groupdeal_type'] = 0;
		  } else {
		  	  $data['groupdeal_status'] = 2;
			  $data['groupdeal_type'] = 0;
		  }
          $form->setValues($data);
      }
      return parent::_prepareForm();
  }
}