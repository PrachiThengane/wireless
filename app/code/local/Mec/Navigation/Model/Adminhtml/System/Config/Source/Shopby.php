<?php
	
class Mec_Navigation_Model_Adminhtml_System_Config_Source_Shopby{
    
	const LEFT_COLUMN = 0;
	const CONTENT = 1;
	const RIGHT_COLUMN = 2;
		
    public function toOptionArray(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            array('value'=>self::LEFT_COLUMN, 'label' => $helper->__('Left Column')),
        	array('value'=>self::CONTENT, 'label' => $helper->__('Content')),        	        	        	
        	array('value'=>self::RIGHT_COLUMN, 'label' => $helper->__('Right Column')),
        );
    	
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionHash(){
    	
    	$helper = Mage::helper('mec_navigation');
    	
        return array(
            self::LEFT_COLUMN => $helper->__('Left Column'),
            self::CONTENT => $helper->__('Content'), 
            self::RIGHT_COLUMN => $helper->__('Right Column'),                   	
        );
    }

}
