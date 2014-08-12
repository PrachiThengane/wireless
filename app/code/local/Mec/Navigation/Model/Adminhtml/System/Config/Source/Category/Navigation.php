<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Category_Navigation{
   
    
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
    	$options = array(
            array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT, 'label' => $helper->__('Default')),
        ); 
        
        $websites = $helper->getAvailavelWebsites();
        
        if(!empty($websites)){
        	$options[] = array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_PLAIN, 'label' => $helper->__('Plain'));
        	$options[] = array('value'=>Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN, 'label' => $helper->__('Dropdown'));
        }
    	
        return $options;
    	
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionHash(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            Mec_Navigation_Model_Layer::FILTER_TYPE_DEFAULT => $helper->__('Default'),
            Mec_Navigation_Model_Layer::FILTER_TYPE_PLAIN => $helper->__('Plain'),
            Mec_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN => $helper->__('Dropdown'),        	        	        	
        );
    }

}
