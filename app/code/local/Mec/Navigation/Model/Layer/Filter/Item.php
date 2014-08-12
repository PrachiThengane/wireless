<?php

class Mec_Navigation_Model_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
    
    public function getRemoveUrl($ajax = false)
    {    	
        $query = array($this->getFilter()->getRequestVar()=>$this->getFilter()->getResetValue($this->getValue()));
        $params['_nosid']       = true;
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = false;
        
        $params['_query']['ajax'] = null;
        
        if($ajax){
        	
        	$params['_query']['ajax'] = true;
        	
        	
        }        
        
        return Mage::helper('mec_navigation')->getFilterUrl('*/*/*', $params);
    }
    
    public function getUrl($ajax = false)
    {
    	
    	if($this->hasData('url')){
    		
    		return $this->getData('url');
    		
    	}
    	
    	$query = array(
	            $this->getFilter()->getRequestVar()=>$this->getValue(),
	            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
	        );
	    
	    $query['ajax'] = null;
	    
    	if($ajax){
        	
        	$query['ajax'] = 1;
        	
        }
        
        return Mage::helper('mec_navigation')->getFilterUrl('*/*/*', array('_current'=>true, '_nosid'=>true, '_use_rewrite'=>true, '_query'=>$query, '_escape'=>false)); 
        
    }
    
}
