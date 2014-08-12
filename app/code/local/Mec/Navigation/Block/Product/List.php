<?php
	
	class Mec_Navigation_Block_Product_List extends Mage_Catalog_Block_Product_List{
		
	    protected $_procartproductlist = null;
	    
		public function getAddToCompareUrl($product){
	        return $this->helper('mec_navigation/compare')->getAddUrl($product);
	    }
	    
    	public function getAddToCartUrl($product, $additional = array()){
        
        	$_modules = Mage::getConfig()->getNode('modules')->children();       	   	   
    	    $_modulesArray = (array)$_modules;	   
    	    if(isset($_modulesArray['Mec_Procart']) && $_modulesArray['Mec_Procart']->is('active')){
                if (Mage::helper('mec_procart')->isProCartEnable() && Mage::getStoreConfig('mec_procart/qty_settings/category_page'))
                    $additional['_query']['gpc_prod_id'] = $product->getId();        
    	    }    
            return parent::getAddToCartUrl($product, $additional);
            
        }   
    
        public function getProcartProductList(){
            
            $_modules = Mage::getConfig()->getNode('modules')->children();       	   	   
    	    $_modulesArray = (array)$_modules;	   
    	    if(isset($_modulesArray['Mec_Procart']) && $_modulesArray['Mec_Procart']->is('active')){
                if (!$this->_procartproductlist){             
                     $this->_procartproductlist = array();
                     $helper = Mage::helper('mec_procart');
                     
                     foreach ($this->getLoadedProductCollection() as $_product){                 
                         $product = Mage::getModel('catalog/product')->load($_product->getId());                 
                         $this->_procartproductlist[$product->getId()] = $helper->getProcartProductData($product);
                     }
                     
                }
                
                return Mage::helper('core')->jsonEncode($this->_procartproductlist);          
    	    }    
        }
        
		public function getToolbarHtml()
	    {
	    	$toolbar = $this->getChild('toolbar');
	        return $toolbar->toHtml(); 
	    }
	    
    }
