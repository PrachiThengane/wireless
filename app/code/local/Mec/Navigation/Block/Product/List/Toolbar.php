<?php

class Mec_Navigation_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{    
	protected $first_render = true; 
	
	protected function _toHtml(){
    	if(Mage::helper('mec_navigation')->isMecNavigationAjax()){
            $this->setTemplate('mec/navigation/catalog/product/list/toolbar.phtml');
        }                
        $html = '';
        if (Mage::getStoreConfig('mec_navigation/general/show_shopby') == Mec_Navigation_Model_Adminhtml_System_Config_Source_Shopby::CONTENT){
	        if ($this->first_render){
	        	$shop_by = false;
	        	if ($this->getLayout()->getBlock('catalogsearch.leftnav')){
	        		$shop_by = $this->getLayout()->getBlock('catalogsearch.leftnav');
	        	}elseif ($this->getLayout()->getBlock('catalog.leftnav')){
	        		$shop_by = $this->getLayout()->getBlock('catalog.leftnav');
	        	}elseif($this->getLayout()->getBlock('mec.enterprise.catalogsearch.leftnav')){
	        		$shop_by = $this->getLayout()->getBlock('mec.enterprise.catalogsearch.leftnav');
	        	}elseif($this->getLayout()->getBlock('mec.enterprise.catalog.leftnav')){
	        		$shop_by = $this->getLayout()->getBlock('mec.enterprise.catalog.leftnav');
	        	}	 
	        	if ($shop_by){
	        		$shop_by->setShopByInContent(true);
	        		$html .= $shop_by->toHtml();
	        		$shop_by->setShopByInContent(false);
	        	}         	
	        	$this->first_render = false;
	        }
        }
        if (!$this->getTemplate()) {
            return $html;
        }
        $html .= $this->renderView();
        return $html;
    }
    
    public function getPagerUrl($params=array()){
    	
    	if(Mage::helper('mec_navigation')->isMecNavigationAjax()){    	    	    
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
    
    
     public function getPagerHtml()
     {                  
         $pagerBlock = $this->getChild('mec_navigation_product_list_toolbar_pager');
         
         if (!$pagerBlock)
         {
             $pagerBlock = $this->getLayout()->createBlock('mec_navigation/product_list_toolbar_pager', 'mec_navigation_product_list_toolbar_pager');
             $this->insert($pagerBlock);
         }     
         
         if ($pagerBlock instanceof Varien_Object) 
         {

            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

             return $pagerBlock->toHtml();
         }

         return '';

     }
     
             
}
