<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
		
        $this->_objectId = 'id';
        $this->_blockGroup = 'groupdeals';
        $this->_controller = 'adminhtml_groupdeals';
        
        $this->_updateButton('save', 'label', Mage::helper('groupdeals')->__('Save Deal'));
        $this->_updateButton('save', 'onclick', 'save()');		
        $this->_updateButton('delete', 'label', Mage::helper('groupdeals')->__('Delete Deal'));
        $this->_updateButton('delete', 'onclick', 'deleteConfirm(\'Are you sure you want to do this?\', \''.$this->getUrl('*/*/delete', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getProductId())).'\')');
		$this->_removeButton('reset');		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);	
		
		$storeId = $this->getRequest()->getParam('store', 0);
		
		if (($this->getRequest()->getParam('groupdeals_id')!=null || $this->getRequest()->getParam('groupdeals_id')!=0) && $this->getProduct()->getTypeId()=='virtual') {
			$data = Mage::registry('groupdeals_data')->getData();			
			
			$_groupdeals = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
			$_product = $this->getProduct();
			
			// check deal's status for all stores
			$stores = Mage::app()->getStores();		
			$finished = true;
			foreach ($stores as $_eachStoreId => $val) 
			{	
				$_storeId = Mage::app()->getStore($_eachStoreId)->getId();
				$product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($this->getProductId());	
				if ($product->getGroupdealStatus()==1 || $product->getGroupdealStatus()==0) {
					$finished = false;
				}
			}
			
			$sold_qty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($_groupdeals);	
			
			if (($_product->getGroupdealStatus()==4 || $_product->getGroupdealStatus()==2) && $sold_qty>=$data['minimum_qty']) {
				if ($finished) {
					$emailMessage = 'Are you sure you want to email the coupons to your customers?';
				} else {
					$emailMessage = 'The deal has not ended yet on all of the stores. Are you sure you want to email the coupons to your customers?';
				}
			} elseif (($_product->getGroupdealStatus()==4 || $_product->getGroupdealStatus()==2) && $sold_qty<$data['minimum_qty']) {
				if ($finished) {
					$emailMessage = 'The target was not met! Are you sure you want to email the coupons to your customers?';	
				} else {
					$emailMessage = 'The target was not met and the deal has not ended yet on all of the stores. Are you sure you want to email the coupons to your customers?';
				}				
			} else {
				$emailMessage = 'The deal has not ended yet! Are you sure you want to email the coupons to your customers?';			
			}
			
			$this->_addButton('email_coupons', array(
				'label'     => Mage::helper('adminhtml')->__('Email Coupons'),
				'onclick'   => 'deleteConfirm(\''.$emailMessage.'\', \''.$this->getUrl('*/*/email', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getProductId())).'\')',           	
			));		 
			
			$this->_addButton('email_merchants', array(
				'label'     => Mage::helper('adminhtml')->__('Email Coupons CSV to Merchant'),
				'onclick'   => 'emailMerchantCoupons()',           	
			));		 
			
			$this->_addButton('preview_coupon', array(
				'label'     => Mage::helper('adminhtml')->__('Preview Coupon'),
				'onclick'   => 'previewCoupon()',
			));		
			
			$groupdeals_id = 'groupdeals_id/'.$this->getRequest()->getParam('groupdeals_id').'/';
			$product_id = 'id/'.$this->getProductId().'/';
		} elseif (($this->getRequest()->getParam('groupdeals_id')!=null || $this->getRequest()->getParam('groupdeals_id')!=0) && ($this->getProduct()->getTypeId()=='simple' || $this->getProduct()->getTypeId()=='configurable')) {
			$data = Mage::registry('groupdeals_data')->getData();			
			$groupdeals_id = 'groupdeals_id/'.$this->getRequest()->getParam('groupdeals_id').'/';
			$product_id = 'id/'.$this->getProductId().'/';			
		} else {
			$groupdeals_id = '';
			$product_id = '';
		}
		
		//$attribute_set_id = Mage::getStoreConfig('groupdeals/attribute_set_id');
		$attribute_set_id = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('attribute_set_name','Group Deal')->getFirstItem()->getId();
		$type = $this->getRequest()->getParam('type', null);
		
        $this->_formScripts[] = "
    
    		function setDealSettings() {
				setLocation('".$this->getUrl('*/*/new')."'+'set/".$attribute_set_id."/type/'+document.getElementById('product_type').value+'/');
			}	
			
   			function setSettings(urlTemplate, setElement, typeElement) {
   			    var template = new Template(urlTemplate, productTemplateSyntax);
   			    setLocation(template.evaluate({attribute_set:$(setElement),type:$(typeElement)}));
   			}
			
			function setSuperSettings(urlTemplate, attributesClass, validateField) {
        		var attributesFields = $$('.' + attributesClass);
        		var attributes = Form.serializeElements(attributesFields, true).attribute;
        		if(typeof attributes == 'string') {
        		    attributes = [attributes];
        		}

       			 if(!attributes) {
        		    $(validateField).value = 'no-attributes';
        		} else {
         		   $(validateField).value = 'has-attributes';
        		}

        		if (editForm.validator.validate()) {
					setLocation('".$this->getUrl('*/*/new')."'+'attributes/'+encode_base64(attributes.join(',')).replace(new RegExp('/','g'),'%2F').replace(new RegExp('=','g'),'%3D')+'/set/".$this->getRequest()->getParam('set', $attribute_set_id)."/type/".$this->getRequest()->getParam('type', 'configurable')."/');     		
				}
    		}

            function save(){
                editForm.submit('".$this->getUrl('*/*/save').$groupdeals_id.$product_id."type/".$type."/set/".$attribute_set_id."/store/".$storeId."/');
            }			
	
            function saveAndContinueEdit(){
                editForm.submit('".$this->getUrl('*/*/save').$groupdeals_id.$product_id."type/".$type."/set/".$attribute_set_id."/store/".$storeId."/back/edit/');
            }	
			
            function emailMerchantCoupons(){
                editForm.submit('".$this->getUrl('groupdeals/adminhtml_groupdeals/emailMerchant').$groupdeals_id.$product_id."');
            }	
			
            function previewCoupon(){
                return window.open('".$this->getUrl('groupdeals/adminhtml_groupdeals/preview').$groupdeals_id.$product_id."store/".$storeId."/');
            }	
			
			function checkMaxLength(Object, MaxLen)
			{
				if (Object.value.length > MaxLen-1) {
					Object.value = Object.value.substr(0, MaxLen);
				}
				return 1;
			}

			function regionReload(value)
			{    
				new Ajax.Updater('region_container', '".$this->getUrl('groupdeals/adminhtml_groupdeals/region')."?country='+value, {
					asynchronous:true, evalScripts:true, 
					onComplete:function(request, json){ 
						if (document.getElementById('country_id').value!='') { document.getElementById('city').disabled = '';document.getElementById('city').value = ''; } 
						else { document.getElementById('city').value = 'Select a country to enter city'; document.getElementById('city').disabled = 'disabled'; document.getElementById('region').value = 'Select a country to enter state/province'; document.getElementById('region').disabled = 'disabled'; } 
					}, 
					onLoading:function(request, json){}}); 
				return false;
			}   		
					
        ";
		if ($this->getRequest()->getParam('groupdeals_id')!=null || $this->getRequest()->getParam('groupdeals_id')!=0) {
			if ($data['country_id']=='') {
				$this->_formScripts[] .= "
					document.getElementById('region').value = 'Select a country to enter state/province';
					document.getElementById('city').value = 'Select a country to enter city';
					document.getElementById('region').disabled = 'disabled';
					document.getElementById('city').disabled = 'disabled';
				";        
			}
			
			if ($data['coupon_expiration_date']=='0000-00-00') {
				$this->_formScripts[] .= "
					document.getElementById('coupon_expiration_date').value = '';
				";        
			}
		} else {
			$this->_formScripts[] .= "
				document.getElementById('region').value = 'Select a country to enter state/province';
				document.getElementById('city').value = 'Select a country to enter city';
				document.getElementById('region').disabled = 'disabled';
				document.getElementById('city').disabled = 'disabled';
			";  			
		}			
		
		if (Mage::getModel('groupdeals/merchants')->isMerchant() && Mage::getModel('groupdeals/merchants')->getPermission('add_edit')==0) {
			$this->_removeButton('save');
			$this->_removeButton('saveandcontinue');
			$this->_removeButton('email_coupons');
			$this->_removeButton('email_merchants');
			$this->_removeButton('preview_coupon');
		}	
		
		if (Mage::getModel('groupdeals/merchants')->isMerchant() && Mage::getModel('groupdeals/merchants')->getPermission('delete')==0) {
			$this->_removeButton('delete');
		}
    }
	
    public function getHeaderText()
    {
        if(Mage::registry('groupdeals_data') && Mage::registry('groupdeals_data')->getId()) {
			if (!$this->getProduct()->getGroupdealType()) {
				$dealType = 'DEAL';
			} else {
				switch ($this->getProduct()->getGroupdealType()) {
					case 1:
						$dealType = 'MAIN DEAL';
						break;
					case 2:
						$dealType = 'SIDE DEAL';
						break;
					case 3:
						$dealType = 'RECENT DEAL';
						break;
					case 0:
						$dealType = 'FUTURE DEAL';
						break;
				}
			}
            return $dealType.' - '.$this->htmlEscape($this->getProduct()->getName());
        } else {
            return Mage::helper('groupdeals')->__('New Deal');
        }
    }	
	
	public function getProduct()
    {
        return Mage::registry('current_product');
    }
	
	public function getProductId()
    {
        return $this->getProduct()->getId();
    }
}