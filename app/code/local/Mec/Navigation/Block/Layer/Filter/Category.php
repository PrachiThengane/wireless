<?php
	
class Mec_Navigation_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
	
	public function getRemoveUrl($ajax = false)
    {
        $query = array($this->_filter->getRequestVar()=>null);
        $params['_nosid']       = true;
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = false;
        
        $params['_query']['ajax'] = null;
        
        if($ajax){
        	
        	$params['_query']['ajax'] = true;
        	
        	
        }
        
        
        return Mage::getUrl('*/*/*', $params);
    }
	
	public function getItems(){
		
	    if(Mage::helper('mec_navigation')->isMecNavigation() && 
	       Mage::getStoreConfigFlag('mec_navigation/category/active')){

	        if(!$this->ajaxEnabled()){
    						
    			$items = parent::getItems();;
    			
    			foreach($items as $key=>$item){
    				
    			    if($category = Mage::getModel('catalog/category')->load($item->getValue())){				
    					
    					$items[$key]->setUrl($category->getUrl());
    					
    				}
    				
    			}
    			
    			return $items;
    			
    		}
	    }
	    
		return parent::getItems();
		
	}
	
	public function getPopupId(){
		
		return 'category';
		
	}
	
	public function ajaxEnabled(){
		
	    if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'){
	        $is_ajax = true; 
	    }else{
	        $is_ajax = Mage::registry('current_category') && 
                       Mage::registry('current_category')->getisAnchor() &&
                       (Mage::registry('current_category')->getDisplayMode() != Mage_Catalog_Model_Category::DM_PAGE);
	    }
	    
	    $is_ajax = $is_ajax && Mage::getStoreConfigFlag('mec_navigation/category/ajax_enabled');
	    		
		return $is_ajax;

	}
	
	public function canShowMinimized(){
		
		if('true' === Mage::app()->getFrontController()->getRequest()->getParam('cat_is_open')){
		
			return false;
		
		}elseif('false' === Mage::app()->getFrontController()->getRequest()->getParam('cat_is_open')){
			
			return true;
			
		}
		
		
		return (bool) Mage::getStoreConfigFlag('mec_navigation/category/show_minimized');
		
	}
	
	public function canShowPopup(){
		
		return (bool) Mage::getStoreConfigFlag('mec_navigation/category/show_help');
		
	}
	
	public function getPopupText(){
		
		return trim(Mage::getStoreConfig('mec_navigation/category/popup_text'));
		
	}
	
	public function getPopupWidth(){
		
		return (int) Mage::getStoreConfig('mec_navigation/category/popup_width');
		
	}
	
	public function getPopupHeight(){
		
		return (int) Mage::getStoreConfig('mec_navigation/category/popup_height');
		
	}
	
	public function canShowCheckbox(){
		
		return (bool) Mage::getStoreConfigFlag('mec_navigation/category/show_checkbox');
		
	}
	
	public function canShowLabels(){
		
		return (bool) Mage::getStoreConfigFlag('mec_navigation/category/show_image_name');
		
	}
	
	public function getImageWidth(){
		
		return (int) Mage::getStoreConfig('mec_navigation/category/image_width');
		
	}
	
	public function getImageHeight(){
		
		return (int) Mage::getStoreConfig('mec_navigation/category/image_height');
		
	}
	
	public function getImageAlign(){
		
		switch(Mage::getStoreConfig('mec_navigation/category/image_align')):
		
			default:
				
				$image_align = 'default';
				
			break;
			
			case (1):
				
				$image_align = 'horizontally';
				
			break;
			
			case (2):
				
				$image_align = '2-columns';
				
			break;
			
		endswitch;
		
		return $image_align;
		
	}
	
    public function canShowResetFirler(){
		
		return (bool) Mage::getStoreConfig('mec_navigation/category/filter_reset');
		
	}
	
    /**
     * Initialize filter template
     *
     */
	
    public function __construct()
    {
        parent::__construct();
        
        if(Mage::helper('mec_navigation')->isMecNavigation() &&
           Mage::getStoreConfigFlag('mec_navigation/category/active')){
        	
        	$type = Mage::getStoreConfig('mec_navigation/category/filter_type');
        	
        	switch($type): 
        	
	        	default:
	        	
	        		$this->_template = ('mec/navigation/layer/filter/category/default.phtml');
	        	
	        	break;
	        	
	        	case(Mec_Navigation_Model_Layer::FILTER_TYPE_IMAGE):
	        	
	        		$this->_template = ('mec/navigation/layer/filter/image.phtml');
	        	
	        	break;
	        	
	        	case(Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
	        	
	        		$this->_template = ('mec/navigation/layer/filter/dropdown.phtml');
	        	
	        	break;
        	
        	endswitch;
        	
        }
        
    }
    
    public function getFilterType(){
        return Mage::getStoreConfig('mec_navigation/category/filter_type');    
    }
    
    public function getInBlockHeight(){
        return Mage::getStoreConfig('mec_navigation/category/inblock_height');    
    }
    
}
