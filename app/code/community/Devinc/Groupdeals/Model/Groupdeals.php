<?php

class Devinc_Groupdeals_Model_Groupdeals extends Mage_Core_Model_Abstract
{		
					
	public function _construct()
	{
		parent::_construct();
		$this->_init('groupdeals/groupdeals');				
	} 
	
	public function updateAfterSave() {			 
		// update product url_key, sku, description, short description, weight
		$productId = Mage::getSingleton('core/session')->getGroupdealsRefresh();
		$groupdealsId = Mage::getSingleton('core/session')->getGroupdealsId();
		$product = Mage::getModel('catalog/product')->load($productId);
		$storeId = Mage::getSingleton('core/session')->getGroupdealsStoreId();
		$productSku = 'deal'.$productId; 	
		
		$updateProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId)->setSku($productSku)->setDescription($product->getGroupdealFineprint())->setShortDescription($product->getGroupdealHighlights())->save();				
		// Create url rewrite		
		$stores = Mage::app()->getStores();		
		foreach ($stores as $_eachStoreId => $val) 
		{
			$store = Mage::app()->getStore($_eachStoreId);			
			if ($store->getRootCategoryId()) {
				$_storeId = $store->getId();
				$product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($productId);
				
				Mage::getSingleton('catalog/url')->refreshProductRewrite($productId, $_storeId);
				$productUrlRewriteId = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/'.$productId)->getFirstItem()->getId();					
				if ($productUrlRewriteId!='') {
					Mage::getModel('core/url_rewrite')->load($productUrlRewriteId)->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdealsId)->save();
				}		
				
				if (count($product->getCategoryIds())>0) {
					foreach ($product->getCategoryIds() as $categoryId) {
						$categoryUrlRewriteId = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/'.$productId.'/category/'.$categoryId)->getFirstItem()->getId();					
						if ($categoryUrlRewriteId!='') {
							Mage::getModel('core/url_rewrite')->load($categoryUrlRewriteId)->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdealsId)->save();
						}	
					}										 
				}											 
			}										 
		} 	
		
		$this->refreshGroupdeals();
		
		Mage::getSingleton('core/session')->setGroupdealsStoreId();
		Mage::getSingleton('core/session')->setGroupdealsRefresh();
		Mage::getSingleton('core/session')->setGroupdealsId();
		
		return $this;
	}
	
	public function convertDateToUtc($datetime, $store_id = 0) {	
		 $offset = $this->getTimezoneOffset(Mage::getStoreConfig('general/locale/timezone', $store_id),'UTC');
		 $time=strtotime($datetime)+$offset;
		
  		   return date('Y-m-d H:i:s',$time);
	}
	
	public function getTimezoneOffset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset;
	}
	
	public function getFormatedPrice($_product, $price)
    {
       	$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		

		$_taxHelper  = Mage::helper('tax');

		$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
				
		$converted_price = Mage::helper('directory')->currencyConvert($price, $baseCurrencyCode, $currentCurrencyCode);	
		$price_tax = $_taxHelper->getPrice($_product, $converted_price, $_simplePricesTax);		
		
		return $currency_symbol.number_format($price_tax,2);
    }
	
	public function getGroupdealsSoldQty($_groupdeals) {	
		$sold_qty = 0;
		//'holded','closed','canceled','fraud'
		$sales_collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('status', array('nin' => array('canceled')));
		$start_date_time = $this->convertDateToUtc($_groupdeals->getDatetimeFrom());
		$end_date_time = $this->convertDateToUtc($_groupdeals->getDatetimeTo());
		foreach($sales_collection as $sale) {
			$sale_item_collection = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('created_at', array("from" =>	$start_date_time, "to" =>  $end_date_time, "datetime" => true))->addFieldToFilter('product_id', $_groupdeals->getProductId())->addFieldToFilter('order_id', $sale->getId());	
			if (count($sale_item_collection)>0) {
				foreach($sale_item_collection as $item) {							
					$sold_qty = $sold_qty+$item->getQtyOrdered();
				}
			}
		}
		
		return $sold_qty;
	}
	
	public function refreshGroupdeals() {
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$groupdeals_collection = Mage::getModel('groupdeals/groupdeals')->getCollection()->setOrder('groupdeals_id', 'DESC');
		
		$store_ids = array(0);	
		if (count($groupdeals_collection)>0) {	
			$website_collection = Mage::getModel('core/website')->getCollection();	
			if (count($website_collection)) {	
				foreach ($website_collection as $website) 
				{	
					$store_collection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website->getId());	
					if (count($store_collection)) {
						foreach ($store_collection as $store) {
							$store_ids[] = $store->getId();
							break;
						}
					}
				}		
			}	
			if (count($store_ids)) {
				foreach ($store_ids as $store_id) {
					Mage::getSingleton('core/session')->setMainDealSet();
					foreach ($groupdeals_collection as $groupdeal) {	
						$main_deal_set = Mage::getSingleton('core/session')->getMainDealSet();
						if ($groupdeal->getCity()!='' && strpos($main_deal_set, $groupdeal->getCity())===false) {
							$main = true;
						} else {					
							$main = false;
						}
						$this->refreshGroupdeal($groupdeal, $store_id, $main);
					}
				}
			}		
		}
	}	
	
	public function refreshGroupdeal($groupdeal, $_storeId = 0, $main) {
		$_product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($groupdeal->getProductId());	
			
		$website_id = Mage::getModel('core/store')->getCollection()->addFieldToFilter('store_id', $_storeId)->getFirstItem()->getWebsiteId();
		$product_website_ids = $_product->getWebsiteIds();
		
		if ($_product->getId()!='' && (in_array($website_id, $product_website_ids) || $_storeId==0)) {				
			if ($st_array = $this->checkStatusType($_product, $groupdeal, $_storeId, $main)) {		
				$productModel = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($groupdeal->getProductId());	
				$magento_version = (int)str_replace(".", "", Mage::getVersion());
				if ($_storeId!=0 && $magento_version>=1410) {			
					$data = array_keys($_product->getData());
					foreach ($data as $attribute_code) {
						$attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attribute_code);		
						if ($attribute && $attribute->getIsGlobal()!=1 && $attribute_code!='groupdeal_status' && $attribute_code!='groupdeal_type') {
							$default_value = Mage::getModel('catalog/product')->setStoreId(0)->load($groupdeal->getProductId())->getData($attribute_code);
							$store_value = $productModel->getData($attribute_code);
							if ($default_value==$store_value) {
								$productModel->setData($attribute_code, false);
							}								
						}
					}						 
				}								
				$productModel->setGroupdealStatus($st_array['status'])->setGroupdealType($st_array['type'])->save();
			}	
		} elseif ($_product->getId()!='') {
			$productModel = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($groupdeal->getProductId());		
			if ($productModel->getGroupdealStatus()!=2 || $productModel->getGroupdealType()!=3) {
				$magento_version = (int)str_replace(".", "", Mage::getVersion());
				if ($magento_version>=1410) {			
					$data = array_keys($_product->getData());
					foreach ($data as $attribute_code) {
						$attribute = Mage::getModel('catalog/product')->getResource()->getAttribute($attribute_code);		
						if ($attribute && $attribute->getIsGlobal()!=1 && $attribute_code!='groupdeal_status' && $attribute_code!='groupdeal_type') {
							$default_value = Mage::getModel('catalog/product')->setStoreId(0)->load($groupdeal->getProductId())->getData($attribute_code);
							$store_value = $productModel->getData($attribute_code);
							if ($default_value==$store_value) {
								$productModel->setData($attribute_code, false);
							}								
						}
					}				 
				}	
				$productModel->setGroupdealStatus(2)->setGroupdealType(3)->save();				
			}					
		}
	}
	
	public function checkStatusType($_product, $groupdeal, $_storeId = 0, $main) {
		$prev_groupdeal_status = $_product->getGroupdealStatus();
		$prev_groupdeal_type = $_product->getGroupdealType();
		
		$stockItem = $_product->getStockItem();
		if ($stockItem->getIsInStock()) {
			$is_in_stock = 1;
		} else {
			$is_in_stock = 2;
		}			
		$product_status = $_product->getStatus();	

		// get store datetime
		$storeDatetime = new DateTime();
		$storeDatetime->setTimezone(new DateTimeZone(Mage::getStoreConfig('general/locale/timezone', $_storeId)));				
		$current_date_time = $storeDatetime->format('Y-m-d H:i:s');
		
		if ($_product->getGroupdealStatus()!=5) {
			$groupdeal_status = 2;
			$groupdeal_type = 3;
			if ($current_date_time>=$groupdeal->getDatetimeFrom() && $current_date_time<=$groupdeal->getDatetimeTo() && $product_status!=2) {
				if ($is_in_stock!=2) {				
					if ($main) {
						$groupdeal_status = 1;
						$groupdeal_type = 1;
						$main_deal_set = Mage::getSingleton('core/session')->getMainDealSet();
						Mage::getSingleton('core/session')->setMainDealSet($main_deal_set.$groupdeal->getCity());
					} else {					
						$groupdeal_status = 1;
						$groupdeal_type = 2;
					}
				} else {				
					$groupdeal_status = 4;
					$groupdeal_type = 3;
				}
			} elseif ($current_date_time<=$groupdeal->getDatetimeFrom() && $product_status!=2) {							
				$groupdeal_status = 0;
				$groupdeal_type = 0;	
			} elseif ($current_date_time>=$groupdeal->getDatetimeTo() && $product_status!=2) {
				$groupdeal_status = 4;
				$groupdeal_type = 3; 
			} else {
				$groupdeal_status = 2;
				$groupdeal_type = 3;
			}		
		} else {
			$groupdeal_status = $_product->getGroupdealStatus();
			$groupdeal_type = $_product->getGroupdealType();			
		}
		
		if ($prev_groupdeal_status==$groupdeal_status && $prev_groupdeal_type==$groupdeal_type) {
			return false;
		} else {
			return array('status' => $groupdeal_status, 'type' => $groupdeal_type);
		}
	}
	
	public function getDecodeString($string, $store_id = null) {
		$string_array = array();
		$items = array();
		$string_array[0] = '';
		if (!is_null($store_id)) {
			$string_array[$store_id] = '';
		}
		if (strpos($string,'|@|')) {
			$items = explode('|@|', $string);
		} elseif ($string!='' && strpos($string,'||')) {
			$items[] = $string;
		} elseif (isset($store_id)) {
			$items[] = $store_id.'||'.$string;			
		} elseif ($string!='') {
			$items[] = '0||'.$string;		
		}
		if (count($items)!=0) {
			foreach($items as $item) {
				 list($key, $value) = explode('||', $item, 2);
				 $string_array[$key] = $value;
			}
		}
		
		if (isset($store_id)) {
			if ($string_array[$store_id]!='') {
				return $string_array[$store_id];
			} else {
				return $string_array[0];			
			}
		}
		
		return $string_array;
	}
	
	public function getEncodeString($string_array) {
		$array_keys = array_keys($string_array);
		$string = '';
		
		$i = 0;
		foreach ($string_array as $string_item) {
			$items[] = $array_keys[$i].'||'.$string_item;
			$i++;
		}	
		$string = implode('|@|', $items);
		
		return $string;
	}
	
	public function createAttributeSet()
	{ 
		$exists  = Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('attribute_set_name','Group Deal')->getFirstItem()->getId();
		if ($exists=='') {
			$entityTypeId   = Mage::getModel('catalog/product')->getResource()->getTypeId();

			/* @var $model Mage_Eav_Model_Entity_Attribute_Set */
			$model  = Mage::getModel('eav/entity_attribute_set')
				->setEntityTypeId($entityTypeId);

			$name = 'Group Deal';
			$model->setAttributeSetName($name);		

			$model->validate();
			$model->save();
			$model->initFromSkeleton(Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId());
			
			$model->save();
			$attribute_set_id = $model->getId();
			$ccdModel = new Mage_Core_Model_Config();
			$ccdModel->saveConfig('groupdeals/attribute_set_id', $attribute_set_id, 'default', 0);			

			$setInfo['SetID'] = $attribute_set_id;
			$setInfo['GroupID'] = Mage::getModel('eav/entity_attribute_group')->getCollection()->addFieldToFilter('attribute_set_id', $attribute_set_id)->addFieldToFilter('default_id', 1)->getFirstItem()->getId();
			 
			$this->createAttribute('Fine Print', 'groupdeal_fineprint', $setInfo, 'textarea');
			$this->createAttribute('Highlights', 'groupdeal_highlights', $setInfo, 'textarea');
			$this->createAttribute('Deal Type', 'groupdeal_type', $setInfo, 'select');
			$this->createAttribute('Deal Status', 'groupdeal_status', $setInfo, 'select');
		} else {
			$ccdModel = new Mage_Core_Model_Config();
			$ccdModel->saveConfig('groupdeals/attribute_set_id', $exists, 'default', 0);	
		}
		
		return $this;
	}
	
	function createAttribute($labelText, $attributeCode, $setInfo = -1, $type = null)
	{ 
		$labelText = trim($labelText);
		$attributeCode = trim($attributeCode);
 
		if($labelText == '' || $attributeCode == '')
		{
			return false;
		}
		
		$is_attribute = Mage::getModel('eav/entity_attribute')->getCollection()->addFieldToFilter('attribute_code', array('eq', $attributeCode))->getFirstItem()->getId();
		if($is_attribute!='')
		{
			return false;
		}
		   
		$productTypes = array('simple', 'virtual', 'configurable', 'downloadable');
 
		if($setInfo != -1 && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false))
		{
			return false;
		}
		
		//>>>> Build the data structure that will define the attribute. See 
		//	   Mage_Adminhtml_Catalog_Product_AttributeController::saveAction(). 
		if ($type=='textarea') {
			$data = array(
							'is_global'					   => '0',
							'frontend_input'				   => 'textarea',
							'default_value_text'			   => '',
							'default_value_yesno'		   => '0',
							'default_value_date'			   => '',
							'default_value_textarea'		   => '',
							'is_unique'					   => '0',
							'is_required'				   => '1',
							'frontend_class'				   => '',
							'is_searchable'				   => '1',
							'is_visible_in_advanced_search' => '1',
							'is_comparable'				   => '1',
							'is_used_for_promo_rules'	   => '0',
							'is_html_allowed_on_front'	   => '1',
							'is_visible_on_front'		   => '0',
							'used_in_product_listing'	   => '0',
							'used_for_sort_by'			   => '0',
							'is_wysiwyg_enabled'			   => '1',
							'is_configurable'			   => '0',
							'is_filterable'				   => '0',
							'is_filterable_in_search'	   => '0',
							'backend_type'				   => 'text',
							'default_value'				   => '',
						); 
		} elseif ($type=='select') {
			$data = array(
							'is_global'					   => '2',
							'frontend_input'				   => 'select',
							'default_value_text'			   => '',
							'default_value_yesno'		   => '0',
							'default_value_date'			   => '',
							'default_value_textarea'		   => '',
							'is_unique'					   => '0',
							'is_required'				   => '0',
							'frontend_class'				   => '',
							'is_searchable'				   => '0',
							'is_visible_in_advanced_search' => '0',
							'is_comparable'				   => '0',
							'is_used_for_promo_rules'	   => '0',
							'is_html_allowed_on_front'	   => '0',
							'is_visible_on_front'		   => '0',
							'used_in_product_listing'	   => '0',
							'used_for_sort_by'			   => '0',
							'is_wysiwyg_enabled'			   => '0',
							'is_configurable'			   => '0',
							'is_filterable'				   => '0',
							'is_filterable_in_search'	   => '0',
							'backend_type'				   => 'int',
							'default_value'				   => '',
						); 
		
		} else {
			return false;
		}
 
		// Valid product types: simple, grouped, configurable, virtual, bundle, downloadable, giftcard
		$data['apply_to']		= $productTypes;
		$data['attribute_code'] = $attributeCode;
		$data['frontend_label'] = array(0 => $labelText); 
	   
		//>>>> Build the model.
 
		$model = Mage::getModel('catalog/resource_eav_attribute');
 
		$model->addData($data);
 
		if($setInfo != -1)
		{
			$model->setAttributeSetId($setInfo['SetID']);
			$model->setAttributeGroupId($setInfo['GroupID']);
		}
 
		$entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
		$model->setEntityTypeId($entityTypeID);
 
		$model->setIsUserDefined(1);			
		$model->save(); 
		
		if ($attributeCode=='groupdeal_type') {
			Mage::getModel('catalog/resource_eav_attribute')->load($model->getId())->setSourceModel('groupdeals/source_type')->save();
		} elseif ($attributeCode=='groupdeal_status') {
			Mage::getModel('catalog/resource_eav_attribute')->load($model->getId())->setSourceModel('groupdeals/source_status')->save();
		}

		return $this;
	}
}