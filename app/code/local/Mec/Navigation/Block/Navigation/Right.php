<?php

class Mec_Navigation_Block_Navigation_Right extends Mage_Core_Block_Template
{
        
    protected function _prepareLayout()
    {          
        
        $right = $this->getLayout()->getBlock('right');
        
        if ($right && Mage::helper('mec_navigation')->isMecNavigation() &&
            Mage::getStoreConfig('mec_navigation/rightcolumnsettings/active'))
        {   
            $right->unsetChild('mec.navigation.right');                  
            $page = Mage::getSingleton('cms/page');                                     
            if ($page->getData('page_id'))
            {                                                                       
                if ($page->getData('navigation_right_column'))
                {
                   $navigation_right = $this->getLayout()->createBlock('mec_navigation/navigation', 'mec.navigation.right')
                                                     ->setTemplate('mec/navigation/catalog/navigation/right.phtml');
                   $navigation_right->SetNavigationPlace(Mec_Navigation_Block_Navigation::RIGTH_COLUMN);  
                   $right->insert($navigation_right, '', false);
                }   
            }
            else
            {
                if (!Mage::getStoreConfig('mec_navigation/rightcolumnsettings/show_shopby')){
                    $navigation_right = $this->getLayout()->createBlock('mec_navigation/navigation', 'mec.navigation.right')
                                                     ->setTemplate('mec/navigation/catalog/navigation/right.phtml');
                    $navigation_right->SetNavigationPlace(Mec_Navigation_Block_Navigation::RIGTH_COLUMN);  
                    $right->insert($navigation_right, '', false);                
                }
            }
        }                               

        parent::_prepareLayout();
        
    }    
}
