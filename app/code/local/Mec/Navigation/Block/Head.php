<?php

class Mec_Navigation_Block_Head extends Mage_Core_Block_Template
{    
    protected function _prepareLayout()
    { 
        parent::_prepareLayout();
        if(Mage::helper('mec_navigation')->isMecNavigation()){         	
        	
        	$styles_block = $this->getLayout()->createBlock('mec_navigation/styles', 'advancednavigation_styles')->setTemplate('mec/navigation/header/styles.php');	        
	        $this->getLayout()->getBlock('head')->setChild('advancednavigation_styles', $styles_block);
	            
	        $this->getLayout()->getBlock('head')->addjs('mec/advanced-navigation.js');
	        $this->getLayout()->getBlock('head')->addjs('mec/category-navigation.js');
	        $this->getLayout()->getBlock('head')->addCss('css/mec/advanced-navigation.css');
        	                         
        }       
    }
}
