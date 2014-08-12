<?php

class Mec_Navigation_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{        
    protected function _construct()
    {
        parent::_construct();        
        if($this->isAjaxPager()){
            $this->setTemplate('mec/navigation/html/pager.phtml');
        }else{
            $this->setTemplate('page/html/pager.phtml');
        }
    }
    
    public function getPagerUrl($params=array())
    {
        if($this->isAjaxPager()){    	
    	   $params['ajax'] = 1;    	
    	}else{
    	   $params['ajax'] = null; 
    	}    	
    	    	    	
        $urlParams = array();
        $urlParams['_nosid']    = true;
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        
        return Mage::helper('mec_navigation')->getFilterUrl('*/*/*', $urlParams);
    }
    
    public function isAjaxPager(){
        return Mage::helper('mec_navigation')->isMecNavigation() &&
               Mage::getStoreConfigFlag('mec_navigation/general/pager') &&  
               ((Mage::registry('current_category') && Mage::registry('current_category')->getisAnchor()) ||
                (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch')); 
    }
    
}
