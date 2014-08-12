<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Notifications extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('notificationsGrid');
      $this->setUseAjax(true);
      $this->setSaveParametersInSession(false);
      $this->setVarNameFilter('notifications_filter');
  }

  protected function _prepareCollection()
  {
	  $groupdeals_city = $this->getCity();
	  $_product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
	  $store_ids = array();
	  if (count($_product->getWebsiteIds())>0) {
		  $product_website_ids = $_product->getWebsiteIds();
		  foreach ($product_website_ids as $website_id) {
			  $stores_collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website_id);
			  foreach ($stores_collection as $store) {
				  $store_ids[] = $store->getId();
			  }
		  }
	  }
	  if (count($store_ids)>0) {
		  $subscribers = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', $groupdeals_city)->addFieldToFilter('store_id', array('in', $store_ids));   					
	  } else {
		  $subscribers = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', $groupdeals_city);  
	  }
	
      $this->setCollection($subscribers);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {	
  
	  $this->addColumn('email', array( 
          'header'    => Mage::helper('groupdeals')->__('Subscriber Email'),
          'align'     => 'left',
          'index'     => 'email',
          'type'      => 'text',
      ));       
	  
	  if (!Mage::app()->isSingleStoreMode()) {
		  $this->addColumn('store_id', array(
			  'header'    => Mage::helper('groupdeals')->__('Subscribed to (Store)'),
			  'index'     => 'store_id',
			  'type'      => 'store',
		      'width'     =>'200px',
			  'store_view'=> true,
			  'display_deleted' => true,
	  	  ));
	  }
	  
      $this->addColumn('new_deal', array(
          'header'    => Mage::helper('groupdeals')->__('New Deal Notification'),
          'align'     => 'left',
          'index'     => 'subscriber_id',
		  'filter' => false,
          'width'     => '100px',
          'type'      => 'options',
          'options'   => array(
              0 => 'Sent',
              1 => 'Pending',
              2 => 'Not Sent',
          ),
          'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_newdeal',
      ));
	  
      $this->addColumn('limit_met', array(
          'header'    => Mage::helper('groupdeals')->__('Target Met Notification'),
          'align'     => 'left',
          'index'     => 'subscriber_id',
		  'filter' => false,
          'width'     => '100px',
          'type'      => 'options',
          'options'   => array(
              0 => 'Sent',
              1 => 'Pending',
              2 => 'Not Sent',
          ),
          'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_limitmet',
      ));
	  
      $this->addColumn('deal_over', array(
          'header'    => Mage::helper('groupdeals')->__('Deal Over Notification'),
          'align'     => 'left',
          'index'     => 'subscriber_id',
		  'filter' => false,
          'width'     => '100px',
          'type'      => 'options',
          'options'   => array(
              0 => 'Sent',
              1 => 'Pending',
              2 => 'Not Sent',
          ),
          'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_dealover',
      ));
	    
      return parent::_prepareColumns();
  }
	
  public function getCity()
  {
      $city = '';
	  if (Mage::registry('groupdeals_data')) {
		  $city = Mage::registry('groupdeals_data')->getCity();
	  } elseif ($this->getRequest()->getParam('city') && $this->getRequest()->getParam('city')!='') {
		  $city = $this->getRequest()->getParam('city');
	  }
      return $city;
  }
	
  public function getGridUrl()
  {
      return $this->getUrl('*/*/notifications', array('_current'=>true, 'city'=>$this->getCity(), 'groupdeals_id'=>$this->getRequest()->getParam('groupdeals_id')));
  }

}