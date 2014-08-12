<?php
 	
	class Mec_Navigation_Model_Resource_Eav_Mysql4_Collection extends Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection{
		
		public function getSelectCountSql(){
	    	
	        $select = parent::getSelectCountSql();
	        $select->reset(Zend_Db_Select::GROUP);
	        
	        return $select;
	    }
		
	}
