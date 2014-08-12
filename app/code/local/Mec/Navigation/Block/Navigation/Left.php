<?php

class Mec_Navigation_Block_Navigation_Left extends Mage_Core_Block_Template
{
        
    protected function _prepareLayout()
    {          
        
        $left = $this->getLayout()->getBlock('left');        
                                
        if ($left && Mage::helper('mec_navigation')->isMecNavigation() &&
            Mage::getStoreConfig('mec_navigation/category/active'))
        {   
            $left->unsetChild('mec.navigation.left');                  
            $page = Mage::getSingleton('cms/page');                                     
            if ($page->getData('page_id'))
            {                                                                       
                if ($page->getData('navigation_left_column'))
                {
                   $navigation_left = $this->getLayout()->createBlock('mec_navigation/navigation', 'mec.navigation.left')
                                                     ->setTemplate('mec/navigation/catalog/navigation/left.phtml');
                   $navigation_left->SetNavigationPlace(Mec_Navigation_Block_Navigation::LEFT_COLUMN);  
                   $left->insert($navigation_left);
                }   
            }
            else
            {                                                                                                   
                if (!Mage::getStoreConfig('mec_navigation/category/show_shopby'))
                {
                    $navigation_left = $this->getLayout()->createBlock('mec_navigation/navigation', 'mec.navigation.left')
                                                     ->setTemplate('mec/navigation/catalog/navigation/left.phtml');
                    $navigation_left->SetNavigationPlace(Mec_Navigation_Block_Navigation::LEFT_COLUMN);                
                    $left->insert($navigation_left);
                }
            }
        }
                                                 
        parent::_prepareLayout();
        
    }    
}
