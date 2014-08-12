<?php
	
	class Mec_Navigation_Model_Resource_Eav_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection{
		
		
		public function getSelectCountSql()
	    {
	    	
	        $select = parent::getSelectCountSql();
	        $select->reset(Zend_Db_Select::GROUP);
	        
	        return $select;
	    }
		
	}
