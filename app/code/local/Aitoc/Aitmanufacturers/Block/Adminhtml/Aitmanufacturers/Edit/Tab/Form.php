<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('aitmanufacturers_form', array('legend'=>Mage::helper('aitmanufacturers')->__('Brand information')));
      $storeId = $this->getStoreId();
      
      $manufacturers = Mage::getModel('aitmanufacturers/aitmanufacturers');
      
	  if (Mage::registry('aitmanufacturers_data')->getId())
	  {
	      $fieldset->addField('manufacturer', 'text', array(
              'label'     => Mage::helper('aitmanufacturers')->__('Brand'),
              'required'  => false,
              'name'      => 'manufacturer',
              'style'     => 'width:500px;',
	          'readonly'  => true,
	          'disabled'  => true,
          ));
          $fieldset->addField('manufacturer_id', 'hidden', array(
              'name'      => 'manufacturer_id',
          ));
	  }
	  else
	  {
          $fieldset->addField('manufacturer_id', 'select', array(
              'label'     => Mage::helper('aitmanufacturers')->__('Brand'),
              'class'     => 'required-entry',
              'required'  => true,
              'name'      => 'manufacturer_id',
              'values'    => $manufacturers->toManufacturersOptionsArray($storeId),
          ));
	  }
      
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Brand Page Title'),
          'required'  => false,
          'name'      => 'title',
          'style'     => 'width:500px;',
      ));
      
      if (!Mage::app()->isSingleStoreMode()) {
          $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => $storeId
            ));
            Mage::registry('aitmanufacturers_data')->setStoreId($storeId);
      }
      else {
          $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('aitmanufacturers_data')->setStoreId(Mage::app()->getStore(true)->getId());
      }
      
      $fieldset->addField('content_editor', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('aitmanufacturers')->__('Description'),
          'title'     => Mage::helper('aitmanufacturers')->__('Description'),
          'style'     => 'width:600px; height:300px;',
          'required'  => true,
          'wysiwyg'	  => Mage::getSingleton('cms/wysiwyg_config')->isEnabled(),
      	  'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(
		      array(
		          'tab_id' 					    => $this->getTabId(),
		      	  'files_browser_window_url'    => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
		      	  'add_variables'               => false,
                  'add_widgets'                 => false,
		      )
		  )
      ));
      Mage::registry('aitmanufacturers_data')->setData('content_editor', Mage::registry('aitmanufacturers_data')->getData('content'));

      $fieldset->addField('small_logo', 'file', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Small Logo'),
          'required'  => false,
          'name'      => 'small_logo',
		  'after_element_html' => (''!=Mage::registry('aitmanufacturers_data')->getData('small_logo')?'<p style="margin-top: 5px"><img src="'.Mage::getBaseUrl('media') . 'aitmanufacturers/logo/' . Mage::registry('aitmanufacturers_data')->getData('small_logo').'" /><br /><a href="'.$this->getUrl('*/*/*/', array('_current'=>true, 'delete'=>'small_logo')).'">'.Mage::helper('aitmanufacturers')->__('Delete Logo').'</a></p>':''),      	  
	  ));
	  
	  $fieldset->addField('small_logo_', 'hidden', array(
        'name'      => 'small_logo_',
      ));
      Mage::registry('aitmanufacturers_data')->setData('small_logo_', Mage::registry('aitmanufacturers_data')->getData('small_logo'));
	  
      $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Image'),
          'required'  => false,
          'name'      => 'image',
		  'after_element_html' => (''!=Mage::registry('aitmanufacturers_data')->getData('image')?'<p style="margin-top: 5px"><img src="'.Mage::getBaseUrl('media') . 'aitmanufacturers/' . Mage::registry('aitmanufacturers_data')->getData('image').'" /><br /><a href="'.$this->getUrl('*/*/*/', array('_current'=>true, 'delete'=>'image')).'">'.Mage::helper('aitmanufacturers')->__('Delete Image').'</a></p>':''),      	  
	  ));
	  
	  $fieldset->addField('image_', 'hidden', array(
        'name'      => 'image_',
      ));
      Mage::registry('aitmanufacturers_data')->setData('image_', Mage::registry('aitmanufacturers_data')->getData('image'));

      $fieldset->addField('list_image', 'file', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Brand Icon'),
          'required'  => false,
          'name'      => 'list_image',
		  'after_element_html' => (''!=Mage::registry('aitmanufacturers_data')->getData('list_image')?'<p style="margin-top: 5px"><img src="'.Mage::getBaseUrl('media') . 'aitmanufacturers/list/' . Mage::registry('aitmanufacturers_data')->getData('list_image').'" /><br /><a href="'.$this->getUrl('*/*/*/', array('_current'=>true, 'delete'=>'list_image')).'">'.Mage::helper('aitmanufacturers')->__('Delete Brand Icon').'</a></p>':''),      	  
	  ));
	  
	  $fieldset->addField('list_image_', 'hidden', array(
        'name'      => 'list_image_',
      ));
      Mage::registry('aitmanufacturers_data')->setData('list_image_', Mage::registry('aitmanufacturers_data')->getData('list_image'));
      
      $fieldset->addField('show_list_image', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Brand Icon on Brands Page?'),
          'name'      => 'show_list_image',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('aitmanufacturers')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('aitmanufacturers')->__('Yes'),
              ),
          ),
      ));
      
      $fieldset->addField('show_brief_image', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Brand Icon in Brands Block?'),
          'name'      => 'show_brief_image',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('aitmanufacturers')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('aitmanufacturers')->__('Yes'),
              ),
          ),
      ));
      
      $fieldset->addField('meta_keywords', 'textarea', array(
          'name'      => 'meta_keywords',
          'label'     => Mage::helper('aitmanufacturers')->__('Meta Keywords'),
          'title'     => Mage::helper('aitmanufacturers')->__('Meta Keywords'),
          'style'     => 'width:500px; height:100px;',
          'required'  => false,
      ));
      
      $fieldset->addField('meta_description', 'textarea', array(
          'name'      => 'meta_description',
          'label'     => Mage::helper('aitmanufacturers')->__('Meta Description'),
          'title'     => Mage::helper('aitmanufacturers')->__('Meta Description'),
          'style'     => 'width:500px; height:100px;',
          'required'  => false,
      ));
		
      $fieldset->addField('url_key', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('URL key'),
          'required'  => false,
          'name'      => 'url_key',
          'after_element_html' => '<p class="nm"><small>' . Mage::helper('aitmanufacturers')->__('(eg: domain.com/<b>url-key</b>.html)') . '</small></p>',
      ));
      
      $fieldset->addField('featured', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Featured'),
          'name'      => 'featured',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('aitmanufacturers')->__('No'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('aitmanufacturers')->__('Yes'),
              ),
          ),
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('aitmanufacturers')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('aitmanufacturers')->__('Disabled'),
              ),
          ),
      ));
      
      $fieldset->addField('sort_order', 'text', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Sort Order'),
          'required'  => false,
          'name'      => 'sort_order',
#          'after_element_html' => '<p class="nm"><small>' . Mage::helper('aitmanufacturers')->__('for right sidebar block') . '</small></p>',
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getAitmanufacturersData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAitmanufacturersData());
          Mage::getSingleton('adminhtml/session')->setAitmanufacturersData(null);
      } elseif ( Mage::registry('aitmanufacturers_data') ) {
          $form->setValues(Mage::registry('aitmanufacturers_data')->getData());
      }
      return parent::_prepareForm();
  }
  
    protected function getStoreId()
    {
        return Mage::registry('store_id');
    }
}
