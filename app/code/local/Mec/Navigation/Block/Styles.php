<?php

class Mec_Navigation_Block_Styles extends Mage_Core_Block_Template
{    
	
	public function getStoreCategories()
    {                        
        $root_category = Mage::app()->getStore()->getRootCategoryId();
        
        $tree = Mage::getResourceModel('catalog/category_tree');        
        $nodes = $tree->loadNode($root_category)
            ->loadChildren(1)
            ->getChildren();
                    
        $collection = Mage::getResourceModel('catalog/category_collection');    
        $collection->addAttributeToSelect('*');    
                
        $tree->addCollectionData($collection, Mage::app()->getStore()->getId(), $root_category, true, true);
            
        return $nodes;    
    }
    
    public function getNavigationCatigoryUrl(){
    	$page = Mage::getSingleton('cms/page');                                     
        if ($page->getData('page_id')){
        	$categoty_id = $page->getData('navigation_category_id');
        	if ($categoty_id){
	        	$categoty = Mage::getModel('catalog/category')->load($categoty_id);
	        	if ($categoty && $categoty->getIsActive()){
	        		return $categoty->getUrl() . '?ajax=1';  
	        	}
        	}
        }    
        
        return false;
    }
    
}
