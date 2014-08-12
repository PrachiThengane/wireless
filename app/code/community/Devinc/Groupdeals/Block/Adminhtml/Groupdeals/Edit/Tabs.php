<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('groupdeals_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('groupdeals')->__('Deal Information'));
	  
  }

  protected function _prepareLayout()
  {	  
  
	$product = $this->getProduct();
	if (!($setId = $product->getAttributeSetId())) {
		$setId = $this->getRequest()->getParam('set', null);
	}

	if ($setId && ($this->getRequest()->getParam('type', null)!='configurable' || $this->getRequest()->getParam('attributes', null)!=null)) {	
		if (!Mage::getModel('groupdeals/merchants')->isMerchant() || Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==1) {
		
			$this->addTab('information_section', array(
				  'label'     => Mage::helper('groupdeals')->__('General'),
				  'title'     => Mage::helper('groupdeals')->__('General'),
				  'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_general')->toHtml(),
			));
			
			$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
				->setAttributeSetFilter($setId)
				->load();
				
			foreach ($groupCollection as $group) {
				$attributes = $product->getAttributes($group->getId(), true);
				//$visible = false;
				
				foreach ($attributes as $key => $attribute) {
					if( !$attribute->getIsVisible() ) {
						unset($attributes[$key]);
					}
				}
				
				foreach ($attributes as $key => $attribute) {
					if ($attributes[$key]->getName()=='special_price') {
						$attributes[$key]->setIsRequired(true);
					}
					if ($attributes[$key]->getName()=='name' || $attributes[$key]->getName()=='description' || $attributes[$key]->getName()=='short_description' || $attributes[$key]->getName()=='weight' || $attributes[$key]->getName()=='sku' || $attributes[$key]->getName()=='news_from_date' || $attributes[$key]->getName()=='news_to_date' || $attributes[$key]->getName()=='status' || $attributes[$key]->getName()=='url_key' || $attributes[$key]->getName()=='visibility' || $attributes[$key]->getName()=='groupdeal_fineprint' || $attributes[$key]->getName()=='groupdeal_highlights' || $attributes[$key]->getName()=='is_imported' || $attributes[$key]->getName()=='groupdeal_status' || $attributes[$key]->getName()=='groupdeal_type' || $attributes[$key]->getName()=='special_from_date' || $attributes[$key]->getName()=='special_to_date' || $attributes[$key]->getName()=='tier_price' || $attributes[$key]->getName()=='options_container' || $attributes[$key]->getName()=='country_of_manufacture' || $attributes[$key]->getName()=='msrp_enabled' || $attributes[$key]->getName()=='msrp_display_actual_price_type' || $attributes[$key]->getName()=='msrp' || $attributes[$key]->getName()=='price_view') {
						unset($attributes[$key]);
					} 		
				}

				if (count($attributes)==0) {
					continue;
				}
				//if ($visible) {
					$this->addTab('group_'.$group->getId(), array(
						'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
						'content'   => $this->getLayout()->createBlock($this->getAttributeTabBlock())
							->setGroup($group)
							->setGroupAttributes($attributes)
							->toHtml(),
					));
				//}
			}    
			
			$this->addTab('inventory_section', array(
				  'label'     => Mage::helper('groupdeals')->__('Inventory'),
				  'title'     => Mage::helper('groupdeals')->__('Inventory'),
				  'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_inventory')->toHtml(),
			));

			/**
			 * Don't display website tab for single mode
			 */
			if (!Mage::app()->isSingleStoreMode()) {
				$this->addTab('websites', array(
					'label'     => Mage::helper('groupdeals')->__('Websites'),
					'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_websites')->toHtml(),
				));
			}		

			$this->addTab('categories', array(
				'label'     => Mage::helper('catalog')->__('Categories'),
				'url'       => $this->getUrl('adminhtml/catalog_product/categories', array('_current' => true)),
				'class'     => 'ajax',
			));

            $this->addTab('related', array(
                'label'     => Mage::helper('catalog')->__('Related Products'),
                'url'       => $this->getUrl('adminhtml/catalog_product/related', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('upsell', array(
                'label'     => Mage::helper('catalog')->__('Up-sells'),
                'url'       => $this->getUrl('adminhtml/catalog_product/upsell', array('_current' => true)),
                'class'     => 'ajax',
            ));

            $this->addTab('crosssell', array(
                'label'     => Mage::helper('catalog')->__('Cross-sells'),
                'url'       => $this->getUrl('adminhtml/catalog_product/crosssell', array('_current' => true)),
                'class'     => 'ajax',
            ));
			
			if ($this->getProduct()->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
				$this->addTab('customer_options', array(
					'label' => Mage::helper('catalog')->__('Custom Options'),
					'url'   => $this->getUrl('adminhtml/catalog_product/options', array('_current' => true)),
					'class' => 'ajax',
				));
			}	
			
			if ($product->getTypeId()=='virtual') {
				$this->addTab('coupon_section', array(
					  'label'     => Mage::helper('groupdeals')->__('Coupon'),
					  'title'     => Mage::helper('groupdeals')->__('Coupon'),
					  'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_coupon')->toHtml(),
				));	
			}
			
			if ($product->getTypeId()=='configurable') {
				$this->addTab('config_section', array(
					  'label'     => Mage::helper('groupdeals')->__('Associated Products'),
					  'title'     => Mage::helper('groupdeals')->__('Associated Products'),
					  'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_config')->toHtml(),
				));	
			}	
		}		
	  
		if (!Mage::getModel('groupdeals/merchants')->isMerchant() || Mage::getModel('groupdeals/merchants')->getPermission('sales')==1) {
			$this->addTab('orders_section', array(
				  'label'     => Mage::helper('groupdeals')->__('Orders'),
				  'title'     => Mage::helper('groupdeals')->__('Orders'),
				  'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_orders')->toHtml(),
			));
		}
		  
		if (!Mage::getModel('groupdeals/merchants')->isMerchant() || Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==1) {
			$this->addTab('notifications_section', array(
				  'label'     => Mage::helper('groupdeals')->__('Notifications'),
				  'title'     => Mage::helper('groupdeals')->__('Notifications'),
				  'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_notifications')->toHtml(),
			));	
		}
		
	} elseif ($this->getRequest()->getParam('type', null)=='configurable') {
		$this->addTab('super_settings', array(
            'label'     => Mage::helper('catalog')->__('Configurable Product Settings'),
            'content'   => $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_super_settings')->toHtml(),
            'active'    => true
        ));	
	} else {
		$this->addTab('set', array(
			'label'     => Mage::helper('catalog')->__('Settings'),
			'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_settings')->toHtml(),
			'active'    => true
		));
	}
	
    return parent::_prepareLayout();
  }
  
  public function getProduct()
  {
        return Mage::registry('current_product');
  }
  
  public function getAttributeTabBlock()
  {
        if (is_null(Mage::helper('adminhtml/catalog')->getAttributeTabBlock())) {
            return 'adminhtml/catalog_product_edit_tab_attributes';
        }
        return Mage::helper('adminhtml/catalog')->getAttributeTabBlock();
  }

}