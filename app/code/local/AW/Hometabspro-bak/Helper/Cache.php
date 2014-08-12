<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Hometabspro
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */?>
<?php
/**
 * Home Tabs Pro Data Helper
 */
class AW_Hometabspro_Helper_Cache extends Mage_Core_Helper_Abstract
{
    protected $_resource;
	protected $_connection;
	
	protected $_storeId;
	
	protected $_statusT;
	protected $_statusAttrId;
	
	protected $_visibilityT;
	protected $_visibilityAttrId;
	protected $_catalogVisibility = Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG;
	protected $_allVisibility = Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH;
	
	protected $_websiteT;
	protected $_websiteId;
	 
	
		public function __construct() {
		 
		
			$this->_resource = $this->_initResource();
			$this->_connection = $this->_initConnection();
			$this->_session = $this->_getSession();
			$this->_initEnv();
			
		
		}
		
		private function _getSession() {
		
			return Mage::getSingleton('core/session');
			
		}
		
		private function _initEnv() {
		
			$this->_storeId = Mage::app()->getStore()->getId();
	 
			$this->_statusT = $this->_resource->getTableName('catalog/product')."_int";
			$this->_statusAttrId = Mage::getResourceModel('catalog/product')->getAttribute('status')->getAttributeId();
			$this->_visibilityT = $this->_statusT;
			$this->_visibilityAttrId = Mage::getResourceModel('catalog/product')->getAttribute('visibility')->getAttributeId();
	 
			$this->_websiteT = $this->_resource->getTableName('catalog/product_website');
			$this->_websiteId = Mage::app()->getWebsite()->getId();

                        $this->_stockStatusT = $this->_resource->getTableName('cataloginventory/stock_status');
	 
		}
		
		private function _initResource() {
		
			return Mage::getSingleton('core/resource');	
			
		}
		
		private function _initConnection() {		
			 
			return $this->_resource->getConnection('core_read');
			 
		}
		
		protected function _joinBase($select,$data) {
		
                     $select 
			 ->join(array("statusTableDefault" => $this->_statusT), "{$data['key']} = statusTableDefault.entity_id AND statusTableDefault.attribute_id = '{$this->_statusAttrId}'
			   AND statusTableDefault.store_id = 0",array('status' => 'IF(statusTable.value_id > 0,statusTable.value,statusTableDefault.value)'))
			  ->joinLeft(array("statusTable" => $this->_statusT), "{$data['key']} = statusTable.entity_id AND statusTable.attribute_id = '{$this->_statusAttrId}'
			 AND statusTable.store_id = '{$this->_storeId}'",array())
		     ->where("IF(statusTable.value_id > 0,statusTable.value,statusTableDefault.value) = '1'")
			  
			  ->join(array("visibTableDefault" => $this->_visibilityT),"{$data['key']} = visibTableDefault.entity_id AND visibTableDefault.attribute_id = '{$this->_visibilityAttrId}'
			  AND visibTableDefault.store_id = 0",array('visibility' => 'IF(visibTable.value_id > 0,visibTable.value,visibTableDefault.value)'))
			  ->joinLeft(array("visibTable" => $this->_visibilityT),"{$data['key']} = visibTable.entity_id AND visibTable.attribute_id = '{$this->_visibilityAttrId}'
			  AND visibTable.store_id = '{$this->_storeId}'",array())
			  ->where("((IF(visibTable.value_id > 0,visibTable.value,visibTableDefault.value) = '{$this->_catalogVisibility}') OR
			  (IF(visibTable.value_id > 0,visibTable.value,visibTableDefault.value) = '{$this->_allVisibility}'))")			  
			 ->join(array("websiteTable" => $this->_websiteT),"websiteTable.product_id = {$data['key']} AND websiteTable.website_id = '{$this->_websiteId}'",array());

                         if(Mage::helper('hometabspro')->confShowOutOfStock()!=1) {                              
                             $select
                              ->join(array('stockt'=>$this->_stockStatusT), "{$data['key']} = stockt.product_id AND stockt.stock_status = '1'  AND stockt.website_id = '{$this->_websiteId}'",array('stock_status'));
			}
 
                       return $select;
 
		}
		
		
		public static function getFlag($key) {		
		
				if(Mage::helper('hometabspro')->confGetCustomParam($key, 'enabled')) {	
					$count = (int) Mage::helper('hometabspro')->confGetCustomParam($key, 'products_count');
					
					if($count > 0) { return $count;	}	

						return false;
				}
		
			return false;
		}
	
	
	
	
	
	
}