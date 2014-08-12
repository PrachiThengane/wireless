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
class AW_Hometabspro_Helper_Cache_Topsellers extends AW_Hometabspro_Helper_Cache
{

	protected $_salesOrderItemT;
	protected $_salesOrderT;
     
		public function __construct() {	
		
			 parent::__construct();	
			 $this->_initVars();
		}
		
		 
		public function setCache($conf) {
		 
			$limit = $conf['limit'];	 
		
		if(version_compare(Mage::getVersion(),"1.3.3.0","<=")) {
		 
			 $select = $this->_connection->select()
              ->from(array('sales' => $this->_salesOrderItemT), array('product_id','orders_sum'=>'SUM(qty_ordered)'))
			  ->join(array('orders' => $this->_salesOrderVarcharT),'sales.order_id = orders.entity_id',array())
			  ->join(array('main'=>$this->_salesOrderT),'sales.order_id = main.entity_id',array())
			  ->where('main.store_id = ?',$this->_storeId)
			  ->where('orders.value = ?', 'complete')
              ->where('sales.parent_item_id IS NULL')             
			 ->group('sales.product_id')
			 ->order('orders_sum DESC')
			 ->limit($limit);
			 
		}	
		
		else {
		
			$select = $this->_connection->select()
              ->from(array('sales' => $this->_salesOrderItemT), array('product_id','orders_sum'=>'SUM(qty_ordered)'))
			  ->joinLeft(array('orders' => $this->_salesOrderT),'sales.order_id = orders.entity_id','status')
			  ->where('orders.status = ?', 'complete')
              ->where('sales.parent_item_id IS NULL')
              ->where("sales.store_id = '{$this->_storeId}'")	 
			  ->group('sales.product_id')
			 ->order('orders_sum DESC')
			 ->limit($limit);
		}	 
			$select = parent::_joinBase($select,array("key"=>"sales.product_id"));
			 
			$where = array_unique($this->_connection->fetchCol($select));
			Mage::getSingleton('core/session')->setHometabsProSellers($where);
			Mage::getSingleton('core/session')->setHometabsProSellersLimit($limit);
			
			 
		}
		
		private function _initVars() {
		
				$this->_salesOrderItemT = $this->_resource->getTableName('sales/order_item');
				$this->_salesOrderT = $this->_resource->getTableName('sales/order');
				 
			if(version_compare(Mage::getVersion(),"1.3.3.0","<=")) {
				$this->_salesOrderVarcharT = $this->_resource->getTableName('sales/order')."_varchar";				 
			}
				 
		}
		
	 
	
	
	
	
	
	
}