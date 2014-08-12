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
class AW_Hometabspro_Helper_Cache_Justadded extends AW_Hometabspro_Helper_Cache
{

	protected $_productT;	 
     
		public function __construct() {	
		
			 parent::__construct();	
			 $this->_initVars();
		}
		
		 
		public function setCache($conf) {
		 
			$limit = $conf['limit'];
			
			$select = $this->_connection->select()
              ->distinct()
              ->from(array('products'=>$this->_productT), array('entity_id'))
              ->order('created_at DESC')
			  ->limit($limit);
			 
			$select = parent::_joinBase($select,array("key"=>"products.entity_id"));
			 
			$where = array_unique($this->_connection->fetchCol($select));
			$this->_session->setHometabsProJustAdded($where);
			$this->_session->setHomeTabsProJustAddedLimit($limit);
			 
		}
		
		private function _initVars() {
		
				$this->_productT = $this->_resource->getTableName('catalog/product');			 
				
		}
		
	 
	
	
	
	
	
	
}