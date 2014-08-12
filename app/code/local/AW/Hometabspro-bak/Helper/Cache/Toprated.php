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
class AW_Hometabspro_Helper_Cache_Toprated extends AW_Hometabspro_Helper_Cache
{

	protected $_reviewSummaryT;
	protected $_reviewEntityT;
     
		public function __construct() {	
		
			 parent::__construct();	
			 $this->_initVars();
		}		
		 
		public function setCache($conf) {
		 
			$limit = $conf['limit'];
			 
			$select = $this->_connection->select()
				->from(array('summary'=>$this->_reviewSummaryT),array('summary.entity_pk_value','summary.rating_summary'))
				->where('summary.store_id = ?',$this->_storeId)
				->join(array('r_entity'=>$this->_reviewEntityT), "(summary.entity_type = r_entity.entity_id AND r_entity.entity_code = 'product')" , array())
				->join(array('r_main'=>$this->_reviewT), "summary.entity_pk_value = r_main.entity_pk_value",array('r_main.status_id'))
				->where('r_main.status_id = 1')	
				->order('summary.rating_summary DESC')				 
			    ->limit($limit);
			 
			$select = parent::_joinBase($select,array("key"=>"summary.entity_pk_value"));
			 
			$where = array_unique($this->_connection->fetchCol($select));
			$this->_session->setHometabsProTopRated($where);
		    $this->_session->setHometabsProTopRatedLimit($limit);
			 
		}
		
		private function _initVars() {
		
				$this->_reviewSummaryT = $this->_resource->getTableName('review/review_aggregate');
			    $this->_reviewEntityT = $this->_resource->getTableName('review/review_entity');
				$this->_reviewT = $this->_resource->getTableName('review/review');
				
		}
		
	 
	
	
	
	
	
	
}